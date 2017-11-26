define(function(require){
    /* 供应商 */
    var cacheMap = {};
    var o={};

    o.get=function(supplier_id) {
        if (!cacheMap[supplier_id]) {
            ajax.post('supplier&action=getDetail',{supplier_id:supplier_id},function(res){
                if (res.retcode!=0) { mwt.notify(res.retmsg,1500,'danger'); }
                else {
                    cacheMap[supplier_id] = res.data;
                }
            },true);
        }
        return cacheMap[supplier_id];
    };
    
    return o;
});
