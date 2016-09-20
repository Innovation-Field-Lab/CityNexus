
<!-- Top Bar Start -->
<div class="topbar">

    <!-- LOGO -->
    <div class="topbar-left">
        <a href="/" class="logo"><span>{{config('citynexus.app_name')}}</span><i class="zmdi zmdi-layers"></i></a>
    </div>

    <!-- Button mobile view to collapse sidebar menu -->
    <div class="navbar navbar-default" role="navigation">
        <div class="container">

            <!-- Page title -->
            <ul class="nav navbar-nav navbar-left">
                <li>
                    <button class="button-menu-mobile open-left">
                        <i class="zmdi zmdi-menu"></i>
                    </button>
                </li>
                @if(isset($pagename))
                <li>
                    <h4 class="page-title">{!! $pagename !!}</h4>
                </li>
                @endif
            </ul>
            {{----}}
            {{--<!-- Right(Notification and Searchbox -->--}}
            <ul class="nav navbar-nav navbar-right">
                {{--<li>--}}
                    {{--<!-- Notification -->--}}
                    {{--<div class="notification-box">--}}
                        {{--<ul class="list-inline m-b-0">--}}
                            {{--<li>--}}
                                {{--<a href="javascript:void(0);" class="right-bar-toggle">--}}
                                    {{--<i class="zmdi zmdi-notifications-none"></i>--}}
                                {{--</a>--}}
                                {{--<div class="noti-dot">--}}
                                    {{--<span class="dot"></span>--}}
                                    {{--<span class="pulse"></span>--}}
                                {{--</div>--}}
                            {{--</li>--}}
                        {{--</ul>--}}
                    {{--</div>--}}
                    {{--<!-- End Notification bar -->--}}
                {{--</li>--}}
                <li class="hidden-xs">
                    <form role="search" class="app-search" action="{{action('\CityNexus\CityNexus\Http\SearchController@getSearch')}}">
                        {{csrf_field()}}
                        <input type="text" id="search" name="query" placeholder="Search..."
                               class="form-control">
                        <input type="submit" style="    border: 0 none;
    height: 0;
    width: 0;
    padding: 0;
    margin: 0;
    overflow: hidden;">
                    </form>
                </li>
                <li>
                    <a href="{{action('\CityNexus\CityNexus\Http\CityNexusController@getSubmitTicket'}}" class="btn btn-sm btn-rounded">Submit Support Ticket</a>
                </li>
            </ul>

        </div><!-- end container -->
    </div><!-- end navbar -->
</div>
<!-- Top Bar End -->