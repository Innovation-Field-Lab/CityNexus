@extends('app')

@section('content')

    <div class="col-md-offset-2 col-md-8">
        <div class="panel panel-default">
            <form class="form-horizontal" action="{{action('UploadController@postProperties')}}" method="post" enctype="multipart/form-data">
            <div class="panel-heading">
                <span class="panel-title">Upload Properties</span>
            </div>
            <div class="panel-body">
                <input type="hidden" name="_token" value="{{csrf_token()}}">
                <div class="form-group">
                    <label for="file" class="control-label col-sm-4">File</label>
                    <div class="col-sm-8">
                        <input type="file" id="file" name="file"/>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <input type="submit" value="Upload File" class="col-sm-offset-4 btn btn-primary">
            </div>
            </form>
        </div>
    </div>

    <div class="col-md-offset-2 col-md-8">
        <div class="panel panel-default">
            <form class="form-horizontal" action="{{action('UploadController@postFire')}}" method="post" enctype="multipart/form-data">
                <div class="panel-heading">
                    <span class="panel-title">Upload Fire Data</span>
                </div>
                <div class="panel-body">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <div class="form-group">
                        <label for="file" class="control-label col-sm-4">File</label>
                        <div class="col-sm-8">
                            <input type="file" id="file" name="file"/>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <input type="submit" value="Upload File" class="col-sm-offset-4 btn btn-primary">
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-offset-2 col-md-8">
        <div class="panel panel-default">
            <form class="form-horizontal" action="{{action('UploadController@postPolice')}}" method="post" enctype="multipart/form-data">
                <div class="panel-heading">
                    <span class="panel-title">Upload Police Data</span>
                </div>
                <div class="panel-body">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <div class="form-group">
                        <label for="file" class="control-label col-sm-4">File</label>
                        <div class="col-sm-8">
                            <input type="file" id="file" name="file"/>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <input type="submit" value="Upload File" class="col-sm-offset-4 btn btn-primary">
                </div>
            </form>
        </div>
    </div>
    <div class="col-md-offset-2 col-md-8">
        <div class="panel panel-default">
            <form class="form-horizontal" action="{{action('UploadController@postTreasury')}}" method="post" enctype="multipart/form-data">
                <div class="panel-heading">
                    <span class="panel-title">Upload Finance Data</span>
                </div>
                <div class="panel-body">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <div class="form-group">
                        <label for="file" class="control-label col-sm-4">File</label>
                        <div class="col-sm-8">
                            <input type="file" id="file" name="file"/>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <input type="submit" value="Upload File" class="col-sm-offset-4 btn btn-primary">
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-offset-2 col-md-8">
        <div class="panel panel-default">
            <form class="form-horizontal" action="{{action('UploadController@postAssessor')}}" method="post" enctype="multipart/form-data">
                <div class="panel-heading">
                    <span class="panel-title">Upload Assessor Data</span>
                </div>
                <div class="panel-body">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <div class="form-group">
                        <label for="file" class="control-label col-sm-4">File</label>
                        <div class="col-sm-8">
                            <input type="file" id="file" name="file"/>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <input type="submit" value="Upload File" class="col-sm-offset-4 btn btn-primary">
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-offset-2 col-md-8">
        <div class="panel panel-default">
            <form class="form-horizontal" action="{{action('UploadController@postCrime')}}" method="post" enctype="multipart/form-data">
                <div class="panel-heading">
                    <span class="panel-title">Upload Crime Data</span>
                </div>
                <div class="panel-body">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">
                    <div class="form-group">
                        <label for="file" class="control-label col-sm-4">File</label>
                        <div class="col-sm-8">
                            <input type="file" id="file" name="file"/>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <input type="submit" value="Upload File" class="col-sm-offset-4 btn btn-primary">
                </div>
            </form>
        </div>
    </div>

    @stop