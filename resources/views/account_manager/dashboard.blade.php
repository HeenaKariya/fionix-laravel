@extends('adminlte::page')

@section('title', 'Account Manager Dashboard')

@section('content_header')
    <h1>List of Approved Money Out Transactions</h1>
@stop

@section('content')
    <div class="card">
        <!-- <div class="card-header">
            <h3 class="card-title">Approved Money Out Transactions</h3>
        </div> -->
        <div class="card-body">
            <div id="table-view">
                <table class="table table-bordered" id="transactions-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Project Site Name</th>
                            <th>Type</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Project A</td>
                            <td>Transfer</td>
                            <td>Account 1</td>
                            <td>Account 2</td>
                            <td>01 July 2024</td>
                            <td>11000</td>
                            <td>Initial transfer</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Project B</td>
                            <td>Expense</td>
                            <td>Account 1</td>
                            <td>Vendor 1</td>
                            <td>03 July 2024</td>
                            <td>11500</td>
                            <td>Payment for materials</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Project C</td>
                            <td>Expense</td>
                            <td>Account 2</td>
                            <td>Vendor 2</td>
                            <td>05 July 2024</td>
                            <td>12000</td>
                            <td>Service charges</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Project D</td>
                            <td>Transfer</td>
                            <td>Account 3</td>
                            <td>Account 4</td>
                            <td>08 July 2024</td>
                            <td>10500</td>
                            <td>Project funding</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div id="list-view" class="d-none">
                <ul class="list-group" id="transactions-list"></ul>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
        var isMobile = window.matchMedia("only screen and (max-width: 760px)").matches;
        if (isMobile) {
            $('#table-view').hide();
            $('#list-view').removeClass('d-none');

            var transactions = [
                {
                    project_site_name: 'Project A',
                    type: 'Transfer',
                    from: 'Account 1',
                    to: 'Account 2',
                    date: '2024-07-01',
                    amount: '11000',
                    notes: 'Initial transfer'
                },
                {
                    project_site_name: 'Project B',
                    type: 'Expense',
                    from: 'Account 1',
                    to: 'Vendor 1',
                    date: '2024-07-02',
                    amount: '11500',
                    notes: 'Payment for materials'
                },
                {
                    project_site_name: 'Project C',
                    type: 'Expense',
                    from: 'Account 2',
                    to: 'Vendor 2',
                    date: '2024-07-03',
                    amount: '12000',
                    notes: 'Service charges'
                },
                {
                    project_site_name: 'Project D',
                    type: 'Transfer',
                    from: 'Account 3',
                    to: 'Account 4',
                    date: '2024-07-04',
                    amount: '10500',
                    notes: 'Project funding'
                }
            ];

            var listItems = '';
            transactions.forEach(function(transaction, index) {
                listItems += '<li class="list-group-item">';
                listItems += 'Project Site Name: ' + transaction.project_site_name + '<br>';
                listItems += 'Type: ' + transaction.type + '<br>';
                listItems += 'From: ' + transaction.from + '<br>';
                listItems += 'To: ' + transaction.to + '<br>';
                listItems += 'Date: ' + transaction.date + '<br>';
                listItems += 'Amount: ' + transaction.amount + '<br>';
                listItems += 'Notes: ' + transaction.notes + '<br>';
                listItems += '</li>';
            });
            $('#transactions-list').html(listItems);
        } else {
            $('#transactions-table').DataTable();
        }
    });
</script>
@stop
