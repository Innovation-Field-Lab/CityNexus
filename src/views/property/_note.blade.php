<div class="list-group-item">
    <P><small>{{$note->creator->fullName()}} - {{$note->created_at->diffForHumans()}}</small></P>
    <p>{{$note->note}}</p>
</div>