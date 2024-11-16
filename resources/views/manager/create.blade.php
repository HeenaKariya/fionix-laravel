@extends('adminlte::page')

@section('title', 'Create Money Request')

@section('content_header')
    <h1>Create Money Request</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Create Money Request</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('money-requests.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="project_id">Project</label>
                    <select name="project_id" id="project_id" class="form-control" required>
                        @foreach ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
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
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="note">Note</label>
                    <textarea name="note" id="note" class="form-control"></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
@stop
