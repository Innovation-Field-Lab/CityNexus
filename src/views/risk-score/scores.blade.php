<input type="hidden" name="scope" value="score">
<label>Select Data Field</label>
    <select name="table_id" class="form-control" id="datafield">
        <option value="">[Select Score]</option>
        @foreach($scores as $i)
            <option value="{{$i->id}}">{{$i->name}}</option>
        @endforeach
    </select>

<br>
<div class="form-group">
    <label for="function" class="control-label">Score Type</label>
    <select name="function" class="form-control" id="function">
        <option value="">[Select One]</option>
        <option value="func">Function</option>
        <option value="range">Range</option>
    </select>
</div>

<div id="func" class="function-settings hidden">
    <div class="form-group">
        <label for="func" class="control-label">Calculation Type</label>
            <select name="func" class="form-control">
                <option value="/">&divide;</option>
                <option value="*">x</option>
                <option value="+">+</option>
                <option value="-">-</option>
            </select>
    </div>
    <div class="form-group">
        <label for="factor" class="control-label col-sm-3">Factor</label>

        <div class="col-sm-9">
            <input type="number" class="form-control" name="factor">
        </div>
    </div>
    </br>
    <div class="form-group">
        <button class="btn btn-primary" onclick="addToScore()">Add to Score</button>
    </div>
</div>

<section id="range" class="function-settings hidden">
    <div class="form-group">
        <label for="range" class="control-label">Range Type</label>
        <select name="range" class="form-control">
            <option value=">">If value is more than</option>
            <option value="<">If value is less than</option>
            <option value="=">If value is equal to</option>
        </select>
        <input type="number" class="form-control" name="test">
    </div>
    <div class="form-group">
        <label for="score" class="control-label">Add to the score</label>
         <input type="number" class="form-control" name="result">
    </div>
    </br>
    <div class="form-group">
        <button class="btn btn-primary" onclick="addToScore()">Add to Score</button>
    </div>

</section>

<script>
    $('#function').change(function(){
        var key = $('#function').val();
        $('.function-settings').addClass('hidden');
        $('#' + key).removeClass('hidden');
    })
</script>