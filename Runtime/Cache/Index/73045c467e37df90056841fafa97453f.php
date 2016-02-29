<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="keywords" content="" />
<meta name="description" content="" />
<link rel="icon" href="/favicon.ico">

<title><?php echo ($seo["title"]); ?> - <?php echo C('WEB_SITE_TITLE');?></title>

<!-- Bootstrap -->
<link href="/Public/Static/bootstrap/css/bootstrap.min.css" rel="stylesheet">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
  <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
  <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->

    <link href="/Public/Index/css/icon/iconfont.css" rel="stylesheet" type="text/css">
    <link href="/Public/Index/css/style.css" rel="stylesheet" type="text/css">
    <link href="/Public/Index/css/index.css" rel="stylesheet" type="text/css">

</head> 
 <body>
  
      <header class="bg">
    <div class="container">
        <nav>
            <a class="navbar-left" href="#">
                <img alt="logo" src="/Public/Index/images/logo.png">
            </a>
            <ul>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?php echo session('city');?><span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">上海市</a></li>
                        <li><a href="#">南京市</a></li>
                        <li><a href="#">宁波市</a></li>
                        <li><a href="#">苏州市</a></li>
                        <li><a href="#">吉安市</a></li>
                        <li><a href="#">济南市</a></li>
                        <li><a href="#">烟台市</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="pull-right">
                <?php if(is_array($tnav)): foreach($tnav as $key=>$vo): if($vo['hover']): ?><li><a href="<?php echo ($vo["url"]); ?>" class="active"><?php echo ($vo["name"]); ?></a></li>
                        <?php else: ?>
                        <li><a href="<?php echo ($vo["url"]); ?>"><?php echo ($vo["name"]); ?></a></li><?php endif; endforeach; endif; ?>
                <?php if(is_login()): ?><li><a href="<?php echo U('Uc/Index/index');?>"><i class="glyphicon glyphicon-user"></i> <?php echo USERNAME;?></a></li>
                    <li><a href="<?php echo U('Uc/Uc/logout');?>"> 退出登录</a></li>
                    <?php else: ?>
                    <li><a href="<?php echo U('Uc/Uc/login');?>">登录</a></li>
                    <li><a href="<?php echo U('Uc/Uc/register');?>">注册</a></li><?php endif; ?>

            </ul>
        </nav>

        <h1>Now join us,come on !</h1>
        <p><a class="btn btn-primary btn-lg" href="list.html" role="button">全部项目></a></p>

    </div>
</header>
  
  
      <section>
          <div class="container pad60">
              <div class="more">
                  <div class="list">
                      <label>运动项目：</label>
                      <select class="form-control" name="items_id" style="width: 200px; height: 40px;">
                          <option value="0">请选择项目</option>
                          <?php if(is_array($items)): foreach($items as $key=>$vo): ?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; ?>
                      </select>
                  </div>
                  <div class="list">
                      <label>运动日期：</label>
                      <select class="form-control" name="by_date" style="width: 200px; height: 40px;">
                          <option value="0">请选择日期</option>
                          <?php if(is_array($date_week)): foreach($date_week as $key=>$vo): ?><option value="<?php echo ($vo["week"]); ?>"><?php echo ($vo["date"]); ?> <?php echo ($vo["week"]); ?></option><?php endforeach; endif; ?>
                      </select>
                  </div>
                  <div class="list">
                      <label>运动时间：</label>
                      <select class="form-control" name="by_time" style="width: 200px; height: 40px;">
                          <option value="0">请选择时间</option>
                          <?php if(is_array($times)): foreach($times as $key=>$vo): ?><option value="<?php echo ($vo); ?>"><?php echo ($vo); ?></option><?php endforeach; endif; ?>
                      </select>
                  </div>
                  <div class="list">
                      <label>场馆名称：</label>
                      <input class="form-control" name="venue_name" type="text" placeholder="场馆名称" style="width: 200px; height: 40px;">
                  </div>
                  <div class="list">
                      <label>&nbsp</label>
                      <button type="button" class="btn btn-primary" style="width: 200px; height: 40px;">快速查询</button>
                  </div>
              </div>

              <div class="title">
                  <h2>热门运动</h2>
              </div>
              <div class="content">
                  <?php if(is_array($items)): foreach($items as $key=>$vo): ?><a href="#"><i class="icon iconfont"><?php echo ($vo["icon"]); ?></i></a><?php endforeach; endif; ?>
              </div>
              <p class="tip">还等什么，一起来运动吧！</p>
              <p class="tip"><a class="btn btn-primary btn-lg" href="list.html" role="button">全部项目></a></p>
          </div>
          <div id="allmap" style="display: none;"></div>
      </section>
  
  
      <footer>
    <div class="container">
        <div class="pad30">
            <a href="#">
                <img alt="logo" src="/Public/Index/images/logo.png">
            </a>
            <div class="row">
                <div class="col-md-7 left">
                    <p><span>客服电话:</span><?php echo C('TEL');?></p>
                    <p><span>电子邮箱:</span><?php echo C('EMAIL');?></p>
                    <p><span>公司地址:</span><?php echo C('ADDR');?></p>
                </div>
                <div class="col-md-2 mid">
                    <a href="#"><i class="icon iconfont">&#xe65a;</i></a>
                    <span>微信公众号</span>
                </div>
                <div class="col-md-3 right">
                    <p><a class="btn stroke btn-size" href="#" role="button"><i class="icon iconfont">&#xe639;</i>iPhone下载</a></p>
                    <p><a class="btn stroke btn-size" href="#" role="button"><i class="icon iconfont">&#xe690;</i>Android下载</a></p>
                </div>
            </div>
            <p>© 2014 BoBoKing. All rights reserved.</p>
        </div>
    </div>
</footer>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="/Public/Static/jquery-1.11.1.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/Public/Static/bootstrap/js/bootstrap.min.js"></script>
  
  

  
 </body>
</html>