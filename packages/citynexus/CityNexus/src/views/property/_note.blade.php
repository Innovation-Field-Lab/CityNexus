<div class="list-group-item" id="note-{{$note->id}}">
    @if(\App\User::find($note->user_id) != null) @if($note->creator->id == Auth::getUser()->id)<div style="color: red; cursor: pointer" class="glyphicon glyphicon-trash pull-right" onclick="deleteNote({{$note->id}})"></div>@endif @endif
        @if(\App\User::find($note->user_id) != null)<small>{{$note->creator->fullname()}} - @endif {{$note->created_at->diffForHumans()}}</small></P>
    <p>{{$note->note}}</p>
</div>