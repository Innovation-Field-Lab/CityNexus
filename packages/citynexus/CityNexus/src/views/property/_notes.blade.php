<div class="panel panel-default">
    <div class="panel-heading">
        <span class="panel-title">Notes</span>
    </div>
    <div class="panel-body">
        <div class="notes" id="notes">
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
            <div id="replyTo" class="hidden">
                <input type="hidden" id="reply_note_id">
                <span id="reply_to_name"></span>
                <span class="label label-default pull-right" onclick="removeReplyToNote()" style="cursor: pointer"><i id="fa fa-times-circle-o"></i> Remove Reply</span>
            </div>
            <textarea name="note" id="note" cols="30" rows="5" class="form-control"></textarea>
            <br/>
            <button class="btn btn-primary pull-right" onclick="saveNote()">Save Note</button>
        </div>
    </div>
</div>

@push('js_footer')
<script>

    function replyToNote(note_id, name) {
        $('#reply_to_name').html('@ ' + name);
        $('#reply_note_id').val(note_id);
        $('#replyTo').removeClass('hidden');
        window.location.href = "#replyTo";
    };

    function removeReplyToNote(){
        $('#replyTo').addClass('hidden');
        $('#reply_note_id').val(null);
    };

    function saveNote()
    {
        var note = $('#note').val();

        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\NoteController@postStore')}}",
            type: "Post",
            data: {
                _token: "{{csrf_token()}}",
                note: note,
                reply_to: $('#reply_note_id').val(),
                property_id: {{$property->id}},
            }
        }).success(function( data )
        {
            if($('#reply_note_id').val() != null)
            {
                $('#reply-notes-' + $('#reply_note_id').val()).prepend( data ).removeClass('hidden');
            }
            else {
                $('#notes').prepend( data );
            }
            $('#note').val( null );
            $('#no-notes').addClass('hidden');
        })
    };

    function deleteNote( id  )
    {
        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\NoteController@getDelete')}}/" + id,
            type: 'GET'
        }).success( function() {
            $("#note-" + id).addClass('hidden');
        })
    };
</script>

@endpush

@push('style')
<style>
    #replyTo {
        height: 25px;
        width: 100%;
        padding: 5px;
        font-size: .9em;
        background-color: lavender;
    }

    .notes {
        padding-left: 10px;
    }
    .note-body {
        border-bottom: 1px dashed;
        padding-bottom: 5px;
    }

    .notes .note {
        padding: 5px;
    }
    .note-footer {
        padding-bottom: 5px;
        padding-top: 10px;
        font-size: .9em;
    }
    .replies .note {
        border-bottom: none;
        border-left: groove;

    }
    .note-byline {
        color: darkgrey;
        font-size: .8em;
    }
</style>
@endpush