@extends('Company/CompanyLayout')
@section('content')

    @if ($activeUser)
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong><?php $aU=$activeUser->count();
            foreach($activeUser as $userActive):
            $aU=$aU-1;
            ?>
            <a target="blank" style="text-decoration:none;cursor:pointer"  href="<?php //echo base_url() ?>company/show_report?s_date=<?php //echo date('Y-m-d', time()) ?>&e_date=<?php //echo date('Y-m-d', time()) ?>&user_id=<?php //echo $user_active->user_id ?>">
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
    if($lateUser){
    ?>
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong><?php $lU=$lateUser->count();
            foreach($lateUser as $userLate):
           $lU=$lU-1;
            ?>
            <a target="blank" style="text-decoration:none;cursor:pointer"  href="<?php //echo base_url() ?>company/show_report?s_date=<?php //echo $date ?>&e_date=<?php //echo $date ?>&user_id=<?php //echo $user_late->user_id ?>">
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
        <strong><a style="text-decoration:none; cursor:pointer" href="<?php //echo base_url()?>company/all_leave">You Have <?php echo $withLeaveNotification?> day's Leave Request</a></strong>
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

@endsection