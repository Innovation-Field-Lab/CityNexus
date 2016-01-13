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
                <b>Select Data Set</b>
                @include('citynexus::risk-score.dataset')
                <form id="new-risk-score">
                    <div class="list-group" id="score-elements">

                    </div>
                </form>
            </div>
            <div class="col-sm-6">
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
        $.ajax({
            url: '/{{config("citynexus.root_directory")}}/risk-score/data-fields/',
            type: 'get',
            data: {
                _token: "{{csrf_token()}}",
                dataset_id: dataset_id
            }
        }).success(function(data){
            $('#datafields').html(data);
        });
    });

    function addToScore()
    {
        var formData = $('#new-score-element').serializeArray();

        $.ajax({
            url: "/{{config("citynexus.root_directory")}}/risk-score/create-element",
            type: "post",
            data: {
                _token: "{{csrf_token()}}",
                form_data: formData
            }
        })
    }
</script>

@endpush

