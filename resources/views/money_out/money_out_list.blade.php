@extends('adminlte::page')

@section('title', 'Money Out List')

@section('content_header')
    <h1>Money Out List</h1>
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
            <form method="GET" action="{{ route('money_out.index') }}">
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
            <table id="moneyOutTable" class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Project</th>
                        <th>Name</th>
                        <th>Transaction ID</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Payment Type</th>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Image/Document</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($moneyOutData as $moneyOut)
                        <tr>
                            <td>{{ $moneyOut->id }}</td>
                            <td>{{ $moneyOut->project->name }}</td>
                            <td>{{ $moneyOut->user->name }}</td>
                            <td>{{ $moneyOut->transaction_id }}</td>
                            <td>{{ $moneyOut->from }}</td>
                            <td>{{ $moneyOut->to }}</td>
                            <td>{{ $moneyOut->payment_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($moneyOut->payment_datetime)->format('h:i A d/m/Y') }}</td>
                            <td>{{ number_format($moneyOut->amount, 2) }}</td>
                            <td>
                                @if($moneyOut->image)
                                    @foreach(json_decode($moneyOut->image) as $file)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>{{ $loop->iteration }}. {{ strlen(basename($file)) > 5 ? substr(basename($file), 0, 5) . '...' : basename($file) }}
                                            </span>
                                            <div>
                                                <a href="#" class="btn create-btn btn-sm view-file" data-file="{{ asset('storage/' . $file) }}" data-filename="{{ basename($file) }}">View</a>
                                                <a href="{{ asset('storage/' . $file) }}" class="btn create-btn btn-sm" download>Download</a>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    No files
                                @endif
                            </td>

                            <td>{{ $moneyOut->notes }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Viewing Files -->
<div class="modal fade" id="viewFileModal" tabindex="-1" role="dialog" aria-labelledby="viewFileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewFileModalLabel">View File</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <iframe id="fileViewer" src="" style="width:100%;height:600px;" frameborder="0"></iframe>
                <img id="imageViewer" src="" style="max-width:100%; max-height:600px;" />
            </div>
        </div>
    </div>
</div>

@stop

@push('css')
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css"> -->
@endpush

@push('js')
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script> -->

    <script>
        $(document).ready(function() {
            var table = $('#moneyOutTable').DataTable({
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

            // Handle file view
        $(document).on('click', '.view-file', function(e) {
        e.preventDefault();
        var fileUrl = $(this).data('file');
        var fileName = $(this).data('filename');
        var fileExtension = fileName.split('.').pop().toLowerCase();

        $('#fileViewer').hide();
        $('#imageViewer').hide();

        if (fileExtension === 'pdf') {
            $('#fileViewer').attr('src', fileUrl).show();
        } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
            $('#imageViewer').attr('src', fileUrl).show();
        }

        $('#viewFileModalLabel').text(fileName); // Set the modal title to the file name
        $('#viewFileModal').modal('show');
    });
        });
    </script>
@endpush
