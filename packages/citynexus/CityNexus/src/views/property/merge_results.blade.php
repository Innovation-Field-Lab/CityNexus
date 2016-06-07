@foreach($results as $result)

    <div class="list-group-item @if($result->id == $id) disabled @endif">
        <input type="checkbox" class="@if($result->id == $id) disabled @endif" @if($result->id != $id) value="{{$result->id}}" name="alias[]"@endif />
        {{ucwords($result->full_address)}}
        <a class="btn btn-primary btn-xs pull-right" href="{{action('\CityNexus\CityNexus\Http\PropertyController@getShow', ['property_id' => $result->id])}}" target="_blank">Profile</a>
    </div>

    @endforeach