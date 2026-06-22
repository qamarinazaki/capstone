@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 rounded-4" style="border-top: 5px solid #dc3545;">
                <div class="card-header bg-white text-center py-3">
                    <h3 class="mb-0 fw-bold" style="color: #dc3545;"><i class="bi bi-plus-circle me-2"></i>Add New Locker</h3>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.lockers.store') }}">
                        @csrf

                        <!-- Locker Number -->
                        <div class="mb-3">
                            <label for="locker_number" class="form-label fw-semibold">Locker Number</label>
                            <input type="text" class="form-control @error('locker_number') is-invalid @enderror" 
                                   id="locker_number" name="locker_number" value="{{ old('locker_number') }}" 
                                   placeholder="e.g., A1, B2, C03" required>
                            @error('locker_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">Unique identifier (max 10 characters).</small>
                        </div>

                        <!-- ESP8266 IP Address -->
                        <div class="mb-3">
                            <label for="esp_ip" class="form-label fw-semibold">ESP8266 IP Address</label>
                            <input type="text" class="form-control @error('esp_ip') is-invalid @enderror" 
                                   id="esp_ip" name="esp_ip" value="{{ old('esp_ip') }}" 
                                   placeholder="e.g., 192.168.1.100">
                            @error('esp_ip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">The local IP address of the ESP8266 controlling this locker (optional).</small>
                        </div>

                        <!-- Initial Status -->
                        <div class="mb-4">
                            <label for="status" class="form-label fw-semibold">Initial Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.lockers.index') }}" class="btn btn-outline-red">
                                <i class="bi bi-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-outline-red px-4">
                                <i class="bi bi-save me-1"></i> Save Locker
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection