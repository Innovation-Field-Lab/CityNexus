@extends(config('citynexus.template'))

@section(config('citynexus.section'))
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title">
                All Property Tags
            </div>
        </div>
        <div class="panel-body">
            <table class="table">
                <thead>
                <tr>
                    <th>Tag Name</th>
                    <th>Count</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tags as $tag)
                    <tr>
                        <th>{{$tag->tag}}</th>
                        <th>{{$tag->properties->count()}}</th>
                        <td>
                            {{--<a class="btn btn-sm btn-primary" href="/{{config('citynexus.root_directory')}}/tags/heat-map/{{$tag->id}}">Heat Map</a>--}}
                            <a class="btn btn-sm btn-primary" href="/{{config('citynexus.root_directory')}}/tags/pin-map/{{$tag->id}}">Pin Map</a>
                            {{--<a class="btn btn-sm btn-primary" href="/{{config('citynexus.root_directory')}}/tags/ranking/{{$tag->id}}">Listing</a>--}}
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@stop

@push('js_footer')


@endpush
