@extends('adminlte::page')

@section('title', 'Projects Wise Balance')

@section('content_header')
    <h1>Projects Wise Balance</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="overall-balance-card">
                <h2>{{ $user->name }}</h2>
                <h1>₹{{ formatIndianCurrency($overallBalance) }}</h1>

                <p>Overall Available Balance</p>
                <a href="{{ route('money-requests.create') }}" class="btn btn-secondary create-btn">Create Money Request</a>
            </div>
            <!-- <form method="GET" action="{{ route('supervisor.dashboard') }}">
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
            <div class="table-responsive">
                <table id="projects-table" class="table table-bordered ">
                    <thead>
                        <tr>
                            <th>Project ID</th>
                            <th>Project Site Name</th>
                            <th>Available Balance</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($projects as $project)
                            <tr>
                                <td>{{ $project->id }}</td>
                                <td>{{ $project->name }}</td>
                                <td>₹{{ formatIndianCurrency($project->supervisorBalance) }}</td>
                                <td>
                                    <a href="{{ route('projects.challans.create', $project->id) }}" class="btn btn-sm btn-primary full-width-view">Add Bill/Challan</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .overall-balance-card {
        text-align: center;
        margin-bottom: 2rem;
    }
    .overall-balance-card h1 {
        font-size: 3rem;
        color: #4CAF50;
    }
    .overall-balance-card p {
        font-size: 1.2rem;
        color: #666;
    }
    .list-group-item {
        margin-bottom: 1rem;
        padding: 1.5rem;
        border: 1px solid #ddd;
        border-radius: 0.5rem;
    }
    .list-group-item strong {
        display: inline-block;
        width: 150px;
    }
</style>
@stop

@section('js')
<script>
   
    $(document).ready(function() {
            var table = $('#projects-table').DataTable({
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.childRowImmediate,
                        type: 'inline',
                        }
                },
                autoWidth: false,
        });

            table.rows().every(function(rowIdx, tableLoop, rowLoop) {
                var row = this;
                if (!row.child.isShown()) {
                    row.child.show();
                    $(row.node()).addClass('shown');  
                }
            });
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
