<div class="list-group-item" id="note-{{$note->id}}">
    @if($note->creator->id == Auth::getUser()->id)<div style="color: red; cursor: pointer" class="glyphicon glyphicon-trash pull-right" onclick="deleteNote({{$note->id}})"></div>@endif
    <small>{{$note->creator->fullname()}} - {{$note->created_at->diffForHumans()}}</small></P>
    <p>{{$note->note}}</p>
</div>