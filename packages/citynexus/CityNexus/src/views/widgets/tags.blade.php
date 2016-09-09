@include('citynexus::widgets.widget_top')

<?php
if(isset($widget->setting->tag_id))
{
    if(\CityNexus\CityNexus\Tag::find($widget->setting->tag_id))
        {
            $tags = \CityNexus\CityNexus\Tag::find($widget->setting->tag_id)->properties->take(20);
        }

}
?>
    @if(isset($tags))
            <a href="{{action('\CityNexus\CityNexus\Http\TagController@getList', [$widget->setting->tag_id])}}"><div class="label label-default">{{\CityNexus\CityNexus\Tag::find($widget->setting->tag_id)->tag}}</div></a>
            @foreach($tags as $i)
                <a href="{{action('\CityNexus\CityNexus\Http\PropertyController@getShow', [$i->id])}}">
                    <div class="inbox-item">
                        <p class="inbox-item-author">{{ucwords($i->full_address)}}</p>
                        <p class="inbox-item-text">Tagged by {{\App\User::find($i->pivot->created_by)->fullName()}}</p>
                        <p class="inbox-item-date">{{$i->pivot->created_at->diffForHumans()}}</p>
                    </div>
                </a>
            @endforeach
    @endif

@include('citynexus::widgets.widget_bottom')