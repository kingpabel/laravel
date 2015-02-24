@extends('Company.CompanyLayout')
@section('content')
    <div>
        <ul class="breadcrumb">
            <li>
                <a href="{!! URL::to('company') !!}">Home</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="{!! URL::to('company/all-leave') !!}">All Leave</a>
            </li>
        </ul>
    </div>
    <div class="row-fluid sortable">
        <div class="box span12">
            <div class="box-header well" data-original-title>
                <h2><i class="icon-user"></i> Leave List</h2>
                <div class="box-icon">
                    <a href="#" class="btn btn-setting btn-round"><i class="icon-cog"></i></a>
                    <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                    <a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
                </div>
            </div>
            <div class="box-content">
                <table id="example" class="display" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Username</th>
                        <th>Leave Date</th>
                        <th>Leave Catagory</th>
                        <th>Leave Reason</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if($allLeave){
                    foreach ($allLeave as $leave):
                    ?>
                    <tr>
                        <td><?php echo $leave->User->username?></td>
                        <td class="center"><?php echo $leave->leave_date?></td>
                        <td class="center"><?php echo $leave->LeaveCategories->category?></td>
                        <td class="center"><?php echo $leave->leave_cause?></td>
                        <td class="center">
                            <?php
                            if($leave->leave_status==0){ ?>
                            <span class="label label-warning">Pending</span>
                            <?php   }
                            elseif($leave->leave_status==1){
                            ?>
                            <span class="label label-success">Granted</span>
                            <?php } else{?>
                            <span class="label label-important">Rejected</span>
                            <?php } ?>
                        </td>
                        <td class="center">
                            <?php
                            if($leave->leave_status == 0){ ?>
                            <a class="btn btn-success" id="grant_<?php echo $leave->leave_id ?>" >
                                <i class="icon-zoom-in icon-white"></i>Grant</a>
                            <a class="btn btn-danger" id="reject_<?php echo $leave->leave_id ?>" >
                                <i class="icon-trash icon-white"></i>Reject</a>
                            <?php   }
                            elseif($leave->leave_status==1){
                            ?>
                            <a class="btn btn-danger" id="reject_<?php echo $leave->leave_id ?>"  >
                                <i class="icon-trash icon-white"></i> Reject</a>
                            <?php } else{?>
                            <a class="btn btn-success" id="grant_<?php echo $leave->leave_id ?>" >
                                <i class="icon-zoom-in icon-white"></i>Grant</a>
                            <?php } ?>
                            <a class="btn btn-danger" id="delete_<?php echo $leave->leave_id ?>">
                                <i class="icon-trash icon-white"></i>
                                Delete
                            </a>
                        </td>
                    </tr>
                    <script type="text/javascript">
                        $(document).ready(function() {
                            $("#delete_<?php echo $leave->leave_id ?>").click(function(event) {
                                event.preventDefault();
                                var values = 'status='+'delete';
                                var chk = confirm("Are you sure to delete this?");
                                if (chk)
                                {
                                    $.ajax({
                                        url: "<?php //echo $this->Url->build(array('controller' => 'admin', 'action' => 'statusUpdate',$leave->leave_id), true); ?>",
                                        type: "POST",
                                        data: values,
                                        cache: false,
                                        success: function(data) {
                                            if(data == true ){
                                                $.pnotify({
                                                    title: 'Message',
                                                    text: 'Status Deleted.To see current view please refresh the page',
                                                    type: 'success',
                                                    delay: 3000
                                                });
                                            }else{
                                                $.pnotify({
                                                    title: 'ERROR',
                                                    text: data,
                                                    type: 'error',
                                                    delay: 3000
                                                });
                                            }
                                        }
                                    });
                                }
                            });
                        });
                    </script>
                    <script type="text/javascript">
                        $(document).ready(function() {
                            $("#grant_<?php echo $leave->leave_id ?>").click(function(event) {
                                event.preventDefault();
                                var values = 'status='+'grant';
                                $.ajax({
                                    url: "<?php //echo $this->Url->build(array('controller' => 'admin', 'action' => 'statusUpdate',$leave->leave_id), true); ?>",
                                    type: "POST",
                                    data: values,
                                    success: function(data) {
                                        if(data == true ){
                                            $.pnotify({
                                                title: 'Message',
                                                text: 'Status Changed.To see current view please refresh the page',
                                                type: 'success',
                                                delay: 3000
                                            });
                                        }else{
                                            $.pnotify({
                                                title: 'ERROR',
                                                text: data,
                                                type: 'error',
                                                delay: 3000
                                            });
                                        }
                                    }
                                });

                            });
                        });
                    </script>
                    <script type="text/javascript">
                        $(document).ready(function() {
                            $("#reject_<?php echo $leave->leave_id ?>").click(function(event) {
                                event.preventDefault();
                                var values = 'status='+'reject';
                                $.ajax({
                                    url: "<?php //echo $this->Url->build(array('controller' => 'admin', 'action' => 'statusUpdate',$leave->leave_id), true); ?>",
                                    type: "POST",
                                    data: values,
                                    success: function(data) {
                                        if(data == true ){
                                            $.pnotify({
                                                title: 'Message',
                                                text: 'Status Changed.To see current view please refresh the page',
                                                type: 'success',
                                                delay: 3000
                                            });
                                        }else{
                                            $.pnotify({
                                                title: 'ERROR',
                                                text: data,
                                                type: 'error',
                                                delay: 3000
                                            });
                                        }
                                    }
                                });

                            });
                        });
                    </script>
                    <?php endforeach;
                    } else{ ?>
                    <tr>
                        <td>No data are available</td>
                        <td>No data are available</td>
                        <td>No data are available</td>
                        <td>No data are available</td>
                        <td>No data are available</td>
                        <td>No data are available</td>
                    </tr>
                    <?php   }
                    ?>
                    </tbody>
                </table>
            </div>
        </div><!--/span-->

    </div>

@endsection
@section('jsBottom')
    {!! HTML::script('js/jquery.dataTables.js') !!}
    {!! HTML::script('js/dataTables.tableTools.js') !!}
    {!! HTML::style('css/jquery.dataTables.css') !!}
    {!! HTML::style('css/dataTables.tableTools.css') !!}
    <script type="text/javascript" language="javascript" class="init">
        $(document).ready(function() {
            $('#example').DataTable( {
                dom: 'T<"clear">lfrtip'
            } );
        } );
    </script>
@endsection