<div class="panel panel-default">
    <div class="panel-heading">
        <span class="panel-title">Images</span>
    </div>
    <div class="panel-body">
        <div class="list-group" id="images">
            <div class="btn btn-primary" onclick="addImage()"><i class="fa fa-plus"></i> Add Image</div>
        </div>
        <div class="list-group">
            @foreach($property->images as $image)
                <div class="list-group-item" onclick="showImage({{$image->id}})" style="cursor: pointer"><i class="fa fa-image"></i> {{$image->caption}} ({{$image->created_at->diffForHumans()}})</div>
            @endforeach
        </div>
    </div>
</div>