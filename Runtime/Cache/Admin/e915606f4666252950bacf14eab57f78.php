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


<!--分类管理: 编辑分类  -->
<div class="nav">
    <ul class="cc">
        <li><a href="<?php echo U('index');?>">配置项管理</a></li>
        <?php if(is_array(C("CONFIG_GROUP_LIST"))): $i = 0; $__LIST__ = C("CONFIG_GROUP_LIST");if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$group): $mod = ($i % 2 );++$i; if(($key) < "5"): ?><li <?php if(($id) == $key): ?>class="current"<?php endif; ?>><a href="<?php echo U('group',array('id'=>$key));?>"><?php echo ($group); ?>配置</a></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
    </ul>
</div>
<form class="J_ajaxForm" data-role="list" action="<?php echo U('save');?>" method="post">

<div class="h_a">相关配置</div>
<div class="table_full">
    <table width="100%">
        <colgroup><col class="th">
        <col width="400">
        <col>
        </colgroup>
        <tbody>
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$config): $mod = ($i % 2 );++$i;?><tr>
                <th><?php echo ($config["title"]); ?></th>
                <td>
                    	<?php switch($config["type"]): case "0": ?><input type="text" class="input length_5" name="config[<?php echo ($config["name"]); ?>]" value="<?php echo ($config["value"]); ?>"><?php break;?>
			<?php case "1": ?><input type="text" class="input length_5" name="config[<?php echo ($config["name"]); ?>]" value="<?php echo ($config["value"]); ?>"><?php break;?>
			<?php case "2": ?><label class="text">
				<textarea name="config[<?php echo ($config["name"]); ?>]" class="length_5"><?php echo ($config["value"]); ?></textarea>
			</label><?php break;?>
			<?php case "3": ?><label class="text">
				<textarea name="config[<?php echo ($config["name"]); ?>]" class="length_5"><?php echo ($config["value"]); ?></textarea>
			</label><?php break;?>
			<?php case "4": ?><select name="config[<?php echo ($config["name"]); ?>]" class="length_3">
				<?php $_result=parse_config_attr($config['extra']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($key); ?>" <?php if(($config["value"]) == $key): ?>selected<?php endif; ?>><?php echo ($vo); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
			</select><?php break;?>
			<?php case "5": ?><div class="text">
			<textarea name="config[<?php echo ($config["name"]); ?>]" class="length_5" readonly=""><?php echo ($config["value"]); ?></textarea>
			</div><?php break;?>
			<?php case "6": ?><input type="password" class="input length_5" name="config[<?php echo ($config["name"]); ?>]" value="<?php echo ($config["value"]); ?>"><?php break;?>
			<?php case "7": ?><!--增加富文本和非明文-->
			<div class="textarea">
			<textarea name="config[<?php echo ($config["name"]); ?>]" class="length_5"><?php echo ($config["value"]); ?></textarea>
	       	 	<?php echo hook('adminArticleEdit', array('name'=>$field['names'],'value'=>$config.name,'id'=>$config.name));?>
			</div><?php break; endswitch;?>
                </td>
                <td><div class="fun_tips"><?php echo ($config["remark"]); ?></div></td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
        </tbody>
    </table>
</div>

<div class="btn_wrap">
    <div class="btn_wrap_pd">
        <input type="hidden" name="id" value="<?php echo ($info["id"]); ?>" />
        <button type="submit" class="btn btn_submit J_ajax_submit_btn">提交</button>
    </div>
</div>
</form>

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