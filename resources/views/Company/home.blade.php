@extends('Company/CompanyLayout')
@section('content')

    @if ($activeUser->count())
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong><?php $aU=$activeUser->count();
            foreach($activeUser as $userActive):
            $aU=$aU-1;
            ?>
            <a target="blank" style="text-decoration:none;cursor:pointer"  href="{!! URL::to('company/report') !!}?s_date=<?php echo date('Y-m-d', time()) ?>&e_date=<?php echo date('Y-m-d') ?>&id=<?php echo $userActive->user_id ?>">
                {{ @$userActive->User->username }}
                @if ($aU != 0) ,
                @endif
            </a>
            <?php
            endforeach; ?>
            @if ($activeUser->count() > 1)
                are
            @else
                is
            @endif
            present today</strong>
    </div>


    @endif
    <?php
    if($lateUser->count()){
    ?>
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong><?php $lU=$lateUser->count();
            foreach($lateUser as $userLate):
           $lU=$lU-1;
            ?>
            <a target="blank" style="text-decoration:none;cursor:pointer"  href="{!! URL::to('company/report') !!}?s_date=<?php echo date('Y-m-d') ?>&e_date=<?php echo date('Y-m-d') ?>&id=<?php echo $userLate->user_id ?>">
                <?php echo $userLate->User->username;
                if($lU != 0) echo ',';?>
            </a>
            <?php
            endforeach;
            if($lateUser->count() > 1)
                echo 'are';
            else echo 'is';
            ?> late today</strong>
    </div>
    <?php } if($totalUser - $activeUser->count()) { ?>
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>
            <?php
            $aU = $totalUser - $activeUser->count();
            if ($aU == $totalUser) echo 'No';
            else echo $aU;
            ?> users <?php
            if ($aU > 1)
                echo 'are';
            else echo 'is';
            ?> not present yet</strong>
    </div>
    <?php
    }
    if($withLeaveNotification){
    ?>
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong><a style="text-decoration:none; cursor:pointer" href="{!! URL::to('company/all-leave') !!}">You Have <?php echo $withLeaveNotification?> day's Leave Request</a></strong>
    </div>
    <?php }?>
    <div>
        <ul class="breadcrumb">
            <li>
                <a href="{!! URL::to('company') !!}">Home</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="{!! URL::to('company') !!}">Dashboard</a>
            </li>
        </ul>
    </div>

    <div class="row-fluid">
        <div class="box span12">
            <div class="box-header well">
                <h2><i class="icon-info-sign"></i> Introduction</h2>
                <div class="box-icon">
                    <a href="#" class="btn btn-setting btn-round"><i class="icon-cog"></i></a>
                    <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                    <a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
                </div>
            </div>
            <div class="box-content">
                <h1>Welcome to {{ @Auth::user()->Company->company_name }} dashboard </h1>

                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="box span12">
            <div class="box-header well">
                <h2><i class="icon-info-sign"></i>Hours Worked Today</h2>
                <div class="box-icon">
                    <a href="#" class="btn btn-setting btn-round"><i class="icon-cog"></i></a>
                    <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                    <a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
                </div>
            </div>
            <div class="box-content">
                <div id="piechart"></div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>

    <?php $message_error=Session::get('flashError'); if ($message_error) { ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $.pnotify({
                title: 'Error',
                text: '<?php echo $message_error ?>',
                type: 'error',
                delay: 3000

            });
        });
    </script>

    <?php } ?>
    <?php $message_success=Session::get('flashSuccess');; if ($message_success) { ?>
    <script type="text/javascript">
        $(document).ready(function() {
            $.pnotify({
                title: 'Message',
                text: '<?php echo $message_success ?>',
                type: 'success',
                delay: 3000

            });
        });
    </script>

    <?php } ?>
    <?php
    $totalHours = 0;
    $totalMinutes = 0;
    $totalSeconds = 0;
    $reports = array();
    foreach($attendanceReport as $report){
        if (!isset($reports[$report->user_id])){
            $totalHours = 0;
            $totalMinutes = 0;
            $totalSeconds = 0;
        }
        $reports[$report->user_id]['id'] = $report->id;
        $reports[$report->user_id]['user_id'] = $report->user_id;
        $reports[$report->user_id]['username'] = $report->User->username;
        $reports[$report->user_id]['time'] = explode(":", $report->timediff);;
        $reports[$report->user_id]['workingHours'] = ($totalHours = $totalHours +  $reports[$report->user_id]['time'][0]);
        $reports[$report->user_id]['workingMinutes'] = ($totalMinutes = $totalMinutes +  $reports[$report->user_id]['time'][1]);
        $reports[$report->user_id]['workingSeconds'] = ($totalSeconds = $totalSeconds +  $reports[$report->user_id]['time'][2]);
    }
    ?>
@endsection
@section('jsBottom')
    <script type="text/javascript">
        google.load("visualization", "1", {packages:["corechart"]});
        google.setOnLoadCallback(drawChart);
        function drawChart() {

            var data = google.visualization.arrayToDataTable([
                ['Task', 'Hours per Day'],
                    <?php foreach($reports as $report){
                     $hours = $report['workingHours'];
                        $minutes = $report['workingMinutes'];
                        $seconds = $report['workingSeconds'];
                        if($report['workingMinutes']/60){
                        $hours = intval($hours + $report['workingMinutes']/60);
                        $minutes=intval($report['workingMinutes']%60);
                        }
                        if($report['workingSeconds']/60){
                        $minutes = intval($minutes + $report['workingSeconds']/60);
                        $seconds=intval($report['workingSeconds']%60);
                        }
                        if(intval($hours) < 10)
                        $hours = intval($hours);
                    ?>

                ["<?php echo $report['username'].'('.$hours. 'hours)'?>",     <?php echo $hours?>],
                <?php }?>
            ]);

            var options = {
                title: 'Daily Activities of All User'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart'));

            chart.draw(data, options);
        }
    </script>
    @endsection