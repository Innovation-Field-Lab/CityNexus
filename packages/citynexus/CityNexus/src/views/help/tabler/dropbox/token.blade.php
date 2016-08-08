{{-- tabler.dropbox.token --}}

@extends('citynexus::help.modal')

@section('title')
    Dropbox Token
@endsection

@section('text')
    <h3>What is this?</h3>
    <p>
        The Dropbox token is a secret code which allows CityNexus to speak
        directly with your Dropbox account. With this token we will be able
        to read and download files which you assign the token to.
    </p>

    <h3>How do I get a Dropbox Token?</h3>
    <p>
        You can get a token by creating a Dropbox api token within the Dropbox
        Developer panel.
    </p>

@endsection