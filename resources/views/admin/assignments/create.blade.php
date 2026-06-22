@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-4" style="border-top: 5px solid #dc3545;">
                <div class="card-header bg-white text-center py-3">
                    <h3 class="mb-0 fw-bold" style="color: #dc3545;"><i class="bi bi-link-45deg me-2"></i>Assign Parcel to Locker</h3>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.assignments.store') }}">
                        @csrf

                        <!-- Option: Use existing parcel -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Select Existing Parcel (optional)</label>
                            <select name="parcel_id" class="form-select">
                                <option value="">-- Choose from existing --</option>
                                @foreach($parcels as $parcel)
                                <option value="{{ $parcel->id }}">Parcel #{{ $parcel->id }} - {{ $parcel->Order_Granular_Status ?? 'No status' }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Or fill in the details below to create a new parcel.</small>
                        </div>

                        <hr class="my-3">

                        <h5 class="mb-3">Create New Parcel</h5>

                        <div class="mb-3">
                            <label for="tracking_id" class="form-label fw-semibold">Tracking ID</label>
                            <input type="text" class="form-control" id="tracking_id" name="tracking_id" placeholder="e.g., NINJA123456789">
                        </div>

                        <div class="mb-3">
                            <label for="customer_name" class="form-label fw-semibold">Customer Name</label>
                            <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="e.g., John Doe">
                        </div>

                        <div class="mb-3">
                            <label for="customer_email" class="form-label fw-semibold">Customer Email</label>
                            <input type="email" class="form-control" id="customer_email" name="customer_email" placeholder="customer@example.com">
                        </div>

                        <div class="mb-3">
                            <label for="customer_phone" class="form-label fw-semibold">Customer Phone</label>
                            <input type="text" class="form-control" id="customer_phone" name="customer_phone" placeholder="e.g., 0123456789">
                        </div>

                        <div class="mb-3">
                            <label for="locker_id" class="form-label fw-semibold">Select Locker (Available Only)</label>
                            <select name="locker_id" id="locker_id" class="form-select" required>
                                <option value="">-- Choose Locker --</option>
                                @foreach($lockers as $locker)
                                <option value="{{ $locker->id }}">{{ $locker->locker_number }} ({{ $locker->status }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.assignments.index') }}" class="btn btn-outline-red">Cancel</a>
                            <button type="submit" class="btn btn-outline-red px-4">Assign & Generate Pickup Code</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection