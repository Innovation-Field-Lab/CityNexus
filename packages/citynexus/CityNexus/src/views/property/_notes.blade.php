<div class="panel panel-default">
    <div class="panel-heading">
        <span class="panel-title">Notes</span>
    </div>
    <div class="panel-body">
        <div class="list-group" id="notes">
            @forelse($property->notes as $note)
                @include('citynexus::property._note')
            @empty
                <div class="list-group-item alert alert-info" id="no-notes">
                    No notes for this property.
                </div>
            @endforelse
        </div>
    </div>
    <div class="panel-body">
        <div class="form">
            <textarea name="note" id="note" cols="30" rows="5" class="form-control"></textarea>
            <br/>
            <button class="btn btn-primary pull-right" onclick="saveNote()">Save Note</button>
        </div>
    </div>
</div>

@push('js_footer')
<script>

    function saveNote()
    {
        var note = $('#note').val();

        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\NoteController@postStore')}}",
            type: "Post",
            data: {
                _token: "{{csrf_token()}}",
                note: note,
                property_id: {{$property->id}},
            }
        }).success(function( data )
        {
            $('#notes').prepend( data );
            $('#note').val( null );
            $('#no-notes').addClass('hidden');
        })
    }

    function deleteNote( id  )
    {
        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\NoteController@getDelete')}}/" + id,
            type: 'GET'
        }).success( function() {
            $("#note-" + id).addClass('hidden');
        })
    }
</script>

@endpush