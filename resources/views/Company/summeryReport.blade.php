@extends('Company.CompanyLayout')
@section('content')


    <div>
        <ul class="breadcrumb">
            <li>
                <a href="<?php //echo $this->Url->build(array('controller' => 'users', 'action' => 'userInfo'), true); ?>">Home</a> <span class="divider">/</span>
            </li>
            <!--<li>
            <a href="<?php /*echo $this->Url->build(array('controller' => 'users', 'action' => 'myLeave'), true); */?>">Individual Leave</a>
        </li>-->
        </ul>
    </div>
    <div class="row-fluid sortable">
        <div class="box span12">
            <div class="box-header well" data-original-title>
                <h2><i class="icon-user"></i>User Summery Report <?php echo $startDate.' to '.$endDate ?></h2>
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
                        <td>
                            Date
                        </td>
                        <td>
                            In Time
                        </td>
                        <td>
                            Out Time
                        </td>
                        <td>
                            Working Time
                        </td>
                        <td>
                            Status
                        </td>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div><!--/span-->
    </div>
    @endsection