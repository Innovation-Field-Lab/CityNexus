@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <a href="/{{config('citynexus.root_directory')}}/admin/refresh-geocoding" class="btn btn-primary">Refresh Geo Coding</a>

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
        @foreach(\CityNexus\CityNexus\Table::whereNull('raw_upload')->get() as $i)
            {{$i->table_title}} {{DB::table($i->table_name)->whereNull('property_id')->count()}} out of {{DB::table($i->table_name)->count()}} un coded.
        @endforeach
    </div>




@stop