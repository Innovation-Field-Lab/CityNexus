<?php
use CityNexus\CityNexus\Tag;

$tags = Tag::all();
?>

<label>Widget Name</label>
<input class="form-control" type="test" name="name">

<label>Description</label>
<textarea class="form-control" type="test" name="description"></textarea>

<label>How many Tags?</label>
<input class="form-control" type="number" name="settings[limit]" value="20">

<label>Select Tag:</label>
<select class="form-control" name="settings[tag_id]" id="">
    <option value="">Select One</option>
    @foreach($tags as $i)
    <option value="{{$i->id}}">{{$i->tag}}</option>
    @endforeach
</select>
