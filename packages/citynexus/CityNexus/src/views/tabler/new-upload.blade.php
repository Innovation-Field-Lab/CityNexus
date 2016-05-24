@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <b>{{$table->table_title}}</b> Upload
            </div>
            <div class="panel-body">
                <form action="/{{config('citynexus.tabler_root')}}/new-upload/{{$table->id}}" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="alert alert-info">
                        Please upload a csv file with the following titles in the first row.<br>
                             @foreach(json_decode($table->scheme) as $i)
                            <div class="label label-default">{{$i->key}}</div>
                        @endforeach
                    </div>
                    <label for="note">Description of Upload</label>
                    <input type="text" name="note" id="note" class="form-control">
                    <br>
                    <input type="file" name="file" id="file">
                    <br>
                    <input type="submit" value="Upload" id="upload" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
@stop
@push('style')
<link href="/vendor/citynexus/plugins/bootstrap-sweetalert/sweet-alert.css" rel="stylesheet" type="text/css" />
@endpush
@if(!isset($settings->unique_id))
    @push('js_footer')
    <script src="/vendor/citynexus/plugins/bootstrap-sweetalert/sweet-alert.min.js"></script>
    <script>
        //Info
        $('#file').change(function () {
            swal({
                title: "No unique ID set for table",
                text: "There is no unique ID set for this table. If you add duplicate data without a unique ID set it will create multiple records within the system. Please consider setting through the table's edit menu.",
                type: "info",
                showCancelButton: false,
                confirmButtonClass: 'btn-info waves-effect waves-light',
                confirmButtonText: 'Okay'
            });
        });
    </script>

    @endpush
@endif
