<input type="hidden" name="scope" value="tag">
<label>Select Tag</label>
    <select name="tag_id" class="form-control" id="datafield">
        <option value="">[Select Tag]</option>
        @foreach($tags as $i)
            <option value="{{$i->id}}">{{$i->tag}}</option>
        @endforeach
    </select>

<br>

<div class="form-group">
    <label for="score_type">
        Score Type
    </label>
    <select name="score_type" id="score_type" class="form-control">
        <option value="">Select type</option>
        <option value="add">Add To Score</option>
        <option value="subtract">Subtract From Score</option>
        <option value="ignore">Ignore Tagged Properties</option>
    </select>
</div>

<div class="form-group hidden" id="factor">
    <label for="factor" id="factor-label">
    </label>
    <input type="number" class="form-control" id="factor" name="factor"/>
</div>

<div class="form-group">
    <button class="btn btn-primary" onclick="addToScore()">Add to Score</button>
</div>


<script>
    $('#score_type').change(function(){
        var key = $('#score_type').val();
        if(key == 'add' || key == 'subtract')
        {
            $('#factor').removeClass('hidden');
        }
        else
        {
            $('#factor').addClass('hidden');
        }

        if(key == 'add')
        {
            $('#factor-label').html('Add to score:');
        }

        if(key == 'subtract')
        {
            $('#factor-label').html('Subtract from score:');
        }
    })
</script>