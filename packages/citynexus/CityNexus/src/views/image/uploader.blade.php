<?php


// TODO Enter your bucket and region details (see details below)
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

    <!-- Direct Upload to S3 Form -->
    <form action="<?php echo $s3FormDetails['url']; ?>"
          method="POST"
          enctype="multipart/form-data"
          class="direct-upload">

        <?php foreach ($s3FormDetails['inputs'] as $name => $value) { ?>
        <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>">
        <?php } ?>

                <!-- Key is the file's name on S3 and will be filled in with JS -->
        <input type="hidden" name="key" value="">
        <input type="file" name="file" multiple>

        <!-- Progress Bars to show upload completion percentage -->
        <div class="progress-bar-area progress-bar-striped" style="height: 25px"></div>
            <textarea class="hidden" id="uploaded"></textarea>


    </form>
    <form action="{{action('\CityNexus\CityNexus\Http\ImageController@postUpload')}}" method="post">
        {!! csrf_field() !!}
        <label for="caption">Image Caption</label>
        <input type="text" class="form-control" name="caption">
        <label for="description">Description</label>
        <textarea name="description" id="description"  class="form-control" rows="5"></textarea>
        @if(isset($property_id))<input type="hidden" name="property_id" value="{{$property_id}}">@endif
        <input type="hidden" name="source" id="source">

        <br><br>
        <input type="submit" id="image_submit" class="btn btn-default disabled" value="Save Image">
    </form>
    <!-- This area will be filled with our results (mainly for debugging) -->

</div>
