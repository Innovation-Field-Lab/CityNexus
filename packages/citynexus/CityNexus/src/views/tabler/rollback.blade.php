@extends(config('citynexus.template'))

@section(config('citynexus.section'))
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                Create New Scheme
            </div>
            <div class="panel-body">
                    {{csrf_field()}}
                    <br><br>
                    <label for="">Dataset Uploads</label>
                    <table class="table" id="table">
                        <thead>
                        <td>Date</td>
                        <td>Note</td>
                        <td>Records</td>
                        <td></td>
                        </thead>
                        <tbody>
                            @foreach($table->uploads as $item)
                                @include('citynexus::tabler._rollback_item')
                            @endforeach
                        </tbody>
                    </table>
            </div>
        </div>
    </div>

@stop