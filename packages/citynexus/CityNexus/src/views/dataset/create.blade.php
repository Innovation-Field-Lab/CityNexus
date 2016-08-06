<?php
$pagename = 'Create Dataset';
$section = 'dataset';
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box table-responsive">
                <form action="{{action('\CityNexus\CityNexus\Http\DatasetController@postCreate')}}" method="post" class="form-horizontal">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="name" class="control-label col-sm-4">Dataset Name</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="name" name="name" value="{{old('name')}}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="control-label col-sm-4">Description</label>

                        <div class="col-sm-8">
                            <textarea type="text" class="form-control" id="description" name="description">{{old('description')}}</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label col-sm-4"></label>

                        <div class="col-sm-8">
                            <input type="submit" class="form-control" id="" name="" value="Create Dataset"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop

@push('js_footer')

@endpush

@push('style')


@endpush