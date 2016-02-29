<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
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
    
    <link rel="stylesheet" href="/Public/Static/sweetalert-master/dist/sweetalert.css" />
    <link rel="stylesheet" href="/Public/Ground/css/index.css">

</head>
<body class="hold-transition skin-blue sidebar-mini fixed">
<div class="wrapper">

    
    <header class="main-header">

<!-- Logo -->
<a href="/play.php?s=" class="logo">
    <!-- mini logo for sidebar mini 50x50 pixels -->
    <span class="logo-mini">场馆</span>
    <!-- logo for regular state and mobile devices -->
    <span class="logo-lg">场馆后台管理</span>
</a>

<!-- Header Navbar: style can be found in header.less -->
<nav class="navbar navbar-static-top" role="navigation">
<!-- Sidebar toggle button-->
<a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
    <span class="sr-only">Toggle navigation</span>
</a>
<!-- Navbar Right Menu -->
<div class="navbar-custom-menu">
<ul class="nav navbar-nav">

<!-- Notifications: style can be found in dropdown.less -->
<li class="dropdown notifications-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-bell-o"></i>
        <span class="label label-warning">10</span>
    </a>
    <ul class="dropdown-menu">
        <li class="header">您有10条未读消息</li>
        <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
                <li>
                    <a href="#">
                        <i class="fa fa-users text-aqua"></i> 未读消息标题
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-warning text-yellow"></i> 未读消息标题
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-users text-red"></i> 未读消息标题
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-shopping-cart text-green"></i> 未读消息标题
                    </a>
                </li>
                <li>
                    <a href="#">
                        <i class="fa fa-user text-red"></i> 未读消息标题
                    </a>
                </li>
            </ul>
        </li>
        <li class="footer"><a href="#">查看全部</a></li>
    </ul>
</li>
<!-- Tasks: style can be found in dropdown.less -->
<li class="dropdown tasks-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-flag-o"></i>
        <span class="label label-danger">3</span>
    </a>
    <ul class="dropdown-menu">
        <li class="header">简单三步完成场馆设置</li>
        <li>
            <!-- inner menu: contains the actual data -->
            <ul class="menu">
                <li><!-- Task item -->
                    <?php if($setA): ?><a href="<?php echo U('Setting/index');?>">
                            <h3>
                                场馆基础设置
                                <small class="pull-right">100%</small>
                            </h3>
                            <div class="progress xs">
                                <div class="progress-bar progress-bar-aqua" style="width: 100%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                    <span class="sr-only">100% Complete</span>
                                </div>
                            </div>
                        </a>
                        <?php else: ?>
                        <a href="<?php echo U('Setting/index');?>">
                            <h3>
                                场馆基础设置
                                <small class="pull-right">0%</small>
                            </h3>
                            <div class="progress xs">
                                <div class="progress-bar progress-bar-aqua" style="width: 0" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                    <span class="sr-only">0% Complete</span>
                                </div>
                            </div>
                        </a><?php endif; ?>

                </li><!-- end task item -->
                <li><!-- Task item -->
                    <?php if($setB): ?><a href="<?php echo U('Setting/items');?>">
                            <h3>
                                场馆项目设置
                                <small class="pull-right">100%</small>
                            </h3>
                            <div class="progress xs">
                                <div class="progress-bar progress-bar-green" style="width: 100%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                    <span class="sr-only">100% Complete</span>
                                </div>
                            </div>
                        </a>
                        <?php else: ?>
                        <a href="<?php echo U('Setting/items');?>">
                            <h3>
                                场馆项目设置
                                <small class="pull-right">0%</small>
                            </h3>
                            <div class="progress xs">
                                <div class="progress-bar progress-bar-green" style="width: 0" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                    <span class="sr-only">0% Complete</span>
                                </div>
                            </div>
                        </a><?php endif; ?>

                </li><!-- end task item -->
                <li><!-- Task item -->
                    <?php if($setC): ?><a href="<?php echo U('Setting/block');?>">
                            <h3>
                                场馆场地设置
                                <small class="pull-right">100%</small>
                            </h3>
                            <div class="progress xs">
                                <div class="progress-bar progress-bar-red" style="width: 100%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                    <span class="sr-only">100% Complete</span>
                                </div>
                            </div>
                        </a>
                        <?php else: ?>
                        <a href="<?php echo U('Setting/block');?>">
                            <h3>
                                场馆场地设置
                                <small class="pull-right">0%</small>
                            </h3>
                            <div class="progress xs">
                                <div class="progress-bar progress-bar-red" style="width: 0" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                                    <span class="sr-only">0% Complete</span>
                                </div>
                            </div>
                        </a><?php endif; ?>
                </li><!-- end task item -->
            </ul>
        </li>
    </ul>
