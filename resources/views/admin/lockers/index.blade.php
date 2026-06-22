@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg border-0 rounded-4" style="border-top: 5px solid #dc3545;">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0 fw-bold" style="color: #dc3545;">📦 Lockers</h2>
                        <a href="{{ route('admin.lockers.create') }}" class="btn btn-outline-red">
                            <i class="bi bi-plus-lg me-1"></i> Add Locker
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Locker Number</th>
                                    <th>ESP IP</th>
                                    <th>Status</th>
                                    <th>Door</th>
                                    <th>Parcel Present</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lockers as $locker)
                                <tr>
                                    <td>{{ $locker->id }}</td>
                                    <td><strong>{{ $locker->locker_number }}</strong></td>
                                    <td>{{ $locker->esp_ip ?? 'Not set' }}</td>
                                    <td>
                                        @if($locker->status == 'available')
                                            <span class="badge bg-success">Available</span>
                                        @elseif($locker->status == 'occupied')
                                            <span class="badge bg-warning">Occupied</span>
                                        @else
                                            <span class="badge bg-secondary">Maintenance</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($locker->door_closed)
                                            <i class="bi bi-door-closed text-success"></i> Closed
                                        @else
                                            <i class="bi bi-door-open text-danger"></i> Open
                                        @endif
                                    </td>
                                    <td>
                                        @if($locker->parcel_present)
                                            <i class="bi bi-box-seam text-warning"></i> Yes
                                        @else
                                            <i class="bi bi-box-seam text-secondary"></i> No
                                        @endif
                                    </td>
                                    <td>{{ $locker->last_updated ? $locker->last_updated->diffForHumans() : 'Never' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.lockers.edit', $locker) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.lockers.destroy', $locker) }}" method="POST" style="display:inline-block;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this locker?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                            @if($locker->esp_ip)
                                                <form action="{{ route('admin.lockers.unlock', $locker) }}" method="POST" style="display:inline-block;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Unlock this locker?')">
                                                        <i class="bi bi-unlock"></i> Test Unlock
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">No lockers found. <a href="{{ route('admin.lockers.create') }}">Add one</a>.</td>
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