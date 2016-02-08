<div class="panel-body">
    <div class="col-sm-6">
        <table class="table table-bordered">
            <tbody>
            <tr>
                <th scope="row">Property ID</th>
                <td>{{$property->id}}</td>
            </tr>

            {{--TODO: Create preferences to choose displayed property variables --}}
            {{--<tr>--}}
                {{--<th scope="row">Occupancy</th>--}}
                {{--<td>{{$property->assessor->occupancy}}</td>--}}
            {{--</tr>--}}
            {{--<tr>--}}
                {{--<th scope="row">Style</th>--}}
                {{--<td>{{$property->assessor->style_desc}}</td>--}}
            {{--</tr>--}}
            {{--<tr>--}}
                {{--<th scope="row">Type</th>--}}
                {{--<td>{{$property->assessor->style}}</td>--}}
            {{--</tr>--}}


            </tbody>
        </table>
    </div>
    <div class="col-sm-6">
        <table class="table table-bordered">
            <tbody>
            {{--<tr>--}}
                {{--<th scope="row">Last Sale Date</th>--}}
                {{--<td>{{$property->assessor->sale_date}}</td>--}}
            {{--</tr>--}}
            {{--<tr>--}}
                {{--<th scope="row">Last Sale Price</th>--}}
                {{--<td>${{number_format($property->assessor->price, 0, '', ',')}}</td>--}}
            {{--</tr>--}}
            {{--<tr>--}}
                {{--<th scope="row">Building Area</th>--}}
                {{--<td>{{number_format($property->assessor->bldg_area_gross, 0, '', ',')}} sf</td>--}}
            {{--</tr>--}}
            {{--<tr>--}}
                {{--<th scope="row">Ah</th>--}}
                {{--<td>{{$property->assessor->ah}}</td>--}}
            {{--</tr>--}}


            </tbody>
        </table>
    </div>
</div>