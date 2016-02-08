<select name="dataset" class="form-control" id="dataset">
    <option value="">Select One</option>
    <option value=""></option>
    <option value="_scores">Existing Scores</option>
    <option value=""></option>
    @foreach($datasets as $i)
        <option value="{{$i->id}}">{{$i->table_title}}</option>
    @endforeach
</select>
