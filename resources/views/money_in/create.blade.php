@extends('adminlte::page')

@section('title', 'Create Money In')

@section('content_header')
    <h1>Create Money In Transaction</h1>
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
            <form action="{{ route('money_in.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="project_id">Project Site Name</label>
                    <select name="project_id" id="project_id" class="form-control" required>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="transaction_id">Transaction ID</label>
                    <input type="text" name="transaction_id" id="transaction_id" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="from">From</label>
                    <input type="text" name="from" id="from" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="to">To</label>
                    <input type="text" name="to" id="to" class="form-control" required>
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
                    <label for="payment_datetime">Payment Date and Time</label>
                    <input type="datetime-local" name="payment_datetime" id="payment_datetime" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" name="amount" id="amount" class="form-control" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary full-width-approve">Save Transaction</button>
                @if (Auth::user()->hasRole('account manager')) 
                    <button type="button" class="btn btn-secondary mt-3 full-width-view" onclick="window.location='{{ route('money_in.index') }}'">Cancel</button>
                @endif
            </form>
        </div>
    </div>
@stop
