@extends('adminlte::page')

@section('title', 'Create Project')

@section('content_header')
    <h1>Create Project</h1>
@stop

@section('content')
    <form action="{{ route('projects.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Project Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="location">Project Site Location</label>
            <textarea name="location" class="form-control" required>{{ old('location') }}</textarea>
            @error('location')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="start_date">Project Start Date</label>
            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d')) }}" required>
            @error('start_date')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="status">Project Site Status</label>
            <select name="status" class="form-control" required>
                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="planning" {{ old('status') == 'planning' ? 'selected' : '' }}>Planning</option>
                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Complete</option>
            </select>
            @error('status')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="supervisor_list">Supervisor List</label>
            <select id="supervisor_list" class="form-control">
                @foreach($supervisors as $supervisor)
                    <option value="{{ $supervisor->id }}">{{ $supervisor->name }}</option>
                @endforeach
            </select>
            <button type="button" id="add-supervisor" class="btn btn-secondary mt-2">Add Supervisor</button>
        </div>
        
        <div class="form-group">
            <ul id="selected-supervisors" class="list-group">
                <!-- Selected supervisors will be listed here -->
            </ul>
            @error('supervisors')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="budget">Project Budget</label>
            <input type="number" name="budget" class="form-control" step="0.01" value="{{ old('budget') }}" min="1">
            @error('budget')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <input type="hidden" name="supervisors[]" id="supervisors">
        <div class="form-group">
            <label for="note">Additional Notes</label>
            <textarea name="note" class="form-control">{{ old('note') }}</textarea>
            @error('note')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success btn-block full-width-approve">Create Project</button>
        @if (Auth::user()->hasRole('manager'))
                <button type="button" class="btn btn-secondary mt-3 full-width-view" onclick="window.location='{{ route('manager.dashboard') }}'">Cancel</button>
        @endif
    </form>
@endsection

@section('js')
<script>
$(document).ready(function() {
    var supervisorList = $('#supervisor_list');
    var selectedSupervisors = $('#selected-supervisors');
    var supervisorsInput = $('#supervisors');

    $('#add-supervisor').click(function() {
        var selectedOption = supervisorList.find('option:selected');
        var supervisorId = selectedOption.val();
        var supervisorName = selectedOption.text();

        if (supervisorId && !selectedSupervisors.find(`li[data-id="${supervisorId}"]`).length) {
            var listItem = `
                <li class="list-group-item d-flex justify-content-between align-items-center" data-id="${supervisorId}">
                    ${supervisorName}
                    <button type="button" class="btn btn-danger btn-sm remove-supervisor">X</button>
                </li>`;
            selectedSupervisors.append(listItem);
            updateSupervisorsInput();
        }
    });

    // Remove supervisor from list
    $(document).on('click', '.remove-supervisor', function() {
        $(this).closest('li').remove();
        updateSupervisorsInput();
    });

    function updateSupervisorsInput() {
        var supervisorIds = selectedSupervisors.find('li').map(function() {
            return $(this).data('id');
        }).get();
        supervisorsInput.val(supervisorIds);
    }
});
</script>
@endsection
