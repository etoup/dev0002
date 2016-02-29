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
    <link rel="stylesheet" href="/Public/Static/AdminLTE/plugins/datepicker/datepicker3.css">
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
                    <li class="active"><a href="<?php echo U('index');?>">订单管理</a></li>
                    <li><a href="<?php echo U('orders');?>">订单列表</a></li>
                    <li class="pull-right">
                        <a href="<?php echo U('index',['d'=>$pre_date]);?>" class="btn btn-box-tool" data-toggle="tooltip" title="昨天" data-original-title="昨天" style="display: inline-block;"><i class="fa fa-chevron-left"></i></a>
                        <a href="<?php echo U('index',['d'=>$next_date]);?>" class="btn btn-box-tool" data-toggle="tooltip" title="明天" data-original-title="明天" style="display: inline-block;"><i class="fa fa-chevron-right"></i></a>
                    </li>
                </ul>
                <div class="tab-content">
                <div class="active tab-pane" id="activity">
                    <div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><i class="icon fa fa-warning"></i> 提示!</h4>
                        可以点击下面价格进行现场预定，请注意时间段
                    </div>
                    <?php if(is_array($list)): foreach($list as $key=>$vo): ?><div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?php echo ($vo["name"]); ?></h3> <small><em><?php echo ($vo["date_week"]); ?></em></small>
                                <!-- tools box -->
                                <div class="pull-right box-tools">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                                </div><!-- /. tools -->
                            </div>
                            <div class="box-body">
                                <div class="mailbox-controls with-border text-center" style="margin-bottom: 5px;">
                                    <button type="button" class="btn btn-default btn-sm" data-toggle="tooltip" data-container="body" title="" data-original-title="待订">
                                        <i class="fa  fa-circle-o"></i></button>
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="tooltip" data-container="body" title="" data-original-title="已订">
                                        <i class="fa fa-circle"></i></button>
                                    <button type="button" class="btn btn-warning btn-sm" data-toggle="tooltip" data-container="body" title="" data-original-title="选中">
                                        <i class="fa fa-check"></i></button>
                                    <button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" data-container="body" title="锁定">
                                        <i class="fa fa-lock"></i></button>
                                </div>
                                <div class="table_main">
                                    <div class="table_body">
                                        <div class="table_body_left">
                                            <?php if(is_array($vo['nodes'])): foreach($vo['nodes'] as $key=>$vv): ?><div class="table_body_li"><?php echo ($vv["start"]); ?>-<?php echo ($vv["end"]); ?></div><?php endforeach; endif; ?>
                                        </div>
                                        <div class="table_body_right">
                                            <?php if(is_array($vo['blocks'])): foreach($vo['blocks'] as $key=>$v): if(is_array($v['prices'])): foreach($v['prices'] as $key=>$p): if($p['has_order']): ?><div class="modal fade" id="modal_<?php echo ($p["order_id"]); ?>" tabindex="-1" role="dialog">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">

                                                                </div><!-- /.modal-content -->
                                                            </div><!-- /.modal-dialog -->
                                                        </div><!-- /.modal --><?php endif; endforeach; endif; endforeach; endif; ?>
                                            <!-- Swiper -->
                                            <div class="swiper-container swiper-container-<?php echo ($vo['id']); ?>">

                                                <div class="swiper-wrapper">
                                                    <?php if(is_array($vo['blocks'])): foreach($vo['blocks'] as $key=>$v): ?><div class="swiper-slide">
                                                            <div class="right_li">
                                                                <div class="right_li_title"><?php echo ($v["name"]); ?></div>
                                                                <?php if(is_array($v['prices'])): foreach($v['prices'] as $key=>$p): if($p['has_order']): ?><a class="right_li_price suc" data-toggle="modal" href="<?php echo U('ordersview',['id'=>$p['order_id']]);?>" data-target="#modal_<?php echo ($p["order_id"]); ?>"><?php echo ($p["price"]); ?>元</a>
                                                                        <?php else: ?>
                                                                        <?php switch($p["status"]): case "1": ?><div class="right_li_price" id="price-<?php echo ($p["id"]); ?>"><?php echo ($p["price"]); ?>元</div>
                                                                                <input type="checkbox" name="check" value="<?php echo ($p["id"]); ?>" style="display: none;" /><?php break;?>
                                                                            <?php default: ?>
                                                                                <div class="right_li_price lock"><?php echo ($p["price"]); ?>元</div><?php endswitch; endif; endforeach; endif; ?>
                                                            </div>
                                                        </div><?php endforeach; endif; ?>
                                                </div>
                                                <?php if(count($vo['blocks']) > 10): ?><!-- Add Pagination -->
                                                    <div class="swiper-pagination swiper-pagination-<?php echo ($vo['id']); ?>" style="bottom: 5px;"></div><?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <div class="pull-right">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-default swiper-button-prev-<?php echo ($vo['id']); ?>" data-toggle="tooltip" data-container="body" title="prev"><i class="fa fa-chevron-left"></i></button>
                                        <button type="button" class="btn btn-default swiper-button-next-<?php echo ($vo['id']); ?>" data-toggle="tooltip" data-container="body" title="next"><i class="fa fa-chevron-right"></i></button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-info reserve"><i class="fa fa-check"></i> 现场预定</button>
                            </div><!-- /.box-footer -->
                        </div><?php endforeach; endif; ?>

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
    <!-- Swiper JS -->
    <script src="/Public/Static/Swiper/dist/js/swiper.min.js"></script>
    <script src="/Public/Static/sweetalert-master/dist/sweetalert.min.js"></script>
    <script src="/Public/Static/AdminLTE/plugins/datepicker/bootstrap-datepicker.js"></script>
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
                var id = vals,url = "<?php echo U('reserves');?>",tag = "#price", nodes = "<?php echo ($nodes); ?>";
                reserves(id,url,tag,nodes);
            }
        });

        $("#datepicker").datepicker();

        function checkedValues(){
            var chk_value =[];
            $('input[name="check"]:checked').each(function(){
                chk_value.push($(this).val());
            });
            return chk_value;
        }

        function reserves(id,url,tag,nodes) {
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
                    data:{id:id,mobile:inputValue,nodes:nodes}
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
                $('.table_main').css({width: '630px'});
                $('.table_body_right').css({width: '525px'});
                p = 5;
            }
            else if(pageSize.windowWidth > 1024 && pageSize.windowWidth<1280){
                $('.table_main').css({width: '945px'});
                $('.table_body_right').css({width: '840px'});
                p = 8;
            }
            else if(pageSize.windowWidth > 1280 && pageSize.windowWidth<1440){
                $('.table_main').css({width: '945px'});
                $('.table_body_right').css({width: '840px'});
                p = 8;
            }
            else if(pageSize.windowWidth > 1440 && pageSize.windowWidth<1600){
                $('.table_main').css({width: '1155px'});
                $('.table_body_right').css({width: '1050px'});
                p = 10;
            }
            else if(pageSize.windowWidth > 1600 && pageSize.windowWidth<1920){
                $('.table_main').css({width: '1575px'});
                $('.table_body_right').css({width: '1470px'});
                p = 14;
            }
            else if(pageSize.windowWidth > 1920){
                $('.table_main').css({width: '1680px'});
                $('.table_body_right').css({width: '1575px'});
                p = 15;
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