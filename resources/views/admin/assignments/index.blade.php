@extends('layouts.app')

@section('content')
<style>
    body {
        background-image: url('{{ asset('images/ninjavan-bg.png') }}') !important;
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }
</style>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg border-0 rounded-4" style="border-top: 5px solid #dc3545;">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0 fw-bold" style="color: #dc3545;">📋 Parcel Assignments</h2>
                        <a href="{{ route('admin.assignments.create') }}" class="btn btn-outline-red">
                            <i class="bi bi-plus-lg me-1"></i> New Assignment
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Tracking ID</th>
                                    <th>Customer Name</th>
                                    <th>Locker</th>
                                    <th>Pickup Code</th>
                                    <th>Customer Phone</th>
                                    <th>Assigned At</th>
                                    <th>Picked Up At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignments as $assignment)
                                <tr>
                                    <td>{{ $assignment->id }}</td>
                                    
                                    {{-- Safe fallback for tracking strings --}}
                                    <td>{{ $assignment->parcel_id ?? ($assignment->parcel->tracking_id ?? 'N/A') }}</td>
                                    
                                    {{-- Pulls Customer Name from related ninjavan_data if available --}}
                                    <td>{{ $assignment->parcel->customer_name ?? 'N/A' }}</td>
                                    
                                    {{-- Locker Assignment Column --}}
                                    <td>{{ $assignment->locker->locker_number ?? ($assignment->parcel->locker_number ?? 'N/A') }}</td>
                                    
                                    {{-- Pickup Code Column --}}
                                    <td>
                                        <code class="bg-light p-1 rounded text-danger fw-bold">
                                            {{ $assignment->pickup_code ?? ($assignment->parcel->pickup_code ?? 'N/A') }}
                                        </code>
                                    </td>
                                    
                                    <td>{{ $assignment->customer_phone ?? ($assignment->parcel->customer_phone ?? '-') }}</td>
                                    
                                    <td>{{ $assignment->assigned_at ? $assignment->assigned_at->format('Y-m-d H:i') : ($assignment->created_at ? $assignment->created_at->format('Y-m-d H:i') : '-') }}</td>
                                    
                                    <td>
                                        @if($assignment->picked_up_at)
                                            <span class="badge bg-success">{{ $assignment->picked_up_at->format('Y-m-d H:i') }}</span>
                                        @elseif(isset($assignment->parcel) && $assignment->parcel->picked_up_at)
                                            <span class="badge bg-success">{{ \Carbon\Carbon::parse($assignment->parcel->picked_up_at)->format('Y-m-d H:i') }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Not picked up</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$assignment->picked_up_at && !(isset($assignment->parcel) && $assignment->parcel->picked_up_at))
                                            <form action="{{ route('admin.assignments.markPickedUp', $assignment) }}" method="POST" style="display:inline;">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Mark as picked up?')">Mark Picked</button>
                                            </form>
                                        @else
                                            <span class="text-muted small"><i class="bi bi-check-circle-fill text-success"></i> Completed</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">No assignments yet. <a href="{{ route('admin.assignments.create') }}">Create one</a>.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection