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
<body style="width:300px;" class="body_none">
<!--添加用户-->
    <form class="J_ajaxForm" data-role="list" action="/admin.php?s=/Venue/add.html" method="post" >
    <input type='hidden' name='type' value="do">
    <div class="pop_cont pop_table" style="height:auto;">
        <table width="100%">
            <col class="th" />
            <col />
            <thead>
            <tr>
                <th>用户名</th>
                <td><input name="username" type="text" class="input length_3"></td>
            </tr>
            </thead>
            <tr>
                <th>密码</th>
                <td><input name="password" type="password" class="input length_3"></td>
            </tr>
            <tr>
                <th>手机号码</th>
                <td><input name="mobile" type="tel" class="input length_3"></td>
            </tr>
            <tr>
                <th>电子邮箱</th>
                <td><input name="email" type="text" class="input length_3 mr10"><label style="display:none">
                <input name="sendEmail" type="checkbox" class="checkbox" value="0">电子邮件通知</label></td>
            </tr>
            <tr>
                <th>场馆名称</th>
                <td><input name="name" type="text" class="input length_3"></td>
            </tr>
        </table>
    </div>
    <div class="pop_bottom">
        <button type="button" class="btn fr" id="J_dialog_close">取消</button>
        <button type="submit" class="btn btn_submit mr10 fr J_ajax_submit_btn">提交</button>
    </div>
    
</form>
<script src="/Public/Static/dev/pages/admin/common/common.js"></script>


</body>
</html>