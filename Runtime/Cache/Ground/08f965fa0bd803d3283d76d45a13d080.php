<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>场馆管理 - <?php echo C('WEB_SITE_TITLE');?></title>
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<!-- Bootstrap 3.3.5 -->
<link rel="stylesheet" href="/Public/Static/AdminLTE/bootstrap/css/bootstrap.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="/Public/Static/AdminLTE/dist/css/font-awesome.min.css">
<!-- Ionicons -->
<link rel="stylesheet" href="/Public/Static/AdminLTE/dist/css/ionicons.min.css">
<!-- jvectormap -->
<link rel="stylesheet" href="/Public/Static/AdminLTE/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
<!-- Select2 -->
<link rel="stylesheet" href="/Public/Static/AdminLTE/plugins/select2/select2.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="/Public/Static/AdminLTE/dist/css/AdminLTE.min.css">
<!-- AdminLTE Skins. Choose a skin from the css/skins
     folder instead of downloading all of them to reduce the load. -->
<link rel="stylesheet" href="/Public/Static/AdminLTE/dist/css/skins/_all-skins.min.css">
<!-- Pace style -->
<link rel="stylesheet" href="/Public/Static/AdminLTE/plugins/pace/pace.min.css">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
    <?php if($jumpUrl): ?><meta http-equiv="refresh" content="<?php echo($waitSecond); ?>; url=<?php echo ($jumpUrl); ?>"><?php endif; ?>
</head>
<body>
<div class="row">
    <div class="col-xs-6 col-md-4"></div>
    <div class="col-xs-6 col-md-4">
        <div class="alert alert-success alert-dismissable" style="margin-top: 20%;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4>	<i class="icon fa fa-check"></i> 信息提示</h4>
            <?php echo($message); ?>
            <?php if($jumpUrl): ?><a href="<?php echo ($jumpUrl); ?>">返回</a>
                <?php else: ?>
                <a href="javascript:window.history.go(-1);">返回</a><?php endif; ?>
        </div>
    </div>
    <div class="col-xs-6 col-md-4"></div>
</div>
</body>
</html>