<?php

namespace App\Http\Controllers;

use App\Http\Requests\PickupRequest;
use App\Models\NinjavanData;
use App\Models\ParcelAssignment;
use App\Models\Locker;
use Illuminate\Support\Facades\Http;

class CustomerPickupController extends Controller
{
    public function showForm()
    {
        return view('customer.pickup');
    }

    public function process(PickupRequest $request)
    {
        // Find the parcel by pickup_code
        $parcel = NinjavanData::where('pickup_code', $request->pickup_code)->firstOrFail();

        // Check if already picked up
        if ($parcel->picked_up_at) {
            return back()->withErrors(['pickup_code' => 'This parcel was already collected.']);
        }

        // Find the locker associated with this parcel (via parcel_assignments)
        $assignment = ParcelAssignment::where('parcel_id', $parcel->id)
            ->whereNull('picked_up_at')
            ->first();

        if (!$assignment) {
            return back()->withErrors(['pickup_code' => 'No active assignment found for this parcel.']);
        }

        $locker = Locker::find($assignment->locker_id);

        // Send unlock command to ESP8266 if IP is set
        if ($locker && $locker->esp_ip) {
            try {
                $response = Http::timeout(5)->get("http://{$locker->esp_ip}/unlock");
                if (!$response->successful()) {
                    \Log::error("Unlock failed for locker {$locker->locker_number}, ESP responded with: " . $response->status());
                    // Still proceed? Maybe show a warning but still mark as picked up.
                } else {
                    \Log::info("Unlock command sent to locker {$locker->locker_number}");
                }
            } catch (\Exception $e) {
                \Log::error("Could not reach ESP8266 at {$locker->esp_ip}: " . $e->getMessage());
                // Optionally: return back()->withErrors(['pickup_code' => 'Locker communication failed. Please contact staff.']);
            }
        } else {
            \Log::warning("No ESP IP set for locker associated with parcel {$parcel->id}");
        }

        // Mark as picked up in both tables
        $parcel->update(['picked_up_at' => now()]);
        if ($assignment) {
            $assignment->update(['picked_up_at' => now()]);
        }

        // Store locker number in session for success page
        session(['locker_number' => $locker->locker_number ?? 'Unknown']);

        return redirect()->route('pickup.success')->with('success', 'Locker opened! Please take your parcel.');
    }

    public function success()
    {
        return view('customer.success');
    }
}