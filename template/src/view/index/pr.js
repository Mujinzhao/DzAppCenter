define(function(require){
	/* PR单 */
    var idx=0,gridid;

    function getRow(im) {
        var prurl = '#/pr/form~prid='+im.prid_code;
        var row = '<tr>'+
            '<td><a href="'+prurl+'" class="grida">['+im.prid+'] '+im.prname+'</a></td>'+
            '<td width="70" align="center">'+prStateRender(im.status)+'</td>'+
            '<td width="90" style="color:gray;">'+im.ctime.substr(0,10)+'</td>'+
            '<td style="text-align:right;width:50px;">'+
              '<a class="mwt-btn mwt-btn-primary mwt-btn-xs" href="'+prurl+'">查看</a></td>'+
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
        var url = 'pr&action=queryMine';
        msg.showLoading(gridid,'加载数据...');
        ajax.post(url,{key:'',status:idx,start:0,limit:5},function(res){
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
            ['待提交'],
            ['审批中'],
            ['已审批']
        ];
        var tabcode = [];
        for (var i=0; i<tabs.length; ++i) {
            var tab = tabs[i];
            var url = "#/requirectl/myprs~s="+tab[0];
            var cls = i==0 ? ' class="mwt-active"' : '';
            var code = '<li'+cls+' name="pr-tab" data-id="'+i+'"><a href="javascript:;">'+tab[0]+'</a></li>';
            tabcode.push(code);
        }
        var code = '<ul class="mwt-nav mwt-nav-tabs">'+
                '<li><span><i class="icon icon-reply"></i> PR单</span></li>'+
                tabcode.join('')+
                '<li class="more"><a href="javascript:;" id="more-'+domid+'">more</a></li>'+
            '</ul>'+
            '<div id="'+gridid+'" style="min-height:200px;"></div>';
		jQuery('#'+domid).html(code);
        jQuery('[name=pr-tab]').unbind('click').click(function(){
            jQuery('[name=pr-tab]').removeClass('mwt-active');
            jQuery(this).addClass('mwt-active');
            var newidx = jQuery(this).data('id');
            if (idx!=newidx) {
                idx = newidx;
                query();
            }
        });
        query();
        jQuery('#more-'+domid).unbind('click').click(function(){
            var url = '#/pr/myprs~s='+idx;
            window.location=url;
        });
	};

	return o;
});
