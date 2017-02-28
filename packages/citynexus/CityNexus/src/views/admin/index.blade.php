@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <a href="/{{config('citynexus.root_directory')}}/admin/refresh-geocoding" class="btn btn-primary">Refresh Geo Coding</a>

    <a href="/{{config('citynexus.root_directory')}}/admin/merge-aliases" class="btn btn-primary">Merge Aliases</a>
    <a href="/{{config('citynexus.root_directory')}}/admin/drop-zero-addresses" class="btn btn-primary">Drop Zero Address</a>
    <a href="/{{config('citynexus.root_directory')}}/admin/drop-no-data" class="btn btn-primary">Drop No Data</a>
    <form action="/{{config('citynexus.root_directory')}}/admin/edit-table">
        {{csrf_field()}}
        <select name="table_name" id="table_name" class="form-control">
            <option value="">Select One</option>
            @foreach($tables as $i)
                <option value="{{$i->table_name}}">{{$i->table_name}}</option>
            @endforeach
        </select>
        <input type="submit" class="btn btn-primary" value="Submit">
    </form>


    <a href="/{{config('citynexus.root_directory')}}/admin/merge-properties" class="btn btn-primary">Merge Properties</a>
    <br>
    <br>

    <div class="list-group">
        @foreach(\CityNexus\CityNexus\Table::whereNotNull('table_name')->get() as $i)
            <div class="list-group-item">{{$i->table_title}} {{DB::table($i->table_name)->whereNull('property_id')->count()}} out of {{DB::table($i->table_name)->count()}} uncoded. <a href="{{action('\CityNexus\CityNexus\Http\TablerController@getShowTable', [$i->table_name])}}" class="btn btn-primary btn-sm">View Table</a></div>
        @endforeach
    </div>

    <br>
    <br>
    <a href="{{action('\CityNexus\CityNexus\Http\AdminController@getLowerCaseEmails')}}">Make all user emails lowercase.</a>

@stop