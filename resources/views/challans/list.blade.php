@extends('adminlte::page')

@section('title', $title)

@section('content_header')
    <h1>{{ $title }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $title }}</h3>
            @if(Auth::user()->hasRole('manager') || Auth::user()->hasRole('supervisor'))
                <a href="{{ route('challans.create') }}" class="btn btn-primary float-right create-btn">Create Challan</a>
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
                            <th>Project Site Name</th>
                            <th>Type</th>
                            <th>From</th>
                            <th>Expense Category</th>
                            <th>Date</th> 
                            <th>Amount</th>
                            <th>Status</th>
                            <th>File</th>
                            @if (!Auth::user()->hasRole('supervisor'))
                            <th>Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($challans as $challan)
                            <tr>
                                <td>{{ $challan->id }}</td>
                                <td>{{ $challan->project->name }}</td>
                                <td>{{ $challan->payment_type }}</td>
                                <td>{{ $challan->user->name }}</td>
                                <td>{{ $challan->expense_category }}</td>
                                <td>{{ \Carbon\Carbon::parse($challan->bill_date)->format('d M Y') }}</td>
                                <td>₹{{ formatIndianCurrency($challan->amount) }}</td>
                                
                                <!-- <td>{{ $challan->expense_category }}</td> -->
                                <td>
                                    @php
                                        $statusClass = '';
                                        if ($challan->status === 'approved') {
                                            $statusClass = 'bg-success text-white';
                                        } elseif ($challan->status === 'rejected') {
                                            $statusClass = 'bg-danger text-white';
                                        }elseif ($challan->status === 'pending') {
                                            $statusClass = 'bg-secondary text-white';
                                        }
                                    @endphp
                                    <span class="label {{ $statusClass }}">{{ ucfirst($challan->status) }}</span>
                                </td>
                                <td>
                                    @if($challan->upload_image)
                                        @foreach(json_decode($challan->upload_image) as $file)
                                            <div class="file-entry">
                                                <span>{{ strlen(basename($file)) > 5 ? substr(basename($file), 0, 5) . '...' : basename($file) }}</span><br>
                                                <button type="button" class="btn btn-primary btn-sm create-btn m-1" data-toggle="modal" data-target="#viewFileModal" data-file="{{ asset('storage/' . $file) }}">View</button>
                                                <a href="{{ asset('storage/' . $file) }}" class="btn btn-secondary btn-sm create-btn m-1" download>Download</a>
                                            </div>
                                        @endforeach
                                    @endif
                                </td>
                                @if (!Auth::user()->hasRole('supervisor'))
                                <td>
                                    <a href="{{ route('projects.challans.show', [$challan->project_id, $challan->id]) }}" class="btn btn-sm btn-primary full-width-view">@if (Auth::user()->hasRole('account manager') && $challan->status == 'approved')View/Edit @else View @endif</a>
                                </td>
                                @endif

                                
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="viewFileModal" tabindex="-1" role="dialog" aria-labelledby="viewFileModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewFileModalLabel">View File</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <iframe id="fileViewer" style="width: 100%; height: 500px;" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </div>
             <!-- end Modal -->   
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
        $('#viewFileModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var fileUrl = button.data('file'); // Extract info from data-* attributes
        var modal = $(this);
        modal.find('.modal-body #fileViewer').attr('src', fileUrl);
    });
    });    
</script>
@stop
