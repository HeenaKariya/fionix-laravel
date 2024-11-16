@extends('adminlte::page')

@section('title', 'Create User')

@section('content_header')
    <h1>Create User</h1>
@stop

@section('content')
    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            @error('email')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="mobile_no">Mobile No.</label>
            <input type="text" name="mobile_no" class="form-control" value="{{ old('mobile_no') }}" required>
            @error('mobile_no')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control" value="{{ old('password') }}" required>
        </div>
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
            @error('password')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="role">Role</label>
            <select name="role" class="form-control" required>
                        @foreach($roles as $role)
                            @if($role->name !== 'admin')
                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
                            @endif    
                        @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary full-width-approve">Create User</button>
        <button type="button" class="btn btn-secondary mt-3 full-width-view" onclick="window.location='{{ route('admin.dashboard') }}'">Cancel</button>
    </form>
@stop