</li>
<li>
    <a href="<?php echo ROOT;?>" target="_blank"><i class="fa fa-send-o"></i></a>
</li>
<!-- User Account: style can be found in dropdown.less -->
<li class="dropdown user user-menu">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <?php if($icon): ?><img src="<?php echo ($icon["path"]); ?>" class="user-image myimg" alt="User Image">
            <?php else: ?>
            <img src="/Public/Ground/images/avatar.jpg" class="user-image myimg" alt="User Image"><?php endif; ?>
        <span class="hidden-xs"><?php echo USERNAME;?></span>
    </a>
    <ul class="dropdown-menu">
        <!-- User image -->
        <li class="user-header">
            <?php if($icon): ?><img src="<?php echo ($icon["path"]); ?>" class="img-circle myimg" alt="User Image">
                <?php else: ?>
                <img src="/Public/Ground/images/avatar.jpg" class="img-circle myimg" alt="User Image"><?php endif; ?>
            <p>
                <?php echo NAME;?>
                <small>2015-10-10 9:30</small>
            </p>
        </li>
        <!-- Menu Body -->
        <li class="user-body">
            <div class="col-xs-4 text-center">
                <a href="#">账户</a>
            </div>
            <div class="col-xs-4 text-center">
                <a href="#">消息</a>
            </div>
            <div class="col-xs-4 text-center">
                <a href="#">积分</a>
            </div>
        </li>
        <!-- Menu Footer-->
        <li class="user-footer">
            <div class="pull-left">
                <a href="#" class="btn btn-default btn-flat">详情</a>
            </div>
            <div class="pull-right">
                <a href="<?php echo U('Public/logout');?>" class="btn btn-default btn-flat">退出登录</a>
            </div>
        </li>
    </ul>
</li>
<!-- Control Sidebar Toggle Button -->

</ul>
</div>

</nav>
</header>

    <!-- Left side column. contains the logo and sidebar -->
    
    <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <?php if($icon): ?><img src="<?php echo ($icon["path"]); ?>" class="img-circle myimg" alt="User Image">
                    <?php else: ?>
                    <img src="/Public/Ground/images/avatar.jpg" class="img-circle myimg" alt="User Image"><?php endif; ?>
            </div>
            <div class="pull-left info">
                <p><?php echo NAME;?></p>
                <a href="#"><i class="fa fa-circle text-success"></i> 普通场馆</a>
            </div>
        </div>
        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="搜索...">
                      <span class="input-group-btn">
                        <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
                      </span>
            </div>
        </form>
        <!-- /.search form -->
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="header">导航列表</li>
            <?php if($active == 'Index'): ?><li class="active"><?php else: ?><li><?php endif; ?>
                <a href="<?php echo U('Index/index');?>">
                    <i class="fa fa-dashboard"></i> <span>场馆首页</span>
                </a>
            </li>
            <?php if($active == 'Orders'): ?><li class="active"><?php else: ?><li><?php endif; ?>
                <a href="<?php echo U('Orders/index');?>">
                    <i class="fa fa-reorder"></i>
                    <span>订单管理</span>
                    <span class="label label-primary pull-right"><?php echo ($orders); ?></span>
                </a>
            </li>
            <?php if($active == 'Statistics'): ?><li class="active"><?php else: ?><li><?php endif; ?>
                <a href="<?php echo U('Statistics/index');?>">
                    <i class="fa fa-pie-chart"></i>
                    <span>统计管理</span>
                    <span class="label label-success pull-right"><?php echo ($settled); ?></span>
                </a>
            </li>
            <?php if($active == 'Locks'): ?><li class="active"><?php else: ?><li><?php endif; ?>
            <a href="<?php echo U('Locks/index');?>">
                <i class="fa fa-lock" style="font-size: 18px;"></i>
                <span>锁定管理</span>
                <span class="label label-danger pull-right"><?php echo ($locks); ?></span>
            </a>
            </li>
            <li class="header">场馆设置</li>
            <?php if($active == 'Setting_index'): ?><li class="active"><?php else: ?><li><?php endif; ?>
                <a href="<?php echo U('Setting/index');?>"><i class="fa fa-circle-o text-red"></i> <span>基础设置</span></a>
            </li>
            <?php if($active == 'Setting_items'): ?><li class="active"><?php else: ?><li><?php endif; ?>
                <a href="<?php echo U('Setting/items');?>"><i class="fa fa-circle-o text-yellow"></i> <span>项目设置</span></a>
            </li>
            <?php if($active == 'Setting_block'): ?><li class="active"><?php else: ?><li><?php endif; ?>
                <a href="<?php echo U('Setting/block');?>"><i class="fa fa-circle-o text-aqua"></i> <span>场地设置</span></a>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>


