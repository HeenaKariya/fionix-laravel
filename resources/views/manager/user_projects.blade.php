@extends('adminlte::page')

@section('title', 'Projects Wise Balance')

@section('content_header')
    <h1>Projects Wise Balance</h1>
@stop

@section('content')
    <div class="card">
        <!-- <div class="card-header">
            <h3 class="card-title">Projects</h3>
        </div> -->
        <div class="card-body">
            <div class="overall-balance-card">
                <h2>{{ $user->name }}</h2>
                <h1>₹{{ number_format($user->balance, 2) }}</h1>
                <p>Overall Available Balance</p>
                <p>Role: {{ strtoupper(implode(', ', $user->getRoleNames()->toArray())) }}</p>
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Project ID</th>
                        <th>Project Site Name</th>
                        <th>Total Money Request</th>
                        <th>Total Expense</th>
                        <th>Current Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $project)
                    <tr>
                        <td>{{ $project->id }}</td>
                        <td>{{ $project->name }}</td>
                        <td>₹{{ formatIndianCurrency($project->approvedRequests) }}</td>
                        <td>₹{{ formatIndianCurrency($project->approvedChallans) }}</td>
                        <td>₹{{ formatIndianCurrency($project->supervisorBalance) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
                <button type="button" class="btn btn-secondary mt-3 full-width-view" onclick="window.location='{{ route('manager.user_list') }}'">Close</button>
        
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
        var table = $('.table').DataTable({
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.childRowImmediate,
                        type: 'inline',
                        }
                },
                autoWidth: false,
                pageLength: 100,
                order: [[0, 'desc']],
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
