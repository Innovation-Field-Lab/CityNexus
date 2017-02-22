<div class="note" id="note-{{$note->id}}">
    <div class="note-footer pull-right">
        <i class="fa fa-reply" style="cursor: pointer" onclick="replyToNote({{$note->id}}, '{{$note->creator->fullname()}}')"></i>
        @if($note->creator->id == Auth::getUser()->id | Auth::getUser()->super_admin == true)<div style="color: red; cursor: pointer" class="glyphicon glyphicon-trash " onclick="deleteNote({{$note->id}})"></div>@endif
    </div>
    <div class="note-byline" data-toggle="tooltip" data-placement="top" title="{{date_format($note->created_at,"m/d/Y")}}"
    >{{$note->creator->fullname()}} - {{$note->created_at->diffForHumans()}}</div>
    <div class="note-body">{{$note->note}}</div>

    <div class="replies @unless($note->replies->count() > 0) hidden @endunless" id="reply-notes-{{$note->id}}">
        @if($note->replies->count() > 0)
            @each('citynexus::property._note', $note->replies, 'note')
        @endif
    </div>
</div>