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
                <li class="current">
                    <a href="<?php echo U('index');?>">话题管理</a>
                </li>
                <li>
                    <a href="<?php echo U('category');?>">话题分类</a>
                </li>
            </ul>
        </div>
        <div class="h_a">功能说明</div>
        <div class="prompt_text">话题，也叫标签，通过给文章设置标签，可以将不同分类的文章汇聚在一起，形成另一种维度的内容聚合。</div>
        <div class="h_a">搜索</div>
        <div class="search_type cc mb10">
            <form action="/admin.php?s=/Tag/index.html" method="post"> 
                <input type="hidden" name="p" value="<?php echo ((isset($p) && ($p !== ""))?($p):1); ?>">
                <input type="hidden" name="soso" value="1">
                <div class="ul_wrap">
                    <ul class="cc">
                        <li>
                            <label>话题名称：</label>
                            <input type="text" class="input length_3" name="keyword" value="<?php echo ($keyword); ?>"></li>
                        <li>
                            <label>热门话题：</label>
                            <select name="ifhot" class="select_3">
                                <option value="-1" <?php echo is_selected($ifhot,-1);?>>不限</option>
                                <option value="1" <?php echo is_selected($ifhot,1);?>>热门</option>
                                <option value="0" <?php echo is_selected($ifhot,0);?>>不热门</option>
                            </select>
                        </li>
                        <li>
                            <label>话题分类：</label>
                            <select name="categoryId" class="select_3">
                                <option value="0">不限</option>
                                <?php if(is_array($categories)): $i = 0; $__LIST__ = $categories;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><option value="<?php echo ($v['category_id']); ?>" <?php echo is_selected($categoryId,$v['category_id']);?>><?php echo ($v['category_name']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                        </li>
                        <li>
                            <label>关注数：</label>
                            <input class="input length_1 mr10" type="number" name="minAttention" value="<?php echo ($minAttention); ?>">
                            <span class="mr10">至</span>
                            <input class="input length_1" type="text" name="maxAttention" value="<?php echo ($maxAttention); ?>"></li>
                        <li>
                            <label>内容数：</label>
                            <input class="input length_1 mr10" type="number" name="minContent" value="<?php echo ($minContent); ?>">
                            <span class="mr10">至</span>
                            <input class="input length_1" type="text" name="maxContent" value="<?php echo ($maxContent); ?>"></li>
                    </ul>
                </div>
                <div class="btn_side">
                    <button class="btn" type="submit">搜索</button>
                    <?php if(!empty($soso)): if($soso): ?><button type="reset" class="btn btn_error" onclick="window.location.href='<?php echo U('index');?>'">撤销检索</button><?php endif; endif; ?>
                </div>
            </form>
        </div>
        <div class="mb10">
            <a title="添加话题" href="<?php echo U('add');?>" class="btn J_dialog">
                <span class="add"></span>
                添加话题
            </a>
        </div>
        <?php if($list): ?><form method="post" action="#" id="J_tag_form" class="J_ajaxForm">
            <div class="table_list">
                <table width="100%">
                    <colgroup>
                    <col width="65">
                    <col width="70">
                    <col width="250">
                    <col>
                    <col>
                    <col width="120"></colgroup>
                <thead>
                    <tr>
                        <td>
                            <label>
                                <input class="J_check_all" data-checklist="J_check_x" data-direction="x" type="checkbox">全选</label>
                        </td>
                        <td>话题logo</td>
                        <td>名称/描述</td>
                        <td>内容数/被关注数</td>
                        <td>话题分类</td>
                        <td>关联话题</td>
                        <td>允许热门</td>
                        <td>操作</td>
                    </tr>
                </thead>
                <tbody id="J_tag_list">
                    <?php if(is_array($list)): foreach($list as $key=>$v): ?><tr>
                        <td>
                            <input class="J_check" name="tag_id[]" value="<?php echo ($v['tag_id']); ?>" data-name="<?php echo ($v['tag_name']); ?>" data-tid="<?php echo ($v['tag_id']); ?>" data-xid="J_check_x" data-yid="J_check_y" type="checkbox"></td>
                        <td>
                            <?php if($v['iflogo']): ?><img src="<?php echo ($v["tag_logo"]); ?>" width="50" height="50" class="fl" />
                            <?php else: ?>
                            <img src="/Uploads/Tag/default.png" width="50" height="50" class="fl" /><?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo U('edit',array('tag_id'=>$v['tag_id']));?>" target="_blank" class="J_tag_name J_dialog"><?php echo ($v['tag_name']); ?></a>
                            <p><?php echo ($v['excerpt']); ?></p>
                        </td>
                        <td>
                            <?php echo ($v['content_count']); ?>/
                            <span><?php echo ($v['attention_count']); ?></span>
                        </td>
                        <td><?php echo ($v['cl']); ?></td>
                        <td>
                            <?php echo ($v['rl']); ?>
                        </td>
                        <td>
                            <?php if($v['ifhot']): ?>是<?php else: ?>否<?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo U('edit',array('tag_id'=>$v['tag_id']));?>" title="编辑话题" class="mr10 J_dialog">编辑</a>
                            <a href="{}" target="_blank">内容管理</a>
                        </td>
                    </tr><?php endforeach; endif; ?>
                </tbody>
            </table>
            <div class="page">
                <?php echo ($_page); ?>
            </div>
        </div>

        <div class="btn_wrap">
            <div class="btn_wrap_pd" id="J_sub_wrap">
                <label class="mr10">
                    <input class="J_check_all" data-checklist="J_check_y" data-direction="y" type="checkbox">全选</label>
                <button class="btn J_ajax_submit_btn" data-subcheck="true" data-action="<?php echo U('sethot');?>" type="submit">设为热门</button>
                <button class="btn J_ajax_submit_btn" data-subcheck="true" data-action="<?php echo U('delhot');?>" type="submit">取消热门</button>
                <button class="btn" id="J_merger_btn" type="submit" style="width:140px;">合并到关联话题</button>
                <button class="btn" id="J_move_btn" type="submit">移动分类</button>
                <button class="btn J_ajax_submit_btn btn_error" data-subcheck="true" data-msg="确定要删除吗？" data-action="<?php echo U('del');?>" type="submit">删除</button>
            </div>
        </div>
    </form>
    <?php else: ?>
    <div class="not_content_mini"> <i></i>
        啊哦，没有符合条件的内容！
    </div><?php endif; ?>
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
    var URL_MERGE = "<?php echo U('merge');?>", //合并
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