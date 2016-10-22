@if($property->files->count() > 0)
<div class="panel panel-default">
    <div class="panel-heading">
        <span class="panel-title">Files</span>
    </div>
    <div class="panel-body">
        <div class="list-group">
            @foreach($property->files as $file)

                    <?php
                        if($file->current != null)
                            {
                                $type = $file->current->type;
                            }
                    ?>
                    @if($type)
                        <div class="list-group-item" @if(substr($type, 0, 6) == 'image/') onclick="showImage({{$file->id}})" @else onclick="downloadFile({{$file->id}})" @endif style="cursor: pointer">
                        @if(
                        $type == 'application/pdf' ||
                        $type == 'application/x-pdf'
                        )
                            <i class="fa fa-file-pdf-o"> </i>
                        @elseif(substr($type, 0, 6) == 'image/')
                            <i class="fa fa-image-o"> </i>
                        @elseif($type == 'application/msword')
                            <i class="fa fa-file-word-o"> </i>
                        @elseif($type == 'application/mspowerpoint')
                            <i class="fa fa-file-powerpoint-o"> </i>
                        @elseif($type == 'application/msexcel')
                            <i class="fa fa-file-excel-o"> </i>
                        @else
                        <i class="fa fa-file"></i>
                        @endif
                        {{$file->caption}} ({{$file->updated_at->diffForHumans()}})</div>
                    @endif
            @endforeach
        </div>
    </div>
</div>
@endif

@push('js_footer')

<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.5.7/jquery.fileupload.js"></script>


<?php

$s3FormDetails = getS3Details(env('S3_BUCKET'), env('S3_REGION'));


// Get all the necessary details to directly upload a private file to S3
// asynchronously with JavaScript using the Signature V4.
//
// param string $s3Bucket your bucket's name on s3.
// param string $region   the bucket's location/region, see here for details: http://amzn.to/1FtPG6r
// param string $acl      the visibility/permissions of your file, see details: http://amzn.to/18s9Gv7
//
// return array ['url', 'inputs'] the forms url to s3 and any inputs the form will need.
//

function getS3Details($s3Bucket, $region, $acl = 'private') {

// Options and Settings
    $awsKey = (!empty(getenv('S3_KEY')) ? getenv('S3_KEY') : S3_KEY);
    $awsSecret = (!empty(getenv('S3_SECRET')) ? getenv('S3_SECRET') : S3_SECRET);

    $algorithm = "AWS4-HMAC-SHA256";
    $service = "s3";
    $date = gmdate("Ymd\THis\Z");
    $shortDate = gmdate("Ymd");
    $requestType = "aws4_request";
    $expires = "86400"; // 24 Hours
    $successStatus = "201";
    $url = "//{$s3Bucket}.{$service}-{$region}.amazonaws.com";

// Step 1: Generate the Scope
    $scope = [
            $awsKey,
            $shortDate,
            $region,
            $service,
            $requestType
    ];
    $credentials = implode('/', $scope);

// Step 2: Making a Base64 Policy
    $policy = [
            'expiration' => gmdate('Y-m-d\TG:i:s\Z', strtotime('+2 hours')),
            'conditions' => [
                    ['bucket' => $s3Bucket],
                    ['acl' => $acl],
                    ['starts-with', '$key', ''],
                    ['starts-with', '$Content-Type', ''],
                    ['success_action_status' => $successStatus],
                    ['x-amz-credential' => $credentials],
                    ['x-amz-algorithm' => $algorithm],
                    ['x-amz-date' => $date],
                    ['x-amz-expires' => $expires],
            ]
    ];
    $base64Policy = base64_encode(json_encode($policy));

// Step 3: Signing your Request (Making a Signature)
    $dateKey = hash_hmac('sha256', $shortDate, 'AWS4' . $awsSecret, true);
    $dateRegionKey = hash_hmac('sha256', $region, $dateKey, true);
    $dateRegionServiceKey = hash_hmac('sha256', $service, $dateRegionKey, true);
    $signingKey = hash_hmac('sha256', $requestType, $dateRegionServiceKey, true);

    $signature = hash_hmac('sha256', $base64Policy, $signingKey);

// Step 4: Build form inputs
// This is the data that will get sent with the form to S3
    $inputs = [
            'Content-Type' => '',
            'acl' => $acl,
            'success_action_status' => $successStatus,
            'policy' => $base64Policy,
            'X-amz-credential' => $credentials,
            'X-amz-algorithm' => $algorithm,
            'X-amz-date' => $date,
            'X-amz-expires' => $expires,
            'X-amz-signature' => $signature
    ];

    return compact('url', 'inputs');
}