<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
    <h1>
        <?php echo ($meta_title); ?>
        <small>管理</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> 首页</a></li>
        <li class="active"><?php echo ($meta_title); ?></li>
    </ol>
</section>

    <!-- Main content -->
    <section class="content">
        
    <div class="row">
        <div class="col-md-3">
            <!-- Profile Image -->
<div class="box box-primary">
    <div class="box-body box-profile">
        <div class="groundimg" data-toggle="tooltip" title="设置图标" data-original-title="设置图标">
            <?php if($icon): ?><img class="profile-user-img img-responsive img-circle myimg" src="<?php echo ($icon["path"]); ?>" alt="User profile picture">
                <?php else: ?>
                <img class="profile-user-img img-responsive img-circle myimg" src="/Public/Ground/images/avatar.jpg" alt="User profile picture"><?php endif; ?>

            <i class="fa fa-cog" data-toggle="modal" data-target="#myModal_<?php echo UID;?>"></i>
        </div>
        <form method="post" role="form" id="form">
            <div class="modal fade" id="myModal_<?php echo UID;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">设置场馆图标</h4>
                        </div>
                        <div class="modal-body">
                            <h4>选择图片</h4>
                            <?php if($album): ?><div class="row" id="defaultimgman">
                                    <?php if(is_array($album)): foreach($album as $key=>$vo): ?><div class="col-md-3 defaultimg">
                                            <img src="<?php echo (get_cover($vo,'path')); ?>" alt="默认用户头像" data-icon="<?php echo ($vo); ?>" class="img-thumbnail myimg">
                                            <span aria-hidden="true" class="glyphicon glyphicon-ok"></span>
                                        </div><?php endforeach; endif; ?>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-warning alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                    <h4><i class="icon fa fa-warning"></i> 提示</h4>
                                    您还没有设置场馆相册，无法选择相册图片！您可以前往 <a href="<?php echo U('Setting/index');?>">基础设置</a> 设置场馆相册，或者直接上传场馆图标。
                                </div><?php endif; ?>

                            <hr>
                            <h4>上传图片</h4>
                            <div class="tip" id="tip">
                                <div class="alert alert-danger" role="alert">
                                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                                    <b id="alertTxt">请选择或者上传图片</b>
                                </div>
                            </div>
                            <div class="upimg">
                                <div class="imageBox">
                                    <div class="thumbBox"></div>
                                    <div class="spinner" style="display: none">Loading...</div>
                                </div>
                                <div class="action">
                                    <!-- <input type="file" id="file" style=" width: 200px">-->
                                    <div class="new-contentarea tc"> <a href="javascript:void(0)" class="upload-img">
                                        <label for="upload-file">上传图片</label>
                                    </a>
                                        <input type="file" class="" name="upload-file" id="upload-file" />
                                    </div>
                                    <input type="button" id="btnCrop"  class="Btnsty_peyton" value="裁切">
                                    <input type="button" id="btnZoomIn" class="Btnsty_peyton" value="+"  >
                                    <input type="button" id="btnZoomOut" class="Btnsty_peyton" value="-" >
                                </div>
                                <div class="cropped"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                            <button type="submit" id="button" data-loading-text="Loading..." class="btn btn-info" autocomplete="off">
                                <span aria-hidden="true" class="glyphicon glyphicon-saved"></span> 提交设置
                            </button>
                            <textarea name="img" style="display: none" id="img"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <h3 class="profile-username text-center"><?php echo NAME;?></h3>
        <p class="text-muted text-center">项目列表</p>

        <ul class="list-group list-group-unbordered">
            <?php if(is_array($venue_items)): foreach($venue_items as $key=>$vo): ?><li class="list-group-item">
                    <b><?php echo ($vo["name"]); ?></b>
                    <div class="btn-group pull-right">
                        <a class="pull-right" data-toggle="dropdown"><i class="fa fa-angle-down"></i></a>
                        <ul class="dropdown-menu" role="menu">
                            <?php switch($vo["status"]): case "-1": ?><li class="ocAjax" data-id="<?php echo ($vo["id"]); ?>" data-status="1" data-url="<?php echo U('Ajax/ocVenueItems');?>" data-tag="#venueItems" id="venueItems-<?php echo ($vo["id"]); ?>"><a href="#">开启</a></li><?php break;?>
                                <?php case "1": ?><li class="ocAjax" data-id="<?php echo ($vo["id"]); ?>" data-status="-1" data-url="<?php echo U('Ajax/ocVenueItems');?>" data-tag="#venueItems" id="venueItems-<?php echo ($vo["id"]); ?>"><a href="#">关闭</a></li><?php break; endswitch;?>

                            <li class="divider"></li>
                            <li>
                                <a data-toggle="modal" href="<?php echo U('Setting/blockadd',['items_id'=>$vo['items_id']]);?>" data-target="#modal_add_<?php echo ($vo["items_id"]); ?>">添加场地</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <div class="modal fade" id="modal_add_<?php echo ($vo["items_id"]); ?>" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">

                        </div><!-- /.modal-content -->
                    </div><!-- /.modal-dialog -->
                </div><!-- /.modal --><?php endforeach; endif; ?>
        </ul>

        <a href="<?php echo U('Setting/items');?>" class="btn btn-primary btn-block"><b>添加项目</b></a>
    </div><!-- /.box-body -->
