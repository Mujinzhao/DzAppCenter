define(function(require){
	/* 插件列表页 */
    var o={};

	o.execute=function(domid,query){
        require('./grid').init(domid);
	};

	return o;
});
