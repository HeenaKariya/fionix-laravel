@extends('adminlte::page')

@section('title', 'Create Money Out')

@section('content_header')
    <h1>Create Money Out Transaction</h1>
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
            <form action="{{ route('money_out.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="project_id">Project Site Name</label>
                    <select name="project_id" id="project_id" class="form-control" required>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                    @error('project_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="transaction_id">Transaction ID</label>
                    <input type="text" name="transaction_id" id="transaction_id" class="form-control" required>
                    @error('transaction_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="from">From</label>
                    <input type="text" name="from" id="from" class="form-control" required>
                    @error('from')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="to">To</label>
                    <input type="text" name="to" id="to" class="form-control" required>
                    @error('to')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
                    @error('payment_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="payment_date">Payment Date and Time</label>
                    <input type="datetime-local" name="payment_date" id="payment_date" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                    @error('payment_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="number" name="amount" id="amount" class="form-control" step="0.01" required>
                    @error('amount')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="image">Upload Image / Document</label>
                    <input type="file" name="upload_image[]" id="upload_image" class="form-control" multiple>
                    @error('upload_image.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" class="form-control"></textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary full-width-approve">Save Transaction</button>
                @if (Auth::user()->hasRole('account manager')) 
                    <button type="button" class="btn btn-secondary mt-3 full-width-view" onclick="window.location='{{ route('money_out.index') }}'">Cancel</button>
                @endif
            </form>
        </div>
    </div>
@stop
