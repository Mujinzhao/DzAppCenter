define(function(require){
require('../OAForm');
/* PR单 */
function PRForm(opts)
{
    var domid;
    var prid = 0;
    var data = {};
	var store;
    if (opts) {
        this.construct(opts);
        if(opts.formid) prid=opts.formid;
    }
    var thiso=this;

    // 加载PR单数据
    this.beforeInit=function() {
        if (prid==0) throw "此PR单不存在或已删除";
        ajax.post("requirectl&action=prDetail",{prid:prid},function(res){
            if (res.retcode==0) {
                data=res.data;
                var editStates = [0,dict.PRO_STATE_EDIT,dict.PRO_AUDIT_FAIL,dict.PRO_AUDIT_CANCEL];
                if (in_array(data.status,editStates) && opts.edit) {
                    thiso.edit=1;
                }
                if (data.status!=dict.PRO_AUDIT_SUCC) {
                    thiso.print = false;
                }
            }
        },true);
        if (!data.prid) throw "此PR单不存在或已删除";
    }

    this.showForm=function(domid) {
        var render = domid;
        var ct = strtotime(data.ctime);
        var cdate = date('Y/m/d',ct);
        var code = '<div class="formdiv">'+
            '<img class="formlogo" src="'+dz.logo+'">'+
            '<h1>采购申请单 (Purchasing Requisition Form)</h1>'+
            '<div class="topbar">'+
//              '<label>申请人: </label><span class="field">'+data.creatorInfo.username+'</span>'+
              '<label>申请日期: </label><span class="field">'+cdate+'</span>'+
              '<label style="float:right">编号(PR No.) <span class="field">'+data.prid+'</span></label>'+
            '</div>'+
            '<table border="1" id="base-'+render+'"></table>'+
            '<table border="1" id="ims-'+render+'"></table>'+
/*            '<table border="1">'+
              '<tr height="130">'+
                '<td width="50%">申请人/部门<br>Originator/Dept.<span class="datespan"></span></td>'+
                '<td width="25%">部门经理<br>Department Manager<span class="datespan"></span></td>'+
                '<td width="25%">财务经理<br>Finance Controller<span class="datespan"></span></td>'+
              '</tr>'+
              '<tr height="130">'+
                '<td>总经理批准(PR 金额超过RMB15,000或者$2500)<br>'+
                    'GM Approve (Any PR exceeding RMB15,000 or USD 2,500)<span class="datespan"></span></td>'+
                '<td colspan="2">PR递交采购日期<br>PR received date by Purchasing<span class="datespan"></span></td>'+
              '</tr>'+
              '<tr>'+
                '<td colspan="3">说明/Notes:'+
                  '<br>1. BU Asia Finance manager should sign on the PR form for BU indirect purchasing;'+
                  '<br>2. Purchasing Group: 127 Yannie Luo'+
                  '<br>3. Purchasing Type: YI-general purchasing; YF-framkework purchasing for regular exp.; YT-purchasing for Capex'+
                  '<br>4. Purchasing Type: YI-general purchasing; YF-framkework purchasing for regular exp.; YT-purchasing for Capex'+
                  '<br>5. SAP auto generated PR No. should be filled up in the paper PR No. for filing and checking purpose.'+
                '</td>'+
              '</tr>'+
            '</table>'+
*/
        '</div>';
        jQuery('#'+domid).html(code);
        var readonly = !this.edit;
        require('./base').show('base-'+render,data,readonly);
		require('./grid').show('ims-'+render,data,readonly);
    }

    // 保存
    this.save=function() {
        var params = {
            prid  : data.prid
        };
        ajax.post('pr&action=save',params,function(res){
            if (res.retcode!=0) mwt.notify(res.retmsg,1500,'danger');
            else mwt.notify('已保存',1500,'success');
        });
    };

    // 提交发送
    this.submit=function() {
		var msgid = mwt.notify('正在提交...',0,'loading');
        ajax.post('pr&action=submit',{prid:data.prid},function(res){
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

mwt.extends(PRForm,mwt.OAForm);

return {
    create: function(domid,prid) {
        return new PRForm(domid,prid);
    }
}
});
