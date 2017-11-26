define(function(require){
   /**
    * 首页控制器
    **/
    var o={};
	var control='index';

	o.conf = {
		controller: control,
		path: [
			'/'+control+'/index'
		]
	};

	// 默认action
	o.indexAction=function() {
        window.location="#/plugins";
	};

	return o;
});
