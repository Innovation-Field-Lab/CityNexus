<?php
$pagename = 'All Property Tags';
$section = 'properties';
?>


@extends(config('citynexus.template'))

@section(config('citynexus.section'))
    <div class="portlet">
        {{--<div class="portlet-heading portlet-default">--}}
            {{--<div class="portlet-widgets">--}}
                {{--<a href="javascript:;" data-toggle="reload"><i class="zmdi zmdi-refresh"></i></a>--}}
                {{--<a data-toggle="collapse" data-parent="#accordion1" href="#bg-primary"><i class="zmdi zmdi-minus"></i></a>--}}
                {{--<a href="#" data-toggle="remove"><i class="zmdi zmdi-close"></i></a>--}}
            {{--</div>--}}
            {{--<div class="clearfix"></div>--}}
        {{--</div>--}}

        <div class="portlet-body">
            <table class="table table-hover">
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
                            <a class="btn btn-sm btn-primary" href="{{action('\CityNexus\CityNexus\Http\TagController@getList', ['id' => $tag->id])}}">List</a>
                            <a class="btn btn-sm btn-primary" onclick="renameTag({{$tag->id}}, '{{$tag->tag}}')"> Merge Tags</a>
                            <a class="btn btn-sm btn-warning" onclick="mergeTag({{$tag->id}})"> <i class="fa fa-code-fork"></i> Merge Tags</a>
                            <a class="btn btn-sm btn-danger" href="{{action('\CityNexus\CityNexus\Http\TagController@getDelete', ['id' => $tag->id])}}"> <i class="fa fa-trash"></i> Delete</a>

                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@stop

@push('js_footer')

<script>
    function mergeTag(id)
    {
        var form = "<form action='{{action('\CityNexus\CityNexus\Http\TagController@postMergeTags')}}' method='post'>" +
                "<input type='hidden' name='old_id' value='" + id +"'>" +
                '{!! csrf_field() !!}' +
                "<label>Select tag to merge into</label>" +
                "<select class='form-control' name='new_id'>" +
                "<option value=''>Select One</option>" +
                @foreach(\CityNexus\CityNexus\Tag::all() as $tag)
                "<option value='{{$tag->id}}' id='tagOp-{{$tag->id}}'>{{$tag->tag}}</option>" +
                @endforeach
                "</select>" +
                "<br>" +
                "<input type='submit' value='Merge' class='btn btn-primary'>" +
                "</form>";
        triggerModal('Merge Tags', form);
        $('#tagOp-' + id).remove();
    }
    function renameTag(id, name)
    {
        var form = "<form action='{{action('\CityNexus\CityNexus\Http\TagController@postRename')}}' method='post'>" +
                "<input type='hidden' name='tag_id' value='" + id +"'>" +
                '{!! csrf_field() !!}' +
                "<label>Change name of tag to:</label>" +
                "<input name='name' class='form-control' value='" + name + "'>" +
                "<br>" +
                "<input type='submit' value='Rename Tag' class='btn btn-primary'>" +
                "</form>";
        var title = 'Rename "' + name + '"';
        triggerModal(title, form);
    }
</script>
@endpush
