@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="card-box">
        <div class="panel-body">
            <div class="col-sm-8">
                <h3>{{$owner}}</h3>
                <div class="panel-group" role="tablist">
                    @foreach($properties as $property)
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="collapseListGroupHeading1"><a class="btn btn-primary" href="{{action('\CityNexus\CityNexus\Http\PropertyController@getShow', [$property->id])}}">Profile</a><h4 class="panel-title"><a
                                        href="#property-{{$property->id}}" class="" role="button" data-toggle="collapse"
                                        aria-expanded="true" aria-controls="collapseListGroup1"> {{$property->address()}}   @if(isset($pscores[$property->id])) <span class="fa fa-star"></span> @endif
                                </a></h4></div>
                        <div class="panel-collapse collapse" role="tabpanel" id="property-{{$property->id}}"
                             aria-labelledby="property-{{$property->id}}" aria-expanded="true">
                            <ul class="list-group">
                                @if(isset($pscores[$property->id]))
                                    @foreach($pscores[$property->id] as $key => $score)
                                    <li class="list-group-item"><b>{{\CityNexus\CityNexus\Score::find($key)->name}}:</b> {{$score}}</li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    </div>

@stop


@push('style')

<style>
    .dataset {
        overflow: auto;
        overflow-y: hidden;
    }

    .typeahead,
    .tt-query,
    .tt-hint {
        width: 100%;
        padding: 8px 8px;
        border: 2px solid #ccc;
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
        outline: none;
    }

    .typeahead {
        background-color: #fff;
    }

    .typeahead:focus {
        border: 2px solid #0097cf;
    }

    .tt-query {
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
    }

    .tt-hint {
        color: #999
    }

    .tt-menu {
        width: 100px;
        margin: 12px 0;
        background-color: #fff;
        border: 1px solid #ccc;
        border: 1px solid rgba(0, 0, 0, 0.2);
        -webkit-border-radius: 8px;
        -moz-border-radius: 8px;
        border-radius: 8px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, .2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, .2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, .2);
    }

    .tt-suggestion {
        padding: 3px 20px;

    }

    .tt-suggestion:hover {
        cursor: pointer;
        color: #fff;
        background-color: #0097cf;
    }

    .tt-suggestion.tt-cursor {
        color: #fff;
        background-color: #0097cf;

    }

    .tt-suggestion p {
        margin: 0;
    }

    .gist {
        font-size: 14px;
    }

</style>

@endpush

