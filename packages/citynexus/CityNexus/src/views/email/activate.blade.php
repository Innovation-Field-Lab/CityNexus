<!-- resources/views/emails/activate.blade.php -->

@extends("email.email-template")

@section("content")

    <p>
        You have been invited to join the {{env("CITYNEXUS_NAME")}} platform.
    </p>
    <p>
        To activate your account follow this link: {{ url('/activate-account?key=' . $token) }}
    </p>
    <p>
        Please note that this platform is still in active development some features may not work as they should.
        If you encounter a function which is not working, require support, or have a feature request please
        click the "Submit Support Ticket" button in the left hand menu.  Than you for your patience and cooperation in
        developing this system.
    </p>

    <small>(C) 2016 CityNexus</small>
@stop