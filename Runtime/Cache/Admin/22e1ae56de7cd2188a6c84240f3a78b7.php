<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<title>系统后台</title>
<link href="/Public/Admin/css/admin_style.css" rel="stylesheet" />
<link href="/Public/Admin/css/jquery.jgrowl.css" rel="stylesheet" type="text/css"/>
<script>
//全局变量，是Global Variables不是Gay Video喔
var GV = {
	JS_ROOT : "/Public/Static/dev/",	//js目录
	JS_VERSION : "v20140707",	    //js版本号
	TOKEN : 'd9b0adaaa5b73879',	    //token ajax全局
	REGION_CONFIG : {},
	SCHOOL_CONFIG : {},
	URL : {
		LOGIN : '<?php echo U("Public/login");?>',	//后台登录地址
		IMAGE_RES: '/Public/Static/image/',	//图片目录
		REGION : '',	                //地区
		SCHOOL : ''	                    //学校
	}
};
</script>
<script src="/Public/Static/dev/wind.js"></script>
<script src="/Public/Static/dev/jquery.js"></script>
<link rel="stylesheet" type="text/css" href="/Public/Admin/css/admin_home.css" media="all">
</head>
<body>
	<!-- 主体 -->
    <div id="indexMain" class="index-main">
       <!-- 插件块 -->
       <div class="container-span"><?php echo hook('AdminIndex');?></div>
    </div>
<div id="msg" class="bottom-right"></div>
<script src="/Public/Static/dev/pages/admin/common/common.js"></script>
<script type="text/javascript" src="/Public/Admin/js/jquery.jgrowl.js"></script>
<script type="text/javascript">
    ;$(function() {
        $('#msg').jGrowl('<a href="/admin.php/MessageNotices/index" class="J_tabframe_trigger" data-level="2" data-parent="Business" data-id="MessageNotices_index">待处理消息数量：<span id="msgNum"><?php echo ($magNum); ?></span>，点击查看</a>', {
            theme:  'manilla',
            speed:  'slow',
            sticky: true,
            click: function(msg) {
                var _SUBMENU_CONFIG = parent.window.SUBMENU_CONFIG;		//导航数据
                parent.window.eachSubmenu(_SUBMENU_CONFIG, 'MessageNotices_index', 'Business', 2, '/admin.php/MessageNotices/index');
            }
        });

//        setInterval(function(){
//            $.post("<?php echo U('Public/getMsgNum');?>", function(data){
//                if(data.state == 'success'){
//                    $('#msgNum').text(data.message);
//                }
//            });
//        }, 120000);
    });
</script>
<?php if($remind): ?><script type="text/javascript">
        ;$(function() {
            $('#msg').jGrowl('<a href="/admin.php/Repayment/collectToday" class="J_tabframe_trigger" data-level="2" data-parent="Funds" data-id="Repayment_collectToday">今日还款数量：<span id="msgNum"><?php echo ($remind); ?></span>笔，点击查看</a>', {
                theme:  'manilla',
                speed:  'slow',
                sticky: true,
                click: function(msg) {
                    var _SUBMENU_CONFIG = parent.window.SUBMENU_CONFIG;		//导航数据
                    parent.window.eachSubmenu(_SUBMENU_CONFIG, 'Repayment_index', 'Funds', 2, '/admin.php/Repayment/collectToday');
                }
            });
        });
    </script><?php endif; ?>


</body>
</html>