?>

<script>
    function addFile()
    {
        $.ajax({
            url: "{{action('\CityNexus\CityNexus\Http\FileController@getUploader')}}?property_id={{$property->id}}",
        }).success(function(data){
            var title = 'File Upload';
            triggerModal(title, data);

                // Assigned to variable for later use.
                var form = $('.direct-upload');
                var filesUploaded = [];

                // Place any uploads within the descending folders
                // so ['test1', 'test2'] would become /test1/test2/filename
                var folders = [];

                var size;
                var type;

                form.fileupload({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    datatype: 'xml',
                    add: function (event, data) {

                        // Show warning message if your leaving the page during an upload.
                        window.onbeforeunload = function () {
                            return 'You have unsaved changes.';
                        };

                        // Give the file which is being uploaded it's current content-type (It doesn't retain it otherwise)
                        // and give it a unique name (so it won't overwrite anything already on s3).
                        var file = data.files[0];
                        var filename = Date.now() + '.' + file.name.split('.').pop();
                        form.find('input[name="Content-Type"]').val(file.type);
                        form.find('input[name="key"]').val((folders.length ? folders.join('/') + '/' : '') + filename);

                        size = file.size;
                        type = file.type;

                        // Actually submit to form to S3.
                        data.submit();

                        // Show the progress bar
                        // Uses the file size as a unique identifier
                        var bar = $('<div class="progress" data-mod="' + file.size + '"><div class="bar"></div></div>');
                        $('.progress-bar-area').append(bar);
                        bar.slideDown('fast');
                    },
                    progress: function (e, data) {
                        // This is what makes everything really cool, thanks to that callback
                        // you can now update the progress bar based on the upload progress.
                        var percent = Math.round((data.loaded / data.total) * 100);
                        $('.progress[data-mod="' + data.files[0].size + '"] .bar').css('width', percent + '%').html(percent + '%');
                    },
                    fail: function (e, data) {
                        // Remove the 'unsaved changes' message.
                        window.onbeforeunload = null;
                        $('.progress[data-mod="' + data.files[0].size + '"] .bar').css('width', '100%').addClass('red').html('');
                    },
                    done: function (event, data) {
                        window.onbeforeunload = null;

                        // Upload Complete, show information about the upload in a textarea
                        // from here you can do what you want as the file is on S3
                        // e.g. save reference to your server using another ajax call or log it, etc.
                        var original = data.files[0];
                        var s3Result = data.result.documentElement.children;
                        filesUploaded.push(s3Result[0].innerHTML);

                        $('#source').val(filesUploaded);
                        $('#size').val(size);
                        $('#type').val(type);



                        $('#file_submit').removeClass('btn-default disabled');
                        $('#file_submit').addClass('btn-success');
                    }
                });
        })
    }

    function showImage(id)
    {
        $.ajax({
            url: '{{action('\CityNexus\CityNexus\Http\FileController@getShow')}}/' + id,
        }).success(function(data){
            var file = '<a href="' + data.source + '" target="_blank"><img style="max-width: 90%" class="model_file" src="' + data.source + '"/></a>'+
                    @can('citynexus', ['property', 'delete'])
                    '<br><a class="pull-right" href="/citynexus/file/delete/' + id + '">' +
                    '<i class="fa fa-trash"></i> </a>' +
                    @endcan
                '<p>' + data.description + '</p>';
            triggerModal(data.caption, file);

        });
    }
    
    function downloadFile(id) {
        window.open("{{action('\CityNexus\CityNexus\Http\FileController@getDownload')}}/" + id);
    }

</script>


@endpush