@extends(config('citynexus.template'))

@section(config('citynexus.section'))
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                Create New Schema
            </div>
            <div class="panel-body">
                <form action="/{{config('citynexus.tabler_root')}}/update-table/{{$table->id}}" method="post">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-sm-12">
                            <label for="table_title">Table Title</label>
                            <input type="text" name="table_title" class="form-control" value="{{$table->table_title}}"required>
                            <label for="description">Table Description</label>
                            <textarea name="description" id="description" cols="30" rows="3" class="form-control">{{$table->description}}</textarea>
                        </div>
                    </div>
                    <br><br>
                    <label for="">Table Elements</label>
                    <table class="table" id="table">
                        <thead>
                        <td>Key</td>
                        <td>Ignore</td>
                        <td>Visible</td>
                        <td>Field Name</td>
                        <td>Field Type</td>
                        <td>Sync</td>
                        <td>Push</td>
                        </thead>
                        <tbody>
                            @foreach($scheme as $key => $item)
                                @include('citynexus::tabler._edit_item')
                            @endforeach
                        </tbody>
                    </table>
                    <input type="submit" class="btn btn-primary" value="Update Table">
                </form>
            </div>
        </div>
    </div>

@stop