<?php
$pagename = 'Create Dataset';
$section = 'dataset';
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <form action="{{action('\CityNexus\CityNexus\Http\DatasetController@postCreateUploader')}}" method="post" class="form-horizontal">
                    {!! csrf_field() !!}
                    <input type="hidden" name="dataset_id" value="{{$dataset_id}}">

                    <div class="form-group">
                        <label for="name" class="control-label col-sm-4">Uploader Name</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="name" name="name" value="{{old('name')}}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="control-label col-sm-4">Uploader Type</label>

                        <div class="col-sm-8">
                            <select name="type" id="upload_type" class="form-control" onchange="getForm()">
                                <option value="">Select One</option>
                                <option value="csv">CSV Upload</option>
                                <option value="dropbox">Dropbox</option>
                            </select>
                        </div>
                    </div>
                    <div id="upload_form">

                    </div>
                </form>
            </div>
        </div>
    </div>

@stop

@push('js_footer')

<script>
    function getForm()
    {
        var type = $('#upload_type').val();
        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\DatasetController@getUploaderTypeForm')}}/" + type
        }).success(function(data){
            $("#upload_form").html(data);
        });
    }
</script>

@endpush

@push('style')


@endpush