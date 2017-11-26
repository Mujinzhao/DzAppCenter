define(function(require){
    /* 采购项FormDialog */
    var form,dialog;
    var item;

    function init_form() {
        form = new MWT.Form();
        form.addField('prod_info',new MWT.TextField({
            render   : 'fm-prod_info',
            value    : '',
            empty    : false
        }));
        form.addField('prod_brand',new MWT.TextField({
            render   : 'fm-prod_brand',
            value    : '',
            empty    : true
        }));
        form.addField('prod_style',new MWT.TextField({
            render   : 'fm-prod_style',
            value    : '',
            empty    : true
        }));
        form.addField('item_num',new MWT.TextField({
            type     : 'number',
            render   : 'fm-item_num',
            value    : '1',
            empty    : false
        }));
        form.addField('item_unit',new MWT.TextField({
            render   : 'fm-item_unit',
            value    : '个',
            empty    : false
        }));
        form.addField('item_unit_price',new MWT.TextField({
            type     : 'number',
            render   : 'fm-item_unit_price',
            style    : 'text-align:right',
            value    : '0',
            empty    : false
        }));
        form.addField('use_info',new MWT.TextField({
            render   : 'fm-use_info',
            value    : '',
            empty    : true
        }));
    }
    
    function init_dialog() {
        init_form();
        var ns = '<span style="color:red;"> *</span>';
        dialog = new MWT.Dialog({
            title   : '采购项',
            width   : 550,
            top     : 50,
            form    : form,
            bodyStyle: 'padding:10px;',
            body    : '<table class="mwt-formtab">'+
                '<tr><td>产品'+ns+'</td><td colspan="3"><div id="fm-prod_info"></div></td></tr>'+
                '<tr>'+
                    '<td width="60">品牌</td><td><div id="fm-prod_brand"></div></td>'+
                    '<td width="60">规格</td><td><div id="fm-prod_style"></div></td>'+
                '</tr>'+
                '<tr>'+
                    '<td>数量'+ns+'</td><td><div id="fm-item_num"></div></td>'+
                    '<td>单位'+ns+'</td><td><div id="fm-item_unit"></div></td>'+
                '</tr>'+
                '<tr><td>单价'+ns+'</td><td colspan="3"><div id="fm-item_unit_price" style="display:inline-block;"></div> 元</td></tr>'+
                '<tr><td>备注</td><td colspan="3"><div id="fm-use_info"></div></td></tr>'+
            '</table>',
            buttons : [
                {label:"确定",cls:'mwt-btn-primary',handler:submit},
                {label:"取消",type:'close',cls:'mwt-btn-default'}
            ]
        });
        dialog.on('open',function(){
            if (item.item_id) {
                dialog.setTitle("编辑采购项");
                form.set(item);
            } else {
                dialog.setTitle("添加采购项");
                form.reset();
            }
        });
    }

    function submit() {
        var data = form.getData();
        try {
            if (data.item_num<=0) throw '数量必须大于0';
            if (data.item_unit_price<=0) throw '单价必须大于0';
            data.item_id = item.item_id;
            data.poid = item.poid;
            ajax.post('po&action=saveItem',data,function(res){
                if (res.retcode!=0) mwt.notify(res.retmsg,1500,'danger');
                else {
                    dialog.close();
                    require('./grid').query();
                }
            });
        } catch (e) {
            mwt.notify(e,1500,'danger');
        }
    }

    var o={};
    o.open=function(_item) {
        item=_item;
        if (!dialog) init_dialog();
        dialog.open();
    };
	return o;
});
