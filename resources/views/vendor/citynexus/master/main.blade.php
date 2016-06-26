@include('citynexus::master._header')


<body class="fixed-left">

<!-- Begin page -->
<div id="wrapper">

    @include('citynexus::master._top_bar')

    @include('citynexus::master._left_menu')

    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="content-page">
        <!-- Start content -->
        <div class="content">
            <div class="container">

                @yield('main')

            </div> <!-- container -->

        </div> <!-- content -->

        <footer class="footer text-right">
            2016 © CityNexus
        </footer>

    </div>


    <!-- ============================================================== -->
    <!-- End Right content here -->
    <!-- ============================================================== -->
    @stack('sidebar')

</div>
<!-- END wrapper -->
<!-- Modal -->
<div id="modal" class="modal-demo">
    <button type="button" class="close" onclick="Custombox.close();">
        <span>&times;</span><span class="sr-only">Close</span>
    </button>
    <h4 class="custom-modal-title" id="modal-title">Modal title</h4>
    <div id="modal-text" class="custom-modal-text">
    </div>
</div>

<script>
    var resizefunc = [];
</script>

<!-- jQuery  -->
<script src="/vendor/citynexus/js/jquery.min.js"></script>
<script src="/vendor/citynexus/js/bootstrap.min.js"></script>
<script src="/vendor/citynexus/js/detect.js"></script>
<script src="/vendor/citynexus/js/fastclick.js"></script>
<script src="/vendor/citynexus/js/jquery.blockUI.js"></script>
<script src="/vendor/citynexus/js/waves.js"></script>
<script src="/vendor/citynexus/js/jquery.nicescroll.js"></script>
<script src="/vendor/citynexus/js/jquery.scrollTo.min.js"></script>

<!-- App js -->
<script src="/vendor/citynexus/js/jquery.core.js"></script>
<script src="/vendor/citynexus/js/jquery.app.js"></script>
<script src="/vendor/citynexus/plugins/toastr/toastr.min.js"></script>
<!-- Modal-Effect -->
<script src="/vendor/citynexus/plugins/custombox/dist/custombox.min.js"></script>
<script src="/vendor/citynexus/plugins/custombox/dist/legacy.min.js"></script>
<script src="/vendor/citynexus/js/typeahead.js"></script>


    <script type="text/javascript">

            function triggerModal(newTitle, newBody)
            {
                $("#modal-title").html(newTitle);
                $("#modal-text").html(newBody);
                Custombox.open({
                    target: '#modal',
                    effect: 'fadein'
                });
            }
            $(function () {
                var i = -1;
                var toastCount = 0;
                var $toastlast;

                var getMessageWithClearButton = function (msg) {
                    msg = msg ? msg : 'Clear itself?';
                    msg += '<br /><br /><button type="button" class="btn btn-default clear">Yes</button>';
                    return msg;
                };

                $('#showtoast').click(function () {
                    var shortCutFunction = $("#toastTypeGroup input:radio:checked").val();
                    var msg = $('#message').val();
                    var title = $('#title').val() || '';
                    var $showDuration = $('#showDuration');
                    var $hideDuration = $('#hideDuration');
                    var $timeOut = $('#timeOut');
                    var $extendedTimeOut = $('#extendedTimeOut');
                    var $showEasing = $('#showEasing');
                    var $hideEasing = $('#hideEasing');
                    var $showMethod = $('#showMethod');
                    var $hideMethod = $('#hideMethod');
                    var toastIndex = toastCount++;
                    var addClear = $('#addClear').prop('checked');

                    toastr.options = {
                        closeButton: $('#closeButton').prop('checked'),
                        debug: $('#debugInfo').prop('checked'),
                        newestOnTop: $('#newestOnTop').prop('checked'),
                        progressBar: $('#progressBar').prop('checked'),
                        positionClass: $('#positionGroup input:radio:checked').val() || 'toast-top-right',
                        preventDuplicates: $('#preventDuplicates').prop('checked'),
                        onclick: null
                    };

                    if ($('#addBehaviorOnToastClick').prop('checked')) {
                        toastr.options.onclick = function () {
                            alert('You can perform some custom action after a toast goes away');
                        };
                    }

                    if ($showDuration.val().length) {
                        toastr.options.showDuration = $showDuration.val();
                    }

                    if ($hideDuration.val().length) {
                        toastr.options.hideDuration = $hideDuration.val();
                    }

                    if ($timeOut.val().length) {
                        toastr.options.timeOut = addClear ? 0 : $timeOut.val();
                    }

                    if ($extendedTimeOut.val().length) {
                        toastr.options.extendedTimeOut = addClear ? 0 : $extendedTimeOut.val();
                    }

                    if ($showEasing.val().length) {
                        toastr.options.showEasing = $showEasing.val();
                    }

                    if ($hideEasing.val().length) {
                        toastr.options.hideEasing = $hideEasing.val();
                    }

                    if ($showMethod.val().length) {
                        toastr.options.showMethod = $showMethod.val();
                    }

                    if ($hideMethod.val().length) {
                        toastr.options.hideMethod = $hideMethod.val();
                    }

                    if (addClear) {
                        msg = getMessageWithClearButton(msg);
                        toastr.options.tapToDismiss = false;
                    }
                    if (!msg) {
                        msg = getMessage();
                    }

                    $('#toastrOptions').text('Command: toastr["'
                            + shortCutFunction
                            + '"]("'
                            + msg
                            + (title ? '", "' + title : '')
                            + '")\n\ntoastr.options = '
                            + JSON.stringify(toastr.options, null, 2)
                    );

                    var $toast = toastr[shortCutFunction](msg, title); // Wire up an event handler to a button in the toast, if it exists
                    $toastlast = $toast;

                    if (typeof $toast === 'undefined') {
                        return;
                    }

                    if ($toast.find('#okBtn').length) {
                        $toast.delegate('#okBtn', 'click', function () {
                            alert('you clicked me. i was toast #' + toastIndex + '. goodbye!');
                            $toast.remove();
                        });
                    }
                    if ($toast.find('#surpriseBtn').length) {
                        $toast.delegate('#surpriseBtn', 'click', function () {
                            alert('Surprise! you clicked me. i was toast #' + toastIndex + '. You could perform an action here.');
                        });
                    }
                    if ($toast.find('.clear').length) {
                        $toast.delegate('.clear', 'click', function () {
                            toastr.clear($toast, {force: true});
                        });
                    }
                });

                function getLastToast() {
                    return $toastlast;
                }

                $('#clearlasttoast').click(function () {
                    toastr.clear(getLastToast());
                });
                $('#cleartoasts').click(function () {
                    toastr.clear();
                });
            })
</script>

<script>
    function getHelp( help )
    {
        $.ajax({
            url: "{{action("\CityNexus\CityNexus\Http\HelpController@getItem")}}/" + help
        }).success(function (data){
            $("#modal").html(data);
            Custombox.open({
                target: '#modal',
                effect: 'fadein'
            });
        })

    }

    jQuery(document).ready(function($) {
        var engine = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.whitespace,
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch: '{{action('\CityNexus\CityNexus\Http\SearchController@getPrefetch')}}'
        });

        $("#search").typeahead({
            hint: true,
            highlight: true,
            minLength: 2
        }, {
            source: engine,
            name: 'search_list'
        });
    });
</script>

@include('citynexus::master.includes._alerts')

@stack('js_footer')

</body>
</html>