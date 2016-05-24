<?php
$pagename = 'All Property Tags';
$section = 'properties';
?>


@extends(config('citynexus.template'))

@section(config('citynexus.section'))
    <div class="portlet">
        {{--<div class="portlet-heading portlet-default">--}}
            {{--<div class="portlet-widgets">--}}
                {{--<a href="javascript:;" data-toggle="reload"><i class="zmdi zmdi-refresh"></i></a>--}}
                {{--<a data-toggle="collapse" data-parent="#accordion1" href="#bg-primary"><i class="zmdi zmdi-minus"></i></a>--}}
                {{--<a href="#" data-toggle="remove"><i class="zmdi zmdi-close"></i></a>--}}
            {{--</div>--}}
            {{--<div class="clearfix"></div>--}}
        {{--</div>--}}

        <div class="portlet-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Tag Name</th>
                    <th>Count</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tags as $tag)
                    @if($tag->properties->count() > 0)
                    <tr>
                        <th>{{$tag->tag}}</th>
                        <th>{{$tag->properties->count()}}</th>
                        <td>
                            {{--<a class="btn btn-sm btn-primary" href="/{{config('citynexus.root_directory')}}/tags/heat-map/{{$tag->id}}">Heat Map</a>--}}
                            <a class="btn btn-sm btn-primary" href="/{{config('citynexus.root_directory')}}/tags/pin-map/{{$tag->id}}">Pin Map</a>
                            <a class="btn btn-sm btn-primary" href="{{action('\CityNexus\CityNexus\Http\TagController@getList', ['id' => $tag->id])}}">List</a>
                        </td>
                    </tr>
                    @endif
                @endforeach
            </table>
        </div>
    </div>
@stop

@push('js_footer')


@endpush
