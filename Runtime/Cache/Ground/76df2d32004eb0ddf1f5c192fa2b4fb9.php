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
                        <?php if(is_array($my_services)): foreach($my_services as $key=>$vo): if($vo['s']): ?><em class="label label-success" style="margin-right: 5px; display: inline-block;"><?php echo ($vo["val"]); ?></em><?php endif; endforeach; endif; ?>
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
                </div><!-- /.box-body -->

            </div><!-- /.box -->

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">场馆相册</h3>
                </div><!-- /.box-header -->
                <?php if($albums): ?><div class="box-body" id="imgdel-body">
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
                    </div><?php endif; ?>

                <div class="box-footer">
                    <?php if($albums): ?><button type="button" class="btn btn-default delete" id="imgdel" data-id="<?php echo ($vo["id"]); ?>" data-url="<?php echo U('imgdel');?>" data-tag="#imgdel"><i class="fa fa-hdd-o"></i> 清空相册</button><?php endif; ?>
                    <button type="button" class="btn btn-primary pull-right" data-toggle="modal" href="<?php echo U('uploadimg');?>" data-target="#modal"><i class="fa fa-upload"></i> 上传图片</button>
                    <div class="modal fade" id="modal" tabindex="-1" role="dialog">
                        <div class="modal-dialog">
                            <div class="modal-content">

                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="box box-primary">
                <form id="form" role="form" action="/play.php?s=/Setting/index.html" method="post">
                    <div class="box-header with-border">
                        <h3 class="box-title">基础设置</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">

                        <!-- text input -->
                        <div class="form-group">
                            <label>定位</label>
                            <div id="l-map" style="width: 100%;height: 300px;overflow: hidden;margin:0;"></div>
                        </div>
                        <!--<?php if($venue['address']): ?>-->
                        <div class="form-group">
                            <label>地址</label>
                            <input type="text" name="address" id="address" class="form-control" value="<?php echo ($venue["address"]); ?>" placeholder="填写地址">
                        </div>
                        <!--<?php else: ?>-->
                        <div class="form-group">
                            <label>地址</label>
                            <input type="text" name="address" id="suggestId" class="form-control" value="<?php echo ($venue["address"]); ?>" placeholder="填写地址">
                            <div id="searchResultPanel" style="border:1px solid #C0C0C0;width:150px;height:auto; display:none;"></div>
                        </div>
                        <!--<?php endif; ?>-->
                        <div class="form-group">
                            <label>城市</label>
                            <input type="text" name="city" id="city" class="form-control" value="<?php echo ($venue["city"]); ?>" placeholder="填写城市">
                        </div>
                        <div class="form-group">
                            <label>区县</label>
                            <input type="text" name="district" id="district" class="form-control" value="<?php echo ($venue["district"]); ?>" placeholder="填写区县">
                        </div>
                        <div class="form-group">
                            <label>经度</label>
                            <input type="text" name="lng" id="lng" class="form-control" value="<?php echo ($venue["lng"]); ?>" placeholder="填写经度">
                        </div>
                        <div class="form-group">
                            <label>纬度</label>
                            <input type="text" name="lat" id="lat" class="form-control" value="<?php echo ($venue["lat"]); ?>" placeholder="填写纬度">
                        </div>

                        <div class="form-group">
                            <label>简介</label>
                            <input type="text" name="summary" class="form-control" value="<?php echo ($venue["summary"]); ?>" placeholder="填写简介">
                        </div>
                        <div class="form-group">
                            <label>电话</label>
                            <input type="text" name="tel" class="form-control" value="<?php echo ($venue["tel"]); ?>" placeholder="填写电话号码">
                        </div>
                        <div class="form-group">
                            <label>公交</label>
                            <input type="text" name="bus" class="form-control" value="<?php echo ($venue["bus"]); ?>" placeholder="填写公交信息">
                        </div>
                        <div class="form-group">
                            <label>商圈</label>
                            <input type="text" name="circle" class="form-control" value="<?php echo ($venue["circle"]); ?>" placeholder="填写所在商圈">
                        </div>
                        <!--<?php if(!$my_services): ?>-->
                        <div class="form-group">
                            <label>服务</label>
                            <select class="form-control select2" name="services[]" multiple="multiple" data-placeholder="选择服务项目" style="width: 100%;">
                                <?php if(is_array($all_services)): foreach($all_services as $key=>$vo): ?><option value="<?php echo ($vo); ?>"><?php echo ($vo); ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>
                        <!--<?php else: ?>-->
                        <div class="form-group">
                            <label>服务</label>
                            <select class="form-control select2" name="services[]" multiple="multiple" data-placeholder="选择服务项目" style="width: 100%;">
                                <?php if(is_array($my_services)): foreach($my_services as $key=>$vo): ?><option value="<?php echo ($vo["val"]); ?>" <?php echo ($vo["s"]); ?>><?php echo ($vo["val"]); ?></option><?php endforeach; endif; ?>
                            </select>
                        </div>
                        <!--<?php endif; ?>-->
                        <div class="form-group">
                            <label>场地材质</label>
                            <select class="form-control select2" name="material" style="width: 100%;">
                                <option value="">请选择场地材质</option>
                                <?php if(is_array($material)): foreach($material as $key=>$vo): if($venue['material'] == $vo): ?><option value="<?php echo ($key); ?>" selected="selected"><?php echo ($vo); ?></option>
                                        <?php else: ?>
                                        <option value="<?php echo ($key); ?>"><?php echo ($vo); ?></option><?php endif; endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>灯光效果</label>
                            <select class="form-control select2" name="light" style="width: 100%;">
                                <option value="">请选择灯光效果</option>
                                <?php if(is_array($light)): foreach($light as $key=>$vo): if($venue['light'] == $vo): ?><option value="<?php echo ($key); ?>" selected="selected"><?php echo ($vo); ?></option>
                                        <?php else: ?>
                                        <option value="<?php echo ($key); ?>"><?php echo ($vo); ?></option><?php endif; endforeach; endif; ?>
                            </select>
                        </div>

                        <!-- input states -->
                        <!--<div class="form-group has-success">-->
                            <!--<label class="control-label" for="inputSuccess"><i class="fa fa-check"></i> Input with success</label>-->
                            <!--<input type="text" class="form-control" id="inputSuccess" placeholder="Enter ...">-->
                        <!--</div>-->
                        <!--<div class="form-group has-warning">-->
                            <!--<label class="control-label" for="inputWarning"><i class="fa fa-bell-o"></i> Input with warning</label>-->
                            <!--<input type="text" class="form-control" id="inputWarning" placeholder="Enter ...">-->
                        <!--</div>-->
                        <!--<div class="form-group has-error">-->
                            <!--<label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> Input with error</label>-->
                            <!--<input type="text" class="form-control" id="inputError" placeholder="Enter ...">-->
                        <!--</div>-->


                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-default">重置</button>
                        <button type="submit" class="btn btn-primary pull-right">提交设置</button>
                    </div><!-- /.box-footer -->
                    <style>
                        #form label.error{
                            color: #dd4b39;
                        }
                    </style>
                </form>
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
    <script src="/Public/Static/jquery.validate.min.js"></script>
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=bprghKO5NHFIW7Qd6Zs6hTjD"></script>
    <script src="/Public/Static/sweetalert-master/dist/sweetalert.min.js"></script>
    <?php if(!$venue['address']): ?><script type="text/javascript">
        function G(id) {
            return document.getElementById(id);
        }

        var map = new BMap.Map("l-map");
        function myFunCity(result){
            var cityName = result.name;
            map.centerAndZoom(cityName,12);      // 初始化地图,设置城市和地图级别。
            //alert("当前定位城市:"+cityName);
            $('#city').val(cityName);
        }
        var myCity = new BMap.LocalCity();
        myCity.get(myFunCity);

        var ac = new BMap.Autocomplete(    //建立一个自动完成的对象
                {"input" : "suggestId"
                    ,"location" : map
                });

        ac.addEventListener("onhighlight", function(e) {  //鼠标放在下拉列表上的事件
            var str = "";
            var _value = e.fromitem.value;
            var value = "";
            if (e.fromitem.index > -1) {
                value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
            }
            str = "FromItem<br />index = " + e.fromitem.index + "<br />value = " + value;

            value = "";
            if (e.toitem.index > -1) {
                _value = e.toitem.value;
                value = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
            }
            str += "<br />ToItem<br />index = " + e.toitem.index + "<br />value = " + value;
            G("searchResultPanel").innerHTML = str;
        });

        var myValue;
        ac.addEventListener("onconfirm", function(e) {    //鼠标点击下拉列表后的事件
            var _value = e.item.value;
            myValue = _value.province +  _value.city +  _value.district +  _value.street +  _value.business;
            G("searchResultPanel").innerHTML ="onconfirm<br />index = " + e.item.index + "<br />myValue = " + myValue;
            $('#city').val(_value.city);
            $('#district').val(_value.district);
            setPlace();
        });

        function setPlace(){
            map.clearOverlays();    //清除地图上所有覆盖物
            function myFun(){
                var pp = local.getResults().getPoi(0).point;    //获取第一个智能搜索的结果

                map.centerAndZoom(pp, 18);
                var mark = new BMap.Marker(pp);
                mark.enableDragging();
                map.addOverlay(mark);    //添加标注

                $('#lng').val(pp.lng);
                $('#lat').val(pp.lat);
            }

            var local = new BMap.LocalSearch(map, { //智能搜索
                onSearchComplete: myFun
            });

            local.search(myValue);
        }

