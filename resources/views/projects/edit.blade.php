<!-- edit.blade.php -->
@extends('adminlte::page')

@section('title', 'Edit Project')

@section('content_header')
    <h1>Edit Project</h1>
@stop

@section('content')
    <form action="{{ route('projects.update', $project->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" value="{{ $project->name }}" required>
        </div>
        <div class="form-group">
            <label for="location">Location</label>
            <input type="text" name="location" class="form-control" value="{{ $project->location }}" required>
        </div>
        <div class="form-group">
            <label for="start_date">Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ $project->start_date }}" required>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select name="status" class="form-control" required>
                <option value="Not Started" {{ $project->status == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                <option value="In Progress" {{ $project->status == 'In Progress' ? 'selected' : '' }}>In Progress</option>
                <option value="Completed" {{ $project->status == 'Completed' ? 'selected' : '' }}>Completed</option>
            </select>
        </div>
        <div class="form-group">
            <label for="supervisors">Supervisors</label>
            <input type="text" name="supervisors" class="form-control" value="{{ $project->supervisors }}" required>
        </div>
        <div class="form-group">
            <label for="note">Note</label>
            <textarea name="note" class="form-control">{{ $project->note }}</textarea>
        </div>
        <button type="submit" class="btn btn-success full-width-approve">Update Project</button>
    </form>
@stop
