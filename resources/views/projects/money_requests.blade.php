@extends('adminlte::page')

@section('title', 'Create New Money Request')

@section('content_header')
    <h1>Create New Money Request</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
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
            <form action="{{ route('money-requests.store') }}" method="POST">
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
                    <label for="date">Date</label>
                    <input type="datetime-local" name="date" id="date" class="form-control" value="{{ \Carbon\Carbon::now()->format('Y-m-d\TH:i') }}" required>
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
                <button type="submit" class="btn btn-primary full-width-approve">Submit Request</button>
                @if(Auth::user()->hasRole('supervisor'))
                    <a href="{{ route('supervisor.dashboard') }}" class="btn btn-warning full-width-cancel">Cancel</a>
                @endif
                @if (Auth::user()->hasRole('manager'))
                    <button type="button" class="btn btn-secondary mt-3 full-width-view" onclick="window.location='{{ route('manager.dashboard') }}'">Cancel</button>
                @endif
            </form>
        </div>
    </div>
@stop
