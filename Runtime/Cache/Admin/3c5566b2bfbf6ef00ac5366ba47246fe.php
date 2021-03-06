<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
    <head>
        <meta charset="UTF-8" />
        <title>欢迎您登录BOBOOKING</title>
        <meta name="robots" content="noindex,nofollow">
        <link href="/Public/Admin/css/admin_login.css" rel="stylesheet" />
    </head>
<body>
    <div class="wrap">
        <h1><a href="">BOBOOKING管理平台</a></h1>
        <form method="post" name="login" action="/admin.php?s=/Public/login.html" autoComplete="off">
            <div class="login" id="login">
                <ul>
                    <li>
                        <input class="input" id="J_admin_name" required name="username" type="text" placeholder="帐号名" title="帐号名" />
                    </li>
                    <li>
                        <input class="input" id="admin_pwd" type="password" required name="password" placeholder="密码" title="密码" />
                    </li>
                    <li>
                        <input class="input w150" type="text" required name="verify" placeholder="验证码" /><a class="reloadverify" id="changeV" title="换一张" href="javascript:void(0)">换一张？</a>
                    </li>
                    <li>
                        <div id="J_verify_code"><img class="verifyimg reloadverify" alt="点击切换" src="<?php echo U('Public/verify');?>"></div>
                    </li>
                </ul>               
                <button class="btn" type="submit" name="submit">
                    <span class="in"><i class="icon-loading"></i>登 录 中 ..</span>
                    <span class="on">登 录</span>
                </button>  
                <div class="check-tips"></div>              
            </div>
        </form>
    </div>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/Public/Static/jquery-1.8.0.min.js"></script>
    <![endif]-->
    <!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/Public/Static/jquery-2.0.3.min.js"></script>
    <!--<![endif]-->
    <script type="text/javascript">
    //表单提交
    $(document)
        .ajaxStart(function(){
            $("button:submit").addClass("log-in").attr("disabled", true);
        })
        .ajaxStop(function(){
            $("button:submit").removeClass("log-in").attr("disabled", false);
        });

    $("form").submit(function(){
        var self = $(this);
        $.post(self.attr("action"), self.serialize(), success, "json");
        return false;

        function success(data){
            if(data.status){
                window.location.href = data.url;
            } else {
                self.find(".check-tips").text(data.info);
                //刷新验证码
                $(".reloadverify").click();
            }
        }
    });
    ;(function(){
        //初始化选中用户名输入框
        $("#login").find("input[name=username]").focus();
        //刷新验证码
        var verifyimg = $(".verifyimg").attr("src");
        $(".reloadverify").click(function(){
            if( verifyimg.indexOf('?')>0){
                $(".verifyimg").attr("src", verifyimg+'&random='+Math.random());
            }else{
                $(".verifyimg").attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());
            }
        });
    })();
    </script>
</body>
</html>