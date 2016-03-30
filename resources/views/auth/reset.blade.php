<!-- resources/views/auth/reset.blade.php -->

@extends('layout.login')

@section('main')

    <style>
        img.bg {
            /* Set rules to fill background */
            min-height: 100%;
            min-width: 1024px;

            /* Set up proportionate scaling */
            width: 100%;
            height: auto;

            /* Set up positioning */
            position: fixed;
            top: 0;
            left: 0;
            z-index:-1;
        }

        @media screen and (max-width: 1024px) { /* Specific to this particular image */
            img.bg {
                left: 50%;
                margin-left: -512px;   /* 50% */
            }
        }
    </style>
    <img src="/img/background.png" class="bg hidden-xs">

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default" id="login">
                    <div class="panel-heading">Reset Password</div>
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

                            <form method="POST" action="/password/reset">
                                {!! csrf_field() !!}
                                <input type="hidden" class="form-control" name="token" value="{{ $token }}">
                                <div>
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                                </div>

                                <div>
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" name="password">
                                </div>

                                <div>
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password" class="form-control" name="password_confirmation">
                                </div>
                                <br>

                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        Reset Password
                                    </button>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


