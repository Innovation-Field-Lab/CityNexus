<style>
    body {
        font-family: "Courier New", Courier, monospace;
    }
    .header {
        font-size: 16pt;
        font-weight: bold;
    }
    .address {
        font-size: 12pt;
    }
    .accessed {
        font-size: 8pt;
    }
    table{
        width: 100%;
    }
    table, th, td {
        border: 1px solid lightgray;
        padding: 5px;
        border-collapse: collapse;
        vertical-align: top;
        font-size: 10px;
    }
    th {
        font-weight: bold;
        font-size: 10px;
    }
    caption{
        font-weight: bold;
        font-size: 14px;
        padding-bottom: 10px;
    }
</style>

<div class="header">Property Report</div>
<div class="address">{{ucwords($property->full_address)}}</div>
<div class="accessed">Accessed: {{date('M j, Y')}}</div>
@if(isset($report_info['property_info']))
    <h4>Property Information</h4>
    @foreach($report_info['property_info'] as $k => $i)
        <p><b>{{$k}}: </b> {{$i}}</p>
    @endforeach
@endif

@if(isset($report_info['datasets']))
    <h3>Data Sets</h3>
    @foreach($report_info['datasets'] as $k => $i)
        @if($i != null)
        <table>
            <caption>{{\CityNexus\CityNexus\Table::where('table_name', $k)->first()->table_title}}</caption>
            <tr>

            @foreach($i[0] as $column => $row)
                    <th>
                        {{ucwords($column)}}
                    </th>
            @endforeach
            </tr>

        @foreach($i as $row)
                <tr>
                    @foreach($row as $cell)
                    <td>
                        {{$cell}}
                    </td>
                    @endforeach
                </tr>
            @endforeach
        </table>
`       @endif
    @endforeach
@endif