</div><!-- /.box -->
<!-- About Me Box -->
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">关于我们</h3>
    </div><!-- /.box-header -->
    <div class="box-body">
        <strong><i class="fa fa-book margin-r-5"></i>  简介</strong>
        <p class="text-muted">
            <?php echo ((isset($venue["summary"]) && ($venue["summary"] !== ""))?($venue["summary"]):'没有设置简介'); ?>
        </p>

        <hr>

        <strong><i class="fa fa-map-marker margin-r-5"></i> 定位</strong>
        <p class="text-muted">
            <?php echo ((isset($venue["address"]) && ($venue["address"] !== ""))?($venue["address"]):'没有设置地址'); ?>
        </p>

        <hr>

        <strong><i class="fa fa-phone margin-r-5"></i> 电话</strong>
        <p class="text-muted">
            <?php echo ((isset($venue["tel"]) && ($venue["tel"] !== ""))?($venue["tel"]):'没有设置电话'); ?>
        </p>

        <hr>

        <strong><i class="fa fa-pencil margin-r-5"></i> 服务</strong>
        <p>
            <?php if(is_array($my_services)): foreach($my_services as $key=>$vo): if($vo['s']): ?><span class="label label-success" style="margin-right: 5px;display: inline-block;"><?php echo ($vo["val"]); ?></span><?php endif; endforeach; endif; ?>
        </p>

        <hr>

        <strong><i class="fa fa-leaf margin-r-5"></i> 场地材质</strong>
        <p class="text-muted">
            <?php echo ((isset($venue['material']) && ($venue['material'] !== ""))?($venue['material']):'没有设置场地材质'); ?>
        </p>

        <hr>

        <strong><i class="fa fa-moon-o margin-r-5"></i> 灯光效果</strong>
        <p class="text-muted">
            <?php echo ((isset($venue['light']) && ($venue['light'] !== ""))?($venue['light']):'没有设置灯光效果'); ?>
        </p>

        <hr>

        <strong><i class="fa fa-camera-retro margin-r-5"></i> 相册</strong>

        <?php if($albums): ?><p>
            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    <?php if(is_array($albums)): foreach($albums as $key=>$vo): if($key == 0): ?><li data-target="#carousel-example-generic" data-slide-to="<?php echo ($key); ?>" class="active"></li>
                            <?php else: ?>
                            <li data-target="#carousel-example-generic" data-slide-to="<?php echo ($key); ?>" class=""></li><?php endif; endforeach; endif; ?>
                </ol>
                <div class="carousel-inner">
                    <?php if(is_array($albums)): foreach($albums as $key=>$vo): if($key == 0): ?><div class="item active">
                                <img src="<?php echo ($vo["path"]); ?>">
                                <div class="carousel-caption">
                                    <?php echo (time_format($vo["create_time"])); ?>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="item">
                                <img src="<?php echo ($vo["path"]); ?>">
                                <div class="carousel-caption">
                                    <?php echo (time_format($vo["create_time"])); ?>
                                </div>
                            </div><?php endif; endforeach; endif; ?>
                </div>
                <a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">
                    <span class="fa fa-angle-left"></span>
                </a>
                <a class="right carousel-control" href="#carousel-example-generic" data-slide="next">
                    <span class="fa fa-angle-right"></span>
                </a>
            </div>
          </p>
          <?php else: ?>
          <p class="text-muted">没有设置相册图片 <a href="<?php echo U('Setting/index');?>">上传图片</a></p><?php endif; ?>


    </div><!-- /.box-body -->
