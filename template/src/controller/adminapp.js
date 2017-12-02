define(function(require){
	/**
     * 应用管理-应用详情模块控制器
     **/
	var posnav = require('common/posnav');
    var o={};
	var control='adminapp';

	o.conf = {
		controller: control,
		path: [
			'/'+control+'/packs',
			'/'+control+'/index'
		]
	};

	function before_action(erurl) {
        var query=erurl.getQuery();
        var appkey = query.appkey ? query.appkey : '';
        if (appkey=='') {
            window.location="#/admin/applist";
            return;
        }
        var appinfo = require('common/cache').getApp(appkey);

        var code = '<div class="cl2">'+
            '<div class="sd">'+
              '<div class="head">'+appinfo.appname+
                '<br><span style="font-size:12px;color:#999;">'+appinfo.appkey+'.'+appinfo.apptype+'</span>'+
                '<br><br><a href="#/admin/applist" class="grida" style="font-size:12px;font-weight:normal;">返回应用列表 «</a>'+
              '</div>'+
              '<ul class="sdul">'+
                '<li><a href="#/adminapp/packs~appkey='+appkey+'"><i class="icon icon-file" style="color:#EC3F09"></i> 版本管理</a></li>'+
              '</ul>'+
            '</div>'+
            '<div class="mn" id="fm-center"></div>'+
        '</div>';
        jQuery('#mainbody').html(code);
        return appinfo
	}

	// 默认action
	o.indexAction=function(erurl) {
        window.location = '#/adminapp/packs';
	};

    // 应用版本列表
    o.packsAction=function(erurl) {
        var appinfo = before_action(erurl);
        require('view/admin/apppacks/page').execute('fm-center',appinfo);
    };

	return o;
});
