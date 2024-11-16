@extends('adminlte::page')

@section('title', 'Challan Details')

@section('content_header')
    <h1>Bill/Challan Details ({{ $challan->id }})</h1>
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
            <!-- <div id="table-view" class="d-none d-md-block">
                <table class="table table-bordered">
                    <tr>
                        <th>Project Site Name</th>
                        <td>{{ $challan->project->name }}</td>
                    </tr>
                    <tr>
                        <th>Request Number</th>
                        <td>{{ $challan->id }}</td>
                    </tr>
                    <tr>
                        <th>Payment Type</th>
                        <td>{{ $challan->payment_type }}</td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>{{ \Carbon\Carbon::parse($challan->bill_date)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Expense Bill/Challan Uploaded by</th>
                        <td>{{ $challan->user->name }}</td>
                    </tr>
                    <tr>
                        <th>Pending Expense Bill/Challan Amount (this project)</th>
                        <td>{{ $challan->pending_amount }}</td>
                    </tr>
                    <tr>
                        <th>Pending Expense Bill/Challan Amount (TOTAL)</th>
                        <td>{{ $challan->total_pending_amount }}</td>
                    </tr>
                    <tr>
                        <th>Bill/Challan Verification Status</th>
                        <td>
                            @php
                                $statusClass = '';
                                if ($challan->status === 'approved') {
                                    $statusClass = 'bg-success text-white';
                                } elseif ($challan->status === 'rejected') {
                                    $statusClass = 'bg-danger text-white';
                                }
                            @endphp
                            <p ><span class="{{ $statusClass }}">{{ ucfirst($challan->status) }}</span></p>
                        </td>
                    </tr>
                    <tr>
                        <th>Amount</th>
                        <td>{{ $challan->amount }}</td>
                    </tr>
                    <tr>
                        <th>Notes</th>
                        <td>{{ $challan->note }}</td>
                    </tr>
                    <tr>
                        <th>Uploaded Files</th>
                        <td>
                            @if($challan->upload_image)
                                @foreach(json_decode($challan->upload_image) as $file)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span>{{ basename($file) }}</span>
                                        <div>
                                            <button type="button" class="btn btn-primary btn-sm create-btn" data-toggle="modal" data-target="#viewFileModal" data-file="{{ asset('storage/' . $file) }}">View</button>
                                            <a href="{{ asset('storage/' . $file) }}" class="btn btn-secondary btn-sm create-btn" download>Download</a>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </td>

                    </tr>
                </table>
            </div> -->
            <div id="list-view1">
                
                <table class="table table-bordered" id="challan-details-table">
                    <thead>
                        <tr>
                            <th>Project Site Name</th>
                            <th>Bill/Challan Number</th>
                            <th>Payment Type</th>
                            <th>Date</th>
                            <th>Expense Category</th>
                            <th>Expense Bill/Challan Uploaded by</th>
                            <th>Pending Expense Bill/Challan Amount (this project)</th>
                            <th>Pending Expense Bill/Challan Amount (TOTAL)</th>
                            <th>Bill/Challan Verification Status</th>
                            <th>Amount</th>
                            <th>Notes</th>
                            <th>Files</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $challan->project->name }}</td>
                            <td>{{ $challan->id }}</td>
                            <td>{{ $challan->payment_type }}</td>
                            <td>{{ \Carbon\Carbon::parse($challan->bill_date)->format('d F Y') }}</td>
                            <td>{{ $challan->expense_category }}</td>
                            <td>{{ $challan->user->name }}</td>
                            <td>{{ $challan->pending_amount }}</td>
                            <td>{{ $challan->total_pending_amount }}</td>
                            <td>
                                <span class="badge {{ $statusClass }}">{{ ucfirst($challan->status) }}</span>
                            </td>
                            <td>{{ $challan->amount }}</td>
                            <td>{{ $challan->note }}</td>
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
                        </tr>
                    </tbody>
                </table>

            </div>
            @if (Auth::user()->hasRole('manager')) 
                    <button type="button" class="btn btn-secondary mt-3 full-width-view" onclick="window.history.back();">Close</button>
            @endif

            
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
            @if (Auth::user()->hasRole('account manager') && $challan->status != 'rejected')
                <form action="{{ route('challans.updateStatus', $challan->id) }}" method="POST" class="mt-3">
                    @csrf
                    @method('PATCH')

                    <div class="form-group">
                        <label for="project_id">Project Site Name</label>
                        <select name="project_id" id="project_id" class="form-control" required>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}" {{ old('project_id', $challan->project_id) == $proj->id ? 'selected' : '' }}>
                                    {{ $proj->name }}
                                </option>
                            @endforeach
                        </select>
                        @if ($errors->has('project_id'))
                            <span class="text-danger">{{ $errors->first('project_id') }}</span>
                        @endif
                    </div>


                    <div class="form-group">
                        <label for="expense_category">Expense Category</label>
                       
                        <select name="expense_category" class="form-control" required>
                            <option value="CANTEEN EXP" {{ old('expense_category', $challan->expense_category) == 'CANTEEN EXP' ? 'selected' : '' }}>CANTEEN EXP.</option>
                            <option value="COMPUTER REPARING EXP" {{ old('expense_category', $challan->expense_category) == 'COMPUTER REPARING EXP' ? 'selected' : '' }}>COMPUTER REPARING EXP.</option>
                            <option value="FESTIVAL EXP" {{ old('expense_category', $challan->expense_category) == 'FESTIVAL EXP' ? 'selected' : '' }}>FESTIVAL EXP.</option>
                            <option value="MOBILE & TELEHPONE RECHARGE EXP" {{ old('expense_category', $challan->expense_category) == 'MOBILE & TELEHPONE RECHARGE EXP' ? 'selected' : '' }}>MOBILE & TELEHPONE RECHARGE EXP.</option>
                            <option value="OFFICE EXP" {{ old('expense_category', $challan->expense_category) == 'OFFICE EXP' ? 'selected' : '' }}>OFFICE EXP.</option>
                            <option value="SITE MISC. ITEM PURCHASE EXP" {{ old('expense_category', $challan->expense_category) == 'SITE MISC. ITEM PURCHASE EXP' ? 'selected' : '' }}>SITE MISC. ITEM PURCHASE EXP.</option>
                            <option value="POSTAGE & COURIER EXP" {{ old('expense_category', $challan->expense_category) == 'POSTAGE & COURIER EXP' ? 'selected' : '' }}>POSTAGE & COURIER EXP.</option>
                            <option value="PRINTING & STATIONERY EXP" {{ old('expense_category', $challan->expense_category) == 'PRINTING & STATIONERY EXP' ? 'selected' : '' }}>PRINTING & STATIONERY EXP.</option>
                            <option value="SALARY EXP" {{ old('expense_category', $challan->expense_category) == 'SALARY EXP' ? 'selected' : '' }}>SALARY EXP.</option>
                            <option value="TRAVELLING EXP" {{ old('expense_category', $challan->expense_category) == 'TRAVELLING EXP' ? 'selected' : '' }}>TRAVELLING EXP.</option>
                            <option value="VEHICLE REPAIRING & MAINTENANCE EXP" {{ old('expense_category', $challan->expense_category) == 'VEHICLE REPAIRING & MAINTENANCE EXP' ? 'selected' : '' }}>VEHICLE REPAIRING & MAINTENANCE EXP.</option>
                            <option value="CAR RENT EXP" {{ old('expense_category', $challan->expense_category) == 'CAR RENT EXP' ? 'selected' : '' }}>CAR RENT EXP.</option>
                            <option value="DIESEL EXP" {{ old('expense_category', $challan->expense_category) == 'DIESEL EXP' ? 'selected' : '' }}>DIESEL EXP.</option>
                            <option value="PETROL EXP" {{ old('expense_category', $challan->expense_category) == 'PETROL EXP' ? 'selected' : '' }}>PETROL EXP.</option>
                            <option value="ELECTRIC BILL EXP" {{ old('expense_category', $challan->expense_category) == 'ELECTRIC BILL EXP' ? 'selected' : '' }}>ELECTRIC BILL EXP.</option>
                            <option value="TRANSPORTING EXP" {{ old('expense_category', $challan->expense_category) == 'TRANSPORTING EXP' ? 'selected' : '' }}>TRANSPORTING EXP.</option>
                            <option value="TENDER FEES EXP" {{ old('expense_category', $challan->expense_category) == 'TENDER FEES EXP' ? 'selected' : '' }}>TENDER FEES EXP.</option>
                            <option value="BANK CHARGES" {{ old('expense_category', $challan->expense_category) == 'BANK CHARGES' ? 'selected' : '' }}>BANK CHARGES</option>
                            <option value="RIGHT OF WAY EXP" {{ old('expense_category', $challan->expense_category) == 'RIGHT OF WAY EXP' ? 'selected' : '' }}>RIGHT OF WAY EXP.</option>
                            <option value="LABOUR WORK EXP" {{ old('expense_category', $challan->expense_category) == 'LABOUR WORK EXP' ? 'selected' : '' }}>LABOUR WORK EXP.</option>
                            <option value="HYDRO RENT EXP" {{ old('expense_category', $challan->expense_category) == 'HYDRO RENT EXP' ? 'selected' : '' }}>HYDRO RENT EXP.</option>
                            <option value="TANKER RENT EXP" {{ old('expense_category', $challan->expense_category) == 'TANKER RENT EXP' ? 'selected' : '' }}>TANKER RENT EXP.</option>
                            <option value="JCB WORK EXP" {{ old('expense_category', $challan->expense_category) == 'JCB WORK EXP' ? 'selected' : '' }}>JCB WORK EXP.</option>
                            <option value="SITE SURVEY EXP" {{ old('expense_category', $challan->expense_category) == 'SITE SURVEY EXP' ? 'selected' : '' }}>SITE SURVEY EXP.</option>
                            <option value="WATER TANKER EXP" {{ old('expense_category', $challan->expense_category) == 'WATER TANKER EXP' ? 'selected' : '' }}>WATER TANKER EXP.</option>
                            <option value="WAY BRIDGE EXP" {{ old('expense_category', $challan->expense_category) == 'WAY BRIDGE EXP' ? 'selected' : '' }}>WAY BRIDGE EXP.</option>
                            <option value="FAST TAG EXP" {{ old('expense_category', $challan->expense_category) == 'FAST TAG EXP' ? 'selected' : '' }}>FAST TAG EXP.</option>
                            <option value="MATERIAL SUPPLY ITEMS" {{ old('expense_category', $challan->expense_category) == 'MATERIAL SUPPLY ITEMS' ? 'selected' : '' }}>MATERIAL SUPPLY ITEMS.</option>
                            <option value="MACHINE & TOOLS REPAIRING EXP" {{ old('expense_category', $challan->expense_category) == 'MACHINE & TOOLS REPAIRING EXP' ? 'selected' : '' }}>MACHINE & TOOLS REPAIRING EXP.</option>
                            <option value="HOTEL & ACCOMODATION EXP" {{ old('expense_category', $challan->expense_category) == 'HOTEL & ACCOMODATION EXP' ? 'selected' : '' }}>HOTEL & ACCOMODATION EXP.</option>
                            <option value="FEBRICATION WORK EXP" {{ old('expense_category', $challan->expense_category) == 'FEBRICATION WORK EXP' ? 'selected' : '' }}>FEBRICATION WORK EXP.</option>
                            <option value="AGRICULTURE EXP" {{ old('expense_category', $challan->expense_category) == 'AGRICULTURE EXP' ? 'selected' : '' }}>AGRICULTURE EXP.</option>
                            <option value="HOUSE & HOTEL RENT EXP" {{ old('expense_category', $challan->expense_category) == 'HOUSE & HOTEL RENT EXP' ? 'selected' : '' }}>HOUSE & HOTEL RENT EXP.</option>
                            <option value="MEDICAL EXP" {{ old('expense_category', $challan->expense_category) == 'MEDICAL EXP' ? 'selected' : '' }}>MEDICAL EXP.</option>
                            <option value="STORE RENT" {{ old('expense_category', $challan->expense_category) == 'STORE RENT' ? 'selected' : '' }}>STORE RENT</option>
                            <option value="TRACTOR RENT" {{ old('expense_category', $challan->expense_category) == 'TRACTOR RENT' ? 'selected' : '' }}>TRACTOR RENT</option>
                        </select>
                        @if ($errors->has('expense_category'))
                            <span class="text-danger">{{ $errors->first('expense_category') }}</span>
                        @endif
                    </div>

                    
                    <div class="form-group">
                        <label for="note">Notes</label>
                        <textarea name="note" id="note" class="form-control" rows="3">{{ old('note') }}</textarea>
                    </div>
                    <div class="form-group">
                    @if (Auth::user()->hasRole('account manager') && $challan->status == 'approved')
                        <button type="submit" name="status" value="approved" class="btn btn-success full-width-approve">Save</button>
                    
                    @elseif ($challan->status == 'pending')
                        <button type="submit" name="status" value="approved" class="btn btn-success full-width-approve">Approved</button>
                    @endif
                    @if (Auth::user()->hasRole('account manager') && $challan->status == 'approved')
                        <button type="submit" name="status" value="rejected" class="btn btn-danger full-width-cancel d-none">Reject</button>
                    @elseif ($challan->status == 'pending')
                    <button type="submit" name="status" value="rejected" class="btn btn-danger full-width-cancel">Reject</button>
                    @endif
                    </div>
                </form>
                @if (Auth::user()->hasRole('account manager') && $challan->status == 'approved')
                <form action="{{ route('challans.destroy', $challan->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger full-width-cancel " onclick="return confirm('Are you sure?')">Delete</button>
                </form>
                @endif
            @else
                <!-- <div class="form-group">
                    <label for="note">Notes</label>
                    <p>{{ $challan->note }}</p>
                </div> -->
            @endif
            @if (Auth::user()->hasRole('account manager')) 
                    <button type="button" class="btn btn-secondary mt-3 full-width-view" onclick="window.history.back();">Close</button>
            @endif
        </div>
        
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
            var table = $('#challan-details-table').DataTable({
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.childRowImmediate,
                        type: 'inline',
                        }
                },
                autoWidth: false,
                paging: false,
                searching: false,
                info: false,
                ordering: false,
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
    $('#viewFileModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var fileUrl = button.data('file'); // Extract info from data-* attributes
        var modal = $(this);
        modal.find('.modal-body #fileViewer').attr('src', fileUrl);
    });
</script>
@stop
