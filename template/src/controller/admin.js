define(function(require){
	/**
     * 应用管理模块控制器
     **/
	var posnav = require('common/posnav');
    var o={};
	var control='admin';

	o.conf = {
		controller: control,
		path: [
			'/'+control+'/applist',
			'/'+control+'/index'
		]
	};

	function before_action() {
        var code = '<div class="cl2">'+
            '<div class="sd">'+
              '<div class="head">应用管理</div>'+
              '<ul class="sdul">'+
                '<li><a href="#/admin/applist"><i class="fa fa-cubes" style="color:#EC3F09"></i> 应用列表</a></li>'+
              '</ul>'+
            '</div>'+
            '<div class="mn" id="fm-center"></div>'+
        '</div>';
        jQuery('#mainbody').html(code);
	}

	// 默认action
	o.indexAction=function(erurl) {
        window.location = '#/admin/applist';
	};

    // 应用列表
    o.applistAction=function(erurl) {
        before_action();
        require('view/admin/applist/page').execute('fm-center',erurl.getQuery());
    };

	return o;
});
