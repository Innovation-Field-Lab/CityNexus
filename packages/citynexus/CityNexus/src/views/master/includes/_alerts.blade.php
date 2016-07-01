@if(\Illuminate\Support\Facades\Session::get('flash_success') != null)
    <script>
        Command: toastr["success"]("{{\Illuminate\Support\Facades\Session::get('flash_success')}}")
    </script>
@elseif(\Illuminate\Support\Facades\Session::get('flash_info') != null)
    <script>
        Command: toastr["alert"]("{{\Illuminate\Support\Facades\Session::get('flash_alert')}}")
    </script>
@elseif(\Illuminate\Support\Facades\Session::get('flash_warning') != null)
    <script>
        Command: toastr["warning"]("{{\Illuminate\Support\Facades\Session::get('flash_warning')}}")
    </script>
@elseif(\Illuminate\Support\Facades\Session::get('flash_danger') != null)
    <script>
        Command: toastr["danger"]("{{\Illuminate\Support\Facades\Session::get('flash_danger')}}")
    </script>
@endif