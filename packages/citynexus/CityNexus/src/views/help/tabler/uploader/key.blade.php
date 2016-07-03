{{-- tabler.uploader.fieldname --}}

@extends('citynexus::help.modal')

@section('title')
    Uploader Key
@endsection

@section('text')
    <h3>What is this?</h3>
    <p>
        This is the version of the column title in the uploaded document.
        This is how the key will be saved in the database. It may vary from the actual
        name used in the uploaded file because it has been cleaned of unusable characters
    </p>

@endsection