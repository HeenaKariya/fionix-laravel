@extends('adminlte::page')

@section('title', 'View Project')

@section('content_header')
    <h1>Project Details ({{ $project->id }})</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">{{ $project->name }}</h3>
            @if (Auth::user()->hasRole('manager') || Auth::user()->hasRole('owner')  || Auth::user()->hasRole('account manager'))
                <a href="{{ route('projects.generatePdf', $project->id) }}" class="btn btn-primary">PDF Report</a>
            @endif
        </div>
        <div class="card-body">
            <table id="example-table" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Project Site Name</th>
                        <th>Location</th>
                        <th>Project Start Date</th>
                        <th>Project Status</th>
                        <th>List of Staff Members Assigned</th>
                        <th>Project Budget</th>
                        @if (Auth::user()->hasRole('manager') || Auth::user()->hasRole('owner')  || Auth::user()->hasRole('account manager'))
                        <th>Total Money In</th>
                        <th>Total Money Out</th>
                        <th>Expense Bill Pending</th>
                        <th>Net Profit/Loss</th>
                        @endif
                        <th>Note</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $project->name }}</td>
                        <td>{{ $project->location }}</td>
                        <td>{{ \Carbon\Carbon::parse($project->start_date)->format('h:i A d/m/Y') }}</td>
                        <td>
                            @php
                                $statusClass = '';
                                if ($project->status === 'approved') {
                                    $statusClass = 'bg-success text-white';
                                } elseif ($project->status === 'rejected') {
                                    $statusClass = 'bg-danger text-white';
                                }
                            @endphp
                            <span class="label {{ $statusClass }}">{{ ucfirst($project->status) }}</span>
                        </td>
                        <td>
                            <ul>
                                @foreach ($project->supervisors as $supervisor)
                                    <li>{{ $supervisor->name }}: {{ $supervisor->mobile_no }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>₹{{ formatIndianCurrency($project->budget) }}</td>
                        @if (Auth::user()->hasRole('manager') || Auth::user()->hasRole('owner')  || Auth::user()->hasRole('account manager'))
                        <td>₹{{ formatIndianCurrency($totalMoneyIn) }}</td>
                        <td>₹{{ formatIndianCurrency($approvedChallansProjectwise) }}</td>
                        <td>₹{{ formatIndianCurrency($pendingChallansProjectwise) }}</td>
                        <td>₹{{ formatIndianCurrency($netProfitLoss) }}</td>

                        @endif
                        <td>{{$project->note }}</td>
                    </tr>
                </tbody>
            </table>

            
            <!-- <div class="form-group">
                <label>Note:</label>
                <p>{{ $project->note }}</p>
            </div> -->
            @if(Auth::user()->hasRole('account manager') || Auth::user()->hasRole('owner') || Auth::user()->hasRole('manager'))


                <!-- List of Pending Expense Bill/Challan as Cards -->
                <div class="form-group project-detail-table">
                    <label>Expense Bill/Challan Pending List</label>
                    @if($pendingChallans->isEmpty())
                        <p>No pending expense bills or challans found.</p>
                    @else
                        <table id="expense-table" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Supervisor Name</th>
                                    <th>Pending Expense Bill/Challan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingChallans as $challan)
                                    <tr>
                                        <td>{{ $challan->user->name }}</td>
                                        <td>₹{{ formatIndianCurrency($challan->amount) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <div class="form-group project-detail-table">
                    <label>Staff Money Overview</label>
                    @if($userBalances->isEmpty())
                        <p>No Staff Money Overview found.</p>
                    @else
                    <table id="user-balance-table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Total Money Request</th>
                                <th>Total Expenses</th>
                                <th>Current Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($userBalances as $balance)
                                <tr>
                                    <td>{{ $balance['user']->name }}</td>
                                    <td>₹{{ number_format($balance['approvedRequests'], 2) }}</td>
                                    <td>₹{{ number_format($balance['approvedExpenses'], 2) }}</td>
                                    <td>₹{{ number_format($balance['currentBalance'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>

                <!-- List of Money In Transactions as Cards -->
                <div class="form-group project-detail-table">
                    <label>Money In Transactions:</label>
                    @if($moneyInTransactions->isEmpty())
                        <p>No transactions found.</p>
                    @else
                        <table id="money-in-table" class="table table-bordered money-table">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Type</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($moneyInTransactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->transaction_id }}</td>
                                        <td>{{ $transaction->payment_type }}</td>
                                        <td>{{ $transaction->from }}</td>
                                        <td>{{ $transaction->to }}</td>
                                        <td>{{ \Carbon\Carbon::parse($transaction->payment_datetime)->format('h:i A d/m/Y') }}</td>
                                        <td>₹{{ formatIndianCurrency($transaction->amount) }}</td>
                                        <td>{{ $transaction->notes ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <!-- List of Money Out Transactions as Cards -->
                <div class="form-group project-detail-table">
                    <label>Money Out Transactions:</label>
                    @if($moneyOutTransactions->isEmpty())
                        <p>No Money Out transactions found.</p>
                    @else
                        <table id="money-out-table" class="table table-bordered money-table">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Type</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($moneyOutTransactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->transaction_id }}</td>
                                        <td>{{ $transaction->payment_type }}</td>
                                        <td>{{ $transaction->from }}</td>
                                        <td>{{ $transaction->to }}</td>
                                        <td>{{ \Carbon\Carbon::parse($transaction->payment_date)->format('h:i A d/m/Y') }}</td>
                                        <td>₹{{ formatIndianCurrency($transaction->amount) }}</td>
                                        <td>{{ $transaction->notes ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <!-- List of Approved Money Requests -->
                    <div class="form-group project-detail-table">
                        <label>Approved Money Requests:</label>
                        @if($approvedMoneyRequests->isEmpty())
                            <p>No approved money requests found.</p>
                        @else
                            <table class="table table-bordered money-table" id="approved-money-requests-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Request ID</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($approvedMoneyRequests as $request)
                                        <tr>
                                            <td>{{ $request->user->name }}</td>
                                            <td>{{ $request->id }}</td>
                                            <td>{{ \Carbon\Carbon::parse($request->date)->format('h:i A d/m/Y') }}</td>
                                            <td>₹{{ formatIndianCurrency($request->amount) }}</td>
                                            
                                            <td>{{ $request->note ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>


                <!-- List of Approved Challans as Cards -->
                <div class="form-group project-detail-table">
                    <label>Expense Bill/Challan Transactions:</label>
                    @if($approvedChallans->isEmpty())
                        <p>No approved expense bills or challans found.</p>
                    @else
                        <table id="expense-challan-table" class="table table-bordered money-table">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>From</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Notes</th>
                                    <th>File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($approvedChallans as $challan)
                                    <tr>
                                        <td>{{ $challan->payment_type }}</td>
                                        <td>{{ $challan->user->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($challan->bill_date)->format('d/m/Y') }}</td>
                                        <td>₹{{ formatIndianCurrency($challan->amount) }}</td>
                                        <td>{{ $challan->note ?? 'N/A' }}</td>
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
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                <button type="button" class="btn btn-secondary mt-3 full-width-view" onclick="window.history.back();">Close</button>
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
            @endif
            <!-- <div class="form-group">
                <label>Money Requests:</label>
                <div id="table-view">
                    <table class="table table-bordered" id="money-requests-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Payment Type</th>
                                <th>Status</th>
                                <th>Note</th>
                                @if (Auth::user()->hasRole('manager') || Auth::user()->hasRole('owner') || Auth::user()->hasRole('account manager'))
                                    <th>Action</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $moneyRequests = $project->moneyRequests->sortByDesc('created_at');
                                if (Auth::user()->hasRole('account manager')) {
                                    $moneyRequests = $moneyRequests->filter(function($request) {
                                        return $request->admin_status === 'approved';
                                    });
                                }
                                if (Auth::user()->hasRole('owner')) {
                                    $moneyRequests = $moneyRequests->filter(function($request) {
                                        return $request->manager_status === 'approved';
                                    });
                                }
                            @endphp
                            @foreach ($moneyRequests as $index => $request)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($request->date)->format('h:i A d/m/Y') }}</td>
                                    <td>₹{{ formatIndianCurrency($request->amount) }}</td>
                                    <td>{{ $request->payment_type }}</td>
                                    <td>
                                        @php
                                            $statusClass = '';
                                            if ($request->status === 'approved') {
                                                $statusClass = 'bg-success text-white';
                                            } elseif ($request->status === 'rejected') {
                                                $statusClass = 'bg-danger text-white';
                                            }
                                        @endphp
                                        <span class="label {{ $statusClass }}">{{ ucfirst($request->status) }}</span>
                                    </td>
                                    <td>{{ $request->note }}</td>
                                    @if ((Auth::user()->hasRole('manager') && $request->status == 'pending') || Auth::user()->hasRole('owner') || Auth::user()->hasRole('account manager') || Auth::user()->hasRole('supervisor'))
                                        <td>
                                            <a href="{{ route('projects.approve', $request->id) }}" class="btn btn-primary btn-sm full-width-view">View</a>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div id="list-view" class="d-none">
                    <ul class="list-group" id="money-requests-list"></ul>
                </div>
            </div> -->

            @if (Auth::user()->hasAnyRole(['supervisor']))
                <div class="form-group">
                    <button type="button" class="btn btn-success full-width-approve" data-toggle="modal" data-target="#moneyRequestModal">
                        Create New Money Request
                    </button>
                </div>
            @endif
        </div>
    </div>

    @if (Auth::user()->hasAnyRole(['manager', 'supervisor']))
        <!-- Money Request Modal -->
        <div class="modal fade" id="moneyRequestModal" tabindex="-1" role="dialog" aria-labelledby="moneyRequestModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('money-requests.store') }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="moneyRequestModalLabel">Create New Money Request</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="project_id">Project</label>
                                <select name="project_id" id="project_id" class="form-control" required>
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="date">Date</label>
                                <input type="date" name="date" id="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="number" name="amount" id="amount" class="form-control" step="0.01" required>
                            </div>
                            <div class="form-group">
                                <label for="payment_type">Payment Type</label>
                                <select name="payment_type" id="payment_type" class="form-control" required>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="UPI">UPI</option>
                                    <option value="DD(Demand Draft)">DD(Demand Draft)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="note">Note</label>
                                <textarea name="note" id="note" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary full-width-approve">Save</button>
                            <button type="button" class="btn btn-secondary full-width-cancel" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@stop

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
    $(document).ready(function() {
        $('#viewFileModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); 
            var fileUrl = button.data('file'); 
            var modal = $(this);
            modal.find('.modal-body #fileViewer').attr('src', fileUrl);
        });
    var exampleTable = $('#example-table').DataTable({
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.childRowImmediate,
                type: 'inline',
            }
        },
        paging: false,
        searching: false,
        info: false,
        ordering: false,
        autoWidth: false,
    });

    exampleTable.rows().every(function(rowIdx, tableLoop, rowLoop) {
        var row = this;
        if (!row.child.isShown()) {
            row.child.show();
            $(row.node()).addClass('shown');  
        }
    });


    var moneyInTable = $('.money-table').DataTable({
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.childRowImmediate,
                type: 'inline',
            }
        },
        paging: true,
        // searching: false,
        // info: false,
        // ordering: false,
        autoWidth: false,
        lengthChange: true,
    });
    // Ensure all rows are shown in responsive mode
    moneyInTable.rows().every(function(rowIdx, tableLoop, rowLoop) {
        var row = this;
        if (!row.child.isShown()) {
            row.child.show();
            $(row.node()).addClass('shown');  
        }
    });

    $('#money-details-table').DataTable({
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.childRowImmediate,
                type: 'inline',
            }
        },
        paging: true,
        // searching: false,
        // info: false,
        // ordering: false,
        autoWidth: false,
    });

    $('#expense-table').DataTable({
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.childRowImmediate,
                type: 'inline',
            }
        },
        paging: true,
        // searching: false,
        // info: false,
        // ordering: false,
        autoWidth: false,
    });

    $('#user-balance-table').DataTable({
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.childRowImmediate,
                type: 'inline',
            }
        },
        paging: true,
        // searching: false,
        // info: false,
        // ordering: false,
        autoWidth: false,
    });

    $('#money-requests-table').DataTable({
        order: [[1, 'desc']],
        responsive: true,
        autoWidth: false,
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
