@extends('layouts.app')

@section('content')
{{-- Custom background for this page only --}}
<style>
    body {
        background-image: url('{{ asset('images/ninjavan-bg.png') }}') !important;
        background-size: cover;
        background-position: center center;
        background-attachment: fixed;
        background-repeat: no-repeat;
    }
    /* Optional: make the card stand out over the background */
    .card {
        backdrop-filter: blur(4px);
        background: rgba(255, 255, 255, 0.92) !important;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-4" style="border-top: 5px solid #dc3545;">
                <div class="card-header bg-white text-center py-4">
                    <i class="bi bi-box-seam fs-1" style="color: #dc3545;"></i>
                    <h2 class="mt-2 fw-bold" style="color: #dc3545;">Parcel Pickup</h2>
                    <p class="text-muted">Enter your 6-digit pickup code</p>
                </div>
                <div class="card-body p-4">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ $errors->first('pickup_code') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('pickup.process') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="pickup_code" class="form-label fw-semibold">Pickup Code</label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('pickup_code') is-invalid @enderror" 
                                   id="pickup_code" 
                                   name="pickup_code" 
                                   placeholder="e.g., 123456"
                                   maxlength="10"
                                   autocomplete="off"
                                   autofocus
                                   required>
                        </div>
                        <button type="submit" class="btn btn-outline-red btn-lg w-100 py-2 fw-bold">
                            <i class="bi bi-unlock-fill me-2"></i> Unlock Locker
                        </button>
                    </form>
                </div>
                <div class="card-footer bg-white text-center py-3">
                    <small class="text-muted">If you have issues, please contact staff for assistance.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection