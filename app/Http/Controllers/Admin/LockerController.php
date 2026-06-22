<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Locker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LockerController extends Controller
{
    public function index()
    {
        $lockers = Locker::all();
        return view('admin.lockers.index', compact('lockers'));
    }

    public function unlock(Locker $locker)
    {
        if (!$locker->esp_ip) {
            return back()->with('error', 'No ESP8266 IP address set for this locker.');
        }

        try {
            $response = Http::timeout(5)->get("http://{$locker->esp_ip}/unlock");
            
            if ($response->successful()) {
                return back()->with('success', "Unlock command sent to locker {$locker->locker_number}.");
            } else {
                return back()->with('error', "Failed to unlock. ESP responded with status: {$response->status()}");
            }
        } catch (\Exception $e) {
            return back()->with('error', "Could not reach ESP8266 at {$locker->esp_ip}. Error: {$e->getMessage()}");
        }
    }

    public function create()
    {
        return view('admin.lockers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'locker_number' => 'required|string|unique:lockers|max:10',
            'esp_ip' => 'nullable|ip',
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        Locker::create($validated);
        return redirect()->route('admin.lockers.index')->with('success', 'Locker created.');
    }

    public function edit(Locker $locker)
    {
        return view('admin.lockers.edit', compact('locker'));
    }

    public function update(Request $request, Locker $locker)
    {
        $validated = $request->validate([
            'locker_number' => 'required|string|max:10|unique:lockers,locker_number,' . $locker->id,
            'esp_ip' => 'nullable|ip',
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        $locker->update($validated);
        return redirect()->route('admin.lockers.index')->with('success', 'Locker updated.');
    }

    public function destroy(Locker $locker)
{
    // Check if there are any active assignments (not picked up)
    $activeAssignments = $locker->assignments()->whereNull('picked_up_at')->count();
    if ($activeAssignments > 0) {
        return back()->with('error', 'Cannot delete locker: it has active parcel assignments. Mark them as picked up first.');
    }
    
    // Delete all assignments (history) or just let cascade handle? We'll manually delete.
    $locker->assignments()->delete();
    
    // Now delete the locker
    $locker->delete();
    
    return redirect()->route('admin.lockers.index')->with('success', 'Locker deleted successfully.');
}
    // API endpoint for IoT sensor updates
    public function updateStatus(Request $request)
{
    $locker = Locker::where('locker_number', $request->locker_number)->first();
    if (!$locker) {
        return response()->json(['success' => false, 'message' => 'Locker not found'], 404);
    }

    // Check if there is an active assignment for this locker (picked_up_at IS NULL)
    $hasActiveAssignment = \App\Models\ParcelAssignment::where('locker_id', $locker->id)
        ->whereNull('picked_up_at')
        ->exists();

    // Determine status:
    // - If active assignment exists → always "occupied"
    // - Else use sensor data: if parcel present OR door open → "occupied", else "available"
    $newStatus = 'available';
    if ($hasActiveAssignment) {
        $newStatus = 'occupied';
    } else {
        $parcelPresent = $request->parcel_present;
        $doorClosed = $request->door_closed;
        if ($parcelPresent || !$doorClosed) {
            $newStatus = 'occupied';
        } else {
            $newStatus = 'available';
        }
    }

    $locker->update([
        'door_closed' => $request->door_closed,
        'parcel_present' => $request->parcel_present,
        'status' => $newStatus,
        'last_updated' => now(),
    ]);

    return response()->json(['success' => true]);
}
}