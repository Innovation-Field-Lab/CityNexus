<form method="post" class="form-horizontal" action="{{action('\CityNexus\CityNexus\Http\PropertyController@postUpdate', ['id' => $property->id])}}">
    {!! csrf_field() !!}
    <div class="form-group">
        <label for="house_number" class="control-label col-sm-3">House Number</label>

        <div class="col-sm-9">
            <input type="number" class="form-control address" id="update_house_number" name="house_number"
                   value="{{$property->house_number}}" onchange="updateFullAddress()" />
        </div>
    </div>
    <div class="form-group">
        <label for="street_name" class="control-label col-sm-3">Street Name</label>

        <div class="col-sm-9">
            <input type="text" class="form-control address" id="update_street_name" name="street_name"
                   value="{{$property->street_name}}" onchange="updateFullAddress()">
        </div>
    </div>
    <div class="form-group">
        <label for="street_type" class="control-label col-sm-3">Street Type</label>

        <div class="col-sm-9">

            <select name="street_type" id="update_street_type" class="form-control address" onchange="updateFullAddress()">
                <option value="">Select One</option>
                <option value="alley" @if($property->street_type == 'alley') selected @endif>Alley</option>
                <option value="avenue" @if($property->street_type == 'aveneu') selected @endif>Avenue</option>
                <option value="boulevard" @if($property->street_type == 'boulevard') selected @endif>Boulevard</option>
                <option value="circle" @if($property->street_type == 'circle') selected @endif>Circle</option>
                <option value="court" @if($property->street_type == 'court') selected @endif>Court</option>
                <option value="drive" @if($property->street_type == 'drive') selected @endif>Drive</option>
                <option value="highway" @if($property->street_type == 'highway') selected @endif>Highway</option>
                <option value="expressway" @if($property->street_type == 'expressway') selected @endif>Express Way</option>
                <option value="lane" @if($property->street_type == 'lane') selected @endif>Lane</option>
                <option value="parkway" @if($property->street_type == 'parkway') selected @endif>Parkway</option>
                <option value="place" @if($property->street_type == 'place') selected @endif>Place</option>
                <option value="road" @if($property->street_type == 'road') selected @endif>Road</option>
                <option value="row" @if($property->street_type == 'row') selected @endif>Row</option>
                <option value="street" @if($property->street_type == 'street') selected @endif>Street</option>
                <option value="square" @if($property->street_type == 'square') selected @endif>Square</option>
                <option value="terrace" @if($property->street_type == 'terrace') selected @endif>Terrace</option>
                <option value="way" @if($property->street_type == 'way') selected @endif>Way</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="unit" class="control-label col-sm-3">Unit</label>

        <div class="col-sm-9">
            <input type="text" class="form-control address" id="update_unit" name="unit" value="{{$property->unit}}" onchange="updateFullAddress()"/>
        </div>
    </div>

    <div class="form-group">
        <label for="full_address" class="control-label col-sm-3">Full Address</label>

        <div class="col-sm-9">
            <input type="text" class="form-control" id="update_full_address_preview" name="full_address"
                   value="{{strtoupper($property->full_address)}}" disabled/>
            <input type="hidden" class="form-control" id="update_full_address" name="full_address"
                   value="{{$property->full_address}}" />
        </div>
    </div>

    <input type="submit" value="Update Property" class="btn btn-primary">
</form>

<script>
    var houseNumber = $('#update_house_number');
    var streetName = $('#update_street_name');
    var streetType = $('#update_street_type');
    var unit = $('#update_unit');
    function updateFullAddress(){
        var fullAddress = '';
        if(houseNumber.val() != '') var fullAddress = fullAddress.concat(' ' + houseNumber.val());
        if(streetName.val() != '') var fullAddress = fullAddress.concat(' ' + streetName.val());
        if(streetType.val() != '') var fullAddress = fullAddress.concat(' ' + streetType.val());
        if(unit.val() != '') var fullAddress = fullAddress.concat(' ' + unit.val());
        $('#update_full_address').val(fullAddress.toLowerCase());
        $('#update_full_address_preview').val(fullAddress.toUpperCase());
    };
</script>