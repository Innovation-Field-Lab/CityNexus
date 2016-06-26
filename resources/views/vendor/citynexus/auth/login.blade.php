@extends('citynexus::master.login')

@section('main')
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
@endsection
