define(function(require){
	/**
     * 流程模块控制器
    **/
	var posnav = require('common/posnav');
    var o={};
	var control='flow';

	o.conf = {
		controller: control,
		path: [
			'/'+control+'/myflow',
			'/'+control+'/index'
		]
	};

	function before_action() {
		require('./login').check();
		if (dz.auth==0) {
			frame.showpage("没有权限");
			throw new Error("没有权限");
		}
	}

	// 默认action
	o.indexAction=function(erurl) {
		var query=erurl.getQuery();
        var f = query['f'] ? query['f'] : '';
		if (f=='') window.location="#/";
        var code = '<div id="flow_detail_div" class="mwt-layout-fill"></div>';
		frame.showpage(code);
		require('view/flow/detail/page').execute('flow_detail_div',f);
	};

    // 我的流程
    o.myflowAction=function(erurl) {
        var query=erurl.getQuery();
        var s = query.s ? query.s : 0;
        var code = '<div class="wp">'+
                posnav.get([{name:'我的流程'}])+
                '<div id="myflow_div" class="wall"></div>'+
            '</div>'+
            require('common/copyright').get();
        frame.showpage(code);
		require('view/flow/myflow/page').execute('myflow_div',s);
    };


	return o;
});
