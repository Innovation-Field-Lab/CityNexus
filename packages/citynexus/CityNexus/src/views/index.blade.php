<?php
$pagename = 'Dashboard';
$section = 'dashboard';
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                City Nexus Home
            </div>
            <div class="panel-body">
                <div class="col-sm-4" id="ticket">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <span class="panel-title">Submit Ticket</span>
                        </div>
                        <div class="panel-body">
                            <label for="type" class="control-label">Ticket Type</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="">Select One</option>
                                <option value="critical">Critical Bug</option>
                                <option value="bug">Other Bug</option>
                                <option value="request">Feature Request</option>
                            </select>
                            <label for="ticket" class="control-label">Related URL</label>
                            <input type="url" name="url" id="url" class="form-control" placeholder="Related URL">
                            <label for="subject" class="control-label">Subject</label>
                            <input type="text" name="subject" id="subject" class="form-control" placeholder="Short description">
                            <label for="ticket" class="control-label">Ticket</label>
                            <textarea name="ticket" class="form-control" id="ticket_message" cols="30" rows="10"></textarea>
                        </div>
                        <div class="panel-footer">
                            <button class="btn btn-primary" onclick="submitTicket()">Submit Ticket</button>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <span class="panel-title">Release Notes</span>
                        </div>
                        <div class="panel-body" style="height:400px; overflow: scroll; overflow-scrolling: default">
                            @include('citynexus::releasenote')
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <span class="panel-title">Dev Log</span>
                        </div>
                        <div class="panel-body" style="height:400px; overflow: scroll; overflow-scrolling: default">
                            @include('citynexus::devlog')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@push('js_footer')

<script>
    function submitTicket()
    {
        var type = $('#type').val();
        var address = $('#url').val();
        var ticket = $('#ticket_message').val();
        var subject = $('#subject').val();

        $.ajax({
            url: "{{config('citynexus.root_directory')}}/submit-ticket",
            type: "post",
            data: {
                _token: "{{csrf_token()}}",
                type: type,
                ticket: ticket,
                url: address,
                user_name: "{{\Illuminate\Support\Facades\Auth::getUser()->first_name}} {{\Illuminate\Support\Facades\Auth::getUser()->last_name}}",
                user_email: "{{\Illuminate\Support\Facades\Auth::getUser()->email}}",
                application: "{{config('app.url')}}",
                subject: subject
            }
        }).success(function(){
            $('#ticket').html('<div class="alert alert-info">Your ticket as been received and you have been sent a copy. You\'ll be contact if additional information is needed. <br>- Your Friendly HKS IFL Dev Team :-)</div>');
        })
    }
</script>

@stop
