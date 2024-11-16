@extends('adminlte::page')

@section('title', 'Change Password')

@section('content_header')
    <h1>Change Password: {{ $user->name }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
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
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.change_password', ['id' => $user->id]) }}">
                @csrf
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                    @if ($errors->has('new_password'))
                        <span class="text-danger">{{ $errors->first('new_password') }}</span>
                    @endif
                </div>
                <div class="form-group">
                    <label for="new_password_confirmation">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" class="form-control" required>
                    @if ($errors->has('new_password_confirmation'))
                        <span class="text-danger">{{ $errors->first('new_password_confirmation') }}</span>
                    @endif
                </div>
                <button type="submit" class="btn btn-primary full-width-approve">Change Password</button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary full-width-cancel">Cancel</a>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    $(document).ready(function() {
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
