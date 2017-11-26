define(function(require){
    /* PR单处理 */
    var o={};

    // 提交PR单
    o.submit=function(prid,callback) {  
         ajax.post('pr&action=submit',{prid:prid},function(res){
            if (res.retcode!=0) {
                mwt.notify(res.retmsg,1500,'danger');
                return;
            }
            if (callback) {callback();}
        });
    };

    // 撤销PR单
    o.cancel=function(prid,callback) {
        ajax.post('pr&action=submitCancel',{prid:prid},function(res){
            if (res.retcode!=0) {
                mwt.notify(res.retmsg,1500,'danger');
                return;
            }
            if (callback) {callback();}
        });
    };

    // 删除PR单
    o.remove=function(prid,callback) {
        ajax.post('pr&action=remove',{prid:prid},function(res){
            if (res.retcode!=0) {
                mwt.notify(res.retmsg,1500,'danger');
                return;
            }
            if (callback) {callback();}
        });
    };

    

    return o;
});
