<?php
$pagename = 'Assign Alias Records';
$section = 'properties';
?>


@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="col-sm-12">
            <div class="card-box">
                {{--<div class="dropdown pull-right">--}}
                {{--<a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">--}}
                {{--<i class="zmdi zmdi-more-vert"></i>--}}
                {{--</a>--}}
                {{--<ul class="dropdown-menu" role="menu">--}}
                {{--<li><a href="#">Action</a></li>--}}
                {{--<li><a href="#">Another action</a></li>--}}
                {{--<li><a href="#">Something else here</a></li>--}}
                {{--<li class="divider"></li>--}}
                {{--<li><a href="#">Separated link</a></li>--}}
                {{--</ul>--}}
                {{--</div>--}}
            <h3>Parent Record: {{ucwords($property->full_address)}}</h3>

            <div class="panel panel-default">
                <div class="panel-heading">
                    Search for other records
                </div>
                <div class="panel-body">
                    <div class="form-inline">
                        <input type="text" class="form-control" placeholder="Search property..." id="merge_search">
                        <button class="btn btn-primary" onclick="search()">Search</button>
                    </div>
                </div>
                <form action="{{action('\CityNexus\CityNexus\Http\TablerController@postMergeRecords')}}" method="post">
                    {{csrf_field()}}
                    <input type="hidden" name="p_id" value="{{$property->id}}">
                <div class="panel-body hidden" id="results">
                    <input type="submit" class="btn btn-primary" value="Merge Records">
                    <br><br>

                    <div class="list-group" id="search-results">
                    </div>
                    <br><br>
                    <input type="submit" class="btn btn-primary" value="Merge Records">
                </div>
            </form>
            </div>
        </div>
    </div>
@stop

@push('js_footer')

<script>
    function search()
    {
        var search = $('#merge_search').val();
        var id = "{{$property->id}}";

        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\TablerController@postMergeSearch')}}",
            type: "post",
            data: {
                _token: "{{csrf_token()}}",
                id: id,
                search: search
            }
        }).success(function(data){
            $('#results').removeClass('hidden');
            $('#search-results').html(data);
        })
    }
</script>

@endpush