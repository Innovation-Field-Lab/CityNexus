@extends('citynexus::help.modal')

@section('title')
    Create Dataset from Upload
@endsection

@section('text')
    <h2>Why you want to be here</h2>
    <p>
        If have a new type of data that you would like to
        upload for the first time, you are in the right
        place! This data should be in a tabular form,
        meaning the data can be stored on a single Excel-like
        format with column headers at the top and then each
        representing an additional point in your dataset.
    </p>
    <h2>Preparing data for upload</h2>
    <p>
        The format you use the first time you upload data to
        this dataset will become the default expected format
        for data to arrive. So, you should consider how replicable
        the format you are getting data in is. If you are able
        to export data directly from another system into a usable
        CSV that is excellent, but if you will need to significantly
        reformat the data each time it comes out of the original
        system that may prohibit frequent updates of the data.
    </p>
    <p>
        Each line in the dataset must have some way to identify
        which property it relates to. Most commonly this will be a
        a street address. While CityNexus will accept addresses in
        a variety of formats, for the best results, address should
        have a house number, street name, street type, and a unit.
        If the only format address come in is "full address" (e.g.
        123 Main Street, Apt 1A) CityNexus will automatically parse
        these addresses into the prefered form.
    </p>
    <p>
        CityNexus also will accept several special data points
        you may wish to include in your system.</p>
    <b>Time Stamp</b>
    <p>
        If you select a timestamp field when uploading records
        will give users more flexibility in filtering data by
        the actual timestamp of the data, rather than just the
        upload date. This could be most useful of records like
        tickets, permits, or notices issues, since multiple may
        occur within a single upload interval.
    </p>
    <b>Unique ID</b>
    <p>
        To avoid duplicate data, consider including a unique ID
        in your datasets where possible. A unique id should an
        incrementing value, like a ticket or invoice number,
        which never repeats. If a record which has already been
        uploaded is uploaded again it will be ignored in preference
        for the record already in the system.
    </p>

    <b>CityNexus Property ID</b>
    <p>
        If possible, your data should include the unique ID which
        CityNexus has assigned to each property within your dataset.
        Using these IDs will allow the highest level of accuracy when
        matching records with the CityNexus database.
    </p>
    @endsection