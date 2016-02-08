<input type="hidden" name="key" value="{{$field->key}}">

<div class="form-horizontal">
    <input type="hidden" name="type" value='integer'>
    <div class="form-group">
        <div class="col-sm-12">
            <label for="scope" class="control-label">Scope</label>
            <select class="form-control" id="scope" name="scope">
                <option value="last">Last Record</option>
                <option value="all">All Records</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-12">
            <label for="period" class="control-label">Period <small>Last how many days, leave blank for no limit.</small></label>
            <input type="number" class="form-control" id="period" name="period"
                   value="{{old('period')}}"/>
        </div>
    </div>

@if($field->type == 'integer' or $field->type == 'float')
    <br>
    <div class="form-group">
        <label for="function" class="control-label col-sm-3">Score Type</label>
            <div class="col-sm-9">
                <select name="function" class="form-control" id="function">
                    <option value="">[Select One]</option>
                    <option value="func">Function</option>
                    <option value="range">Range</option>
                </select>
            </div>
        </div>
    </div>
    <section id="func" class="function-settings hidden">
        <div class="form-group">
            <label for="func" class="control-label col-sm-3">Calculation Type</label>

            <div class="col-sm-9">
                <select name="func" class="form-control">
                    <option value="/">&divide;</option>
                    <option value="*">x</option>
                    <option value="+">+</option>
                    <option value="-">-</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="factor" class="control-label col-sm-3">Factor</label>

            <div class="col-sm-9">
                <input type="number" class="form-control" name="factor">
            </div>
        </div>
        <button class="btn btn-primary" onclick="addToScore()">Add to Score</button>
    </section>
    <section id="range" class="function-settings hidden">
        <div class="form-group">
            <label for="range" class="control-label col-sm-3">Range Type</label>

            <div class="col-sm-9">
                <select name="range" class="form-control">
                    <option value=">">If value is more than</option>
                    <option value="<">If value is less than</option>
                    <option value="=">If value is equal to</option>
                </select>
                <input type="number" class="form-control" name="test">
            </div>
        </div>
        <div class="form-group">
            <label for="score" class="control-label col-sm-3">Add to the score</label>

            <div class="col-sm-9">
                <input type="number" class="form-control" name="result">
            </div>
        </div>
        <button class="btn btn-primary" onclick="addToScore()">Add to Score</button>
    </section>
</div>

<script>
    $('#function').change(function(){
        var key = $('#function').val();
        $('.function-settings').addClass('hidden');
        $('#' + key).removeClass('hidden');
    })
</script>

@elseif($)
    <br>
    <div class="form-group">
        <label for="function" class="control-label col-sm-3">Score Type</label>
        <div class="col-sm-9">
            <select name="function" class="form-control" id="function">
                <option value="">[Select One]</option>
                <option value="func">Function</option>
                <option value="range">Range</option>
            </select>
        </div>
    </div>
    </div>
    <section id="func" class="function-settings hidden">
        <div class="form-group">
            <label for="func" class="control-label col-sm-3">Calculation Type</label>

            <div class="col-sm-9">
                <select name="func" class="form-control">
                    <option value="/">&divide;</option>
                    <option value="*">x</option>
                    <option value="+">+</option>
                    <option value="-">-</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="factor" class="control-label col-sm-3">Factor</label>

            <div class="col-sm-9">
                <input type="number" class="form-control" name="factor">
            </div>
        </div>
        <button class="btn btn-primary" onclick="addToScore()">Add to Score</button>
    </section>
    <section id="range" class="function-settings hidden">
        <div class="form-group">
            <label for="range" class="control-label col-sm-3">Range Type</label>

            <div class="col-sm-9">
                <select name="range" class="form-control">
                    <option value=">">If value is more than</option>
                    <option value="<">If value is less than</option>
                    <option value="=">If value is equal to</option>
                </select>
                <input type="number" class="form-control" name="test">
            </div>
        </div>
        <div class="form-group">
            <label for="score" class="control-label col-sm-3">Add to the score</label>

            <div class="col-sm-9">
                <input type="number" class="form-control" name="result">
            </div>
        </div>
        <button class="btn btn-primary" onclick="addToScore()">Add to Score</button>
    </section>
    </div>

    <script>
        $('#function').change(function(){
            var key = $('#function').val();
            $('.function-settings').addClass('hidden');
            $('#' + key).removeClass('hidden');
        })
    </script>

@else

    <section>
        <div class="form-group">
            <label for="range" class="control-label col-sm-3">How to test field</label>

            <div class="col-sm-9">
                <select name="function" id="testtype" class="form-control">
                    <option value="">Select One</option>
                    <option value="empty">If field is empty</option>
                    <option value="notempty">If field is not empty</option>
                    <option value="equals">If field is equal to:</option>
                    <option value="contains">If field contains:</option>
                </select>
                <br>
                <div id="test_field" class="hidden">
                    <input type="number"  class="form-control" name="test">
                    </br>
                </div>
            </div>

            <div class="form-group">
                <label for="score" class="control-label col-sm-3">Add to the score</label>

                <div class="col-sm-9">
                    <input type="number" class="form-control" name="result">
                </div>
            </div>
        </div>
        <button class="btn btn-primary" onclick="addToScore()">Add to Score</button>

    </section>

    <script>

        $('#testtype').change(function(){
            var test = $('#testtype').val();
            if (test == 'equals' | test == 'contains') {
                $('#test_field').removeClass('hidden');
            }
            else {
                $('#test_field').addClass('hidden');
            }
        });

    </script>
@endif

