#############################################<br>
####<br>
####<br>
####   The following ticket has been received.<br>
####   Critical bugs will be addressed ASAP<br>
####   and an update will be deployed once completed.<br>
####   Non-critical bugs will be deployed in regular<br>
####   Monday evening product updates. Please check<br>
####   the development log to see updates on bugs or<br>
####   feature requests.<br>
####<br>
####   - Your Friendly HKS IFL Dev Team<br>
####<br>
#############################################<br>

<p><b>Date Time: </b>{{\Carbon\Carbon::now()}}</p><br>
<p><b>From: </b> <a href="mailto:{{$request->get('user_email')}}">{{$request->get('user_name')}}</a></p>
<p><b>Type: </b> {{$request->get('type')}}</p>
<p><b>Application: {{$request->get('application')}}</b></p>
@if($request->get('url') != null)
<p><b>Reference URL:</b> <a href="{{$request->get('url')}}">{{$request->get('url')}}</a></p>
    @endif

<p><b>Subject: </b> {{$request->get('subject')}}</p>

<p><b>Message: </b> {{$request->get('ticket')}}</p>