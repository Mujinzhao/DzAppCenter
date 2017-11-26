define(function(require){
    /* 用户信息 */
    var usermap = {};
    var userDialog;

    var o={};

    o.getUserInfo=function(uid) {
        if (!usermap[uid]) {
            ajax.post('uc&action=userinfo',{uid,uid},function(res){
                if (res.retcode==0) {
                    usermap[uid] = res.data;
                }
            },true);
        }
        return usermap[uid];
    };

    o.showInDialog=function(uid) {
        var userinfo = o.getUserInfo(uid);
        if (!userDialog) {
            userDialog = new MWT.Dialog({
                title   : '用户信息',
                width   : 400,
                top     : 80,
                body    : '<div id="user-dialog-body-div"></div>',
                bodyStyle: 'padding:10px;'
            });
            userDialog.on('open',function(){
                var code = '<table class="usertab">'+
                    '<tr>'+
                      '<td rowspan="3" style="text-align:center;">'+
                        '<img src="'+userinfo.avatar+'">'+
                        '<br>'+userinfo.username+
                      '</td>'+
                      '<th>姓名:</th><td>'+userinfo.realname+'</td>'+
                    '</tr>'+
                    '<tr><th>部门:</th><td>'+userinfo.group_name+'</td></tr>'+
                    '<tr><th>E-mail:</th><td><a href="mailto:'+userinfo.email+'">'+userinfo.email+'</a></td></tr>'+
                '</table>';
                jQuery('#user-dialog-body-div').html(code);
            });
        }
        userDialog.open();
    };

    return o;
});
