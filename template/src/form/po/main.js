define(function(require){
require('../OAForm');
/* PO单 */
function POForm(opts)
{
    var poid = 0;
    var data = {};
	var store;
    if (opts) {
        this.construct(opts);
        if(opts.formid) poid=opts.formid;
        this.title = '采购订单';
    }
    var thiso=this;

    // 加载PO单数据
    this.beforeInit=function() {
        if (poid==0) throw "此PO单不存在或已删除";
        ajax.post("po&action=getDetail",{poid:poid},function(res){
            if (res.retcode==0) {
                data=res.data;
                var editStates = [0,dict.PRO_STATE_EDIT,dict.PRO_AUDIT_FAIL,dict.PRO_AUDIT_CANCEL];
                if (in_array(data.status,editStates) && opts.edit) {
                    thiso.edit=1;
                }
                if (data.status!=dict.PRO_AUDIT_SUCC) {
                    thiso.print=false;
                }
            }
        },true);
        if (!data.poid) throw "此PO单不存在或已删除";
    }

    this.showForm=function(domid) {
        var render = domid;
        var ct = strtotime(data.ctime);
        var cdate = date('Y/m/d',ct);
        var code = '<div class="formdiv">'+
            '<img class="formlogo" src="'+dz.logo+'">'+
            '<h1>采购订单 (Purchasing Order)</h1>'+
            '<div class="topbar">'+
//              '<label>申请人: </label><span class="field">'+data.creatorInfo.username+'</span>'+
              '<label>申请日期: </label><span class="field">'+cdate+'</span>'+
              '<label style="float:right">编号(PO No.) <span class="field">'+data.poid+'</span></label>'+
            '</div>'+
            '<table border="1">'+
              '<tr>'+
                '<td width="50%" style="padding:10px;vertical-align:top;"><div id="supplier-'+render+'"></div></td>'+
                '<td width="50%" style="padding:10px;vertical-align:top;"><div id="address-'+render+'"></div></td>'+
              '</tr>'+
            '</table>'+
            '<table border="1" id="ims-'+render+'"></table>'+
        '</div>';
        jQuery('#'+domid).html(code);
        var readonly = !this.edit;
        require('./supplier').init('supplier-'+render,data,readonly);
        require('./address').init('address-'+render,data,readonly);
		require('./grid').init('ims-'+render,data,readonly);
    }

    // 保存
    this.save=function() {
        setTimeout(function(){
            mwt.notify('已保存',1500,'success');
        },400);
        /*
        var params = {
            poid  : data.poid,
        };
        ajax.post('requirectl&action=prSave',params,function(res){
            if (res.retcode!=0) mwt.notify(res.retmsg,1500,'danger');
            else mwt.notify('已保存',1500,'success');
        });*/
    };

    // 提交发送
    this.submit=function() {
		var msgid = mwt.notify('正在提交...',0,'loading');
        ajax.post('po&action=submit',{poid:data.poid},function(res){
			mwt.notify_destroy(msgid);
            if (res.retcode!=0) mwt.notify(res.retmsg,1500,'danger');
            else {
                mwt.notify('提交成功',1500,'success');
                setTimeout(function(){
                    window.location.reload(); 
                },1500);
            }
        });
    };
}

mwt.extends(POForm,mwt.OAForm);

return {
    create: function(domid,poid) {
        return new POForm(domid,poid);
    }
}
});
