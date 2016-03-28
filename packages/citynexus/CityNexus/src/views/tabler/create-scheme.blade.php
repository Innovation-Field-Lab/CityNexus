@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                Create New Scheme
            </div>
            <div class="panel-body">
                <form action="/{{config('citynexus.tabler_root')}}/create-scheme/{{$table_id}}" method="post">
                    {{csrf_field()}}
                    <div class="row">
                        <div class="col-sm-8">
                            <label for="table_title">Table Title</label>
                            <input type="text" name="table_name" class="form-control" required>
                            <label for="description">Table Description</label>
                            <textarea name="description" id="description" cols="30" rows="3" class="form-control"></textarea>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="timestamp" class="control-label col-sm-4">Time Stamp</label>

                                <div class="col-sm-8">
                                    <select name="timestamp" class="form-control" id="timestamp">
                                        <option value="">Use Today's Date</option>
                                        @foreach($table as $key => $item)
                                            <option value="{{$key}}">{{$key}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br><br>
                    <label for="">Table Elements</label>
                    <table class="table" id="table">
                        <thead>
                        <td>Ignore</td>
                        <td>Visible</td>
                        <td>Field Name</td>
                        <td>Key</td>
                        <td>First Value</td>
                        <td>Field Type</td>
                        <td>Sync</td>
                        <td>Push</td>
                        </thead>
                        <tbody>
                            @foreach($table as $key => $item)
                                @include('citynexus::tabler._scheme_item')
                                @endforeach
                        </tbody>
                    </table>
                    <input type="submit" class="btn btn-primary" value="Save and Complete Upload">
                </form>
            </div>
        </div>
    </div>

@stop