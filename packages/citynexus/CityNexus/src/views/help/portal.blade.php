<?php
$pagename = 'Support Portal';
$section = 'help';
?>

@extends(config('citynexus.template'))

@section(config('citynexus.section'))
    <iframe src="https://citynexus.freshdesk.com/support/home" width="100%" height="800px"></iframe>

@stop