//        var map = new BMap.Map("allmap");
//        var point = new BMap.Point(116.331398,39.897445);
//        map.centerAndZoom(point,15);
//        map.enableScrollWheelZoom();    //启用滚轮放大缩小
//
//        function myFun(result){
//            var cityName = result.name;
//            map.setCenter(cityName);
//            //alert("当前定位城市:"+cityName);
//            $('#city').val(cityName);
//        }
//        var myCity = new BMap.LocalCity();
//        myCity.get(myFun);
//
//        function showInfo(e){
//            //alert(e.point.lng + ", " + e.point.lat);
//            var pt = new BMap.Point(e.point.lng, e.point.lat);
//            var marker = new BMap.Marker(pt);//创建标注
//            map.addOverlay(marker);
//            map.panTo(pt);
//        }
//        map.addEventListener("click", showInfo);

//        var pt = new BMap.Point(116.417, 39.909);
//        var myIcon = new BMap.Icon("http://developer.baidu.com/map/jsdemo/img/fox.gif", new BMap.Size(300,157));
//        var marker2 = new BMap.Marker(pt,{icon:myIcon});  // 创建标注
//        map.addOverlay(marker2);

        $("#form").validate({
            rules: {
                address: "required",
                city: "required",
                district: "required",
                lng: "required",
                lat: "required",
                summary: "required",
                tel: "required",
                bus: "required"
            },
            messages: {
                address: "请填写地址",
                city: "请填写城市",
                district: "请填写区县",
                lng: "请填写经度",
                lat: "请填写纬度",
                summary: "请填写简介",
                tel: "请填写电话",
                bus: "请填写公交"
            }
        });
    </script>
    <?php else: ?>
    <script type="text/javascript">
        var map = new BMap.Map("l-map");
        var point = new BMap.Point(116.331398,39.897445);
        map.centerAndZoom(point,15);
        map.clearOverlays();
        var new_point = new BMap.Point($('#lng').val(),$('#lat').val());
        var marker = new BMap.Marker(new_point);  // 创建标注
        map.addOverlay(marker);              // 将标注添加到地图中
        map.panTo(new_point);

        $("#form").validate({
            rules: {
                address: "required",
                city: "required",
                district: "required",
                lng: "required",
                lat: "required",
                summary: "required",
                tel: "required",
                bus: "required"
            },
            messages: {
                address: "请填写地址",
                city: "请填写城市",
                district: "请填写区县",
                lng: "请填写经度",
                lat: "请填写纬度",
                summary: "请填写简介",
                tel: "请填写电话",
                bus: "请填写公交"
            }
        });
    </script><?php endif; ?>
    <script>
        $('.delete').on('click',function() {
            var id = $(this).attr("data-id"),url = $(this).attr("data-url"),tag = $(this).attr("data-tag");
            deleteData(id,url,tag);
        });

        function deleteData(id,url,tag) {
            swal({
                title: "提示",
                text: "您确定要清空相册吗？",
                type: "warning",
                //animation: "slide-from-top",
                showCancelButton: true,
                closeOnConfirm: false,
                confirmButtonText: "确定清空",
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
                                $(tag+'-body').remove();
                                $(tag).remove();
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