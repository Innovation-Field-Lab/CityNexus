<?php $id = str_random(); ?>

<div class="list-group-item" id="{{$id}}">
    <span class="glyphicon glyphicon-trash pull-right" style="color: darkred; cursor: pointer" onclick="removeFromScore('{{$id}}')"></span>

    <input type="hidden" name="elements[]" value="{{json_encode($element)}}">

    @if(isset($element['table_id']))

        @if($element['function'] == 'func')
            Add to score the <div class="label label-default">{{\CityNexus\CityNexus\Score::find($element['table_id'])->name}} score</div>
            {{$element['func']}}
            <div class="label label-default">{{$element['factor']}}</div>
        @elseif($element['function'] = 'range')
            If <div class="label label-default">{{\CityNexus\CityNexus\Score::find($element['table_id'])->name}} score</div> is
            <div class="label label-default">{{$element['range']}} {{$element['test']}}</div>
            then add to score
            <div class="label label-default">{{$element['result']}}</div>

        @endif

    @else
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
        then add to score
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
    @endif

</div>
