<div class="panel-body dataset">
    <table class="table table-striped">
        <thead>
            <tr>

            <?php $table = json_decode($tables->find($key)->scheme, false); ?>

                @foreach($table as $column)
                @if(isset($column->show) && $column->show == true)
                    <th>@if(isset($column->meta) && $column->meta != null) <i onclick="viewMeta('{{$column->meta}}', '{{$column->name}}')" class="fa  fa-info-circle center" style="cursor: pointer"></i>@endif<br>{{$column->name}} </th>
                @endif
            @endforeach
            </tr>
        </thead>
        <tbody>
        @foreach($dataset as $row)
            <tr @unless($row->property_id == $property->id) class="warning" <?php $disclaimer = true; ?> @endunless>
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
@if($disclaimer)<div class="panel-footer warning">* Alias properties highlighted in light yellow</div> <?php $disclaimer = false; ?>@endif
