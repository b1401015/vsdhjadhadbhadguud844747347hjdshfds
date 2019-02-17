
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Send push
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?=base_url('/_admin/index')?>"><i class="fa fa-dashboard"></i>Home</a></li>
            <li> Send push</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <form class="form-validate form-horizontal" action="" id="frm_add" method="POST" enctype="multipart/form-data">
                <!-- main form -->
                <div class="col-md-12">
                    <!-- general form elements disabled -->
                    <div class="box box-warning">
                        <div class="box-body">
                            <div class="panel-body">
                                <div class="col-xs-12">
                                    <div class="form-group text-right">
                                        <button class="btn btn-primary"  id="btn_add" data-loading-text="<i class='fa fa-circle-o-notch fa-spin'></i> Processing" type="button">Send</button>
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
                                        <div class="form-group ">
                                            <label for="title" class="control-label col-lg-2">Title<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <input class="form-control" id="title" name="title" type="text" required />
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="content" class="control-label col-lg-2">Content<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <textarea style="resize: none; overflow: hidden; word-wrap: break-word; height: 54px;" rows="3" class="form-control animated" id="content" name="content"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="type" class="control-label col-lg-2">Type<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <select class="form-control" name="type" id="type" _autocheck="true" required >
                                                    <option selected value="event">Event</option>
                                                    <option value="blog">Blog</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="country_field" class="form-group">
                                            <label for="country_id" class="control-label col-lg-2">User<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <select class="form-control select2" name="account_id[]" id="account_id" _autocheck="true" multiple="multiple" required >
                                                    <option selected value="all">All users</option>
                                                    <?php foreach($accounts as $account): ?>
                                                        <option value="<?=$account->_id ?>"><?='[ID: '. $account->_id .'] '. $account->fullname ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div id="input_time" class="form-group">
                                            <label for="time" class="control-label col-lg-2">Time</label>
                                            <div class="col-lg-5">
                                                <input type="text" id="time" name="time" class="appointment-control datetime thoi-gian-giao col-xs-12 col-sm-4 col-md-4 form-control">
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

<!-- Add new product JS -->
<script type="text/javascript" src="<?php echo public_url('admin'); ?>/console/notification/send_push.js"></script>
