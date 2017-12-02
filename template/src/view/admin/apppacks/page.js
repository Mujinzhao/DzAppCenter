define(function(require){
	/* APP版本管理页 */
    var o={};

	o.execute=function(domid,appinfo){
        require('./grid').init(domid,appinfo);
	};

	return o;
});
