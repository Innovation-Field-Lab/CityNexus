<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Chelsea CityNexus</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="active"><a href="/">Home</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Properties <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="/citynexus/properties">All Properties</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Data Sets <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="/tabler">All Data Sets</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="/tabler/uploader">New From Upload</a></li>
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Property Scores <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="/citynexus/risk-score/scores">All Scores</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="/citynexus/risk-score/create">Create New Score</a></li>
                    </ul>
                </li>


            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user"></span> {{\Illuminate\Support\Facades\Auth::getUser()->first_name}}</a>
                    <ul class="dropdown-menu">
                        <li><a href="/citynexus/settings">Settings</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="/auth/logout">Log Out</a></li>
                    </ul>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>