@if($property->images->count() > 0)
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="panel-title">Images</span>
    </div>
    <div class="panel-body">
        <div class="list-group">
            @foreach($property->images as $image)
                <div class="list-group-item" onclick="showImage({{$image->id}})" style="cursor: pointer"><i class="fa fa-image"></i> {{$image->caption}} ({{$image->created_at->diffForHumans()}})</div>
            @endforeach
        </div>
    </div>
</div>
@endif

@push('js_footer')


<script>
    function addImage()
    {
        var title = 'Add Image';
        var uploader = "<form action='{{action('\CityNexus\CityNexus\Http\ImageController@postUpload')}}' method='post' enctype='multipart/form-data'>'" +
                '{!! csrf_field() !!}' +
                "<input type='hidden' name='property_id' value='{{$property->id}}'>" +
                "<input type='file' name='image'>" +
                "<label for='caption'>Caption</label>" +
                "<input class='form-control' type='text' name='caption' required>" +
                "<label for='description'>Description</label>" +
                "<textarea class='form-control' name='description'></textarea>" +
                "<br><br><input class='btn btn-primary' type='submit' value='Upload Image'>";
        triggerModal(title, uploader);
    }

    function showImage(id)
    {
        $.ajax({
            url: '{{action('\CityNexus\CityNexus\Http\ImageController@getShow')}}/' + id,
        }).success(function(data){
            var image = '<a href="' + data.source + '" target="_blank"><img style="max-width: 90%" class="model_image" src="' + data.source + '"/></a>'+
                    @can('citynexus', ['property', 'delete'])
                    '<br><a class="pull-right" href="/citynexus/image/delete/' + id + '">' +
                    '<i class="fa fa-trash"></i> </a>' +
                    @endcan
                '<p>' + data.description + '</p>';
            triggerModal(data.caption, image);

        });
    }

</script>


@endpush
