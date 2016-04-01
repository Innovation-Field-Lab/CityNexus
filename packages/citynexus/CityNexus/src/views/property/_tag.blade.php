<div class="list-group-item" id="tag-{{$tag->id}}">
    {{$tag->tag}}
<span class="pull-right">
    <div class="btn btn-xs" onclick="confirmDelete({{$tag->id}})" id="delete-tag-{{$tag->id}}">
        <i class="glyphicon glyphicon-trash"></i>
    </div>
</span>
</div>
