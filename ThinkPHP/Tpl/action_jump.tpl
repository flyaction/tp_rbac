<?php
    if(C('LAYOUT_ON')) {
        echo '{__NOLAYOUT__}';
    }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>页面跳转提示</title>
<style type="text/css">
	body{ margin:0px; padding:0px; font-size:12px;background:#FFFFFF;  font-family: '微软雅黑'; font-size:13px; color:#666666;}
	a{ font-size:13px; color:#2571be; text-decoration:none;}
	#cont{ width:60%; height:200px; border:1px solid #CCCCCC; margin:0px auto; margin-top:100px; background:#FFFFFF;}
	#cont_main{ width:80%; height:120px;  margin:0px auto; margin-top:50px;}
	#cont_main_l{ width:20%; height:100px; float:left; text-align:center; }
	#cont_main_r{ width:80%; height:120px; float:left; line-height:30px;}
	#cont_main_r .a{ font-size:16px; font-weight:bold;}
</style>
</head>

<body>

	<div style="background:#FFFFFF; height:500px;">
		<div id="cont">
			<div id="cont_main">
				
				<div id="cont_main_l">
					<?php if(isset($message)) {?>
						<img src="__PUBLIC__/build/images/icon_success.png" />
					<?php }else{?>
						<img src="__PUBLIC__/build/images/icon_failed.png" />
					<?php }?>
				</div>	
				<div id="cont_main_r">
					
					<?php if(isset($message)) {?>
						<span class="a"><?php echo($message); ?></span><br />
					<?php }else{?>
						<span class="a"><?php echo($error); ?></span><br />
					<?php }?>
					
					<span>提示你可以进行以下操作：</span><br />
					系统将在 <b id="wait"><?php echo($waitSecond); ?></b> 秒钟会自动跳转 <a id="href" href="<?php echo($jumpUrl); ?>">点击跳转</a></span>		
				</div>
				
				
			</div>
		</div>
	</div>
	
<script type="text/javascript">
	(function(){
	var wait = document.getElementById('wait'),href = document.getElementById('href').href;
	var interval = setInterval(function(){
		var time = --wait.innerHTML;
		if(time <= 0) {
			location.href = href;
			clearInterval(interval);
		};
	}, 1000);
	})();
</script>
</body>
</html>
