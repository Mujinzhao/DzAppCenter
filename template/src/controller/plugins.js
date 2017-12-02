define(function(require){
	/**
     * 插件页模块控制器
    **/
    var o={};
	var control='plugins';

	o.conf = {
		controller: control,
		path: [
			'/'+control+'/view',
			'/'+control+'/index'
		]
	};

    function before_action() {
        active_top_nav('plugins');
    }

	// 插件列表页
	o.indexAction=function(erurl) {
        require('view/plugins/list/page').execute('mainbody',erurl.getQuery());
	};

    // 插件详情页
    o.viewAction=function(erurl) {
        jQuery('#mainbody').html("view");   
    };

	return o;
});
