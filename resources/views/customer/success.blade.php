@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow-lg border-0 rounded-4" style="border-top: 5px solid #dc3545;">
                <div class="card-body p-5">
                    <i class="bi bi-check-circle-fill display-1" style="color: #28a745;"></i>
                    <h1 class="mt-3 fw-bold">Parcel Retrieved!</h1>
                    
                    {{-- Show locker number --}}
                    <div class="alert alert-info mt-4">
                        <i class="bi bi-door-open-fill me-2"></i>
                        <strong>Locker Number:</strong> {{ session('locker_number', 'Unknown') }}
                    </div>
                    
                    <p class="lead">Please take your parcel from locker <strong>{{ session('locker_number') }}</strong> and close the door.<br>Thank you for using NinjaVault.</p>
                    
                    <a href="{{ route('pickup.form') }}" class="btn btn-danger btn-lg mt-3">
                        <i class="bi bi-arrow-repeat me-2"></i> Pick Up Another Parcel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection