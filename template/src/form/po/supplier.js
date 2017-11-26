define(function(require){
    /* 供应商信息 */
    var domid,supplier_id,supplierSelect,data;

    function showSupplier() {
        try {
            if (supplier_id==0) throw '请选择供应商';
            var supplierInfo = require('cache/supplier').get(supplier_id);
            if (!supplierInfo) throw '供应商['+supplier_id+']不存在或已删除';
            // show
            var code = '<p>'+
                '<em>'+supplierInfo.company_name+'</em>'+
                '<br><br>地址: '+supplierInfo.company_address+
                '<br>联系人: '+supplierInfo.company_faren+
                '<br>电话: '+supplierInfo.company_faren_tel+
            '</p>';
            jQuery('#body-'+domid).html(code);
        } catch (e) {
            msg.showException('body-'+domid,e);
        }
    }

    var o={};

    o.init=function(_domid,_data,readonly) {
        domid = _domid;
        data = _data;
        var userInfo = data.creatorInfo;
        supplier_id = data.supplier_id;
        var setbtn = '<button class="mwt-btn mwt-btn-default mwt-btn-xs" style="float:right;">'+
            (supplier_id==0?'选择供应商':supplier_id)+'</button>';
		var code = '<div class="title">'+
                '<label>供应商</label>'+
                '<span style="float:right">编号: <div id="supsel-'+domid+'" style="display:inline-block;"></div></span>'+
            '<div>'+
            '<div id="body-'+domid+'" class="infodiv"></div>';
		jQuery('#'+domid).html(code);
        showSupplier();

        if (!readonly) {
            // 供应商选择控件
            supplierSelect = require('common/select_supplier').create('supsel-'+domid);
            if (supplier_id!=0) {
                supplierSelect.setText(supplier_id);
            }
            supplierSelect.on('change',setSupplier);
        } else {
            code = supplier_id;
            jQuery('#supsel-'+domid).html(code);
        }
    };

    function setSupplier() {
        supplier_id = supplierSelect.getValue();
        if (supplier_id!=0) {
            ajax.post('po&action=setSupplier',{poid:data.poid,supplier_id:supplier_id},function(res){
                if (res.retcode!=0) { mwt.notify(res.retmsg,1500,'danger'); } 
                else {
                    data.supplier_id = supplier_id;
                    showSupplier();
                }
            });
        }
    }

	return o;
});
