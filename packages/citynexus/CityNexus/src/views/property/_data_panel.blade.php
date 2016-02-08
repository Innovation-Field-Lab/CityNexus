<div class="panel-body dataset">
    <table class="table table-striped">
        <thead>
            <tr>

            <?php $table = json_decode($tables->find($key)->scheme, false); ?>

                @foreach($table as $column)
                @if(isset($column->show) && $column->show == true)
                    <th>{{$column->name}}</th>
                @endif
            @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach($dataset as $row)
            <tr>
            @foreach($row as $k => $r)
                @if(isset($table->$k->show) && $table->$k->show == true)
                <td>{{$r}}</td>
                @endif
            @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
</div>