</div><!-- /.box -->

        </div>
        <div class="col-md-9">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li><a href="<?php echo U('index');?>">订单管理</a></li>
                    <li class="active"><a href="<?php echo U('comment');?>">评论管理</a></li>
                    <li><a href="<?php echo U('reserve');?>">预订管理</a></li>
                    <li><a href="<?php echo U('lock');?>">锁定管理</a></li>
                    <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="timeline">
                        <?php if($_list): if(is_array($_list)): foreach($_list as $key=>$vo): ?><!-- Post -->
                                <div class="post clearfix">
                                    <div class="user-block">
                                        <img class="img-circle img-bordered-sm" src="/Public/Ground/images/logo.jpg" alt="user image">
                                        <span class="username">
                                          <a href="#"><?php echo ($vo["username"]); ?></a>
                                          <a href="#" class="pull-right btn-box-tool text-yellow">
                                              <?php $__FOR_START_4939__=0;$__FOR_END_4939__=$vo['stars'];for($i=$__FOR_START_4939__;$i < $__FOR_END_4939__;$i+=1){ ?><i class="fa fa-fw fa-star"></i><?php } ?>
                                          </a>
                                        </span>
                                        <span class="description"><?php echo (time_format($vo["created_time"])); ?></span>
                                    </div>
                                    <!-- /.user-block -->
                                    <p>
                                        <?php echo ($vo["cont"]); ?>
                                    </p>

                                    <?php if($vo['reply']): ?><p class="pull-right text-success">
                                            <?php echo ($vo["reply"]); ?>
                                        </p>
                                        <?php else: ?>
                                        <form class="form-horizontal" action="/play.php?s=/Index/comment.html" method="post">
                                            <div class="form-group margin-bottom-none">
                                                <div class="col-sm-10">
                                                    <input class="form-control input-sm" name="reply" placeholder="填写回复内容">
                                                </div>
                                                <div class="col-sm-2">
                                                    <input name="id" type="hidden" value="<?php echo ($vo["id"]); ?>" />
                                                    <button type="submit" class="btn btn-danger pull-right btn-block btn-sm">回复</button>
                                                </div>
                                            </div>
                                        </form><?php endif; ?>

                                </div>
                                <!-- /.post --><?php endforeach; endif; ?>
                            <div class="box-footer">
                                <div class="row">
                                    <div class="col-md-3">
                                        <p>场馆得分：<strong class="text-yellow"><?php echo ($score); ?></strong></p>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar progress-bar-danger" style="width: <?php echo ($score); ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="page pull-right">
                                            <?php echo ($_page); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="callout callout-danger">
                                <h4>哎呦</h4>
                                <p>没有您要是数据，请重新检索</p>
                            </div><?php endif; ?>
                    </div><!-- /.tab-pane -->
                </div>
            </div>
        </div>
    </div>
    </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<footer class="main-footer">
    
    <div class="pull-right hidden-xs">
    <b>Version</b> 2.3.0
