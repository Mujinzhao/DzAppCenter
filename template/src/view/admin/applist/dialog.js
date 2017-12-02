define(function(require){
    /* dialog.js, (c) 2017 mawentao */
    var form,dialog,params,callback;

    function init_dialog() 
    {/*{{{*/
        //1. new form
        form = new MWT.Form();
        form.addField('appkey',new MWT.TextField({
            render      : 'fm-appkey',
            value       : '',
            placeholder : 'app唯一标识',
            empty       : false,
            errmsg      : "请输入appkey,不超过30个字符,包含字母数字和下划线,且必须是字母开头",
            checkfun    : function(v){
                return /^[a-zA-z][a-zA-Z_0-9]{0,29}$/.test(v);
            }
        }));
        form.addField('appname',new MWT.TextField({
            render      : 'fm-appname',
            value       : '',
            placeholder : '应用名',
            empty       : false,
            errmsg      : "请输入应用名,不超过30个字符",
            checkfun    : function(v){return v.length<=30;}
        }));
        form.addField('appdesc',new MWT.TextField({
            type        : 'textarea',
            render      : 'fm-appdesc',
            style       : 'height:100px;line-height:18px;',
            value       : '',
            placeholder : '应用介绍',
            empty       : false,
            errmsg      : "请输入应用介绍,不超过200个字符",
            checkfun    : function(v){return v.length<=200;}
        }));
        form.addField('author',new MWT.TextField({
            render      : 'fm-author',
            value       : '',
            placeholder : '作者',
            empty       : false,
            errmsg      : "请输入应用作者,不超过30个字符",
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
                '<tr><td width="100">AppKey:</td><td><div id="fm-appkey"></div></td></tr>'+
                '<tr><td>应用名:</td><td><div id="fm-appname"></div></td></tr>'+
                '<tr><td>应用介绍:</td><td><div id="fm-appdesc"></div></td></tr>'+
                '<tr><td>作者:</td><td><div id="fm-author"></div></td></tr>'+
            '</table>',
            buttons : [
                {label:"提交",cls:'mwt-btn-primary',handler:submitClick},
                {label:"取消",type:'close',cls:'mwt-btn-default'}
            ]
        });
        //3. dialog open event
        dialog.on('open',function(){
            form.reset();
            if (params.appid) {
                dialog.setTitle("编辑APP");
                form.set(params);
                jQuery('#fm-appkeytxt').attr('disabled','disabled').css({background:'#eee'});
            } else {
                dialog.setTitle("添加APP");
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
        data.appid = params.appid;
        try {
            //data.id = params.id;
            ajax.post('admin&action=saveApp',data,function(res){
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
