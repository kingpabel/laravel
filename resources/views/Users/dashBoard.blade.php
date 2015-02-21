@extends('Users/UserLayout')
@section('content')
<?php echo $welcome_message=Session::get('welcome_message'); ?>
@if ($welcome_message)
<div class="alert alert-info">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong>{{ $welcome_message }}</strong>
</div>
@endif
@if ($leaveUpdate)
<div class="alert alert-info">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <strong><a style="text-decoration:none; cursor:pointer" href="<?php // echo base_url()  ?>user/my_leave">Your's {{ $leaveUpdate }} Leave Application Updated</a></strong>
</div>
@endif

<div>
    <ul class="breadcrumb">
        <li>
            <a href=" {!! URL::to('user') !!}">Home</a> <span class="divider">/</span>
        </li>
        <li>
            <a href="{!! URL::to('user') !!}">Dashboard</a>
        </li>
    </ul>
</div>
<div class="row-fluid sortable">
    <div class="box span4">
        <div class="box-header well" data-original-title>
            <h2><i class="icon-list-alt"></i>  Punch In/Out</h2>
            <div class="box-icon">
                <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                <a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
            </div>
        </div>
        <div class="box-content">
            <?php
            if ($status == 'Punch Out')
                $punch_url = 'punch-out';
            else
                $punch_url = 'punch-in';
            ?>
                {!! link_to("user/$punch_url/","$status",array('class'=>'btn btn-large btn-success')) !!}
        </div>
    </div>
    <!--/span-->

    <div class="box span4">
        <div class="box-header well" data-original-title>
            <h2><i class="icon-user"></i> Member Activity</h2>
            <div class="box-icon">
                <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                <a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
            </div>
        </div>
        <div class="box-content">
            <div class="box-content">
                <script type="text/javascript" src="<?php // echo base_url();  ?>js/datepickr.js"></script>
                <link rel="stylesheet" href="<?php // echo base_url(); ?>design.css" />

                <div class="form-group span12">
                    <label for="datepick2" class="span2 control-label">Date</label>
                    <div class="span6">
                        <input type="text" readonly class="datepicker" name="first_date" value="<?php echo date('Y-m-d', time()); ?>">
                    </div>
                </div>
                <div class="form-group span12">
                    <label for="datepick4" class="span2 control-label">To</label>
                    <div class="span6">
                        <input type="text" readonly class="datepicker2" name="second_date" value="<?php echo date('Y-m-d', time()); ?>">
                    </div>
                </div>
                <div class="form-group span12">
                    <label for="datepick4" class="span2 control-label"></label>
                    <div class="span6">
                        <button onclick="window.open('<?php // echo base_url()  ?>user/show_report?s_date=' + datepick2.value + '&e_date=' + datepick4.value)" type="button" class="btn btn-primary">
                            My Report</button>
                    </div>
                </div>

            </div>
            <script type="text/javascript">
                new datepickr('datepick2', {
                    'dateFormat': 'Y-m-d'
                });
                new datepickr('datepick4', {
                    'dateFormat': 'Y-m-d'
                });

                new datepickr('datepick3', {
                    'fullCurrentMonth': false,
                    'dateFormat': 'l, F j'
                });
            </script>
        </div>
    </div>
</div><!--/span-->
<?php $punch_message_success=Session::get('punchMessageSuccess');; if ($punch_message_success) { ?>
<script type="text/javascript">
    $(document).ready(function() {
        $.pnotify({
            title: 'Message',
            text: '<?php echo $punch_message_success ?>',
            type: 'success',
            delay: 3000

        });
    });
</script>

<?php } ?>
<?php $punch_message_error=Session::get('punchMessageError'); if ($punch_message_error) { ?>
<script type="text/javascript">
    $(document).ready(function() {
        $.pnotify({
            title: 'Logout',
            text: '<?php echo $punch_message_error ?>',
            type: 'success',
            delay: 3000

        });
    });
</script>

<?php } ?>
@endsection

