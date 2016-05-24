@extends('app')

@section('content')

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                All Current Property Data
            </div>
            <div class="panel-body">
                <table class="table" id="properties-table">
                    <thead>
                        <td>Risk Score</td>
                        <td>Location</td>
                        <td>Police</td>
                        <td>Fire</td>
                    <td></td>
                    </thead>
                    <tbody>
                        @foreach($data as $property)
                        <tr>
                            <td>
                                {{$property->currentScore()}}
                            </td>
                            <td>
                                {{ucwords($property->full_address)}}
                            </td>
                            <td>
                                {{$property->policeScore()}}
                            </td>
                            <td>
                                {{$property->fireScore()}}
                            </td>
                            <td><a class="btn btn-xs btn-primary" href="{{action('\CityNexus\CityNexus\Http\CitynexusController@getProperty')}}/{{$property->id}}">View</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @stop

@push('javascript')
    <script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function()
                {
                    $('#properties-table').DataTable();
                }
        );
    </script>

    @endpush

@push('style')
<link href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css" rel="stylesheet">

@endpush