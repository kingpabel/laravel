<!DOCTYPE html>
<html lang="en">
<head>
    <title>
    </title>
    {!! HTML::script('js/charisma/js/jquery-1.7.2.min.js') !!}
    {!! HTML::script('js/pnotify/jquery.pnotify.js') !!}
    {!! HTML::script('css/bootstrap/js/bootstrap-alert.js') !!}
    {!! HTML::style('css/bootstrap/css/bootstrap.css') !!}
    {!! HTML::style('css/bootstrap/css/bootstrap-responsive.css') !!}
    {!! HTML::style('css/charisma/css/bootstrap-cerulean.css') !!}
    {!! HTML::style('css/charisma/css/charisma-app.css') !!}
    {!! HTML::style('css/jquery-ui.css') !!}
    {!! HTML::style('css/charisma/css/opa-icons.css') !!}
    {!! HTML::style('css/pnotify/jquery.pnotify.default.icons.css') !!}
    {!! HTML::style('css/pnotify/jquery.pnotify.default.css') !!}
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-60038966-1', 'auto');
        ga('send', 'pageview');

    </script>
    @yield('jsBottom')
    <script>
        $(function() {
            $( ".datepicker" ).datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat:'yy-mm-dd'
            });
            $( ".datepicker2" ).datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat:'yy-mm-dd'
            });
        });
    </script>
    <script>
        $(function(){
            $(".input").bind("keyup blur",function() {
                var $th = $(this);
                $th.val( $th.val().replace(/[^A-z0-9,#. _@-]/g, function(str) { return ''; } ) );
            });
        })
    </script>
    <!-- The HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js') !!}
    <![endif]-->
    <script type="text/javascript" language="javascript">
        function resultDelete()
        {
            var chk=confirm("Are you sure to delete this?");
            if(chk)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
    </script>
</head>

<body>


<!-- topbar starts -->
<div class="navbar navbar-inverse">
    <div class="navbar-inner">
        <div class="container-fluid">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".top-nav.nav-collapse,.sidebar-nav.nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href="http://www.kingpabel.com">Kingpabel</a>
            <!-- user dropdown starts -->
            <div class="btn-group pull-right" >
                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="icon-user"></i><span class="hidden-phone"> {{  Auth::user()->username  }}</span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <!-- <li><a href="#">Profile</a></li>
                     <li class="divider"></li> -->
                    <li>
                        {!! link_to("user/logout","Logout") !!}

                    </li>
                </ul>
            </div>
            <!-- user dropdown ends -->
        </div>
    </div>
</div>
<!-- topbar ends -->
<div class="container-fluid">
    <div class="row-fluid">

        <!-- left menu starts -->
        <div class="span2 main-menu-span">
            <div class="well nav-collapse sidebar-nav">
                <ul class="nav nav-tabs nav-stacked main-menu">
                    <li class="nav-header hidden-tablet">Main</li>
                    <li><a class="ajax-link" href="{!! URL::to('user') !!}"><i class="icon-home"></i><span class="hidden-tablet"> Dashboard</span></a></li>
                    <li class="nav-header hidden-tablet"> Profile</li>
                    <li><a class="ajax-link" href="{!! URL::to('user/update-profile') !!}"><i class="icon-edit"></i><span class="hidden-tablet"> Edit Info</span></a></li>
                    <li><a class="ajax-link" href="{!! URL::to('user/change-password') !!}"><i class="icon-edit"></i><span class="hidden-tablet"> Change Password</span></a></li>
                    <li class="nav-header hidden-tablet"> Leave</li>
                    <li><a class="ajax-link" href="{!! URL::to('user/apply-leave') !!}"><i class="icon-align-justify"></i><span class="hidden-tablet"> Apply</span></a></li>
                    <li><a class="ajax-link" href="{!! URL::to('user/my-leave') !!}"><i class="icon-calendar"></i><span class="hidden-tablet"> My Leave</span></a></li>
                </ul>
                <label id="for-is-ajax" class="hidden-tablet" for="is-ajax"></label>
            </div><!--/.well -->
        </div><!--/span-->
        <!-- left menu ends -->

        <noscript>
            <div class="alert alert-block span10">
                <h4 class="alert-heading">Warning!</h4>
                <p>You need to have <a href="http://en.wikipedia.org/wiki/JavaScript" target="_blank">JavaScript</a> enabled to use this site.</p>
            </div>
        </noscript>

        <div id="content" class="span10">
            @yield('content')

        </div><!--/row-->





        <!-- content ends -->
    </div><!--/#content.span10-->
</div><!--/fluid-row-->

<hr>



<footer>
</footer>

</div><!--/.fluid-container-->

<!-- external javascript
================================================== -->
<!-- Placed at the end of the document so the pages load faster -->
{!! HTML::script('js/jquery-ui.js') !!}
{!! HTML::script('js/charisma/js/bootstrap-dropdown.js') !!}
{!! HTML::script('js/charisma/js/bootstrap-tab.js') !!}
{!! HTML::script('js/charisma/js/bootstrap-tooltip.js') !!}
{!! HTML::script('js/charisma/js/bootstrap-popover.js') !!}
{!! HTML::script('js/charisma/js/jquery.cookie.js') !!}
{!! HTML::script('js/charisma/js/jquery.chosen.min.js') !!}
{!! HTML::script('js/charisma/js/jquery.uniform.min.js') !!}
{!! HTML::script('js/charisma/js/jquery.uniform.min.js') !!}
{!! HTML::script('js/charisma/js/jquery.cleditor.min.js') !!}
{!! HTML::script('js/charisma/js/jquery.noty.js') !!}
{!! HTML::script('js/charisma/js/jquery.elfinder.min.js') !!}
{!! HTML::script('js/charisma/js/jquery.raty.min.js') !!}
{!! HTML::script('js/charisma/js/jquery.iphone.toggle.js') !!}
{!! HTML::script('js/charisma/js/jquery.autogrow-textarea.js') !!}
{!! HTML::script('js/charisma/js/jquery.uploadify-3.1.min.js') !!}
{!! HTML::script('js/charisma/js/jquery.history.js') !!}
{!! HTML::script('js/charisma/js/charisma.js') !!}

</body>
</html>