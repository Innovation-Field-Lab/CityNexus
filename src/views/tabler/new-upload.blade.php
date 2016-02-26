@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <b>{{$table->table_title}}</b> Upload
            </div>
            <div class="panel-body">
                <form action="/{{config('citynexus.tabler_root')}}/new-upload/" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="alert alert-info">
                        Please upload a csv file with the following titles in the first row.<br>

                        @foreach(json_decode($table->scheme) as $i)
                            <div class="label label-default">{{$i->key}}</div>
                        @endforeach
                    </div>
                    <input type="file" name="file">
                    <input type="submit" value="Upload" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
@stop