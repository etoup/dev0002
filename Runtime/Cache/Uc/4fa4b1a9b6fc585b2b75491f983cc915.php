<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" href="/favicon.ico">

<title><?php echo ($seo["title"]); ?> - <?php echo C('WEB_SITE_TITLE');?></title>

<!-- Bootstrap core CSS -->
<link href="/Public/Static/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
<!--[if lt IE 9]><script src="/Public/Uc/js/ie8-responsive-file-warning.js"></script><![endif]-->
<script src="/Public/Uc/js/ie-emulation-modes-warning.js"></script>

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="/Public/Uc/js/html5shiv.min.js"></script>
<script src="/Public/Uc/js/respond.min.js"></script>
<![endif]-->

    <link rel="stylesheet" href="/Public/Static/AdminLTE/dist/css/AdminLTE.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="/Public/Static/AdminLTE/plugins/iCheck/square/blue.css">
    <link href="/Public/Uc/css/login.css" rel="stylesheet" type="text/css">

</head> 
<body class="hold-transition">
    
    <div class="login-box">
        <a href="index.html" class="login-logo"></a>
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">账号登录</p>
            <?php if($new): ?><div class="tip" id="tip" style="display: block;">
                    <div class="alert alert-warning" role="alert">
                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                        <b id="alertTxt">注册成功，请登录激活</b>
                    </div>
                </div>
                <?php else: ?>
                <div class="tip" id="tip" style="display: none;">
                    <div class="alert alert-danger" role="alert">
                        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                        <b id="alertTxt">用户名和密码为必填项</b>
                    </div>
                </div><?php endif; ?>
            <form action="/Uc/Uc/login.html" method="post" role="form" id="form">
                <div class="form-group has-feedback">
                    <input type="text" name="username" value="<?php echo ((isset($username) && ($username !== ""))?($username):''); ?>" class="form-control" placeholder="用户名">
                    <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" name="password" class="form-control" placeholder="密码">
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <?php if($username): ?><input type="checkbox" name="rememberme" checked=""> 记住用户名
                                    <?php else: ?>
                                    <input type="checkbox" name="rememberme"> 记住用户名<?php endif; ?>
                            </label>
                        </div>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4 pad10">
                        <a href="<?php echo U('forgotPwd');?>">忘记密码？</a>
                    </div>
                    <!-- /.col -->
                </div>
                <button type="submit" id="button" data-loading-text="Loading..." class="btn btn-primary btn-block btn-flat" autocomplete="off">
                    <span aria-hidden="true" class="glyphicon glyphicon-saved"></span> 提交登录
                </button>
            </form>

            <div class="text-center otherCount">
                <p>--- 用合作网站账号登录 --- </p>
                <ul class="cf">
                    <li id="weixin"></li>
                </ul>
            </div>
            <!-- /.social-auth-links -->

            <p>还没有账号？<a href="<?php echo U('register');?>" class="text-center">免费注册</a></p>

        </div>
        <!-- /.login-box-body -->
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="/Public/Static/jquery-1.11.1.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/Public/Static/bootstrap/js/bootstrap.min.js"></script>
    
        <script type="text/javascript" src="/Public/Static/jquery.form.js"></script>
        <!-- iCheck -->
        <script src="/Public/Static/AdminLTE/plugins/iCheck/icheck.min.js"></script>
        <script>
            $(function () {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_square-blue',
                    radioClass: 'iradio_square-blue',
                    increaseArea: '20%' // optional
                });

                $('#button').on('click', function () {
                    var $btn = $(this).button('loading');
                    var options = {
                        url:   '/Uc/Uc/login.html',
                        beforeSubmit :showRequest,
                        success: showResponse,
                        timeout: 3000
                    };
                    $('#form').ajaxForm(options);
                });

                function showRequest(formData, jqForm, options){
                    var queryString = $.param(formData);
                    var formElement = jqForm[0];
                    var username = formElement.username.value;
                    var password = formElement.password.value;
                    if(username==''){
                        $('#tip').show().children().find('b').text('用户名不能为空');
                        $("input[name='username']").focus();
                        $('#button').button('reset');
                        return false;
                    }
                    if(password==''){
                        $('#tip').show().children().find('b').text('密码不能为空');
                        $("input[name='password']").focus();
                        $('#button').button('reset');
                        return false;
                    }
                    return true;
                }
                function showResponse(data){
                    if(data.state === 'success') {
                        window.location.href = decodeURIComponent(data.referer);
                    }else{
                        $('#tip').show().children().find('b').text(data.message);
                        $('#button').button('reset');
                    }
                }

                $('#tips').popover({
                    'placement':'bottom',
                    'trigger':'hover'
                });
            });
        </script>
    
</body>
</html>