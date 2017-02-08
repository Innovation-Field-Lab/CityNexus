<?php
$pagename = 'Import Custom Score';
$section = 'scores';
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="col-sm-offset-2 col-sm-8">
            <div class="card-box table-responsive">
                <form class="form" action="{{action('\CityNexus\CityNexus\Http\RiskScoreController@postUpload')}}" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    @if(isset($_GET['score_id']))
                    <input type="hidden" name="score_id" value="{{$_GET['score_id']}}">
                    @endif
                    <div class="alert alert-info col-sm-12">
                        In order to upload a custom score, CityNexus will expect two column in your CSV or Excel worksheet:
                        "property_id", "score". Only files with both fields will be accepted and only records which include
                        both a property_id and score will be saved. Any additional columns will be ignored.
                    </div>
                    <div class="form-group">
                        <label for="name">Score Name</label>
                        <input type="text" class="form-control" name="name" @if(isset($_GET['score_id'])) value="{{\CityNexus\CityNexus\Score::find($_GET['score_id'])->name}}" @endif>
                    </div>
                    <input type="file" name="file">
                    <br>
                    <input type="submit" value="Upload" id="upload" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>

@stop

@push('js_footer')

@endpush

@push('style')


@endpush