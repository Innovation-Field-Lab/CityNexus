<!-- resources/views/emails/activate.blade.php -->

@extends("email.email-template")

@section("content")

    <p>

        The file you have requested is now available: <a href="{{$path}}">{{$path}}</a>.

    </p>

    <p>
        This message will self destruct in 24 hours.
    </p>
@stop