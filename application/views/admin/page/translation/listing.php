<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Translation list
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?= base_url('_admin/index') ?>"><i class="fa fa-dashboard"></i>Home</a></li>
            <li class="active">Translation</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">

                    <div class="hide" id="upload_container">
                        <div>
                            Sample file <a download href="<?=public_url(). SAMPLE_TRANSLATION_FILE ?>"><?=strtolower(TRANSLATION_ALIAS_EN) ?>_sample.xlsx</a>
                        </div>
                        <div>
                            <strong>Upload list <?=strtolower(TRANSLATIONS_ALIAS) ?> (Excel):</strong>
                        </div>
                        <form id="frm_upload" method="post" action="/api/admin/world_word/import-wword-category" enctype="multipart/form-data">
                            <input type="file" name="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/>
                        </form><br/>
                        <button type="button" class="btn btn-primary" onclick="upload_excel_file();">Upload</button>
                        <span class="hide" id="txt_upload_status"></span>
                        <hr/>
                    </div> <!-- .upload-container -->

                    <div class="box-header">
                        <a href="<?= base_url('_admin/translation/add') ?>">
                            <button type="button" class="btn btn-info pull-left">
                                Thêm mới
                            </button>
                        </a>
                        <!-- Upload excel file-->
<!--                        <button onclick="toggle_frm_upload();" style="margin-left: 10px" type="button" class="btn btn-info pull-left">-->
<!--                            Tải lên file Excel-->
<!--                        </button>-->
                    </div>
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Keyword</th>
                                <th>Ngôn ngữ</th>
                                <th>Nội dung</th>
                                <th>Ngày tạo</th>
                                <th>Ngày chỉnh sửa</th>
                                <th>Công cụ</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->
    </section><!-- /.section -->
</div><!-- /.content -->

<script type="text/javascript" src="<?php echo public_url('admin'); ?>/console/translation/list.js"></script>