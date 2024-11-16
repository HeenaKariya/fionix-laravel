@extends('adminlte::page')

@section('title', 'Money Request')

@section('content_header')
    <h1>Money Request ({{ $moneyRequest->id }})</h1>
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
        <!-- <div class="card-header">
            <h3 class="card-title">Request from Project: {{ $moneyRequest->project->name }}</h3>
        </div> -->
        <div class="card-body">
            <table class="table table-bordered" id="req-table">
                <thead>
                    <tr>
                        <th>Project Site Name</th>
                        <th>Request Number</th>
                        <th>Payment Type</th>
                        <th>Date</th>
                        <th>Money Requested by</th>
                        <th>Pending Expense Bill/Challan Amount(this project)</th>
                        <th>Pending Expense Bill/Challan Amount(TOTAL)</th>
                        <th>Amount</th>
                        
                        <!-- <th>Balance for this Project</th> -->
                        @if (Auth::user()->hasRole('manager'))
                            <th>Total Balance</th>
                        @endif
                        <th>Status</th>
                        @if (!empty($moneyRequest->manager_status))
                            <th>Manager Status</th>
                            <th>Manager</th>
                            <th>Manager Status Updated At</th>
                        @endif
                        @if (!empty($moneyRequest->admin_status))
                            <th>Owner Status</th>
                            <th>Owner</th>
                            <th>Owner Status Updated At</th> 
                        @endif
                        <th>Note</th>
                        @if (!empty($moneyRequest->manager_note))
                            <th>Manager Note</th>
                        @endif
                        @if (!empty($moneyRequest->admin_note))
                            <th>Owner Note</th>
                        @endif
                        @if (!empty($moneyRequest->amanager_note))
                            <th>Account Manager Note</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $moneyRequest->project->name }}</td>
                        <td>{{ $moneyRequest->id }}</td>
                        <td>{{ $moneyRequest->payment_type }}</td>
                        <td>{{ \Carbon\Carbon::parse($moneyRequest->date)->format('h:i A d/m/Y') }}</td>
                        <td>{{ $moneyRequest->user->name }}</td>
                        <td>₹{{ formatIndianCurrency($projectbalance) }}</td>
                        <td>
                            ₹{{ Auth::user()->hasRole('manager') || Auth::user()->hasRole('supervisor') ? formatIndianCurrency($overallBalance) : formatIndianCurrency($overallBalance1) }}
                        </td>
                        <td>₹{{ formatIndianCurrency($moneyRequest->amount) }}</td>
                        
                        <!-- <td>₹{{ formatIndianCurrency($moneyRequest->project->budget) }}</td> -->
                        @if (Auth::user()->hasRole('manager'))
                            <td>₹{{ formatIndianCurrency($projectbalance) }}</td>
                        @endif
                        <td>
                            @php
                                $statusClass = '';
                                if ($moneyRequest->status === 'approved') {
                                    $statusClass = 'bg-success text-white';
                                } elseif ($moneyRequest->status === 'rejected') {
                                    $statusClass = 'bg-danger text-white';
                                }
                            @endphp
                            <span class="label {{ $statusClass }}">{{ ucfirst($moneyRequest->status) }}</span>
                        </td>
                        @if (!empty($moneyRequest->manager_status))
                            <td>
                                @php
                                    $statusClass = '';
                                    if ($moneyRequest->manager_status === 'approved') {
                                        $statusClass = 'bg-success text-white';
                                    } elseif ($moneyRequest->manager_status === 'rejected') {
                                        $statusClass = 'bg-danger text-white';
                                    }
                                @endphp
                                <span class="label {{ $statusClass }}">{{ ucfirst($moneyRequest->manager_status) }}</span>
                            </td>
                            <td>{{ $moneyRequest->manager ? $moneyRequest->manager->name : 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($moneyRequest->manager_status_updated_at)->format('h:i A d/m/Y') }}</td>
                        @endif
                        @if (!empty($moneyRequest->admin_status))
                            <td>
                                @php
                                    $statusClass = '';
                                    if ($moneyRequest->admin_status === 'approved') {
                                        $statusClass = 'bg-success text-white';
                                    } elseif ($moneyRequest->admin_status === 'rejected') {
                                        $statusClass = 'bg-danger text-white';
                                    }
                                @endphp
                                <span class="label {{ $statusClass }}">{{ ucfirst($moneyRequest->admin_status) }}</span>
                            </td>
                            <td>{{ $moneyRequest->admin ? $moneyRequest->admin->name : 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($moneyRequest->admin_status_updated_at)->format('h:i A d/m/Y') }}</td>
                        @endif
                        <td>{{ $moneyRequest->note }}</td>
                        @if (!empty($moneyRequest->manager_note))
                            <td>{{ $moneyRequest->manager_note }}</td>
                        @endif
                        @if (!empty($moneyRequest->admin_note))
                            <td>{{ $moneyRequest->admin_note }}</td>
                        @endif
                        @if (!empty($moneyRequest->amanager_note))
                            <td>{{ $moneyRequest->amanager_note }}</td>
                        @endif
                    </tr>
                </tbody>
            </table>

            @if ($moneyRequest->manager_status == '' && Auth::user()->hasRole('manager') && $moneyRequest->user_id != Auth::id())
                <form action="{{ route('money-requests.updateStatus', $moneyRequest->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="note">Note:</label>
                        <textarea name="note" id="note" class="form-control">{{ old('note') }}</textarea>
                    </div>
                    <button type="submit" name="status" value="approved" class="btn btn-success mb-2 full-width-approve">Approve</button>
                    <button type="submit" name="status" value="rejected" class="btn btn-danger mb-2 full-width-cancel">Reject</button>
                </form>
            @elseif ($moneyRequest->manager_status == 'approved' && Auth::user()->hasRole('owner') && is_null($moneyRequest->admin_status))
                <form action="{{ route('money-requests.updateStatus', $moneyRequest->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="amount">Amount (₹):</label>
                        <input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount', $moneyRequest->amount) }}" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_note">Owner Note:</label>
                        <textarea name="admin_note" id="admin_note" class="form-control">{{ old('admin_note') }}</textarea>
                    </div>
                    <button type="submit" name="status" value="approved" class="btn btn-success d-block mb-2 full-width-approve">Approve</button>
                    <button type="submit" name="status" value="rejected" class="btn btn-danger d-block mb-2 full-width-cancel">Reject</button>
                </form>
            @elseif ($moneyRequest->admin_status == 'approved' && $moneyRequest->amanager_status == '' && Auth::user()->hasRole('account manager'))
                <form action="{{ route('money-requests.updateStatus', $moneyRequest->id) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="amanager_note">Notes from Accountant:</label>
                        <textarea name="amanager_note" id="amanager_note" class="form-control">{{ old('accountant_note') }}</textarea>
                    </div>
                    <button type="submit" name="amanager_status" value="approved" class="btn btn-success d-block mb-2 full-width-approve">Payment DONE</button>

                </form>
                
                <form action="{{ route('money-requests.destroy', $moneyRequest->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger full-width-cancel " onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            @endif
            
            @if (request()->query('source') === 'store')
            <button type="button" class="btn btn-secondary mt-3 full-width-approve" 
                onclick="window.location.href='{{ route('supervisor.moneyRequests.pending') }}';">Close
            </button>
            @else
                <button type="button" class="btn btn-secondary mt-3 full-width-approve" onclick="window.history.back();">Close</button>
            @endif
        </div>
    </div>

@stop

@section('js')
<script>
    $(document).ready(function() {
        // Toastr configuration
        var table = $('#req-table').DataTable({
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
