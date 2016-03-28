<tr>
    <td>
        {{$item->created_at->diffForHumans()}}
    </td>
    <td>
        {{$item->note}}
    </td>
    <td>
        {{\Illuminate\Support\Facades\DB::table($item->table->table_name)->where('upload_id', $item->id)->count()}}
    </td>
    <td>
        <a class="btn btn-sm btn-danger" href="/{{config('citynexus.tabler_root')}}/remove-upload/{{$item->id}}"><i class="glyphicon glyphicon-trash"></i></a>
    </td>
</tr>