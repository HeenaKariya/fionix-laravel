@extends('adminlte::page')

@section('title', 'Owner Dashboard - Active Projects')

@section('content_header')
    <h1>Active Projects</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Active Projects</h3>
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
            <!-- <form method="GET" action="{{ route('owner.dashboard.active') }}">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date', \Carbon\Carbon::parse($endDate)->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group d-flex justify-content-start align-items-end">
                            <button type="submit" class="btn small-width-view">Filter</button>
                        </div>
                    </div>
                </div>
            </form> -->
            <div id="table-view">
                <table class="table table-bordered" id="active-projects-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Project Site Name</th>
                            <th>Location</th>
                            <th>Project Budget</th>
                            <th>Total Revenue</th>
                            <th>Total Expense</th>
                            <!-- <th>Status</th> -->
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>{{ $project->id }}</td>
                                <td>{{ $project->name }}</td>
                                <td>{{ $project->location }}</td>
                                <td>₹{{ formatIndianCurrency($project->budget) }}</td>
                                <td>₹{{ formatIndianCurrency($totalRevenues[$project->id]) }}</td>
                                <td>₹{{ formatIndianCurrency($totalExpenses[$project->id]) }}</td>
                                <!-- <td>{{ ucfirst($project->status) }}</td> -->
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
            var totalRevenues = @json($totalRevenues);
            var totalExpenses = @json($totalExpenses);

            var listItems = '';
            projects.forEach(function(project, index) {
                // Defensive check to ensure the value is a number
                var totalRevenue = totalRevenues[project.id];
                if (isNaN(totalRevenue) || totalRevenue === undefined || totalRevenue === null) {
                    totalRevenue = 0;
                }

                var totalExpense = totalExpenses[project.id];
                if (isNaN(totalExpense) || totalExpense === undefined || totalExpense === null) {
                    totalExpense = 0;
                }

                // Ensure the values are treated as numbers
                totalRevenue = parseFloat(totalRevenue).toFixed(2);
                totalExpense = parseFloat(totalExpense).toFixed(2);

                console.log('Total Revenue for project ' + project.id + ':', totalRevenue);
                console.log('Total Expense for project ' + project.id + ':', totalExpense);

                listItems += '<li class="list-group-item">';
                listItems += '<strong>Project Site Name:</strong><br> ' + project.name + '<br>';
                listItems += '<strong>Location:</strong><br> ' + project.location + '<br>';
                listItems += '<strong>Project Budget:</strong><br> ' +  formatNumberIndian(project.budget) + '<br>';
                listItems += '<strong>Total Revenue:</strong><br> ' + formatNumberIndian(totalRevenue) + '<br>';
                listItems += '<strong>Total Expense:</strong><br> ' + formatNumberIndian(totalExpense) + '<br>';
                listItems += '<a href="' + '/owner/' + project.id  + '" class="btn btn-sm btn-primary d-block mb-2 full-width-view" style="background-color: #eef1f4; border: 1px solid black; font-weight: bold;">View</a> ';
                listItems += '</li>';
            });

            console.log('Generated list items:', listItems);

            $('#projects-list').html(listItems);
        } else {
            $('#active-projects-table').DataTable();
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
    function formatNumberIndian(num) {
        num = parseFloat(num); 
        if (isNaN(num)) return '0.00'; 
        var x = num.toFixed(2); 
        var lastThree = x.substring(x.length - 6);
        var otherNumbers = x.substring(0, x.length - 6);
        if (otherNumbers != '') lastThree = ',' + lastThree;
        var formattedNum = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
        return formattedNum;
    }

</script>
@stop
