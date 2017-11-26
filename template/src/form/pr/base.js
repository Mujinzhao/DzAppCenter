define(function(require){
    /* 基础信息 */
    var o={};

    o.show=function(domid,data,readonly) {
        var userInfo = data.creatorInfo;
		var code = '<thead><tr><th colspan="8" class="partition">基本信息</th></tr></thead>'+
            '<tr>'+
                '<td>申请人</td><td><span class="field">'+userInfo.realname+'</span></td>'+
                '<td>工号</td><td><span class="field">'+userInfo.staff_id+'</span></td>'+
                '<td>部门</td><td><span class="field">'+userInfo.group_name+'</span></td>'+
//            '</tr>'+
//            '<tr>'+
//                '<td>PR单类型</td><td colspan="5">原材料采购</td>'+
            '</tr>';
		jQuery('#'+domid).html(code);
    };

	return o;
});
