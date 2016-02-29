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
    
    <link rel="stylesheet" href="/Public/Static/AdminLTE/plugins/daterangepicker/daterangepicker-bs3.css">
    <link rel="stylesheet" href="/Public/Static/AdminLTE/plugins/timepicker/bootstrap-timepicker.min.css">
    <link rel="stylesheet" href="/Public/Static/sweetalert-master/dist/sweetalert.css" />
    <link rel="stylesheet" href="/Public/Static/Swiper/dist/css/swiper.min.css">
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
                    <li><a href="<?php echo U('comment');?>">评论管理</a></li>
                    <li><a href="<?php echo U('reserve');?>">预订管理</a></li>
                    <li class="active"><a href="<?php echo U('lock');?>">锁定管理</a></li>
                    <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="lock">
                        <div class="alert alert-warning alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h4><i class="icon fa fa-warning"></i> 提示</h4>
                            批量锁定可以根据运营需要，按照项目、场地、日期段、星期、时间段进行精确锁定；锁定后可以提前解锁
                        </div>
                    </div>
                    <form action="/play.php?s=/Index/lock.html" method="post" id="form_reserve">
                        <div class="box box-default">
                            <div class="box-header with-border">
                                <h3 class="box-title">批量锁定</h3>
                                <div class="box-tools pull-right">
                                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                </div>
                            </div><!-- /.box-header -->
                            <div class="box-body">

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>项目和场地 </label>
                                            <div class="row" id="element">
                                                <div class="col-md-6">
                                                    <select class="form-control select2 items" name="items" id="items" disabled="disabled" data-url="<?php echo U('getItems');?>" style="width: 100%;">
                                                        <option value="" selected>请选择</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <select class="form-control select2 block" name="block" disabled="disabled" data-url="<?php echo U('getBlocks');?>" data-json-space="data" style="width: 100%;">
                                                        <option value="" selected>请选择</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div><!-- /.form-group -->
                                    </div><!-- /.col -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>日期 </label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="text" name="ordersdate" value="<?php echo ((isset($ordersdate) && ($ordersdate !== ""))?($ordersdate):''); ?>" class="form-control pull-right" readonly id="reservation">
                                            </div>
                                        </div><!-- /.form-group -->
                                    </div><!-- /.col -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>星期 </label>
                                            <select class="form-control select2" name="weeks" style="width: 100%;">
                                                <option value="" selected>请选择</option>
                                                <?php if(is_array($weeks)): foreach($weeks as $key=>$vo): ?><option value="<?php echo ($key); ?>"><?php echo ($vo); ?></option><?php endforeach; endif; ?>
                                            </select>
                                        </div><!-- /.form-group -->
                                    </div><!-- /.col -->
                                    <div class="col-md-3">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="bootstrap-timepicker">
                                                    <div class="form-group">
                                                        <label>开始时间 </label>
                                                        <div class="input-group">
                                                            <input type="text" name="start" value="10:00" id="start" class="form-control timepicker">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-clock-o"></i>
                                                            </div>
                                                        </div><!-- /.input group -->
                                                    </div><!-- /.form group -->
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="bootstrap-timepicker">
                                                    <div class="form-group">
                                                        <label>结束时间 </label>
                                                        <div class="input-group">
                                                            <input type="text" name="end" value="15:00" id="end" class="form-control timepicker">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-clock-o"></i>
                                                            </div>
                                                        </div><!-- /.input group -->
                                                    </div><!-- /.form group -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <span id="summary_reserve" class="text-red"></span>
                                <button type="submit" class="btn btn-primary pull-right" id="submit_reserve">立即锁定</button>
                                <style>
                                    form label.error{
                                        color: #dd4b39;
                                        margin-bottom: 0;
                                    }
                                </style>
                            </div>
                        </div>
                    </form>
                    <?php if($list): ?><div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">日志列表</h3>
                                <div class="box-tools pull-right">
                                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                </div>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <table class="table table-bordered table-striped table-hover">
                                    <tr>
                                        <th>运动项目</th>
                                        <th>场地</th>
                                        <th>开始日期</th>
                                        <th>结束日期</th>
                                        <th>星期</th>
                                        <th>开始时间</th>
                                        <th>结束时间</th>
                                        <th>创建时间</th>
                                        <th>操作</th>
                                    </tr>
                                    <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr id="lockdel-<?php echo ($vo["id"]); ?>">
                                            <td><?php echo ($vo["name"]); ?></td>
                                            <td><?php echo ($vo["block_name"]); ?></td>
                                            <td><?php echo date('Y-m-d',$vo['startdate']);?></td>
                                            <td><?php echo date('Y-m-d',$vo['enddate']);?></td>
                                            <td><?php echo ($vo["week"]); ?></td>
                                            <td><?php echo ($vo["starttime"]); ?></td>
                                            <td><?php echo ($vo["endtime"]); ?></td>
                                            <td><?php echo (time_format($vo["created_time"])); ?></td>
                                            <td>
                                                <a href="<?php echo U('Locks/seclists',['lid'=>$vo['id']]);?>"><span class="label label-primary">查看</span></a>
                                                <a href="#" class="delete" data-id="<?php echo ($vo["id"]); ?>" data-url="<?php echo U('lockdel');?>" data-tag="#lockdel"><span class="label label-warning">解除</span></a>
                                            </td>
                                        </tr><?php endforeach; endif; ?>
                                </table>
                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <div class="page pull-right">
                                    <?php echo ($_page); ?>
                                </div>
                            </div>
                        </div><!-- /.box -->
                        <?php else: ?>
                        <div class="callout callout-danger">
                            <h4>哎呦</h4>
                            <p>没有您要是数据，请重新检索</p>
                        </div><?php endif; ?>
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
    <!-- Swiper JS -->
    <script src="/Public/Static/Swiper/dist/js/swiper.min.js"></script>
    <script src="/Public/Static/sweetalert-master/dist/sweetalert.min.js"></script>
    <!-- date-range-picker -->
    <script src="/Public/Static/AdminLTE/plugins/daterangepicker/moment.min.js"></script>
    <script src="/Public/Static/AdminLTE/plugins/daterangepicker/daterangepicker.js"></script>
    <script src="/Public/Static/AdminLTE/plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="/Public/Static/select/js/jquery.cxselect.min.js"></script>
    <script src="/Public/Static/jquery.validate.min.js"></script>

    <script src="/Public/Ground/js/win.js"></script>


    <script>
        //根据屏幕尺寸控制
        var pageSize = getPageSize();
        var p = 5;
        $('.right_li_price').on('click',function() {
            var hasD = $(this).hasClass('del');
            var hasL = $(this).hasClass('lock');
            var hasS = $(this).hasClass('suc');
            var hasC = $(this).hasClass('check');

            if(!hasS && !hasD && !hasL){
                if(hasC){
                    $(this).removeClass('check').next('input').removeAttr('checked');
                }else{
                    $(this).addClass('check').next('input').attr('checked','true');
                }
            }
        });
        $('.reserve').on('click',function() {
            var vals = checkedValues();
            if(vals.length==0){
                swal({title: "错误",   text: "请选择要操作的项目", type: "error",  timer: 3000,   showConfirmButton: false });
            }else{
                var id = vals,url = "<?php echo U('reserves');?>",tag = "#price";
                reserves(id,url,tag);
            }
        });

        $('.ocAjax').on('click',function() {
            var id = $(this).attr("data-id"),status = $(this).attr("data-status"),url = $(this).attr("data-url"),tag = $(this).attr("data-tag");
            ocData(id,status,url,tag);
        });

        //Date range picker
        $('#reservation').daterangepicker({
            startDate: '<?php echo ($startDate); ?>',
            endDate: '<?php echo ($endDate); ?>',
            format:'YYYY/MM/DD',
            locale:{
                applyLabel:'确定',
                cancelLabel:'关闭',
                fromLabel: '从',
                toLabel: '至'
            }
        });

        $('#element').cxSelect({
            selects: ['items', 'block'],
            jsonName: 'name',
            jsonValue: 'value'
        });

        $(".timepicker").timepicker({
            showInputs: false,
            showMeridian:false
        });

        $('#submit_reserve').on('click', function () {
            var $btn = $(this).button('loading');

            $("#form_reserve").validate({
                errorPlacement:function(error,element) {
                    error.appendTo(element.parents('.form-group').children('label'));
                },
                rules: {
                    items: "required",
                    block   : "required",
                    ordersdate:"required",
                    weeks     :"required",
                    start     : "required",
                    end     : "required"
                },
                messages: {
                    items   : "请选择项目",
                    block   : "请选择场地",
                    ordersdate:"请选择日期",
                    weeks     :"请选择星期",
                    start   : "请填写开始时间",
                    end     : "请填写结束时间"
                },
                showErrors:function(errorMap,errorList) {

                    if(this.numberOfInvalids() > 0){
                        $("#summary_reserve").html('<i class="fa fa-fw fa-info-circle"></i> '+this.numberOfInvalids()+'个选项没有填写完整');
                        this.defaultShowErrors();
                        $btn.button('reset');
                    }
                }
            });
        });

        $('.delete').on('click',function() {
            var id = $(this).attr("data-id"),url = $(this).attr("data-url"),tag = $(this).attr("data-tag");
            deleteData(id,url,tag);
        });

        function deleteData(id,url,tag) {
            swal({
                title: "提示",
                text: "您确定要解锁吗？",
                type: "warning",
                //animation: "slide-from-top",
                showCancelButton: true,
                closeOnConfirm: false,
                confirmButtonText: "确定解锁",
                confirmButtonColor: "#ec6c62",
                cancelButtonText:"取消"
            }, function() {
                $.ajax({
                    url: url,
                    type: "POST",
                    data:{id:id}
                })
                    .done(function(data) {
                        if(data.status){
                            swal({
                                title: "提示",
                                text:data.msg,
                                type: "success"
                            },
                            function(){
                                $(tag+'-'+id).remove();
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

        function checkedValues(){
            var chk_value =[];
            $('input[name="check"]:checked').each(function(){
                chk_value.push($(this).val());
            });
            return chk_value;
        }

        function reserves(id,url,tag) {
            swal({
                title: "提示",
                text: "请填写预定人手机号码",
                type: "input",
                animation: "slide-from-top",
                showCancelButton: true,
                closeOnConfirm: false,
                inputPlaceholder: "填写手机号码",
                confirmButtonText: "提交",
                confirmButtonColor: "#ec6c62",
                cancelButtonText:"取消"
            }, function(inputValue) {
                if (inputValue === false) return false;
                if (inputValue === "") {
                    swal.showInputError("请填写预定人手机号码");
                    return false;
                }
                $.ajax({
                    url: url,
                    type: "POST",
                    data:{id:id,mobile:inputValue}
                })
                    .done(function(data) {
                        if(data.status){
                            swal({
                                title: "提示",
                                text:data.msg,
                                type: "success"
                            },
                            function(){
                                var ids = checkedValues();
                                for(var i =0;i<ids.length;i++){
                                    $(tag+'-'+ids[i]).addClass('suc');
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

    <?php if(is_array($list)): foreach($list as $key=>$vo): ?><!-- Initialize Swiper -->
        <script>
            //alert(pageSize.windowWidth);
            if(pageSize.windowWidth < 700){
                $('.table_main').css({width: '525px'});
                $('.table_body_right').css({width: '420px'});
                p = 4;
            }
            else if(pageSize.windowWidth > 700 && pageSize.windowWidth<1024){
                //alert(2);
            }
            else if(pageSize.windowWidth > 1024 && pageSize.windowWidth<1280){
                //alert(2);
            }
            else if(pageSize.windowWidth > 1280 && pageSize.windowWidth<1440){
                //alert(3);
            }
            else if(pageSize.windowWidth > 1440 && pageSize.windowWidth<1600){
                //alert(4);
            }
            else if(pageSize.windowWidth > 1600 && pageSize.windowWidth<1920){
                $('.table_main').css({width: '1155px'});
                $('.table_body_right').css({width: '1050px'});
                p = 10;
            }
            else if(pageSize.windowWidth > 1920){
                $('.table_main').css({width: '1155px'});
                $('.table_body_right').css({width: '1050px'});
                p = 10;
            }
            var n = "<?php echo ($vo['id']); ?>";
            var swiper = new Swiper('.swiper-container-'+n, {
                pagination: '.swiper-pagination-'+n,
                slidesPerView: p,
                paginationClickable: true,
                nextButton: '.swiper-button-next-'+n,
                prevButton: '.swiper-button-prev-'+n
            });
        </script><?php endforeach; endif; ?>

</body>
</html>