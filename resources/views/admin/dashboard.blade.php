@extends('layouts.admin')

@section('admin-content')
<div class="admin-dash-header">
  <h1>Admin Dashboard</h1>
  <p>Welcome to the GSU Admin Portal. Manage facilities, requests, and users from here.</p>
</div>

<div class="quick-access-section">
  <h2>Quick access</h2>
  <div class="admin-grid">
    <a href="{{ route('admin.facilities.index') }}" class="admin-card">
        <h3>Facilities</h3>
        <p>Manage campus facilities and equipment</p>
    </a>
    <a href="{{ route('admin.forms.facilities.index') }}" class="admin-card">
        <h3>Utilization Requests</h3>
        <p>Review and approve facility utilization submissions</p>
    </a>
    <a href="{{ route('admin.bookings.index') }}" class="admin-card">
        <h3>Bookings</h3>
        <p>View and adjust confirmed bookings</p>
    </a>
    <a href="{{ route('admin.users.index') }}" class="admin-card">
        <h3>User Management</h3>
        <p>Create and manage portal accounts</p>
    </a>
  </div>
</div>
@endsection
