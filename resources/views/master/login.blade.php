<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">

    <!-- App Favicon -->
    <link rel="shortcut icon" href="/images/favicon.ico">

    <!-- App title -->
    <title>{{config('citynexus.app_name')}}</title>

    <!-- App CSS -->
    <link href="/vendor/citynexus/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/vendor/citynexus/css/core.css" rel="stylesheet" type="text/css" />
    <link href="/vendor/citynexus/css/components.css" rel="stylesheet" type="text/css" />
    <link href="/vendor/citynexus/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="/vendor/citynexus/css/pages.css" rel="stylesheet" type="text/css" />
    <link href="/vendor/citynexus/css/menu.css" rel="stylesheet" type="text/css" />
    <link href="/vendor/citynexus/css/responsive.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <script src="/vendor/citynexus/js/modernizr.min.js"></script>

</head>
<body>

<div class="account-pages"></div>
<div class="clearfix"></div>
<div class="wrapper-page">

    <div class="m-t-40 card-box">
        <div class="text-center">
            <a href="/" class="logo"><span>{{config('citynexus.app_name')}}</span></a>
            <h5 class="text-muted m-t-0 font-600">{{config('citynexus.slogan')}}</h5>
        </div>
        <div class="text-center">
            <h4 class="text-uppercase font-bold m-b-0">Sign In</h4>
        </div>

        <div class="panel-body">
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/login') }}">
                {{csrf_field()}}
                <div class="form-group ">
                    <div class="col-xs-12">
                        <input class="form-control" type="email" required="" name="email" placeholder="username@domain.com">
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-xs-12">
                        <input class="form-control" type="password" required="" name="password" placeholder="Password">
                    </div>
                </div>

                <div class="form-group ">
                    <div class="col-xs-12">
                        <div class="checkbox checkbox-custom">
                            <input id="checkbox-signup" type="checkbox" name="remember">
                            <label for="checkbox-signup">
                                Remember me
                            </label>
                        </div>

                    </div>
                </div>

                <div class="form-group text-center m-t-30">
                    <div class="col-xs-12">
                        <button class="btn btn-custom btn-bordred btn-block waves-effect waves-light" type="submit">Log In</button>
                    </div>
                </div>

                <div class="form-group m-t-30 m-b-0">
                    <div class="col-sm-12">
                        <a href="{{ url('/password/email') }}" class="text-muted"><i class="fa fa-lock m-r-5"></i> Forgot your password?</a>
                    </div>
                </div>
            </form>

        </div>
    </div>
    <!-- end card-box-->

    {{--<div class="row">--}}
        {{--<div class="col-sm-12 text-center">--}}
            {{--<p class="text-muted">Don't have an account? <a href="page-register.html" class="text-primary m-l-5"><b>Sign Up</b></a></p>--}}
        {{--</div>--}}
    {{--</div>--}}

</div>
<!-- end wrapper page -->



<script>
    var resizefunc = [];
</script>

<!-- jQuery  -->
<script src="/vendor/citynexus/js/jquery.min.js"></script>
<script src="/vendor/citynexus/js/bootstrap.min.js"></script>
<script src="/vendor/citynexus/js/detect.js"></script>
<script src="/vendor/citynexus/js/fastclick.js"></script>
<script src="/vendor/citynexus/js/jquery.slimscroll.js"></script>
<script src="/vendor/citynexus/js/jquery.blockUI.js"></script>
<script src="/vendor/citynexus/js/waves.js"></script>
<script src="/vendor/citynexus/js/wow.min.js"></script>
<script src="/vendor/citynexus/js/jquery.nicescroll.js"></script>
<script src="/vendor/citynexus/js/jquery.scrollTo.min.js"></script>

<!-- App js -->
<script src="/vendor/citynexus/js/jquery.core.js"></script>
<script src="/vendor/citynexus/js/jquery.app.js"></script>

</body>
</html>