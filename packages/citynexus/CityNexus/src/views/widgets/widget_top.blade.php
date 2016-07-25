<div class="col-sm-4" id="widget-{{$widget->id}}">
    <div class="card-box">
        @can('citynexus', ['admin', 'dashboard'])
        <div class="dropdown pull-right">
            <a href="#" class="dropdown-toggle card-drop" data-toggle="dropdown" aria-expanded="false">
                <i class="zmdi zmdi-more-vert"></i>
            </a>
            <ul class="dropdown-menu" role="menu">
                <li><a onclick="removeWidget({{$widget->id}})">Remove Widget</a></li>
            </ul>
        </div>
        @endcan
        <h4 class="header-title m-t-0 m-b-30">{{$widget->name}}</h4>
        <div class="inbox-widget nicescroll" style="height: 315px;">