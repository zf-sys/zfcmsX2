{__NOLAYOUT__}
<?php
if(config('web.site_path')){
    if(config('web.site_path')!=''){
    $this->site_path = config('web.site_path').'/';
    }else{
    $this->site_path = '';
    }
}else{
    $this->site_path = '';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo(strip_tags($msg));?></title>
<link href="/{$this->site_path}public/static/tpl/css/404.css" rel="stylesheet" type="text/css" />
<script src="/{$this->site_path}public/static/zfcms/style/jquery-1.8.3.min.js"></script>
<script type="text/javascript">
    $(function() {
        var h = $(window).height();
        $('body').height(h);
        $('.mianBox').height(h);
        centerWindow(".tipInfo");
    });

    //2.将盒子方法放入这个方，方便法统一调用
    function centerWindow(a) {
        center(a);
        //自适应窗口
        $(window).bind('scroll resize',
                function() {
                    center(a);
                });
    }

    //1.居中方法，传入需要剧中的标签
    function center(a) {
        var wWidth = $(window).width();
        var wHeight = $(window).height();
        var boxWidth = $(a).width();
        var boxHeight = $(a).height();
        var scrollTop = $(window).scrollTop();
        var scrollLeft = $(window).scrollLeft();
        var top = scrollTop + (wHeight - boxHeight) / 2;
        var left = scrollLeft + (wWidth - boxWidth) / 2;
        $(a).css({
            "top": top,
            "left": left
        });
    }
</script>
</head>
<body>
<div class="mianBox">
    <img src="/{$this->site_path}public/static/tpl/images/yun0.png" alt="" class="yun yun0" />
    <img src="/{$this->site_path}public/static/tpl/images/yun1.png" alt="" class="yun yun1" />
    <img src="/{$this->site_path}public/static/tpl/images/yun2.png" alt="" class="yun yun2" />
    <img src="/{$this->site_path}public/static/tpl/images/bird.png" alt="" class="bird" />
    <img src="/{$this->site_path}public/static/tpl/images/san.png" alt="" class="san" />
    <div class="tipInfo">
        <div class="in">
            <div class="textThis">
                <h2><?php echo(strip_tags($msg));?></h2>
                <p><span>页面自动<a id="href" href="<?php echo($url);?>">跳转</a></span><span>等待<b id="wait"><?php echo($wait);?></b>秒</span></p>
                
            </div>
        </div>
    </div>
</div>

</body>
</html>
<script type="text/javascript">                            
(function(){
    var wait = document.getElementById('wait'),
        href = document.getElementById('href').href;
    var interval = setInterval(function(){
        var time = --wait.innerHTML;
        if(time <= 0) {
            location.href = href;
            clearInterval(interval);
        };
    }, 1000);
})();
</script>