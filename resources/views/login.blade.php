    <html>
    <head>
        <title>
            Attendance
        </title>
             {!! HTML::script('js/jquery-1.9.0.min.js') !!}
             {!! HTML::script('js/bootstrap3/js/bootstrap.js') !!}
             {!! HTML::script('js/pnotify/jquery.pnotify.js') !!}
             {!! HTML::script('js/bootstrap3/js/jquery.nicescroll.min.js') !!}
            {!! HTML::style('css/pnotify/jquery.pnotify.default.css') !!}
            {!! HTML::style('css/pnotify/jquery.pnotify.default.icons.css') !!}
            {!! HTML::style('css/bootstrap3/css/bootstrap.min.css') !!}
            {!! HTML::style('css/bootstrap3/css/font-awesome.min.css') !!}
            {!! HTML::style('css/bootstrap3/css/main.css') !!}
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

    <div class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse" >
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php // echo base_url()?>">ROX ATN</a>
            </div>
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li <?php //if($menu=='home'){ ?> class="active" <?php //}?>><a href="{!! URL::to('/') !!}">Home</a></li>
                    <li <?php //if($menu=='registration'){ ?>  <?php //}?>><a href="{!! URL::to('login/create-company') !!}">Registration</a></li>
                </ul>
            </div><!--/.nav-collapse -->
        </div>
    </div>
    <body>
    <div class="rc">
        <div class="container main">
                @yield('content')
        </div>
    </div>
    <section id="bottom" style="margin-top: 50px">
        <div class="container">
            <div class="bottom">
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-md-3 margin-btm">
                        <!--                            <h3>Umbro</h3>
                                                    <ul>
                                                        <li>Our Themes</li>
                                                        <li>About Us</li>
                                                        <li>Our Blog</li>
                                                    </ul>-->
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-3 margin-btm">
                        <!--<h3>Support</h3>-->
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-3 margin-btm">
                        <!--<h3>Partners</h3>-->
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-3 margin-btm">
                        <!--<h3>Newsletter</h3>-->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <footer id="footer">
        <div class="container">
            <div class="footer">
                <div class="row">
                    <div class="col-md-12">
                        <span>&copy; 2013 RoxCoder. All Rights Reserved.</span>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    </body>
    </html>