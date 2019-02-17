<!-- blueimp Gallery styles -->
<link rel="stylesheet" href="//blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="<?php echo public_url('admin'); ?>/plugins/jQuery-File-Upload/css/jquery.fileupload.css">
<link rel="stylesheet" href="<?php echo public_url('admin'); ?>/plugins/jQuery-File-Upload/css/jquery.fileupload-ui.css">
<!-- CSS adjustments for browsers with JavaScript disabled -->
<noscript><link rel="stylesheet" href="<?php echo public_url('admin'); ?>/plugins/jQuery-File-Upload/css/jquery.fileupload-noscript.css"></noscript>
<noscript><link rel="stylesheet" href="<?php echo public_url('admin'); ?>/plugins/jQuery-File-Upload/css/jquery.fileupload-ui-noscript.css"></noscript>

<style type="text/css">
    .page-footer{
        display: none;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Thêm ảnh sản phẩm
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Forms</a></li>
            <li class="active">General Elements</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="container">
                    <!-- The file upload form used as target for the file upload widget -->
                    <form id="fileupload" action="//jquery-file-upload.appspot.com/" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="record_id" name="record_id" value="<?=$record->_id ?>" />
                        <!-- Redirect browsers with JavaScript disabled to the origin page -->
                        <noscript><input type="hidden" name="redirect" value="https://blueimp.github.io/jQuery-File-Upload/"></noscript>
                        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                        <div class="row fileupload-buttonbar">
                            <div class="col-lg-7">
                                <!-- The fileinput-button span is used to style the file input field as button -->
                                <span class="btn btn-success fileinput-button">
                                <i class="glyphicon glyphicon-plus"></i>
                                <span>Thêm file...</span>
                                <input type="file" name="files[]" multiple>
                            </span>
                                <button type="submit" class="btn btn-primary start">
                                    <i class="glyphicon glyphicon-upload"></i>
                                    <span>Bắt đầu tải</span>
                                </button>
                                <button type="reset" class="btn btn-warning cancel">
                                    <i class="glyphicon glyphicon-ban-circle"></i>
                                    <span>Hủy</span>
                                </button>
                                <!-- The global file processing state -->
                                <span class="fileupload-process"></span>
                            </div>
                            <!-- The global progress state -->
                            <div class="col-lg-5 fileupload-progress fade">
                                <!-- The global progress bar -->
                                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                                </div>
                                <!-- The extended global progress state -->
                                <div class="progress-extended">&nbsp;</div>
                            </div>
                        </div>
                        <!-- The table listing the files available for upload/download -->
                        <table role="presentation" class="table table-striped">
                            <tbody class="files">
                            <?php if(!empty($record_files)): ?>
                            <?php foreach($record_files as $record_file): ?>
                            <tr class="template-download fade in">
                                <td>
                                        <span class="preview">
                                            <a href="<?=$record_file->file_src ?>" title="<?=$record_file->file_name ?>" download="<?=$record_file->file_name ?>" data-gallery="">
                                                <img width="100" src="<?=$record_file->thumb_file_src ?>">
                                            </a>
                                         </span>
                                </td>
                                <td>
                                    <p class="name">
                                        <a href="<?=$record_file->file_src ?>" title="<?=$record_file->file_name ?>" download="<?=$record_file->file_name ?>" data-gallery=""><?=$record_file->file_name ?></a>
                                    </p>
                                </td>
                                <td>
                                        <span class="size">
                                            <?php
                                                $bytes = !empty($record_file->file_size) ? $record_file->file_size : 0;
                                                $kilobyte = 1024;
                                                $megabyte = $kilobyte * 1024;
                                                $gigabyte = $megabyte * 1024;

                                                if($bytes < $kilobyte){
                                                    echo $bytes .' B';
                                                }
                                                else if($bytes < $kilobyte){
                                                    echo number_format(($bytes / $kilobyte), 2, ',', '.') .' KB';
                                                }
                                                else if($bytes < $megabyte){
                                                    echo number_format(($bytes / $megabyte), 2, ',', '.') .' MB';
                                                }
                                                else if($bytes < $gigabyte){
                                                    echo number_format(($bytes / $gigabyte), 2, ',', '.') .' GB';
                                                }
                                                else if($bytes < $megabyte){
                                                    echo number_format(($bytes / $megabyte), 2, ',', '.') .' MB';
                                                }
                                            ?>
                                        </span>
                                </td>
                                <td>
                                    <button class="btn btn-danger delete photo-delete" data-id="<?=$record_file->_id ?>">
                                        <i class="glyphicon glyphicon-trash"></i>
                                        <span>Xóa</span>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </form>
                    <br>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Chú ý</h3>
                        </div>
                        <div class="panel-body">
                            <ul>
                                <li>Kích thước file tối đa là <strong>5 MB</strong>.</li>
                                <li>Tối đa <strong>10</strong> file.</li>
                                <li>Chỉ file hình ảnh(<strong>JPG, JPEG, PNG</strong>) được cho phép tải lên.</li>
                                <li>File sẽ tự động bị xóa sau <strong>5 phút</strong> nếu không nhấn tải lên.</li>
                                <li>Bạn có thế <strong>kéo &amp; thả</strong> các file từ máy tính vào trang này (xem thêm <a target="_blank" href="https://github.com/blueimp/jQuery-File-Upload/wiki/Browser-support">các trình duyệt hỗ trợ</a>).</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- The blueimp Gallery widget -->
                <div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
                    <div class="slides"></div>
                    <h3 class="title"></h3>
                    <a class="prev">‹</a>
                    <a class="next">›</a>
                    <a class="close">×</a>
                    <a class="play-pause"></a>
                    <ol class="indicator"></ol>
                </div>

            </div>
        </div>   <!-- /.row -->
    </section><!-- /.content -->
    <!-- / Main content -->
</div><!-- /.row -->


<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled>
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">

</script




<!-- jQuery File Upload Plugin -->
<!-- ========================= -->

<script src="//blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="//blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="//blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
<!-- blueimp Gallery script -->
<script src="//blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>

<script src="<?php echo public_url('admin'); ?>/dist/js/jquery.ui.widget.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="<?php echo public_url('admin'); ?>/plugins/jQuery-File-Upload/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="<?php echo public_url('admin'); ?>/plugins/jQuery-File-Upload/js/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="<?php echo public_url('admin'); ?>/plugins/jQuery-File-Upload/js/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="<?php echo public_url('admin'); ?>/plugins/jQuery-File-Upload/js/jquery.fileupload-image.js"></script>
<!-- The File Upload audio preview plugin -->
<script src="<?php echo public_url('admin'); ?>/plugins/jQuery-File-Upload/js/jquery.fileupload-audio.js"></script>
<!-- The File Upload video preview plugin -->
<script src="<?php echo public_url('admin'); ?>/plugins/jQuery-File-Upload/js/jquery.fileupload-video.js"></script>
<!-- The File Upload validation plugin -->
<script src="<?php echo public_url('admin'); ?>/plugins/jQuery-File-Upload/js/jquery.fileupload-validate.js"></script>
<!-- The File Upload user interface plugin -->
<script src="<?php echo public_url('admin'); ?>/plugins/jQuery-File-Upload/js/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
<script src="<?php echo public_url('admin'); ?>/plugins/jQuery-File-Upload/js/main_photo.js"></script>

<!-- Add new product JS -->
<script type="text/javascript" src="<?php echo public_url('admin'); ?>/console/record/gallery.js"></script>