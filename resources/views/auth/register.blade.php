
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Signin Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="/css/app.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/signin.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="../../assets/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body>

<div class="container">

    <form method="POST" action="/auth/register" class="form-signin">
        {!! csrf_field() !!}
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

        <div class="alert alert-info">
            Looks like this is the first time you've logged into this instance of CityNexus! Please create a super user
            account. Once this account has been created you won't see this screen again.
        </div>

        <h2>Set Up Super User</h2>

        <div>
            <label for="name">First Name</label>
            <input class='form-control' type="text" name="first_name" value="{{ old('first_name') }}">
        </div>

        <div>
            <label for="name">Last Name</label>
            <input class='form-control' type="text" name="last_name" value="{{ old('last_name') }}">
        </div>

        <div>
            <label for="email">Email</label>
            <input class='form-control' type="email" name="email" value="{{ old('email') }}">
        </div>

        <div>
            <label for="password">Password</label>
            <input class='form-control' type="password" name="password">
        </div>

        <div>
            <label for="password_confirmation">Confirm Password</label>
            <input class='form-control' type="password" name="password_confirmation">
        </div>

        <div>
            <button class="btn btn-primary" type="submit">Register</button>
        </div>
    </form>

</div> <!-- /container -->


<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="/vendor/assets/js/ie10-viewport-bug-workaround.js"></script>
</body>
</html>
