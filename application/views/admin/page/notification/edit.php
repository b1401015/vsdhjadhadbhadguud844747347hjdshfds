<link rel="stylesheet" type="text/css" href="<?php echo public_url('admin'); ?>/plugins/dom-calendar/jquery.datetimepicker.css">
<link rel="stylesheet" type="text/css" href="<?php echo public_url('admin'); ?>/plugins/dom-calendar/jquery.timepicker.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo public_url('admin'); ?>/plugins/dom-calendar/jquery.periodpicker.min.css">

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Edit Notification
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?=base_url('/_admin/index')?>"><i class="fa fa-dashboard"></i>Home</a></li>
            <li> Edit Notification</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <form class="form-validate form-horizontal" action="" id="frm_edit" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="notification_id" name="notification_id" value="<?=$notification->_id ?>" />
                <!-- main form -->
                <div class="col-md-12">
                    <!-- general form elements disabled -->
                    <div class="box box-warning">
                        <div class="box-body">
                            <div class="panel-body">
                                <div class="col-xs-12">
                                    <div class="form-group text-right">
                                        <button id="btn_edit" name="btn_edit" type="button" class="btn btn-info"><i class="fa fa-edit"></i> Update</button>
                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div id="info" class="tab-pane">
                                        <br/>

                                        <!--                                        <div class="form-group ">-->
                                        <!--                                            <label for="show_news" class="control-label col-lg-2">Set time to send push</label>-->
                                        <!--                                            <div class="col-lg-2">-->
                                        <!--                                                <input type="checkbox" name="is_push_time" id="is_push_time">-->
                                        <!--                                            </div>-->
                                        <!--                                        </div>-->
                                        <div id="input_time" class="form-group">
                                            <label for="time" class="control-label col-lg-2">Time<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <input type="text" id="time" name="time" class="appointment-control datetime thoi-gian-giao col-xs-12 col-sm-4 col-md-4 form-control" value="<?=date('Y-m-d H:i', strtotime($notification->push_time)) ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="title" class="control-label col-lg-2">Title<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <input class="form-control" id="title" name="title" type="text" value="<?=$notification->_title ?>" required />
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="content" class="control-label col-lg-2">Content<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <textarea style="resize: none; overflow: hidden; word-wrap: break-word; height: 54px;" rows="3" class="form-control animated" id="content" name="content"><?=$notification->_content ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="type" class="control-label col-lg-2">Type<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <select class="form-control" name="type" id="type" _autocheck="true" required >
                                                    <option <?php if($notification->screen == EVENT_TYPE): ?> selected <?php endif; ?> value="event">Event</option>
                                                    <option <?php if($notification->screen == BLOG_TYPE): ?> selected <?php endif; ?> value="blog">Blog</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                </div><!--/.col (right) -->
            </form>
        </div>   <!-- /.row -->
    </section><!-- /.content -->
    <!-- / Main content -->
</div><!-- /.row -->

<script type="text/javascript" src="<?php echo public_url('admin'); ?>/plugins/dom-calendar/jquery.datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo public_url('admin'); ?>/plugins/dom-calendar/jquery.timepicker.min.js"></script>
<script type="text/javascript" src="<?php echo public_url('admin'); ?>/plugins/dom-calendar/jquery.periodpicker.full.min.js"></script>

<!-- Add new product JS -->
<script type="text/javascript" src="<?php echo public_url('admin'); ?>/console/notification/edit.js"></script>
