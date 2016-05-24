@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                Create New Schema
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
                                    <select name="settings[timestamp]" class="form-control" id="timestamp">
                                        <option value="">Use Today's Date</option>
                                        @foreach($table as $key => $item)
                                            <option value="{{$key}}">{{$key}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <label for="timestamp" class="control-label col-sm-4">Unique ID</label>

                                <div class="col-sm-8">
                                    <select name="settings[unique_id]" class="form-control" id="unique_id">
                                        <option value="">None</option>
                                        @foreach($table as $key => $item)
                                            <option value="{{$key}}">{{$key}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <br><br>
                            <div class="form-group">
                                <label for="timestamp" class="control-label col-sm-4">Property ID</label>

                                <div class="col-sm-8">
                                    <select name="settings[property_id]" class="form-control" id="property_id">
                                        <option value="">None</option>
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
                        <td>Field Name
                            <i class="ti-help" style="cursor: pointer" onclick="getHelp('tabler.uploader.fieldname')" ></i>
                        </td>
                        <td>Field Type</td>
                        <td>Sync
                            <i class="ti-help" style="cursor: pointer" onclick="getHelp('tabler.uploader.sync')" ></i>
                        </td>
                        <td>Push</td>
                        </thead>
                        <tbody>
                            @foreach($table as $key => $item)
                                @include('citynexus::tabler._scheme_item')
                                @endforeach
                        </tbody>
                    </table>
                    <div class="form-group alert alert-info" id="syncCheck">
                        <label for="sync_field" class="control-label col-sm-4">Please verify your sync fields</label>
                        <div class='btn btn-primary' onclick="enableSubmit()">I did!</div>
                    </div>
                    <input type="submit" class="btn btn-primary hidden" id="submit" value="Save and Complete Upload">
                </form>
            </div>
        </div>
    </div>

@stop

@push('js_footer')

<script>
    function enableSubmit()
    {
        $('#syncCheck').addClass('hidden');
        $('#submit').removeClass('hidden');

    }

    $('#timestamp').change( function() {
        var id = $('#timestamp').val();
        $('#type-' + id).val('datetime').addClass('disable');

    });
</script>

@stop