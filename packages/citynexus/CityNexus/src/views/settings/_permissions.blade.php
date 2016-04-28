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
                    'title' => 'Administrator',
                    'key' => 'admin',
                    'permissions' => [
                            [
                                    'permission' => "View Admin Panel",
                                    'key' => 'view'
                            ]
                    ]
            ]
    ]

?>

<input type="hidden" name="user_id" value="{{$user->id}}">
{{csrf_field()}}

<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
    {{--Data sets--}}

    @foreach($permission_sets as $i)
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
                        <div class="list-group-item">
                            <input type="checkbox" name="permissions[{{$i['key']}}][{{$p['key']}}]" value="true" class="{{$i['key']}}"
                                   @if(isset($permissions->$i['key']->$p['key']) && $permissions->$i['key']->$p['key']) checked @endif
                            > <label for="">{{ var_dump($p['permission']) }}</label>
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
