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
                        <div class="col-sm-8">
                            <label for="table_title">Table Title</label>
                            <input type="text" name="table_title" class="form-control" value="{{$table->table_title}}"required>
                            <label for="description">Table Description</label>
                            <textarea name="description" id="description" cols="30" rows="3" class="form-control">{{$table->description}}</textarea>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="timestamp" class="control-label col-sm-4">Time Stamp</label>

                                <div class="col-sm-8">
                                    <select name="settings[timestamp]" class="form-control" id="timestamp">
                                        <option value="">Use Today's Date</option>
                                        @foreach($table as $key => $item)
                                            <option value="{{$key}} @if(isset($settings->timestamp) && $settings->timestamp == $key) selected @endif">{{$key}}</option>
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
                                            <option value="{{$key}}" @if(isset($settings->unique_id) && $settings->unique_id == $key) selected @endif>{{$key}}</option>
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
                                            <option value="{{$key}}" @if(isset($settings->property_id) && $settings->property_id == $key) selected @endif >{{$key}}</option>
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
                        <td>Key</td>
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