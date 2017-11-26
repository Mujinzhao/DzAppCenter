define(function(require){
    /* 送货地址 */
    var cacheMap = {};
    var o={};

    o.get=function(addrid) {
        if (!cacheMap[addrid]) {
            ajax.post('address&action=getDetail',{addrid:addrid},function(res){
                if (res.retcode!=0) { mwt.notify(res.retmsg,1500,'danger'); }
                else {
                    cacheMap[addrid] = res.data;
                }
            },true);
        }
        return cacheMap[addrid];
    };
    
    return o;
});
