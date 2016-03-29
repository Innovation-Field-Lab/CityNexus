@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                Create New Risk Score
            </div>
        </div>
        <div class="panel-body">
            <div class="col-sm-6">
                <form id="new-risk-score" method="post" action="/{{config('citynexus.root_directory')}}/risk-score/save-score">
                    {!! csrf_field() !!}

                    <label for="name" class="control-label">Score Name</label>
                    <input type="text" class="form-control" id="name" name="name"
                                   value="{{old('name')}}"/>
                    <br>
                    <div class="list-group" id="score-elements">
                        <div class="list-group-item well">Score Elements</div>
                    </div>
                    <input type="submit" class="btn btn-primary" value="Save Score">
                </form>
            </div>
            <div class="col-sm-6 well">
                <b>Select Data Set</b>
                @include('citynexus::risk-score.dataset')
                <br>
                <form id="new-score-element" onsubmit="return false">
                <div id="datafields"></div>
                </form>
            </div>
        </div>
    </div>
    </div>

@stop

@push('js_footer')

<script>
    $('#dataset').change(function(){
        var dataset_id = $('#dataset').val();
        if(dataset_id != null) {
            $.ajax({
                url: '/{{config("citynexus.root_directory")}}/risk-score/data-fields/',
                type: 'get',
                data: {
                    _token: "{{csrf_token()}}",
                    dataset_id: dataset_id
                }
            }).success(function (data) {
                $('#datafields').html(data);
            })
        }
        else {
            $('#datafields').addClass('hidden');

        }
    });

    function addToScore()
    {
        var formData = $('#new-score-element').serialize();

        $.ajax({
            url: "/{{config("citynexus.root_directory")}}/risk-score/create-element?" + formData,
            type: "get",
            data: {
                _token: "{{csrf_token()}}"
            }
        }).success(function( data ) {
            $("#score-elements").append( data );
        })
    }

    function removeFromScore(id)
    {
        $("#" + id).html(null);
    }
</script>

@endpush

