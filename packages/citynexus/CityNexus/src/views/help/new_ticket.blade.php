<?php
$pagename = 'Support Portal';
$section = 'help';
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))
    <div class="row col-sm-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                Submit Support Ticket
            </div>
            <div class="panel-body">

                <form action="{{action('\CityNexus\CityNexus\Http\CitynexusController@postSubmitTicket')}}" class="form-horizontal" method="post">
                    {!! csrf_field() !!}
                    <div class="form-group">
                        <label for="ticket_type" class="control-label col-sm-4">Ticket Type</label>

                        <div class="col-sm-8">
                            <select name="type_type" id="type_type" class="form-control">
                                <option value="">Select One</option>
                                <option value="support">Support Request</option>
                                <option value="bug">Bug Report</option>
                                <option value="request">Feature Request</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subject" class="control-label col-sm-4">Subject</label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="subject" name="subject" value="{{old('subject')}}"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="related_url" class="control-label col-sm-4">Related URL <br>(if applicable) </label>

                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="related_url" name="related_url"
                                   value="{{$referer}}"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="message" class="control-label col-sm-4">Ticket</label>

                        <div class="col-sm-8">
                            <textarea name="ticket" id="" class="form-control" cols="30" rows="10">{{old('ticket')}}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-8">
                            <input type="submit" class="form-control" id="submit" name="submit" value="Submit Ticket"/>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>

@stop