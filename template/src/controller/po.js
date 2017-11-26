define(function(require){
	/**
     * PO单模块控制器
    **/
    var o={};
	var control='po';

	o.conf = {
		controller: control,
		path: [
			'/'+control+'/form',
			'/'+control+'/list',
			'/'+control+'/index'
		]
	};

	function before_action() {
		require('./login').check();
		if (dz.auth!=2) {
			frame.showpage("没有权限");
			throw new Error("没有权限");
		}
	}

	// 默认action
	o.indexAction=function(erurl) {
        window.location = '#/'+control+'/form';
	};

    // PO单详情
    o.formAction=function(erurl) {
        before_action();
        var query=erurl.getQuery();
        // 创建PO单
        var poid=query.poid ? query.poid : '';
        if (poid=='') {
            msg.showLoading('frame-center','正在创建PO单...');
            setTimeout(function(){
                ajax.post('po&action=create',{},function(res){
                    if (res.retcode!=0) {
                        msg.showException('frame-center',res.retmsg);
                    } else {
                        window.location = '#/po/form~poid='+res.data;
                    }
                });   
            },1000);
            return;
        }
        // 显示Form        
        jQuery('#frame-center').css({background:'#fff'});
        require('form/po/main').create({
            render : 'frame-center',
            formid : poid,
            print  : true,
            edit   : true
        }).init();
    };

    // PO单列表
    o.listAction=function(erurl) {
        before_action();
        var query=erurl.getQuery();
        var s = query.s ? query.s : 0;
        var code = '<div class="wp">'+
                posnav.get([{name:'我的PO单'}])+
                '<div id="mypos_div" class="wall"></div>'+
            '</div>'+
            require('common/copyright').get();
        frame.showpage(code);
		require('view/po/mypos/page').execute('mypos_div',s);
    };

	return o;
});
