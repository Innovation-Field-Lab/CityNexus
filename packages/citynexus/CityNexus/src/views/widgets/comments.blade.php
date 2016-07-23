
<?php
if(isset($widget->setting->filter))
{
    $filter = '%' . $widget->setting->filter . '%';
    $notes = \CityNexus\CityNexus\Note::orderBy('created_at', "DEC")->where('note', 'LIKE', $filter)->take(20)->with('creator')->with('property')->get();
}
else
{
    $notes = \CityNexus\CityNexus\Note::orderBy('created_at', "DEC")->take(20)->with('creator')->with('property')->get();

}
?>


@include('citynexus::widgets.widget_top')

    @if(isset($widget->setting->filter))
        <p>Filtered by {{ucwords($widget->setting->filter)}}</p>
    @endif
     @foreach($notes as $i)
            <a href="{{action('\CityNexus\CityNexus\Http\PropertyController@getShow', [$i->property_id])}}#note-{{$i->id}}">
                <div class="inbox-item">
                    {{--<div class="inbox-item-img"><img src="/images/users/avatar-1.jpg" class="img-circle" alt=""></div>--}}
                    <p class="inbox-item-author">{{ucwords($i->property->full_address)}}</p>
                    @if(\App\User::find($i->user_id) != null) <p class="inbox-item-text">By {{$i->creator->fullname()}}</p>@endif
                    <p class="inbox-item-text">{{substr($i->note, 0, 65)}} ...</p>
                    <p class="inbox-item-date">{{$i->created_at->diffForHumans()}}</p>
                </div>
            </a>
     @endforeach

@include('citynexus::widgets.widget_bottom')