</div>
<strong>Copyright &copy; 2014-2015 <a href="http://bobooking.com">BoBooKing</a>.</strong> All rights reserved.

</footer>

</div><!-- ./wrapper -->


    <!-- jQuery 2.1.4 -->
<script src="/Public/Static/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js"></script>
<!-- Bootstrap 3.3.5 -->
<script src="/Public/Static/AdminLTE/bootstrap/js/bootstrap.min.js"></script>
<!-- FastClick -->
<script src="/Public/Static/AdminLTE/plugins/fastclick/fastclick.min.js"></script>
<!-- AdminLTE App -->
<script src="/Public/Static/AdminLTE/dist/js/app.min.js"></script>
<!-- Sparkline -->
<script src="/Public/Static/AdminLTE/plugins/sparkline/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="/Public/Static/AdminLTE/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="/Public/Static/AdminLTE/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- SlimScroll 1.3.0 -->
<script src="/Public/Static/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- ChartJS 1.0.1 -->
<script src="/Public/Static/AdminLTE/plugins/chartjs/Chart.min.js"></script>
<!-- Select2 -->
<script src="/Public/Static/AdminLTE/plugins/select2/select2.full.min.js"></script>
<script src="/Public/Static/AdminLTE/plugins/pace/pace.min.js"></script>
<script>
    $(document).ajaxStart(function() { Pace.restart(); });
    $(function () {
        //Initialize Select2 Elements
        $(".select2").select2();
    });
</script>
    <script src="/Public/Static/sweetalert-master/dist/sweetalert.min.js"></script>
    <script>
        $('.ocAjax').on('click',function() {
            var id = $(this).attr("data-id"),status = $(this).attr("data-status"),url = $(this).attr("data-url"),tag = $(this).attr("data-tag");
            ocData(id,status,url,tag);
        });
        function ocData(id,status,url,tag) {
            var text = '';
            switch (status){
                case '-1':
                    text='关闭后将无法预订，您确定要关闭吗？';
                    break;
                case '1':
                    text='开启后将可以预订，您确定要开启吗？';
                    break;
            }
            swal({
                title: "提示",
                text: text,
                type: "warning",
                //animation: "slide-from-top",
                showCancelButton: true,
                closeOnConfirm: false,
                confirmButtonText: "确定",
                confirmButtonColor: "#ec6c62",
                cancelButtonText:"取消"
            }, function() {
                $.ajax({
                    url: url,
                    type: "POST",
                    data:{id:id,status:status}
                })
                        .done(function(data) {
                            if(data.status){
                                swal({
                                            title: "提示",
                                            text:data.msg,
                                            type: "success"
                                        },
                                        function(){
                                            switch (status){
                                                case '-1':
                                                    $(tag+'-'+id).attr('data-status','1').children('a').text('开启');
                                                    break;
                                                case '1':
                                                    $(tag+'-'+id).attr('data-status','-1').children('a').text('关闭');
                                                    break;
                                            }

                                        });
                            }else{
                                swal("提示", data.msg, "error");
                            }
                        })
                        .error(function(data) {
                            swal("错误", "服务器连接失败", "error");
                        });
            });
        }
    </script>

</body>
</html>