@extends('adminlte::page')

@section('title', 'Supervisor Dashboard - Finished Projects')

@section('content_header')
    <h1>Finished Projects</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Finished Projects</h3>
        </div>
        <div class="card-body">
            <div id="table-view">
                <table class="table table-bordered" id="finished-projects-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Start Date</th>
                            <th>Status</th>
                            <th>Project Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>{{ $project->id }}</td>
                                <td>{{ $project->name }}</td>
                                <td>{{ $project->location }}</td>
                                <td>{{ \Carbon\Carbon::parse($project->start_date)->format('d F Y') }}</td>
                                <td>{{ $project->status }}</td>
                                <td>{{ $project->supervisorBalance  }}</td>
                                <td>
                                    <a href="{{ route('supervisor.projects.show', $project->id) }}" class="btn btn-sm btn-primary full-width-view">View</a>
                                    <a href="/projects/{{ $project->id }}/challans" class="btn btn-sm btn-success full-width-approve">Challan</a>
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
            var overallBalance = '{{ $overallBalance }}';
            var projects = @json($projects);
            var listItems = '';
            // Add overall balance section
            listItems += '<li class="list-group-item text-center">';
            listItems += '<h2>₹' + overallBalance + '</h2>';
            listItems += '<p>Available Balance</p>';
            listItems += '<a href="{{ route('money-requests.create') }}" class="btn btn-secondary full-width-approve">Request Money</a>';
            listItems += '</li>';

            projects.forEach(function(project, index) {
                listItems += '<li class="list-group-item">';
                listItems += '<strong>Project ID:</strong> ' + project.id + '<br>';
                listItems += '<strong>Name:</strong> ' + project.name + '<br>';
                listItems += '<strong>Available Balance:</strong> ₹' + project.supervisorBalance + '<br>';
                listItems += '<strong>Location:</strong> ' + project.location + '<br>';
                listItems += '<strong>Start Date:</strong> ' + moment(project.start_date).format('D MMMM YYYY') + '<br>';
                listItems += '<strong>Status:</strong> ' + project.status + '<br>';
                listItems += '<a href="{{ route("supervisor.projects.show", "") }}/' + project.id + '" class="btn btn-sm btn-primary full-width-view">View</a> ';
                listItems += '<a href="/projects/' + project.id + '/challans" class="btn btn-sm btn-success full-width-approve">Challan</a>';
                listItems += '</li>';
            });
            $('#projects-list').html(listItems);
        } else {
            $('#projects-table').DataTable({
                order: [[0, 'desc']]
            });
        }

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
