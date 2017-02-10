<div class="panel panel-default">
    <div class="panel-heading">
        Property Tags
    </div>
    <div class="panel-body">
        <div class="list-group" id="property_tags">
            @forelse($property->tags as $tag)
                @include('citynexus::property._tag')
            @empty
                <div class="alert alert-info" id="no-tags"> No tags currently associated with property</div>
            @endforelse
            <div class="hidden" id="pending"><i class="glyphicon glyphicon-refresh"></i></div>
        </div>

        @if($property->trashedTags->count() > 0)

            <div class="btn btn-xs btn-default" id="show-trash" onclick="$('#property_trash_tags').removeClass('hidden'); $('#show-trash').addClass('hidden'); ">View Deleted Tags</div>

            <div class="list-group hidden" id="property_trash_tags">
                @foreach($property->trashedTags as $tag)
                    @include('citynexus::property._trash_tag')
                @endforeach
            </div>
        @endif

    </div>
    <div class="panel-footer">
        <div id="new-tag-input">
            <input class="form-control typeahead" type="text" id="new-tag" placeholder="Add new tag">
        </div>
    </div>
</div>


@push('js_footer')

<script>
    {{--add tags--}}
    var substringMatcher = function(strs) {
        return function findMatches(q, cb) {
            var matches, substringRegex;

            // an array that will be populated with substring matches
            matches = [];

            // regex used to determine if a string contains the substring `q`
            substrRegex = new RegExp(q, 'i');

            // iterate through the pool of strings and for any string that
            // contains the substring `q`, add it to the `matches` array
            $.each(strs, function(i, str) {
                if (substrRegex.test(str)) {
                    matches.push(str);
                }
            });

            cb(matches);
        };
    };

    var tags = {!! json_encode($tags) !!};

    $('#new-tag-input .typeahead').typeahead({
                hint: true,
                highlight: true,
                minLength: 1
            },
            {
                name: 'states',
                source: substringMatcher(tags)
            });

    $("#new-tag").bind("keypress", {}, addTag);
    function addTag(e) {
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code == 13) { //Enter keycode
            e.preventDefault();

            var tag = $('#new-tag').val();
            $('#new-tag').val('');
            $('#no-tags').addClass('hidden');
            $('#pending').removeClass('hidden');
            $.ajax({
                url: "{{action('\CityNexus\CityNexus\Http\PropertyController@postAssociateTag')}}",
                type: 'post',
                data: {
                    _token: "{{csrf_token()}}",
                    property_id: {{$property->id}},
                    tag: tag
                }
            }).success( function( data ) {
                        $("#pending").addClass('hidden');
                        $('#new-tag-input').val(null);
                        $('#property_tags').append(data);
                    }
            );
        }
    };


    {{--Delete Tag--}}

    function confirmDelete(id)
    {
        $('#delete-tag-' + id).addClass('btn-danger');
        $('#delete-tag-' + id).attr('onclick', 'removeTag(' + id +')');
    }

    function removeTag(id)
    {
        $('#tag-' + id).addClass('hidden');
        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\PropertyController@postRemoveTag')}}",
            type: "post",
            data: {
                _token: "{{csrf_token()}}",
                property_id: {{$property->id}},
                tag_id: id
            }
        })
    }
</script>

@endpush