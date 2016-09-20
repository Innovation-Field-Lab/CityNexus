#############################################<br>
####<br>
####   Related Application: {{env('CITYNEXUS_NAME')}}<br>
####   User: {{$user->fullname()}}, <{{$user->email}}><br>
####   Ticket Type: {{$request->get('ticket_type')}}<br>
@if($request->get('related_url') != null)
      #### Reference URL: <a href="{{$request->get('related_url')}}">{{$request->get('related_url')}}</a> </br>
@endif
####<br>
#############################################<br>

 <p> {{$request->get('ticket')}}</p>