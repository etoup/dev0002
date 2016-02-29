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
</head>
<body>
<div class="wrap">
    <div class="nav">
        <ul class="cc">
            <li class="current"><a href="<?php echo U('index');?>">配置项管理</a></li>
            <li><a href="<?php echo U('group');?>">基础配置</a></li>
        </ul>
    </div>
    <div class="h_a">提示信息</div>
    <div class="mb10 prompt_text">
        <ul>
            <li>配置项管理：新增、删除相关系统配置项</li>
        </ul>
    </div>
    <div class="mb10"><a class="btn" href="<?php echo U('add');?>" title="添加配置项" role="button"><span class="add"></span>添加配置项</a></div>
    <div class="h_a">搜索</div>
    <div class="search_type cc mb10">
        <form action="/admin.php?s=/Config/index.html" method="post">
        <input type="hidden" name="p" value="<?php echo ((isset($p) && ($p !== ""))?($p):1); ?>">
        <input type="hidden" name="soso" value="1">
        <label>分组类型：</label>
        <select name="gids" size="5" class="mr10" multiple>
            <?php if(is_array($gids)): foreach($gids as $key=>$item): if($key == $groupList): ?><option value="<?php echo ($key); ?>" selected=""><?php echo ($item); ?></option>
                <?php else: ?>
                <option value="<?php echo ($key); ?>"><?php echo ($item); ?></option><?php endif; endforeach; endif; ?>
        </select>
        <label>关键字包含：</label><input name="name" type="text" class="input length_2 mr10" value="<?php echo ((isset($name) && ($name !== ""))?($name):''); ?>">
        <button type="submit" class="btn">搜索</button>
        <?php if(!empty($soso)): if($soso): ?><button type="reset" class="btn btn_error" onclick="window.location.href='<?php echo U('index');?>'">撤销检索</button><?php endif; endif; ?>
        </form>
    </div>
    <div id="top-alert" class="alert alert-error" style="display: none;">
        <button class="close fixed" style="margin-top: 4px;">×</button>
        <div class="alert-content">这是内容</div>
    </div>
    <?php if(!empty($list)): ?><div class="table_list">
        <table width="100%">
            <thead>
                <tr>
                    <td width="60">ID</td>
                    <td>名称</td>
                    <td>标题</td>
                    <td>分组</td>
                    <td>类型</td>
                    <td>操作</td>
                </tr>
            </thead>
        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$config): $mod = ($i % 2 );++$i;?><tr>
                <td><?php echo ($config["id"]); ?></td>
                <td><a href="<?php echo U('edit?id='.$config['id']);?>"><?php echo ($config["name"]); ?></a></td>
                <td><?php echo ($config["title"]); ?></td>
                <td><?php echo (get_config_group($config["group"])); ?></td>
                <td><?php echo (get_config_type($config["type"])); ?></td>
                <td>
                    <a class="mr10" title="编辑" href="<?php echo U('edit',array('id'=>$config['id']));?>">编辑</a>
					<a class="mr10 J_ajax_del" title="删除" href="<?php echo U('del',array('id'=>$config['id']));?>">删除</a>
                </td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </table>
    </div>
    <div class="page">
        <?php echo ($_page); ?>
    </div>
    <?php else: ?>
    <div class="not_content_mini"><i></i>啊哦，没有符合条件的内容！</div><?php endif; ?>  
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