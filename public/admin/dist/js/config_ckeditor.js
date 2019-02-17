/*
* Author: VietNgo
* Created date: 20170722
*/

function ckeditor (name) {

    CKEDITOR.config.extraPlugins = 'filebrowser';
    CKEDITOR.config.uiColor = '#9AB8F3';
    CKEDITOR.config.language = 'vi';
    CKEDITOR.config.fillEmptyBlocks = false;

    CKEDITOR.replace( name, {
        filebrowserBrowseUrl : '/public/admin/plugins/ckfinder/ckfinder.html',
        filebrowserImageBrowseUrl : '/public/admin/plugins/ckfinder/ckfinder.html?type=Images',
        filebrowserFlashBrowseUrl : '/public/admin/plugins/ckfinder/ckfinder.html?type=Flash',
        filebrowserUploadUrl : '/public/admin/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
        filebrowserImageUploadUrl : '/public/admin/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images',
        filebrowserFlashUploadUrl : '/public/admin/plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash',
        toolbar:[
            ['Source','-','Save','NewPage','Preview','-','Templates'],
            ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print'],
            ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
            ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'HiddenField'],
            ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
            ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
            ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
            ['Link','Unlink','Anchor'],
            ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
            ['Styles','Format','Font','FontSize'],
            ['TextColor','BGColor'],
            ['Maximize', 'ShowBlocks','-','About']
        ]
    });
}