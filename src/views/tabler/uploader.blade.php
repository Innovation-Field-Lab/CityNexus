@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                Create Data Set From Upload
            </div>
            <div class="panel-body">
                <form action="/{{config('citynexus.tabler_root')}}/uploader" method="post" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="alert alert-info">
                        Please upload a csv file with field titles in the first row.
                    </div>
                    <input type="file" name="file">
                    <input type="submit" value="Upload" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>

@stop