<?php
$pagename = 'Search Results';
$section = 'search';
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
            @if($results->count() != 0)
                {!! $results->appends(Input::except('page'))->render() !!}

                <table class="table">
                @foreach($results as $i)
                    <tr>
                        <td>
                            {{ucwords($i->full_address)}}
                        </td>
                        <td>
                            <a class='btn btn-primary btn-sm'href="{{action('\CityNexus\CityNexus\Http\PropertyController@getShow', ['id' => $i->id])}}">View Property</a>
                        </td>

                    </tr>
                @endforeach
            </table>

                {!! $results->appends(Input::except('page'))->render() !!}

            @else
                <div class="alert alert-info">
                    No properties found!
                </div>
            @endif
        </div>
    </div>

@stop