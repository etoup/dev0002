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
<div class="wrap J_check_wrap">
    <div class="nav">
        <ul class="cc">
            <li class="current"><a href="<?php echo U('index');?>">场馆列表</a></li>
            <li><a href="<?php echo U('items');?>">运动项目</a></li>
        </ul>
    </div>
    <div class="h_a">提示信息</div>
    <div class="mb10 prompt_text">
        <ul>
            <li>用户名和电子邮箱支持模糊搜索。用户名或电子邮箱输入“a” 则检索出所有以a开头的用户名或电子邮箱。</li>
            <li>可以对场馆进行新增、编辑、关闭、删除等操作。</li>
        </ul>
    </div>
    <div class="mb10"><a href="<?php echo U('add');?>" class="btn J_dialog" title="添加场馆" role="button"><span class="add"></span>添加场馆</a></div>
    <div class="h_a">搜索</div>
    <div class="search_type cc mb10">
        <form action="/admin.php?s=/Venue/index.html" method="post">
        <input type="hidden" name="p" value="<?php echo ((isset($p) && ($p !== ""))?($p):1); ?>">
        <input type="hidden" name="soso" value="1">
        <label>关键字包含：</label><input name="username" type="text" class="input length_2 mr10" value="<?php echo ((isset($username) && ($username !== ""))?($username):''); ?>" placeholder="用户名或真实姓名">
        <label>手机号码：</label><input name="mobile" type="text" class="input mr10" value="<?php echo ((isset($mobile) && ($mobile !== ""))?($mobile):''); ?>">
        <label>电子邮箱：</label><input name="email" type="text" class="input mr10" value="<?php echo ((isset($email) && ($email !== ""))?($email):''); ?>">
        <label>注册时间：</label><input type="text" name="time_start" value="<?php echo ((isset($time_start) && ($time_start !== ""))?($time_start):''); ?>" class="input length_2 mr5 J_date"><span class="mr5">至</span><input type="text" name="time_end" value="<?php echo ((isset($time_end) && ($time_end !== ""))?($time_end):''); ?>" class="input length_2 mr10 J_date">
        <button type="submit" class="btn mr5">搜索</button>
        <?php if(!empty($soso)): if($soso): ?><button type="reset" class="btn btn_error" onclick="window.location.href='<?php echo U('index');?>'">撤销检索</button><?php endif; endif; ?>
        </form>
    </div>
    <div id="top-alert" class="alert alert-error" style="display: none;">
        <button class="close fixed" style="margin-top: 4px;">×</button>
        <div class="alert-content">这是内容</div>
    </div>
    <?php if(!empty($_list)): ?><form method="post" action="#" id="J_tag_form" class="J_ajaxForm">
    <div class="table_list">
        <table width="100%">
            <colgroup>
            <col width="65">
            </colgroup>
            <thead>
                <tr>
                    <td>
                        <label>
                            <input class="J_check_all" data-checklist="J_check_x" data-direction="x" type="checkbox">全选</label>
                    </td>
                    <td>UID</td>
                    <td>用户名</td>
                    <td>场馆名称</td>
                    <td>手机号码</td>
                    <td>电子邮箱</td>
                    <td>注册时间</td>
                    <td>状态</td>
                    <td>操作</td>
                </tr>
            </thead>
            <tbody id="J_tag_list">
            <?php if(is_array($_list)): $i = 0; $__LIST__ = $_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                <td>
                    <input class="J_check" name="id[]" value="<?php echo ($vo['id']); ?>" data-name="<?php echo (op_t($vo["username"])); ?>" data-tid="<?php echo ($vo['id']); ?>" data-xid="J_check_x" data-yid="J_check_y" type="checkbox">
                </td>
                <td><?php echo ($vo["uid"]); ?></td>
                <td><a href="<?php echo U('Admin/User/edit',array('uid'=>$vo['id']));?>" ><?php echo (op_t($vo["username"])); ?></a></td>
                <td><?php echo (op_t($vo["name"])); ?></td>
                <td><?php echo ($vo["mobile"]); ?></td>
                <td><?php echo ($vo["email"]); ?></td>
                <td><?php echo (time_format($vo["reg_time"])); ?></td>
                <td>
                    <?php if($vo['status']): ?>启用<?php else: ?>禁用<?php endif; ?>
                </td>
                <td>
                    <a href="<?php echo U('edit?id='.$vo['id']);?>" class="mr10 J_dialog" title="编辑场馆信息">编辑</a>
                    <a href="<?php echo U('del',array('id'=>$vo['id']));?>" class="mr10 J_ajax_del">删除</a>
                </td>
            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
    </div>
    <div class="page">
        <?php echo ($_page); ?>
    </div>
    <div class="btn_wrap">
        <div class="btn_wrap_pd" id="J_sub_wrap">
            <label class="mr10"><input class="J_check_all" data-checklist="J_check_y" data-direction="y" type="checkbox">全选</label>
            <button class="btn J_ajax_submit_btn" data-subcheck="true" data-action="<?php echo U('enable');?>" type="submit">启用</button>
            <button class="btn J_ajax_submit_btn" data-subcheck="true" data-action="<?php echo U('disable');?>" type="submit">禁用</button>
            <button class="btn J_ajax_submit_btn btn_error" data-subcheck="true" data-msg="确定要删除吗？" data-action="<?php echo U('del');?>" type="submit">删除</button>
        </div>
    </div>
    </form>
    <?php else: ?>
    <div class="not_content_mini"><i></i>啊哦，没有符合条件的用户！</div><?php endif; ?>  
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


<script>
    Wind.use('dialog', 'ajaxForm', function(){
        var URL_MERGE = "<?php echo U('appoint');?>", //合并
                URL_MOVE = "<?php echo U('move');?>";       //移动

        //点击合并
        $('#J_merger_btn').on('click', function(e){
            e.preventDefault();
            tagManPop($(this), 'merger');

        });

        //点击移动
        $('#J_move_btn').on('click', function(e){
            e.preventDefault();
            tagManPop($(this), 'move');
        });

        function tagManPop(btn, type) {
            var url = (type == 'merger' ? URL_MERGE : URL_MOVE),
                    title = (type == 'merger' ? '合并到关联话题' : '移动分类');
            btn.parent().find('span').remove();
            if(getCheckedTr()) {
                Wind.dialog.open( url ,{
                    onClose : function() {
                        btn.focus();
                    },
                    title: title
                });

            }else{
                $( '<span class="tips_error">请至少选择一项</span>' ).appendTo(btn.parent()).fadeIn( 'fast' );
            }

        }

        //选择统计
        function getCheckedTr(){
            if($('#J_tag_list input.J_check:checked').length >= 1) {
                return true;
            }else{
                return false;
            }
        }

    });
</script>
</body>
</html>