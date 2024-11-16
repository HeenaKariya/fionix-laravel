@extends('adminlte::page')

@section('title', 'Money In List')

@section('content_header')
    <h1>Money In List</h1>
@stop

@section('content')
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
    <div class="card">
        <div class="card-body">
            
            <form method="GET" action="{{ route('money_in.index') }}">
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

            <table id="moneyInTable" class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Project</th>
                        <th>Name</th>
                        <th>Transaction ID</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Payment Type</th>
                        <th>Payment Date and Time</th>
                        <th>Amount</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($moneyInData as $moneyIn)
                        <tr>
                            <td>{{ $moneyIn->id }}</td>
                            <td>{{ $moneyIn->project->name }}</td>
                            <td>{{ $moneyIn->user->name }}</td>
                            <td>{{ $moneyIn->transaction_id }}</td>
                            <td>{{ $moneyIn->from }}</td>
                            <td>{{ $moneyIn->to }}</td>
                            <td>{{ $moneyIn->payment_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($moneyIn->payment_datetime)->format('h:i A d/m/Y') }}</td> 
                            <td>{{ number_format($moneyIn->amount, 2) }}</td>
                            <td>{{ $moneyIn->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@push('css')
    <!-- DataTables CSS -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css"> -->
@endpush

@push('js')
    <!-- DataTables JS -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script> -->
    <script>
        $(document).ready(function() {
            var table = $('#moneyInTable').DataTable({
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
        });
    </script>
@endpush
