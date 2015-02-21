@extends('Company.CompanyLayout')
@section('content')
<div>
    <ul class="breadcrumb">
        <li>
            <a href="{!! URL::to('company') !!}">Home</a> <span class="divider">/</span>
        </li>
        <li>
            <a href="{!! URL::to('company/all-user') !!}">Total User</a>
        </li>
    </ul>
</div>
<div class="row-fluid sortable">
    <div class="box span12">
        <div class="box-header well" data-original-title>
            <h2><i class="icon-user"></i> Users</h2>
            <div class="box-icon">
                <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                <a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
            </div>
        </div>
        <div class="box-content">
            <table class="table table-striped table-bordered bootstrap-datatable datatable">
                <thead>
                <tr>
                    <th colspan="3" style="background-color:#CCCCCC; color:#FF0000">
                        <span style="padding-top:5px"> Date Range&nbsp;&nbsp;</span><input type="text" readonly id="from" name="first_date" value="<?php echo date('Y-m-d', time());   ?>">&nbsp;&nbsp;<span style="color:black">To</span>&nbsp;&nbsp;
                        <input type="text" readonly id="to" name="second_date" value="<?php echo date('Y-m-d', time());   ?>">
                        <button onclick="window.open('company/summeryReport?s_date='+datepick2.value + '&e_date='+datepick4.value)"  type="button" class="btn btn-default">

                            Summery Report</button>
                    </th>
                </tr>
                </thead>
                <thead>
                <tr>
                    <th>Username</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($allUser as $user): ?>
                <tr>

                    <td>  {!! $user->username !!}</td>
                    <td class="center">
                        @if($user->status == 1)
                        <span class="label label-success">Active</span>
                        @else
                        <span class="label label-warning">Inactive</span>
                        @endif
                    </td>
                    <td class="center">
                        <button onclick="window.open('?s_date=' + from.value + '&e_date=' + to.value + '&id=' +<?php echo $user->id ?>)" type="button" class="btn btn-success">

                            <i class="icon-zoom-in icon-white"></i>Individual Report</button>

                        <a class="btn btn-info" style="text-decoration: none" href="">
                            <i class="icon-edit icon-white"></i>Update
                        </a>
                        <a class="btn btn-info" style="text-decoration: none" id="status_change_<?php echo $user->id ?>" href="#">
                            <span class="label <?php echo $user->status == 1 ? 'label-warning':'label-success'?>"><?php echo $user->status == 1 ? "Inactive" : 'Active'; ?></span>
                            <input type="hidden" name="status" id="status_<?php echo $user->id ?>" value="<?php echo $user->status == 1 ? 'inactive': 'active'?>"
                        </a>
                        @if($user->ip_address)
                        <a class="btn btn-info" style="text-decoration: none" href="">
                            <span class="label label-warning">Remove IP</span></a>
                        @else
                        <a class="btn btn-info" data-toggle="modal" href="#myReport_<?php echo $user->id; ?>" style="text-decoration: none" >
                            <span class="label label-success">Add IP</span></a>
                        @endif

                    </td>
                </tr>

                <div id="myReport_<?php echo $user->id; ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3>Add IP</h3>
                    </div>
                    <form class="form-horizontal" id="add_ip_<?php echo $user->id?>">
                        <div class="modal-body">
                            <fieldset>
                                <div class="control-group">
                                    <label class="control-label" for="user_first_name">IP Address</label>
                                    <div class="controls">
                                        <input type="text" required class="input-xlarge ipType number" id="ip_address" name="ip_address" placeholder="ip address">
                                        <input type="hidden" name="id" value="<?php echo $user->id?>">
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="modal-footer">
                            <a href="#" data-dismiss="modal" class="btn">Close</a>
                            <button  type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
                <script type="text/javascript">
                    $(document).ready(function() {
                        $("#add_ip_<?php echo $user->id?>").submit(function(event) {
                            event.preventDefault();
                            var values = $("#add_ip_<?php echo $user->id?>").serialize();
                            $.ajax({
                                url: "{!! URL::to('company/add-ip') !!}",
                                type: "POST",
                                data: values,
                                cache: false,
                                success: function(data) {
                                    if(data == 'true' ){
                                        $.pnotify({
                                            title: 'Message',
                                            text: 'IP address added successfully',
                                            type: 'success',
                                            delay: 3000
                                        });
                                    }else{
                                        $.pnotify({
                                            title: 'Message',
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
                        $("#status_change_<?php echo $user->id ?>").click(function(event) {
                            var values = 'status='+$('#status_<?php echo $user->id ?>').val();
                            alert(values);
                            $.ajax({
                                url: '{!! URL::to("company/status-change/$user->id") !!}',
                                type: "POST",
                                data: values,
                                cache: false,
                                success: function(data) {
                                    if(data=='true') {
                                        $.pnotify({
                                            title: 'Success',
                                            text: 'Updated Successfully',
                                            type: 'success',
                                            delay: 3000

                                        });
                                    }else{
                                        $.pnotify({
                                            title: 'Error',
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
                    $(function() {
                        $( "#from" ).datepicker({
                            dateFormat:'yy-mm-dd',
                            defaultDate: "+1w",
                            changeMonth: true,
                            numberOfMonths: 1,
                            onClose: function( selectedDate ) {
                                $( "#to" ).datepicker( "option", "minDate", selectedDate );
                            }
                        });
                        $( "#to" ).datepicker({
                            dateFormat:'yy-mm-dd',
                            defaultDate: "+1w",
                            changeMonth: true,
                            numberOfMonths: 1,
                            onClose: function( selectedDate ) {
                                $( "#from" ).datepicker( "option", "maxDate", selectedDate );
                            }
                        });
                    });
                </script>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div><!--/span-->

</div>
<?php //$total_user_message=$this->Flash->render('total_user_message'); if ($total_user_message) { ?>
<script type="text/javascript">
    $(document).ready(function() {
        $.pnotify({
            title: 'Message',
            text: '<?php //echo $total_user_message ?>',
            type: 'success',
            delay: 3000

        });
    });
</script>

<?php //} ?>
@endsection
@section('jsBottom')
       {!! HTML::script('js/inputmusk.js') !!}
    <script>
    $('.ipType').inputmask({
        mask: '999.999.999.999'
    })
</script>
@endsection
