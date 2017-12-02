define(function(require){
    /* dialog.js, (c) 2017 mawentao */
    var form,dialog,params,callback;

    function init_dialog() 
    {/*{{{*/
        //1. new form
        form = new MWT.Form();
        form.addField('packname',new MWT.TextField({
            render      : 'fm-packname',
            value       : '',
            placeholder : '版本名称',
            empty       : false,
            errmsg      : "请输入版本名称,不超过30个字符",
            checkfun    : function(v){return v.length<=30;}
        }));
        //2. new dialog
        dialog = new MWT.Dialog({
            title     : '对话框',
            top       : 50,
            width     : 550,
            form      : form,
            bodyStyle : 'padding:10px;',
            body : '<table class="mwt-formtab">'+
                '<tr><td width="100">版本名称:</td><td><div id="fm-packname"></div></td></tr>'+
//              '<tr><td>版本号:</td><td><div id="fm-version"></div></td></tr>'+
            '</table>',
            buttons : [
                {label:"提交",cls:'mwt-btn-primary',handler:submitClick},
                {label:"取消",type:'close',cls:'mwt-btn-default'}
            ]
        });
        //3. dialog open event
        dialog.on('open',function(){
            form.reset();
            if (params.rid) {
                dialog.setTitle("编辑版本信息");
                form.set(params);
                jQuery('#fm-appkeytxt').attr('disabled','disabled').css({background:'#eee'});
            } else {
                dialog.setTitle("添加分支版本");
                jQuery('#fm-appkeytxt').removeAttr('disabled').css({background:'#fff'});
            }
        });
    }/*}}}*/

    var o={};
    o.open=function(_params,_callback){
        params   = _params;
        callback = _callback;
        if (!dialog) init_dialog();
        dialog.open();
    };

    /////////////////////////////////////
    // 提交按钮点击事件
    function submitClick() {
        var data = form.getData();
        data.rid = params.rid;
        data.appkey = params.appkey;
        try {
            //data.id = params.id;
            ajax.post('admin&action=saveApppack',data,function(res){
                if (res.retcode!=0) mwt.notify(res.retmsg,1500,'danger');
                else {
                    dialog.close();
                    if (callback) callback();
                }
            });
        } catch (e) {
            mwt.notify(e,1500,'danger');
        }
    }

    return o;
});
