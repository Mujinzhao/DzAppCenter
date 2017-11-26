define(function(require){
/**
 * 需求方控制器
 **/
	var posnav = require('common/posnav');
    var o={};
	var control='requirectl';

	o.conf = {
		controller: control,
		path: [
			'/'+control+'/pr',
			'/'+control+'/myflow',
			'/'+control+'/myflowtodo',
			'/'+control+'/myprs',
			'/'+control+'/mypos',
			'/'+control+'/index'
		],
		// 左部菜单
		menu: [
			{name:'流程', icon:'sicon-shuffle', submenu:[
				{name:'我发起的流程',icon:'sicon-shuffle',action:'myflow'},
				{name:'待处理的流程',icon:'sicon-shuffle',action:'myflowtodo'}
			]},
			{name:'PR单', icon:'icon icon-reply', submenu:[
				{name:'我的PR单',icon:'icon icon-reply',action:'myprs'},
				{name:'待处理的PR单',icon:'icon icon-reply',action:'myrevpr'}
			]},
			{name:'PO单', icon:'icon icon-order', submenu:[
				{name:'我的PO单',icon:'icon icon-order',action:'mypos'}
			]},
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
	o.indexAction=function() {
        window.location = '#/'+control+'/myprs';
	};

	// 我发起的流程
	o.myflowAction=function(erurl) {
		before_action();
        var code = '<div id="myflow_div" class="ctlgrid" style="top:10px;">我发起的流程</div>';
		frame.showpage(code);
		require('view/flow/myflow/page').execute('myflow_div');
	};

    // 待我处理的流程
    o.myflowtodoAction=function(erurl) {
		before_action();
        var code = '<div id="myflowtodo_div" class="ctlgrid" style="top:10px;">待处理的流程</div>';
		frame.showpage(code);
		require('view/flow/mytodo/page').execute('myflowtodo_div');
    };


	// 我的PR单
	o.myprsAction=function(erurl) {
		before_action();
        var query=erurl.getQuery();
        var prstate = query['s'] ? query['s'] : 1;
        var code = '<div id="requirectl_myprs_div" class="ctlgrid" style="top:10px;"></div>';
		frame.showpage(code);
		require('view/requirectl/myprs/page').execute('requirectl_myprs_div',prstate);
	};

	// PR单
	o.prAction=function(erurl) {
		before_action();
        var query=erurl.getQuery();
        var prid=query.prid ? query.prid : '';
        if (prid=='') {
            window.location='#/'+control+'/myprs';
            return;
        }
        require('form/pr/main').create({
            render : 'frame-center',
            formid : prid,
            print  : false,
            edit   : true
        }).init();
	};

    // PO单
    o.myposAction=function(erurl) {
        before_action();
        var query=erurl.getQuery();
        var state = query['s'] ? query['s'] : 1;
        var code = '<div id="mypos_div" class="ctlgrid" style="top:10px;"></div>';
        frame.showpage(code);
//        require('view/po/mypos/page').execute('mypos_div',state);
    };

	return o;
});
