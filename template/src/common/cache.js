define(function(require){
    var cachepool = {};

    var o={};

    o.getApp=function(appkey) {
        var key = '#app#'+appkey;
        if (!cachepool[key]) {
            ajax.post('admin&action=getApp',{appkey:appkey},function(res){
                if (res.retcode!=0) mwt.alert(res.retmsg);
                else cachepool[key] = res.data;
            },true);
        }
        return cachepool[key];
    };

    o.removeApp=function(appkey) {
        var key = '#app#'+appkey;
        if (cachepool[key]) delete cachepool[key];
    };

    return o;    
});
