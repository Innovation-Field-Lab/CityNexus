<h4>Choose File to Upload</h4>
    <table class="table table-striped">
        @foreach($items as $i)
            @if(isset($i->size) && $i->size != null)
            <tr>
                <td>
                    {{$i->name}}
                </td>
                <td>
                    {{date('F j, Y h:m', strtotime($i->client_modified))}}
                </td>
                <td>
                    <div class="btn btn-primary btn-sm" onclick="processUpload('{{$i->path_display}}')" >Select</div>
                </td>
            </tr>
            @endif
        @endforeach
    </table>
