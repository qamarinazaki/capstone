<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NinjavanData;
use App\Models\Locker;
use App\Models\ParcelAssignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = ParcelAssignment::with(['parcel', 'locker'])->latest()->get();
        return view('admin.assignments.index', compact('assignments'));
    }

    public function create()
    {
        $parcels = NinjavanData::whereDoesntHave('currentAssignment')->get(); // unassigned parcels
        $lockers = Locker::where('status', 'available')->get();
        return view('admin.assignments.create', compact('parcels', 'lockers'));
    }

    public function store(Request $request)
{
    $request->validate([
        'parcel_id' => 'nullable|exists:ninjavan_data,id',
        'tracking_id' => 'nullable|string|unique:ninjavan_data,tracking_id',
        'customer_name' => 'nullable|string',
        'customer_email' => 'nullable|email',
        'customer_phone' => 'nullable|string',
        'locker_id' => 'required|exists:lockers,id',
    ]);

    // If a new parcel is being created (tracking_id provided)
    if ($request->tracking_id) {
        $parcel = NinjavanData::create([
            'tracking_id' => $request->tracking_id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'Order_Granular_Status' => 'Pending', // or any default
            'Delivery_Date' => now()->toDateString(),
        ]);
        $parcelId = $parcel->id;
    } else {
        // Use existing parcel
        $parcelId = $request->parcel_id;
        if (!$parcelId) {
            return back()->withErrors(['parcel_id' => 'Either select an existing parcel or provide tracking ID.']);
        }
    }

    $locker = Locker::findOrFail($request->locker_id);
    if ($locker->status !== 'available') {
        return back()->withErrors(['locker_id' => 'Locker is not available.']);
    }

    // Generate unique pickup code (6 digits)
    do {
        $pickupCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    } while (ParcelAssignment::where('pickup_code', $pickupCode)->exists());

    // Create assignment
    $assignment = ParcelAssignment::create([
        'parcel_id' => $parcelId,
        'locker_id' => $locker->id,
        'pickup_code' => $pickupCode,
        'customer_phone' => $request->customer_phone,
        'assigned_at' => now(),
    ]);

    // Update locker status
    $locker->update(['status' => 'occupied', 'last_updated' => now()]);

    // Update ninjavan_data record with pickup_code and locker_number
    $parcel = NinjavanData::find($parcelId);
    $parcel->update([
        'pickup_code' => $pickupCode,
        'locker_number' => $locker->locker_number,
    ]);

    return redirect()->route('admin.assignments.index')->with('success', "Assigned! Pickup Code: {$pickupCode}");
}

    // Optional: mark as picked up manually (though the customer flow already does it)
    public function markPickedUp(ParcelAssignment $assignment)
    {
        $assignment->update(['picked_up_at' => now()]);
        $assignment->locker->update(['status' => 'available', 'last_updated' => now()]);
        return back()->with('success', 'Marked as picked up.');
    }
}  