<div class="panel-body">
    <table class="table table-striped">
        <thead>
            <tr>
            @foreach($dataset[0] as $k => $nothing)
                <th>{{ucwords(str_replace('_', ' ', $k))}}</th>
            @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach($dataset as $row)
            <tr>
            @foreach($row as $r)
                <td>{{$r}}</td>
            @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
</div>