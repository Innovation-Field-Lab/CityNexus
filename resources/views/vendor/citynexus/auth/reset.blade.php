<!-- resources/views/auth/reset.blade.php -->

@extends('citynexus::master.login')

@section('main')

    <form method="POST" action="/password/reset">
        {!! csrf_field() !!}
        <input type="hidden" class="form-control" name="token" value="{{ $token }}">
        <div>
            <label for="email">Email</label>
            <input type="email" class="form-control" name="email" value="{{ old('email') }}">
        </div>

        <div>
            <label for="password">Password</label>
            <input type="password" class="form-control" name="password">
        </div>

        <div>
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" class="form-control" name="password_confirmation">
        </div>
        <br>

        <div>
            <button type="submit" class="btn btn-primary">
                Reset Password
            </button>
        </div>
    </form>

@endsection


