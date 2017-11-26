define(function(require){
	/* 流程信息 */
    var idx=0,gridid;

    function getRow(im) {
        var flowurl = '#/flow~f='+im.pgid;
        var row = '<tr>'+
           '<td><a href="'+flowurl+'" class="grida">'+im.progress_title+'</a></td>';
        if (idx==0) {
            row += '<td width="100">'+im.realname+'</td>';
            row += '<td width="100" style="color:gray;">'+im.active_time.substr(0,10)+'</td>';
        } else {
            row += '<td width="100" align="center">'+prStateRender(im.status)+'</td>';           
            row += '<td width="100" style="color:gray;">'+im.ctime.substr(0,10)+'</td>';
        }
        row += '<td style="text-align:right;width:50px;">'+
            '<a class="mwt-btn mwt-btn-primary mwt-btn-xs" href="'+flowurl+'">'+
                (idx==0 ? '立即处理' : '查看')+
            '</a></td>'+
        '</tr>';
        return row;
    };


    function showList(list) {
        if (!list || list.length==0) {
            msg.showEmpty(gridid,'暂无记录');
            return;
        }
        var rows = [];
        for (var i=0;i<list.length;++i) {
            var im = list[i];
            rows.push(getRow(im));
        }
        var code = '<div class="mwt-grid">'+
          '<div class="mwt-grid-body striped" style="border:none;">'+
            '<table class="listab">'+rows.join('')+'</table>'+
          '</div>'+
        '</div>';
        jQuery('#'+gridid).html(code);
    }
    

    function query(){
        var url = 'progress&action=mytodo';
        if (idx!=0) url = 'progress&action=myflow';
        msg.showLoading(gridid,'加载数据...');
        ajax.post(url,{key:'',start:0,limit:5},function(res){
            if (res.retcode!=0) { msg.showException(gridid,res.retmsg); }
            else {
                showList(res.data.root);
            } 
        });
    };


    var o={};

	o.init=function(domid){
        gridid = 'grid-'+domid;
        var tabs = [
            ['待处理的流程'],
            ['我发起的流程']
        ];
        var tabcode = [];
        for (var i=0; i<tabs.length; ++i) {
            var tab = tabs[i];
            var url = "#/requirectl/myprs~s="+tab[0];
            var cls = i==0 ? ' class="mwt-active"' : '';
            var code = '<li'+cls+' name="flow-tab" data-id="'+i+'"><a href="javascript:;">'+tab[0]+'</a></li>';
            tabcode.push(code);
        }
        var code = '<ul class="mwt-nav mwt-nav-tabs">'+
                '<li><span><i class="sicon-equalizer"></i> 流程</span></li>'+
                tabcode.join('')+
                '<li class="more"><a href="javascript:;" id="more-'+domid+'">more</a></li>'+
            '</ul>'+
            '<div id="'+gridid+'" style="min-height:200px;"></div>';
		jQuery('#'+domid).html(code);
        jQuery('[name=flow-tab]').unbind('click').click(function(){
            jQuery('[name=flow-tab]').removeClass('mwt-active');
            jQuery(this).addClass('mwt-active');
            var newidx = jQuery(this).data('id');
            if (idx!=newidx) {
                idx = newidx;
                query();
            }
        });
        query();

        jQuery('#more-'+domid).unbind('click').click(function(){
            var url = '#/flow/myflow~s='+idx;
            window.location=url;
        });
	};

	return o;
});
