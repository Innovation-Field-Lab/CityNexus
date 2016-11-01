<div class="panel panel-default">
    <div class="panel-heading">
        Data Sets
    </div>
    <div class="panel-body">
        <div class="panel-group " id="accordion" role="tablist" aria-multiselectable="true">
            @if($apts->count() > 0)
                <div class="panel panel-default ">
                    <div class="panel-heading " role="tab" id="apartments_heading">
                        <h4 class="panel-title">
                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#apartments_detail" aria-expanded="false" aria-controls="collapseTwo">
                                Other Units at this Address
                            </a>
                        </h4>
                    </div>
                    <div id="apartments_detail" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                        <div class="panel-body">
                            <table class="table">
                                <thead>
                                <tr>
                                    <td>
                                        Unit
                                    </td>
                                    <td>
                                        Profile
                                    </td>
                                </tr>
                                </thead>
                                @foreach($apts as $apt)
                                    <tr>
                                        <td>
                                            {{$apt->unit}}
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-primary" href="{{action('\CityNexus\CityNexus\Http\PropertyController@getShow', ['id' => $apt->id])}}">Details</a>
                                        </td>
                                    </tr>
                                @endforeach

                            </table>
                        </div>
                    </div>
                </div>
            @endif
            @foreach($datasets as $key => $dataset)
                <div class="panel panel-default ">
                    <div class="panel-heading " role="tab" id="{{preg_replace('/\s+/', '_', $key)}}_heading">
                        <h4 class="panel-title">
                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#{{preg_replace('/\s+/', '_', $key)}}_detail" aria-expanded="false" aria-controls="collapseTwo">
                                {{$tables->find($key)->table_title}} ({{ count($dataset) }})
                            </a>
                        </h4>
                    </div>
                    <div id="{{preg_replace('/\s+/', '_', $key)}}_detail" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                        @include('citynexus::property._data_panel')
                    </div>
                </div>
            @endforeach


        </div>
    </div>
</div>