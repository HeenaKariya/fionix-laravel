@extends('adminlte::page')

@section('title', 'Owner Dashboard - Pending Projects')

@section('content_header')
    <h1>Pending Projects</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pending Projects</h3>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <div id="table-view">
                <table class="table table-bordered" id="pending-projects-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Start Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $project->name }}</td>
                                <td>{{ $project->location }}</td>
                                <td>{{ \Carbon\Carbon::parse($project->start_date)->format('d F Y') }}</td>
                                <td>{{ ucfirst($project->status) }}</td>
                                <td>
                                    <a href="{{ route('owner.projects.show', $project->id) }}" class="btn btn-sm btn-primary full-width-view">View</a>
                                    <!-- <a href="{{ route('projects.challans.index', $project->id) }}" class="btn btn-sm btn-success">Challan</a> -->
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="list-view" class="d-none">
                <ul class="list-group" id="projects-list"></ul>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
    $(document).ready(function() {
        var isMobile = window.matchMedia("only screen and (max-width: 760px)").matches;
        if (isMobile) {
            $('#table-view').hide();
            $('#list-view').removeClass('d-none');

            var projects = @json($projects);
            var listItems = '';
            projects.forEach(function(project, index) {
                listItems += '<li class="list-group-item">';
                listItems += 'Name: ' + project.name + '<br>';
                listItems += 'Location: ' + project.location + '<br>';
                listItems += 'Start Date: ' + moment(project.start_date).format('D MMMM YYYY') + '<br>';
                listItems += 'Status: ' + project.status.charAt(0).toUpperCase() + project.status.slice(1) + '<br>';
                listItems += '<a href="' + '/owner/' + project.id  + '" class="btn btn-sm btn-primary d-block mb-2 full-width-view">View</a> ';
                // listItems += '<a href="' + '/projects/' + project.id  + '/challans/' + '" class="btn btn-sm btn-success d-block">Challan</a> ';
                listItems += '</li>';
            });
            $('#projects-list').html(listItems);
        } else {
            $('#pending-projects-table').DataTable();
        }

        // Toastr configuration
        toastr.options = {
            positionClass: 'toast-bottom-right',
            hideDuration: 5000,
            timeOut: 5000,
            extendedTimeOut: 1000,
        };

        @if(session('success'))
            toastr.success('{{ session('success') }}');
        @endif
        @if(session('error'))
            toastr.error('{{ session('error') }}');
        @endif
    });
</script>
@stop
