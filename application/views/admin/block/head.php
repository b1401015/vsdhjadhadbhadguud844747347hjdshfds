<title><?=ucfirst(APP_TITLE) ?></title>
<?php require_once(APPPATH. 'views/admin/block/head_common.php'); ?>
<!-- AdminLTE Skins. Choose a skin from the css/skins
     folder instead of downloading all of them to reduce the load. -->
<link href="<?php echo public_url('admin');?>/dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

<!-- jQuery 2.1.4 -->
<script src="<?php echo public_url('admin');?>/plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo public_url('admin');?>/bootstrap/js/validator/bootstrapValidator.js"></script>
<script type="text/javascript" src="<?php echo public_url('admin');?>/plugins/number/jquery.number.min.js"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="<?php echo public_url('admin');?>/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- DATA TABES SCRIPT -->
<script src="<?php echo public_url('admin');?>/plugins/datatables/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="<?php echo public_url('admin');?>/plugins/datatables/dataTables.bootstrap.min.js" type="text/javascript"></script>
<!-- SlimScroll -->
<script src="<?php echo public_url('admin');?>/plugins/slimScroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<!-- FastClick -->
<script src="<?php echo public_url('admin');?>/plugins/fastclick/fastclick.min.js" type="text/javascript"></script>
<!-- Lightbox -->
<link href="<?=public_url('admin') ?>/plugins/lightbox/css/lightbox.min.css " rel="stylesheet" type="text/css" />
<!-- Select2 -->
<link href="<?=public_url('admin') ?>/plugins/select2/select2.min.css " rel="stylesheet" type="text/css" />
<!-- DateTimePicker -->
<link href="<?=public_url('admin') ?>/plugins/dom-calendar/jquery.datetimepicker.css " rel="stylesheet" type="text/css" />
<!-- Daterange picker -->
<link href="<?=public_url('admin') ?>/plugins/daterangepicker/daterangepicker-bs3.css" rel="stylesheet" type="text/css" />
<!-- Theme style -->
<link href="<?php echo public_url('admin');?>/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />

<!-- AdminLTE App -->
<script src="<?php echo public_url('admin');?>/dist/js/app.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo public_url('admin');?>/dist/js/common.js"></script>
<script type="text/javascript" src="<?php echo public_url('admin');?>/dist/js/constants.js"></script>
<script type="text/javascript" src="<?php echo public_url('admin');?>/plugins/lightbox/js/lightbox.min.js"></script>
<script type="text/javascript" src="<?php echo public_url('admin');?>/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="<?php echo public_url('admin');?>/plugins/dom-calendar/jquery.datetimepicker.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.2/moment.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo public_url('admin');?>/plugins/daterangepicker/daterangepicker.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo public_url('admin');?>/dist/js/demo.js" type="text/javascript"></script>
<!-- page script -->
<script type="text/javascript">
    var baseURL = '<?=base_url()?>';
</script>