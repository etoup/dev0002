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

    
    <link rel="stylesheet" href="/Public/Static/icon/iconfont.css" />
    <link rel="stylesheet" href="/Public/Static/AdminLTE/plugins/timepicker/bootstrap-timepicker.min.css">
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
            <div class="box box-primary">
                <form id="form" role="form" action="/play.php?s=/Setting/items.html" method="post">
                    <div class="box-header with-border">
                        <h3 class="box-title">添加项目</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="form-group">
                            <label>选择项目 </label>
                            <div>
                                <select class="form-control select2" name="items_id" id="items_id" style="width: 100%;">
                                    <option selected="selected" value="">请选择</option>
                                    <?php if(is_array($items)): foreach($items as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="bootstrap-timepicker">
                            <div class="form-group">
                                <label>开始时间 </label>
                                <div class="input-group">
                                    <input type="text" name="start" value="06:00" id="start" class="form-control timepicker">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                </div><!-- /.input group -->
                            </div><!-- /.form group -->
                        </div>
                        <div class="bootstrap-timepicker">
                            <div class="form-group">
                                <label>结束时间 </label>
                                <div class="input-group">
                                    <input type="text" name="end" value="21:00" id="end" class="form-control timepicker">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                </div><!-- /.input group -->
                            </div><!-- /.form group -->
                        </div>
                        <div class="form-group">
                            <label>运动时长 </label>
                            <div class="input-group">
                                <input type="text" name="duration" value="60" id="duration" class="form-control" placeholder="运动时长">
                                <span class="input-group-addon">分</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>初始价格 </label>
                            <div class="input-group">
                                <input type="text" name="price" value="100" id="price" class="form-control" placeholder="初始价格">
                                <span class="input-group-addon">元</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>初始场地数量 </label>
                            <div class="input-group">
                                <input type="text" name="num" value="5" id="num" class="form-control" placeholder="场地数量">
                                <span class="input-group-addon">块</span>
                            </div>
                        </div>

                    </div>
                    <div class="box-footer">
                        <span id="summary" class="text-red"></span>
                        <button type="submit" class="btn btn-primary pull-right" id="submit">提交设置</button>
                    </div><!-- /.box-footer -->
                    <style>
                        #form label.error{
                            color: #dd4b39;
                        }
                    </style>
                </form>
            </div>
        </div>
        <div class="col-md-9">
            <?php if(!$venue_items): ?><div class="alert alert-warning alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <h4><i class="icon fa fa-warning"></i>提示</h4>
                    您没有添加项目，请在左边进行新增项目设置
                </div>
                <?php else: ?>
                <div class="row">
                    <?php if(is_array($venue_items)): foreach($venue_items as $key=>$vo): ?><div class="col-md-3">
                            <!-- Profile Image -->
                            <?php if($vo['status'] == -1): ?><div class="box box-danger" id="venueItems-box-<?php echo ($vo["id"]); ?>"><?php else: ?><div class="box box-primary" id="venueItems-box-<?php echo ($vo["id"]); ?>"><?php endif; ?>
                                <div class="box-header">
                                    <div class="box-tools pull-right">
                                        <?php switch($vo["status"]): case "-1": ?><button type="button" class="btn btn-box-tool ocAjax" data-toggle="tooltip" title="开启" data-id="<?php echo ($vo["id"]); ?>" data-status="1" data-url="<?php echo U('Ajax/ocVenueItems');?>" data-tag="#venueItems" id="venueItems-<?php echo ($vo["id"]); ?>"><i class="fa fa-check"></i></button><?php break;?>
                                            <?php case "1": ?><button type="button" class="btn btn-box-tool ocAjax" data-toggle="tooltip" title="关闭" data-id="<?php echo ($vo["id"]); ?>" data-status="-1" data-url="<?php echo U('Ajax/ocVenueItems');?>" data-tag="#venueItems" id="venueItems-<?php echo ($vo["id"]); ?>"><i class="fa fa-times"></i></button><?php break; endswitch;?>
                                    </div>
                                </div>
                                <div class="box-body box-profile">
                                    <i class="icon iconfont" style="font-size: 50px; text-align: center; display: block;"><?php echo ($vo['icon']); ?></i>

                                    <h3 class="profile-username text-center"><?php echo ($vo["name"]); ?></h3>

                                    <p class="text-muted text-center"><?php echo (time_format($vo["created_time"])); ?></p>

                                    <ul class="list-group list-group-unbordered">
                                        <li class="list-group-item">
                                            <b>开放时间</b> <a class="pull-right"><?php echo ($vo["start"]); ?>-<?php echo ($vo["end"]); ?></a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>运动时长</b> <a class="pull-right"><?php echo ($vo["duration"]); ?>'</a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>初始价格</b> <a class="pull-right"><?php echo (money($vo["price"])); ?></a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>初始场地</b> <a class="pull-right"><?php echo ($vo["num"]); ?></a>
                                        </li>
                                    </ul>
                                    <?php if($vo['status'] == -1): ?><a href="<?php echo U('block',['items_id'=>$vo['items_id']]);?>" class="btn btn-danger btn-block" id="venueItems-btn-<?php echo ($vo["id"]); ?>"><b>查看场地</b></a>
                                        <?php else: ?>
                                        <a href="<?php echo U('block',['items_id'=>$vo['items_id']]);?>" class="btn btn-primary btn-block" id="venueItems-btn-<?php echo ($vo["id"]); ?>"><b>查看场地</b></a><?php endif; ?>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div><?php endforeach; endif; ?>
                </div><?php endif; ?>
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
    <script src="/Public/Static/AdminLTE/plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="/Public/Static/sweetalert-master/dist/sweetalert.min.js"></script>
    <script type="text/javascript">
        $('#submit').on('click', function () {
            var $btn = $(this).button('loading');

            $("#form").validate({
                errorPlacement:function(error,element) {
                    error.appendTo(element.parent('div').prev('label'));
                },
                rules: {
                    items_id: "required",
                    start   : "required",
                    end     : "required",
                    duration: "required",
                    price   : "required",
                    num     : "required"
                },
                messages: {
                    items_id: "请选择项目",
                    start   : "请填写开始时间",
                    end     : "请填写结束时间",
                    duration: "请填写运动时长",
                    price   : "请填写初始价格",
                    num     : "请填写初始场地数量"
                },
                showErrors:function(errorMap,errorList) {
                    $("#summary").html('<i class="fa fa-fw fa-info-circle"></i> '+this.numberOfInvalids()+'个选项没有填写完整');
                    this.defaultShowErrors();
                    if(this.numberOfInvalids() > 0){
                        $btn.button('reset');
                    }
                }
            });
        });



        $(".timepicker").timepicker({
            showInputs: false,
            showMeridian:false
        });

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
                                        $(tag+'-'+id).attr('data-status','1').children('i').attr('class','fa fa-check');
                                        $(tag+'-box-'+id).attr('class','box box-danger');
                                        $(tag+'-btn-'+id).attr('class','btn btn-danger btn-block');
                                        break;
                                    case '1':
                                        $(tag+'-'+id).attr('data-status','-1').children('i').attr('class','fa fa-times');
                                        $(tag+'-box-'+id).attr('class','box box-primary');
                                        $(tag+'-btn-'+id).attr('class','btn btn-primary btn-block');
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