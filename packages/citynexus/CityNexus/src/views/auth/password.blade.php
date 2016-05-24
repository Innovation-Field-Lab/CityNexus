<!-- resources/views/auth/password.blade.php -->

@extends('citynexus::master.login')

@section('main')

        <form method="POST" action="/password/email">
            {!! csrf_field() !!}
            <div>
                <label for="email">Email</label>
                <input type="email" class="form-control" name="email" value="{{ old('email') }}">
            </div>
            <br>
            <div>
                <button type="submit" class="btn btn-primary">
                    Send Password Reset Link
                </button>
            </div>
        </form>

@endsection


