<div class="list-group-item">
    <input type="hidden" name="elements[]" value="{{json_encode($element)}}">

    @if($element['function'] == 'func')

        On table
        <div class="label label-default">{{$element['table_name']}}</div>
        add to score (
        <div class="label label-default">{{$element['key']}}</div>
        {{$element['func']}}
        <div class="label label-default">{{$element['factor']}}</div>)

    @elseif($element['function'] = 'range')
        On table
        <div class="label label-default">{{$element['table_name']}}</div>

        if <div class="label label-default">{{$element['key']}}</div>
        is
        <div class="label label-default">{{$element['range']}} {{$element['test']}}</div>
        then add
        <div class="label label-default">{{$element['result']}}</div>

    @endif
    <br>
    <i>For
        @if($element['scope'] == 'all')
            all records
        @elseif($element['scope'] == 'last')
            most recent record
        @endif

        @if($element['period'] != null)
            over the last {{$element['period']}} days
        @endif
    </i>

</div>
