<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Edit translation
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?=base_url('/_admin/index') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Edit translation</a></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <form class="form-validate form-horizontal" action="" id="frm_edit" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="translation_id" value="<?=$translation->_id ?>" />
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
                                                <input class="form-control" id="keyword" name="keyword" type="text" value="<?=$translation->keyword ?>" required />
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
                                                <textarea style="resize: none; overflow: hidden; word-wrap: break-word; height: 54px;" rows="3" class="form-control animated" id="_content" name="_content" required><?=$translation->_content ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-xs-12">
                                    <div class="form-group text-right">
                                        <button id="btn_edit" name="btn_edit" type="button" class="btn btn-info"><i class="fa fa-edit"></i> Cập nhật</button> &nbsp;&nbsp;
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
<script type="text/javascript" src="<?php echo public_url('admin'); ?>/console/translation/edit.js"></script>