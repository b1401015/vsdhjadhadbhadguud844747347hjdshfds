<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Add translation
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?=base_url('/_admin/index') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Add translation</a></li>
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
                                    <div id="keyword" class="tab-pane">
                                        <br/>
                                        <div class="form-group ">
                                            <label for="keyword" class="control-label col-lg-2">Keyword</label>
                                            <div class="col-lg-5">
                                                <input class="form-control" id="keyword" name="keyword" type="text" value="" required />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="language_id" class="control-label col-lg-2">Language<span class="required">*</span></label>
                                            <div class="col-lg-5">
                                                <?php if(!empty($languages)): ?>
                                                <select class="form-control" name="language_id" id="language_id" _autocheck="true" required >
                                                    <?php foreach($languages as $language): ?>
                                                        <option value="<?=$language->_id ?>"><?=$language->name ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="_content" class="control-label col-lg-2">Content</label>
                                            <div class="col-lg-5">
                                                <textarea style="resize: none; overflow: hidden; word-wrap: break-word; height: 54px;" rows="3" class="form-control animated" id="_content" name="_content" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-xs-12">
                                    <div class="form-group text-right">
                                        <button id="btn_add" name="btn_add" type="button" class="btn btn-primary"><i class="fa fa-plus"></i> Thêm</button> &nbsp;&nbsp;
                                        <button type="button" class="btn btn-danger" onclick="return window.history.back();"><i class="fa fa-rotate-left"></i> Quay lại DS</button>
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
<script type="text/javascript" src="<?php echo public_url('admin'); ?>/console/translation/add.js"></script>