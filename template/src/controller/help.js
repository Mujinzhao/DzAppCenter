define(function(require){
    /* 帮助中心, (c) 2016 mawentao */
	var frame=require('frame');
	var control='help';
    var o={};

	// 控制器配置
	o.conf = {
		controller: control,
		// url路由
		path: [
			'/'+control+'/index'
		]
	};

	// 默认action
	o.indexAction=function() {
        var code = '<div class="cl">'+
            '<h2>使用帮助</h2>'+
            '<div class="wall">'+
                '本站是 Discuz! 产品的应用下载站点。安装本站应用需要先安装Disucz! X3.2及以上版本。你可以参照以下教程搭建自己的Discuz!站点并安装本站应用。'+
            '</div>'+
            '<h3>第一步：安装Discuz!</h3>'+
            '<div class="wall">Discuz!官网下载地址：<a href="http://www.discuz.net/thread-3570835-1-1.html" target="_blank">http://www.discuz.net/thread-3570835-1-1.html</a>'+
               '<p>本站应用只支持UTF8版本：<a href="http://download.comsenz.com/DiscuzX/3.2/Discuz_X3.2_SC_UTF8.zip">http://download.comsenz.com/DiscuzX/3.2/Discuz_X3.2_SC_UTF8.zip</a></p>'+ 
            '</div>'+
             
            '<h3>第二步：修改配置，将应用中心地址指向本站</h3>'+
            '<div class="wall">'+
                '编辑配置文件：dsicuz安装目录/config/config_global.php，在第5行插入以下代码：'+
                '<div class="code">'+
                    "$_config['addonsource'] = 'MyAppCenter';<br>"+
                    "$_config['addon'] = array(<br>"+
                    "&nbsp;&nbsp;&nbsp;&nbsp;'MyAppCenter' => array(<br>"+
                    "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'website_url' &nbsp;=> '"+dz.siteurl+"',<br>"+
                    "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'download_url' => '"+dz.siteurl+"index.php',<br>"+
                    "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'download_ip' &nbsp;=> '',<br>"+
                    "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'check_url' &nbsp;&nbsp;&nbsp;=> '"+dz.siteurl+"?ac=check&file=',<br>"+
                    "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'check_ip' &nbsp;&nbsp;&nbsp;&nbsp;=> '',<br>"+
                    "&nbsp;&nbsp;&nbsp;&nbsp;)<br>"+
                    ");"+
                '</div>'+
            '</div>'+

            '<h3>第三步：进入管理后台，获取更多应用</h3>'+
            '<div><img src="'+dz.siteurl+'template/static/dz_app_center_help.png" style="width:100%;border:solid 1px #ddd;"></div>'+
        '</div>';
		jQuery('#mainbody').html(code);
	};

	return o;
});
