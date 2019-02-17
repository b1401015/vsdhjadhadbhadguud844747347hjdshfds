<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin | Log in</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.4 -->
    <link href="<?php echo public_url('admin');?>/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Font Awesome Icons -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="<?php echo public_url('admin');?>/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="<?php echo public_url('admin');?>/plugins/iCheck/square/blue.css" rel="stylesheet" type="text/css" />

    <link href="<?php echo public_url('');?>css/common.css" rel="stylesheet" type="text/css" />

    <style>
        #label_message{
            color: red;
        }
    </style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- jQuery 2.1.4 -->
    <script src="<?php echo public_url('admin');?>/plugins/jQuery/jQuery-2.1.4.min.js" type="text/javascript"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="<?php echo public_url('admin');?>/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- iCheck -->
    <script src="<?php echo public_url('admin');?>/plugins/iCheck/icheck.min.js" type="text/javascript"></script>
    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' // optional
            });
        });
    </script>
    <script src="<?php echo public_url('');?>js/constant.js" type="text/javascript"></script>
    <script src="<?php echo public_url('');?>js/common.js" type="text/javascript"></script>
    <script src="<?php echo public_url('');?>js/sha256.min.js" type="text/javascript"></script>
    <script src="<?php echo public_url('admin');?>/js/admin_uri.js" type="text/javascript"></script>
    <script src="<?php echo public_url('admin');?>/js/login.js" type="text/javascript"></script>
</head>
<body class="login-page">
<div class="login-box">
    <div class="login-logo">
        <b>Admin</b>
    </div><!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Sign in to start your session</p>
        <form action="../../index2.html" method="post">
            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="Account" id="txt_account"/>
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Password" id="txt_password"/>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="form-group">
                <table width="100%">
                    <tr>
                        <td width="50%" id="img_captcha"><?php echo $captcha;?></td>
                        <td><span class="glyphicon glyphicon-refresh pointer margin_l_10" onclick="loginPage.refresh_captcha();"></span></td>
                    </tr>
                </table>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" placeholder="Input captcha" id="txt_captcha"/>
            </div>
            <div class="row">
                <div class="col-xs-4">
                    <button type="button" class="btn btn-primary btn-block btn-flat" onclick="loginPage.process_login();">Sign In</button>
                </div><!-- /.col -->
            </div>
        </form>
        <div id="label_message"></div>
    </div><!-- /.login-box-body -->
</div><!-- /.login-box -->

<div class="container-fluid">
    <div class="row"><div class="col-12">
        <p class="text-center">Copyright @THUDDEV 2018</p>
    </div></div>
</div>

</body>
</html>
