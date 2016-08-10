<?php

    $permission_sets = [
        [
            'title' => 'Datasets',
            'key' => 'datasets',
            'permissions' => [
                    [
                            'permission' => "View Dataset",
                            'key' => 'view'
                    ],
                    [
                            'permission' => "View Raw Dataset",
                            'key' => 'raw'
                    ],
                    [
                            'permission' => "Create New Dataset",
                            'key' => 'create'
                    ],
                    [
                            'permission' => "Upload to Dataset",
                            'key' => 'upload'
                    ],
                    [
                            'permission' => "Edit Dataset",
                            'key' => 'edit'
                    ],
                    [
                            'permission' => "Delete Dataset",
                            'key' => 'delete'
                    ],
                    [
                            'permission' => "Export Dataset",
                            'key' => 'export'
                    ],
                    [
                            'permission' => "Rollback Upload",
                            'key' => 'rollback'
                    ]

            ]
        ],
            [
                    'title' => 'Scores',
                    'key' => 'scores',
                    'permissions' => [
                            [
                                    'permission' => "View Score",
                                    'key' => 'view'
                            ],
                            [
                                    'permission' => "View Raw Scores",
                                    'key' => 'raw'
                            ],
                            [
                                    'permission' => "Create New Score",
                                    'key' => 'create'
                            ],
                            [
                                    'permission' => "Refresh Score",
                                    'key' => 'refresh'
                            ],
                            [
                                    'permission' => "Edit Score",
                                    'key' => 'edit'
                            ],
                            [
                                    'permission' => "Delete Score",
                                    'key' => 'score'
                            ],
                            [
                                    'permission' => "Export Score",
                                    'key' => 'score'
                            ]
                    ]
            ],
            [
                    'title' => 'Reports',
                    'key' => 'reports',
                    'permissions' => [
                            [
                                    'permission' => "View Reports",
                                    'key' => 'view'
                            ],
                            [
                                    'permission' => "Create Reports",
                                    'key' => 'create'
                            ],
                            [
                                    'permission' => "Save Reports",
                                    'key' => 'save'
                            ],
                            [
                                    'permission' => "Delete Score",
                                    'key' => 'score'
                            ],
                    ]
            ],
            [
                    'title' => 'Users',
                    'key' => 'usersAdmin',
                    'permissions' => [
                            [
                                    'permission' => "Create User",
                                    'key' => 'create'
                            ],
                            [
                                    'permission' => "Delete User",
                                    'key' => 'delete'
                            ],
                            [
                                    'permission' => "Assign User Permissions",
                                    'key' => 'assign'
                            ],
                    ]
            ],
            [
                    'title' => 'Properties',
                    'key' => 'properties',
                    'permissions' => [
                            [
                                    'permission' => "View Properties List",
                                    'key' => 'view',
                            ],
                            [
                                    'permission' => "View Properties Details",
                                    'key' => 'show',
                            ],
                            [
                                    'permission' => "Merge Properties",
                                    'key' => 'merge',
                            ],
                            [
                                    'permission' => "Edit Properties Record",
                                    'key' => 'edit',
                            ],
                            [
                                    'permission' => "Create Properties Record",
                                    'key' => 'create',
                            ]
                    ]
            ],
            [
                    'title' => 'Administrator',
                    'key' => 'admin-rights',
                    'permissions' => [
                            [
                                    'permission' => "View Admin Panel",
                                    'key' => 'view'
                            ],
                            [
                                    'permission' => "Hard Delete Data",
                                    'key' => 'delete'
                            ],
                            [
                                    'permission' => "Edit App Settings",
                                    'key' => 'edit'
                            ]
                    ]
            ]
    ]

?>
@if(isset($user))
<input type="hidden" name="user_id" value="{{$user->id}}">
{{csrf_field()}}
<div class="form-horizontal">
<div class="form-group">
    <label for="first_name" class="control-label col-sm-4">First Name</label>

    <div class="col-sm-8">
        <input type="text" class="form-control" id="first_name" name="first_name" value="{{$user->first_name}}"/>
    </div>
</div>
<div class="form-group">
    <label for="last_name" class="control-label col-sm-4">Last Name</label>

    <div class="col-sm-8">
        <input type="text" class="form-control" id="last_name" name="last_name" value="{{$user->last_name}}"/>
    </div>
</div>

<div class="form-group">
    <label for="title" class="control-label col-sm-4">Title</label>

    <div class="col-sm-8">
        <input type="text" class="form-control" id="title" name="title" value="{{$user->title}}"/>
    </div>
</div>

<div class="form-group">
    <label for="department" class="control-label col-sm-4">Department</label>

    <div class="col-sm-8">
        <input type="text" class="form-control" id="department" name="department" value="{{$user->department}}"/>
    </div>
</div>
</div>

    <h4>Edit Permissions</h4>
@endif

<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    {{--Data sets--}}

    @foreach($permission_sets as $i)
        <?php $group = $i['key']; ?>
    <div class="panel panel-default">
        <div class="panel-heading" role="tab" id="headingOne">
            <h4 class="panel-title">
                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#{{$i['key']}}" aria-expanded="true" aria-controls="collapseOne">
                    {{$i['title']}}
                </a>
            </h4>
        </div>
        <div id="{{$i['key']}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
            <div class="panel-body">
                <div class="list-group">
                        @foreach($i['permissions'] as $p)
                        <?php $method = $p['key']; ?>
                        <div class="list-group-item">
                            <input type="checkbox" name="permissions[{{$i['key']}}][{{$p['key']}}]" value="true" class="{{$i['key']}}"
                                   @if(isset($permissions->$group->$method)) checked @endif
                            > <label for="">{{ $p['permission'] }}</label>
                        </div>
                    @endforeach
                    <br><br>
                    <button class="btn btn-sm" onclick="select('{{$i['key']}}')" id="{{$i['key']}}SelectAll"> Check All </button>
                    <button class="btn btn-sm hidden" onclick="clearChecks('{{$i['key']}}')" id="{{$i['key']}}UnselectAll"> Uncheck All </button>
                </div>
            </div>
        </div>
    </div>

    @endforeach
