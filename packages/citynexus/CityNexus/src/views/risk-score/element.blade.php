<?php $id = str_random(); ?>

<div class="list-group-item" id="{{$id}}">
    <span class="glyphicon glyphicon-trash pull-right" style="color: darkred; cursor: pointer" onclick="removeFromScore('{{$id}}')"></span>

    <input type="hidden" name="elements[]" value="{{json_encode($element)}}">

    @if(isset($element['scope']) && $element['scope'] == 'score')

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
    @elseif(isset($element['scope']) && $element['scope'] == 'tag')
        @if($element['score_type'] == 'ignore')
            Remove all scores tagged <div class="label label-default">{{ucwords(\CityNexus\CityNexus\Tag::find($element['tag_id'])->tag)}}</div>.
        @else
            For all properties currently tagged <div class="label label-default">{{ucwords(\CityNexus\CityNexus\Tag::find($element['tag_id'])->tag)}}</div>
            {{$element['score_type']}} <div class="label label-default">{{$element["factor"]}}</div> @if($element['score_type'] == 'add') to @else from @endif score.
        @endif
    @else
    @if($element['function'] == 'func')

        On table
        <div class="label label-default">{{$element['table_name']}}</div>
        add to score (
        <div class="label label-default">{{$element['key']}}</div>
        {{$element['func']}}
        <div class="label label-default">{{$element['factor']}}</div>)

    @elseif($element['function'] == 'range')
        On table
        <div class="label label-default">{{$element['table_name']}}</div>

        if <div class="label label-default">{{$element['key']}}</div>
        is
        <div class="label label-default">{{$element['range']}} {{$element['test']}}</div>
        then add to score
        <div class="label label-default">{{$element['result']}}</div>

    @else
        On table
        <div class="label label-default">{{$element['table_name']}}</div>

        if <div class="label label-default">{{$element['key']}}</div>
        is
        @if($element['function'] == 'empty')
                <div class="label label-default">Empty</div>
        @elseif($element['function'] == 'notempty')
                <div class="label label-default">Not Empty</div>
        @elseif($element['function'] == 'contains')
            <div class="label label-default">Contains: {{$element['test']}}</div>
        @elseif($element['function'] == 'contains')
            <div class="label label-default">Contains: {{$element['test']}}</div>
        @elseif($element['function'] == 'equals')
            <div class="label label-default">Equals: {{$element['test']}}</div>
        @elseif($element['function'] == 'doesntequal')
            <div class="label label-default">Doesn't Equal: {{$element['test']}}</div>
        @elseif($element['function'] == 'doesntcontain')
            <div class="label label-default">Doesn't Contain: {{$element['test']}}</div>
        @endif
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
