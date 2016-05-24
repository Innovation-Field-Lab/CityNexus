{{-- tabler.uploader.fieldname --}}

@extends('citynexus::help.modal')

@section('title')
    Uploader Field Name
@endsection

@section('text')
    <h3>Where did this come from?</h3>
    <p>
        When your data is first uploaded CityNexus attempts to
        create a "Human Readable" name for your data which removes
        underscores and provides nice capitalization. If the name
        of this field still isn't very readable though you may want
        to make further changes to it to ensure it is as helpful to
        users as possible.
    </p>

    <h3>Where will this be used?</h3>
    <p>
        This data label will be used throughout CityNexus where
        this particular data point is being referenced.
    </p>

    <h3>Can I use special characters?</h3>
    <p>
        Yes! This is only a label for the data so will not effect
        how the data is actually stored in the database. However
        you are still encouraged to keep such labels brief.
    </p>
@endsection