<!DOCTYPE html>
<html>
<head>
    <title>{{ $project->name }} - Income & Expense Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .summary-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }
        .summary-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .net-loss {
            color: red;
        }
        .net-profit {
            color: green;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $project->name }}</h2>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th colspan="3">INCOME (RECEIPT)</th>
                <th colspan="3">EXPENSE</th>
            </tr>
            <tr>
                <th>Date</th>
                
                <th>Description
                </th>
                <th>Amount</th>
                <th>Date</th>
                
                <th>Description
                </th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $maxRows = max(count($moneyInTransactions), count($combinedOutTransactions));
            @endphp
            @for($i = 0; $i < $maxRows; $i++)
                <tr>
                    {{-- Left Side: Income Transactions --}}
                    @if(isset($moneyInTransactions[$i]))
                        <td>{{ \Carbon\Carbon::parse($moneyInTransactions[$i]->payment_datetime)->format('d-m-Y') }}</td>
                                               
                        {{--<td> {!! "FROM:" . $moneyInTransactions[$i]->from . " <br>TO:" . $moneyInTransactions[$i]->to . " <br>NOTES:" . $moneyInTransactions[$i]->notes !!}</td>--}}
                       
                          <td> {{ $moneyInTransactions[$i]->notes  }}</td>  
                        <td>{{ number_format($moneyInTransactions[$i]->amount, 2) }}</td>
                    @else
                        <td colspan="3"></td>
                    @endif

                    {{-- Right Side: Outgoing Transactions --}}
                    @if(isset($combinedOutTransactions[$i]))
                        <td>
                            {{ \Carbon\Carbon::parse(
                                $combinedOutTransactions[$i]->type === 'money_out' 
                                    ? $combinedOutTransactions[$i]->payment_date 
                                    : $combinedOutTransactions[$i]->bill_date
                            )->format('d-m-Y') }}
                        </td>

                        <td>
                            @if($combinedOutTransactions[$i]->expense_category)
                                {{ $combinedOutTransactions[$i]->expense_category }}
                            @elseif($combinedOutTransactions[$i]->to)
                                {{ $combinedOutTransactions[$i]->to . " - " . $combinedOutTransactions[$i]->note }}
                            @endif
                        </td>

                        <td>{{ number_format($combinedOutTransactions[$i]->amount, 2) }}</td>
                    @else
                        <td colspan="3"></td>
                    @endif

                </tr>
            @endfor
            @foreach($userBalances as $userName => $balance)
                @if($balance != 0)
                    <tr>
                        <td colspan="3"></td> {{-- Skip Income Side --}}
                        <td>{{ \Carbon\Carbon::now()->format('d-m-Y') }}</td>
                        <td colspan="1"><strong>{{ $userName }}</strong></td>
                        <td colspan="3">{{ number_format($balance, 2) }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <table class="summary-table">
        <tr>
            <td colspan="2"><strong>TOTAL RECEIVED:</strong></td>
            <td>{{ number_format($totalReceived, 2) }}</td>
            <td><strong>TOTAL PAYMENT:</strong></td>
            <td>{{ number_format($totalPayment, 2) }}</td>
        </tr>
        
    </table>
    <table class="summary-table">
    @if ( $netResult > 0 ) { 
        <tr>|
            <td ><strong>NET PROFIT:</strong></td>
            <td class="{{ $netResult > 0 ? 'net-profit' : '' }}">{{ $netResult > 0 ? number_format(abs($netResult), 2) : '0.00' }}</td>
        </tr>
        
    } 
    @else {
        <tr>
            <td><strong>NET LOSS:</strong></td>
            <td class="{{ $netResult < 0 ? 'net-loss' : '' }}">{{ $netResult < 0 ? number_format(abs($netResult), 2) : '0.00' }}</td>
        </tr>

    }
    @endif  
    </table>
</body>
</html>
