@extends('adminlte::page')

@section('title', 'Challans')

@section('content_header')
    <h1>Challans for {{ $project->name }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Challan List</h3>
            @if(Auth::user()->hasRole('manager') || Auth::user()->hasRole('supervisor'))
                <a href="{{ route('projects.challans.create', $project->id) }}" class="btn btn-primary float-right create-btn">Create Challan</a>
            @endif
        </div>
        <div class="card-body">
            <form method="GET" id="dateFilterForm">
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
            </form>
            <div id="table-view1">
                <table class="table table-bordered" id="challans-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bill Date</th>
                            <th>Amount</th>
                            <th>Payment Type</th>
                            <th>Expense Category</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($challans as $challan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ \Carbon\Carbon::parse($challan->bill_date)->format('d M Y') }}</td>
                                <td>{{ $challan->amount }}</td>
                                <td>{{ $challan->payment_type }}</td>
                                <td>{{ $challan->expense_category }}</td>
                                <td>
                                @php
                                    $statusClass = '';
                                    if ($challan->status === 'approved') {
                                        $statusClass = 'bg-success text-white';
                                    } elseif ($challan->status === 'rejected') {
                                        $statusClass = 'bg-danger text-white';
                                    } elseif ($challan->status === 'pending') {
                                        $statusClass = 'bg-secondary text-white'; 
                                    }
                                @endphp
                                    <span class="label {{ $statusClass }}">{{ ucfirst($challan->status) }}</span>
                                </td>
                                <td>{{ $challan->user->name }}</td>
                                <td>
                                    <a href="{{ route('projects.challans.show', [$project->id, $challan->id]) }}" class="btn btn-sm btn-primary full-width-view">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="list-view1" class="d-none">
                <ul class="list-group" id="challans-list"></ul>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
            var table = $('#challans-table').DataTable({
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.childRowImmediate,
                        type: 'inline',
                        }
                },
                autoWidth: false,
                order: [[0, 'desc']],
        });

        table.rows().every(function(rowIdx, tableLoop, rowLoop) {
                var row = this;
                if (!row.child.isShown()) {
                    row.child.show();
                    $(row.node()).addClass('shown');  
                }
            });
    });
</script>
@stop
