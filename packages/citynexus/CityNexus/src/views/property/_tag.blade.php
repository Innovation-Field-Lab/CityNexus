<div class="list-group-item" id="tag-{{$tag->id}}"
     data-toggle="tooltip" data-placement="left" title="Tagged: {{date_format($tag->pivot->created_at,"m/d/Y")}} @if(null != $tag->pivot->created_by)
        Tagged By: {{\App\User::find($tag->pivot->created_by)->fullname()}} @endif">
    <i class="glyphicon glyphicon-tag"></i>
    {{$tag->tag}}
<span class="pull-right">
    <div class="btn btn-xs" onclick="confirmDelete({{$tag->id}})" id="delete-tag-{{$tag->id}}">
        <i class="glyphicon glyphicon-trash"></i>
    </div>
</span>
</div>
