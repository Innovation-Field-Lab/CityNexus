{{-- tabler.uploader.fieldname --}}

@extends('citynexus::help.modal')

@section('title')
    Uploader Sync
@endsection

@section('text')
    <h3>What is this?</h3>
    <p>
        Sync fields are what allows CityNexus to connect datasets
        with each other through related properties. Any data being
        uploaded needs to be connected with an address.
    </p>
    <h3>How should addresses be formatted?</h3>
    <p>
        As consistently as possible is the short answer. If there
        are a variety of ways to export addresses the preference
        would be to have house number, street name, street type, and
        unit as four separate fields. If that is not possible, CityNexus
        will parse full address in to these for parts.
    </p>
    <h3>What if addresses aren't exactly the same?</h3>
    <p>
        CityNexus tries to compensate for most variations in how
        properties are addressed, including matching variations on
        street types (e.g. Street, STR, St., etc). CityNexus will also
        ignore any capitalization or stray punctuation in addresses.
    </p>

@endsection