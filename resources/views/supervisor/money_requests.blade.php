@extends('adminlte::page')

@section('title', $title)

@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <!-- <h3 class="card-title">{{ $title }}</h3> -->
            <!-- @if(Auth::user()->hasRole('manager') || Auth::user()->hasRole('supervisor'))
                <a href="{{ route('money-requests.create') }}" class="btn btn-primary float-right create-btn">Create Money Request</a>
            @endif -->
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
                <table class="table table-bordered" id="requests-table">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Project Name</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment Type</th>
                            <th>Status</th>
                            <th>Manager Status</th>
                            <th>Owner Status</th>
                            <th>Note</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $request)
                            <tr>
                                <td>{{ $request->id }}</td>
                                <td>{{ $request->project->name }}</td>
                                @php
                                    $myDate = Carbon\Carbon::parse($request->date);
                                @endphp
                                <td data-sort="{{ $myDate->format('YmdHis') }}">{{ \Carbon\Carbon::parse($request->date)->format('h:i A d/m/Y') }}</td>
                                <td>₹{{ formatIndianCurrency($request->amount) }}</td>
                                <td>{{ $request->payment_type }}</td>
                                <td>
                                @php
                                    $statusClass = '';
                                    if ($request->status === 'approved') {
                                        $statusClass = 'bg-success text-white';
                                    } elseif ($request->status === 'rejected') {
                                        $statusClass = 'bg-danger text-white';
                                    } elseif ($request->status === 'pending') {
                                        $statusClass = 'bg-secondary text-white'; 
                                    }
                                @endphp
                                    <span class="label {{ $statusClass }}">{{ ucfirst($request->status) }}</span>
                                </td>
                                <td>{{ $request->manager_status ? ucfirst($request->manager_status) : 'Pending' }}</td>
                                <td>{{ $request->admin_status ? ucfirst($request->admin_status) : 'Pending' }}</td>
                                <td>{{ $request->note }}</td>
                                <td>
                                    <a href="{{ route('projects.approve', $request->id) }}" class="btn btn-sm btn-primary full-width-view">View</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div id="list-view1" class="d-none">
                <ul class="list-group" id="requests-list"></ul>
            </div>
        </div>
    </div>
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
    $(document).ready(function() {
            var table = $('#requests-table').DataTable({
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.childRowImmediate,
                        type: 'inline',
                        }
                },
                autoWidth: false,
                order: [
                    [0, 'desc'],
                    [2, 'desc'],
                ],
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
