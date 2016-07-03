<?php
$pagename = 'Task Details';
$section = 'tasks';
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
                
                
               <h3>{{$task->task}}</h3>
                @if($task->property != null)
                    @foreach($task->property as $property)
                    <a href="{{action('\CityNexus\CityNexus\Http\PropertyController@getShow', [$property->id])}}">{{$property->full_address}}</a>
                    @endforeach
                @endif
                @if($task->due_at != null)
                    <p><b>Due: {{$task->due_at->diffForHumans()}}</b></p>
                @endif
                <p>{{$task->description}}</p>
                <a class="btn btn-success" href="{{action('\CityNexus\CityNexus\Http\TaskController@getMarkComplete',  [$task->id])}}">Mark Complete</a>
              
               
            </div>
        </div>
        @stop

        @push('js_footer')


        @endpush