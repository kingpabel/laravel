@extends('Company.CompanyLayout')
@section('content')
    <div>
        <ul class="breadcrumb">
            <li>
                <a href="{!! URL::to('Company') !!}">Home</a> <span class="divider">/</span>
            </li>
            <li>
                <a href="{!! URL::to('Company/leave-category') !!}">Leave Catagory</a>
            </li>
        </ul>
    </div>

    <div class="row-fluid sortable">
        <div class="box span12">
            <div class="box-header well" data-original-title>
                <h2><i class="icon-edit"></i> Create Category</h2>
                <div class="box-icon">
                    <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                    <a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
                </div>
            </div>
            <div class="box-content">
                    {!! Form::open(array('id' => 'category', 'accept-charset' => 'utf-8', 'class' => 'form-horizontal', 'url' => 'company/leave-category')) !!}
                    <fieldset>
                        <div class="control-group">
                            <label class="control-label" for="catagory">Category Name</label>
                            <div class="controls">
                                <input type="text" required class="input-xlarge input" name="category" id="category_name" placeholder="Catagory Name">
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label" for="catagory_num">Maximum in a Year</label>
                            <div class="controls">
                                <input type="text" required class="input-xlarge number" name="category_num" id="category_num" placeholder="Maximum in a Year">
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-success">Create</button>
                            <button type="reset" class="btn">Cancel</button>
                        </div>
                    </fieldset>
                </form>

            </div>
        </div><!--/span-->

    </div>


    <div class="row-fluid sortable">
        <div class="box span12">
            <div class="box-header well" data-original-title>
                <h2><i class="icon-user"></i> Category List</h2>
                <div class="box-icon">
                    <a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
                    <a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
                </div>
            </div>
            <div class="box-content" id="ajax_table">
                <table class="table table-striped table-bordered bootstrap-datatable datatable">
                    <thead>
                    <tr>
                        <th>SL</th>
                        <th>Category</th>
                        <th>Maximum Number in Year</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody id="categoryAjax">
                    <?php
                    if($allCategory){
                    foreach($allCategory as $key=>$category): ?>
                    <tr class="list" id="row_<?php echo $category->id ?>">
                        <td><?php echo $key+1?></td>
                        <td class="center"><?php echo $category->category?></td>
                        <td class="center"><?php echo $category->category_num?></td>
                        <td class="center">
                            <a class="btn btn-danger" id="delete_<?php echo $category->id ?>" >
                                <i class="icon-white icon-trash"></i>Delete</a>
                        </td>
                    </tr>
                    <script type="text/javascript">
                        $(document).ready(function() {
                            $("#delete_<?php echo $category->id ?>").click(function(event) {
                                event.preventDefault();
                                var values = ' ';
                                var chk = confirm("Are you sure to delete this?");
                                if (chk)
                                {
                                    $.ajax({
                                        url: '{!! URL::to("company/delete-leave-category/$category->id") !!}',
                                        type: "GET",
                                        data: values,
                                        cache: false,
                                        success: function(data) {
                                            if(data == 'true' ){
                                                $("#row_<?php echo $category->id ?>").hide();
                                                $.pnotify({
                                                    title: 'Success',
                                                    text: 'Leave Category Deleted',
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
                    <?php endforeach;
                    }else{?>
                    <tr>
                        <td>
                            No data are availables
                        </td>
                        <td>
                            No data are availables
                        </td>
                        <td>
                            No data are availables
                        </td>
                        <td>
                            No data are availables
                        </td>
                    </tr>
                    <?php   }
                    ?>
                    </tbody>
                </table>
            </div>
        </div><!--/span-->

    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $("#category").submit(function(event) {
                event.preventDefault();
                var values = $("#category").serialize();
                $.ajax({
                    url: "{!! URL::to('company/leave-category') !!}",
                    type: "POST",
                    data: values,
                    dataType: "json",
                    cache: false,
                    success: function(data) {
                        if(data.type == 'success' ){
                            $.pnotify({
                                title: 'Message',
                                text: 'Leave Category Created Successfully',
                                type: 'success',
                                delay: 3000
                            });
                            $("#categoryAjax").html(data.info);
                        }else{
                            $.pnotify({
                                title: 'ERROR',
                                text: data.info,
                                type: 'error',
                                delay: 3000
                            });
                        }
                    }
                });
            });
        });
    </script>


@endsection