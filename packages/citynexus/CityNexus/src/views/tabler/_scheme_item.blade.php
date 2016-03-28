<tr>
    <td>
        <input type="checkbox" name="map[{{$key}}][skip]" @if($item->skip)checked@endif>
    </td>
    <td><input type="checkbox" name="map[{{$key}}][show]" @if($item->show)checked@endif></td>
    <td><input type="text" name="map[{{$key}}][name]" class="form-control" value="{{ucwords($key)}}"></td>
    <td>
        {{$builder->cleanName($key)}}
        <input type="hidden" name="map[{{$key}}][key]" value="{{$key}}">
    </td>
    <td><select name="map[{{$key}}][type]" class="form-control">
            <option value="string">String</option>
            <option value="integer" @if($item->type == 'integer') selected @endif>Integer</option>
            <option value="float" @if($item->type == 'float') selected @endif>Float</option>
            <option value="boolean" @if($item->type == 'boolean') selected @endif>Boolean</option>
            {{-- TODO: Datetime not working on scheme, need to make it convert when uploading --}}
            <option value="datetime" @if($item->type == 'datetime') selected @endif>Date Time</option>
        </select></td>
    <td>
        <select name="map[{{$key}}][sync]" id="" class="form-control">
                <option value=""></option>
                @foreach(config('citynexus.sync') as $k => $i)
                <option value="{{$k}}" @if($item->sync == $i)selected@endif>{{$i}}</option>
                @endforeach
        </select>
    </td>
    <td><select name="map[{{$key}}][push]" id="" class="form-control">
            <option value=""></option>
            @foreach(config('citynexus.push') as $k => $i)
                <option value="{{$k}}" @if($item->push == $i)selected@endif>{{$i}}</option>
            @endforeach
        </select></td>
</tr>
