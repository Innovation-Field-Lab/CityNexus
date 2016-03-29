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

    @stop