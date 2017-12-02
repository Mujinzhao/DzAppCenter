define(function(require){
    /* 发布版本 */
    var form,dialog,params,callback;


    function init_dialog() 
    {/*{{{*/
        //1. new form
        form = new MWT.Form();
        form.addField('version',new MWT.TextField({
            render      : 'fm-version',
            value       : '',
            placeholder : '版本号:',
            empty       : false,
            errmsg      : "请输入版本号,不超过30个字符",
            checkfun    : function(v){return v.length<=30;}
        }));
        //2. new dialog
        dialog = new MWT.Dialog({
            title     : '发布版本',
            top       : 50,
            width     : 500,
            form      : form,
            bodyStyle : 'padding:10px;',
            body : '<form method="POST" enctype="multipart/form-data">'+
              '<table class="mwt-formtab">'+
                '<tr><td width="100">版本号:</td><td><div id="fm-version"></div></td></tr>'+
                '<tr><td>应用包(zip):</td>'+
                    '<td>'+
                        '<button class="mwt-btn mwt-btn-primary mwt-btn-xs" id="zipfile-sel-btn">选择文件</button><br>'+
                        '<span id="zipfile-span"></span>'+
                        '<input type="file" id="zipfile" name="zipfile" accept="application/zip" style="display:none;"/>'+
                    '</td>'+
                '</tr>'+
              '</table>'+
            '</form>',
            buttons : [
                {label:"提交",cls:'mwt-btn-primary',handler:submitClick},
                {label:"取消",type:'close',cls:'mwt-btn-default'}
            ]
        });
        //3. dialog open event
        dialog.on('open',function(){
            form.reset();
            jQuery('#zipfile-sel-btn').unbind('click').click(function(){
                jQuery('#zipfile').val("").click();
                return false;
            });
            jQuery('#zipfile').unbind('change').change(function(){
                var v = jQuery(this).val();
                jQuery('#zipfile-span').html(v);
            });
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
        var zipfile = jQuery('#zipfile').val();
        if (zipfile=="") {
            mwt.notify('请选择zip包',1500,'danger');
            return;
        }
        var ps = [
            'rid='+params.rid,
            'packversion='+data.version
        ];
        var upurl= ajax.getAjaxUrl("admin&action=releaseApppack&fileElementId=zipfile&"+ps.join('&'));
        var msgid = mwt.notify('正在提交发布...',0,'loading');
        jQuery.ajaxFileUpload({
            url: upurl,
            secureuri: false,
            fileElementId: 'zipfile',
            dataType: 'json',
            timeout: 30000,
            complete: function(data) {
                mwt.notify_destroy(msgid);
                console.log(data);
//                create();
            },
            success: function(res,status) {
                if (res.retcode!=0) {
                    mwt.alert(res.retmsg);
                } else {
                    mwt.notify('恭喜你，应用发布成功',1500,'success');
                    dialog.close();
                    if (callback) callback();                   
                }
            },  
            error: function (data, status, e) {
                mwt.alert("Error: "+e);
            }
        });
    }

    return o;
});
