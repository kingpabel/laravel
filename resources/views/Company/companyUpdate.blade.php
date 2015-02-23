@extends('Company.CompanyLayout')
@section('content')
    <div>
        <ul class="breadcrumb">
            <li>
                <a href="{!! URL::to('company') !!}">Home</a> <span class="divider">/</span>
            </li>
            <li>
                <a href=''{!! URL::to("company/user-update/$user->id") !!}'>Update Info</a>
            </li>

        </ul>
    </div>
    <div class="row-fluid sortable">
        <div class="box span12">
            <div class="box-header well" data-original-title>
                <h2><i class="icon-edit"></i> Update Info</h2>
                <div class="box-icon">
                    <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                    <a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
                </div>
            </div>
            <div class="box-content">
                <form class="form-horizontal" id="company_creation" method="post" action="<?php //echo $this->Url->build(array('controller' => 'admin', 'action' => 'updateInfo'), true); ?>">
                    <fieldset>
                        <div class="control-group">
                            <label class="control-label" for="company_name">Company Name</label>
                            <div class="controls">
                                <input type="text" required class="input-xlarge input" name="company_name" id="company_name" placeholder="company name" value="<?php //echo $info->company_name?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="company_user_name">Company User Name</label>
                            <div class="controls">
                                <input type="text" required class="input-xlarge input" id="company_user_name" name="company_user_name" placeholder="company user name" value="<?php// echo $info->company_user_name?>">
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="company_email">Company Email</label>
                            <div class="controls">
                                <input type="email" required class="input-xlarge" id="company_email" name="company_email" placeholder="company Email" value="<?php// echo $info->company_email?>">
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-success">Update</button>
                            <button type="reset" class="btn">Cancel</button>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div><!--/span-->
    </div>
@endsection