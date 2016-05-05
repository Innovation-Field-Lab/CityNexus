<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">

    <link rel="shortcut icon" href="/images/favicon.ico">

    <title>{{config('citynexus.app_name')}}</title>

    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="/vendor/citynexus/plugins/morris/morris.css">

    <!-- App css -->
    <link href="/vendor/citynexus/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/vendor/citynexus/css/core.css" rel="stylesheet" type="text/css" />
    <link href="/vendor/citynexus/css/components.css" rel="stylesheet" type="text/css" />
    <link href="/vendor/citynexus/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="/vendor/citynexus/css/pages.css" rel="stylesheet" type="text/css" />
    <link href="/vendor/citynexus/css/menu.css" rel="stylesheet" type="text/css" />
    <link href="/vendor/citynexus/css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="/vendor/citynexus/plugins/toastr/toastr.min.css" rel="stylesheet" type="text/css" />

    @stack('style')

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>

    <![endif]-->
    @stack('js_header')
    <script src="/vendor/citynexus/js/modernizr.min.js"></script>

</head>