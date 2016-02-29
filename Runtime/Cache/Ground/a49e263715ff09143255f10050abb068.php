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
    <link rel="stylesheet" href="/Public/Static/sweetalert-master/dist/sweetalert.css" />
    <link rel="stylesheet" href="/Public/Static/Swiper/dist/css/swiper.min.css">
    <link rel="stylesheet" href="/Public/Static/AdminLTE/plugins/daterangepicker/daterangepicker-bs3.css">
    <link rel="stylesheet" href="/Public/Ground/css/index.css">

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
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li><a href="<?php echo U('index');?>">订单管理</a></li>
                    <li class="active"><a href="<?php echo U('orders');?>">订单列表</a></li>
                    <li class="pull-right"><a href="#" class="text-muted"><i class="fa fa-gear"></i></a></li>
                </ul>
                <div class="tab-content">
                <div class="active tab-pane" id="activity">
                    <div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-warning"></i> 提示!</h4>
                        订单列表展示所有订单，并对订单进行搜索等相关操作
                    </div>

                    <div class="box box-default">
                        <form action="/play.php?s=/Orders/orders.html" method="post">
                            <input type="hidden" name="p" value="<?php echo ((isset($p) && ($p !== ""))?($p):1); ?>">
                            <input type="hidden" name="soso" value="1">
                            <div class="box-header with-border">
                                <h3 class="box-title">搜索</h3>
                                <div class="box-tools pull-right">
                                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                </div>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>单号</label>
                                            <input type="text" name="keyword" value="<?php echo ((isset($keyword) && ($keyword !== ""))?($keyword):''); ?>" class="form-control" id="keyword" placeholder="单号关键字">
                                        </div><!-- /.form-group -->

                                    </div><!-- /.col -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>项目和场地</label>
                                            <div class="row" id="element">
                                                <div class="col-md-6">
                                                    <select class="form-control select2 items" name="items" disabled="disabled" data-url="<?php echo U('getItems');?>" style="width: 100%;">
                                                        <option selected="selected">请选择</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <select class="form-control select2 block" name="block" disabled="disabled" data-url="<?php echo U('getBlocks');?>" data-json-space="data" style="width: 100%;">
                                                        <option selected="selected">请选择</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div><!-- /.form-group -->

                                    </div><!-- /.col -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>类型</label>
                                            <select class="form-control select2" name="typestxt" style="width: 100%;">
                                                <option>请选择</option>
                                                <?php if(is_array($typestxtArr)): foreach($typestxtArr as $key=>$vo): if($typestxt == $key): ?><option value="<?php echo ($key); ?>" selected><?php echo ($vo["title"]); ?></option>
                                                        <?php else: ?>
                                                        <option value="<?php echo ($key); ?>"><?php echo ($vo["title"]); ?></option><?php endif; endforeach; endif; ?>
                                            </select>
                                        </div><!-- /.form-group -->
                                    </div><!-- /.col -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>状态</label>
                                            <select class="form-control select2" name="statustxt" style="width: 100%;">
                                                <option>请选择</option>
                                                <?php if(is_array($statustxtArr)): foreach($statustxtArr as $key=>$vo): if($statustxt == $key): ?><option value="<?php echo ($key); ?>" selected><?php echo ($vo["title"]); ?></option>
                                                        <?php else: ?>
                                                        <option value="<?php echo ($key); ?>"><?php echo ($vo["title"]); ?></option><?php endif; endforeach; endif; ?>
                                            </select>
                                        </div><!-- /.form-group -->
                                    </div><!-- /.col -->
                                </div><!-- /.row -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>订单时间:</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="text" name="ordersdate" value="<?php echo ((isset($ordersdate) && ($ordersdate !== ""))?($ordersdate):''); ?>" class="form-control pull-right" readonly id="reservation">
                                            </div>
                                            <!-- /.input group -->
                                        </div>
                                    </div>

                                </div>
                            </div><!-- /.box-body -->
                            <div class="box-footer">
                                <button type="reset" class="btn btn-default">重置</button>

                                <?php if($soso): ?><a class="btn btn-warning pull-right" href="<?php echo U('orders');?>" style="margin-left: 10px">撤销检索</a><?php endif; ?>

                                <button type="submit" class="btn btn-primary pull-right">搜索</button>
                            </div>
                        </form>

                    </div><!-- /.box -->

                    <?php if($list): ?><div class="box box-primary">
                            <div class="box-header with-border">
                                <h3 class="box-title">列表</h3>
                                <div class="box-tools pull-right">
                                    <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                </div>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <table class="table table-bordered table-striped table-hover">
                                    <tr>
                                        <th>单号</th>
                                        <th>运动项目</th>
                                        <th>场地</th>
                                        <th>时间</th>
                                        <th>价格</th>
                                        <th>类型</th>
                                        <th>下单时间</th>
                                        <th>状态</th>
                                        <th style="width: 100px">操作</th>
                                    </tr>
                                    <?php if(is_array($list)): foreach($list as $key=>$vo): ?><tr id="ordersdel-<?php echo ($vo["id"]); ?>" <?php if($vo['status'] == -1): ?>class="text-red"<?php endif; ?>>
                                            <td><?php echo ($vo["num"]); ?></td>
                                            <td><?php echo ($vo["name"]); ?></td>
                                            <td><?php echo ($vo["block_name"]); ?></td>
                                            <td><?php echo ($vo["nodes"]); ?> <?php echo ($vo["start"]); ?>-<?php echo ($vo["end"]); ?></td>
                                            <td><?php echo ($vo["price"]); ?></td>
                                            <td>
                                                <?php if($vo['types']): ?>现场预定
                                                    <?php else: ?>
                                                    线上预定<?php endif; ?>
                                            </td>
                                            <td><?php echo (time_format($vo["created_time"])); ?></td>
                                            <td id="ordersdel-status-<?php echo ($vo["id"]); ?>">
                                                <?php switch($vo["status"]): case "-1": ?>撤单<?php break;?>
                                                    <?php case "1": ?>已付款<?php break;?>
                                                    <?php case "2": ?>已使用<?php break;?>
                                                    <?php case "8": ?>已结算<?php break;?>
                                                    <?php default: ?>未付款<?php endswitch;?>
                                            </td>

                                            <td>
                                                <a data-toggle="modal" href="<?php echo U('ordersview',['id'=>$vo['id']]);?>" data-target="#modal_<?php echo ($vo["id"]); ?>"><span class="label label-success">查看</span></a>
                                                <?php if($vo['status'] == 1): ?><a href="#" id="ordersdel-delbtn-<?php echo ($vo["id"]); ?>" class="delete" data-id="<?php echo ($vo["id"]); ?>" data-url="<?php echo U('ordersdel');?>" data-tag="#ordersdel"><span class="label label-warning">撤单</span></a><?php endif; ?>
                                                <div class="modal fade" id="modal_<?php echo ($vo["id"]); ?>" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">

                                                        </div><!-- /.modal-content -->
                                                    </div><!-- /.modal-dialog -->
                                                </div><!-- /.modal -->
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

                </div><!-- /.tab-pane -->
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
    <!-- date-range-picker -->
    <script src="/Public/Static/AdminLTE/plugins/daterangepicker/moment.min.js"></script>
    <script src="/Public/Static/AdminLTE/plugins/daterangepicker/daterangepicker.js"></script>
    <script src="/Public/Static/select/js/jquery.cxselect.min.js"></script>
    <script>
        //Date range picker
        $('#reservation').daterangepicker({
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

        $('.delete').on('click',function() {
            var id = $(this).attr("data-id"),url = $(this).attr("data-url"),tag = $(this).attr("data-tag");
            $.ajax({
                type: "POST",
                url: "<?php echo U('checkTimes');?>",
                data: {id:id},
                success: function(data){
                    if(data.msg){
                        swal("错误", data.msg, "error");
                    }else{
                        deleteData(id,url,tag);
                    }
                }
            });
        });

        function deleteData(id,url,tag) {
            swal({
                title: "提示",
                text: "撤单后该订单无法进行结算，您确定要撤单吗？",
                type: "warning",
                //animation: "slide-from-top",
                showCancelButton: true,
                closeOnConfirm: false,
                confirmButtonText: "确定撤单",
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
                                $(tag+'-'+id).addClass('text-red');
                                $(tag+'-status-'+id).text('撤单');
                                $(tag+'-delbtn-'+id).remove();
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