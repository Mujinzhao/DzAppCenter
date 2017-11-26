define(function(require){
	/* 我的PR单 */
    var o={};

	o.execute=function(domid,state){
        var tabs = [
            ['待提交'],
            ['审批中'],
            ['已审批']
        ];
        var tabcode = [];
        for (var i=0; i<tabs.length; ++i) {
            var tab = tabs[i];
            var url = "#/pr/myprs~s="+i;
            var cls = i==state ? ' class="mwt-active"' : '';
            var code = '<li'+cls+'><a href="'+url+'">'+tab[0]+'</a></li>';
            tabcode.push(code);
        }
        var code = '<ul class="mwt-nav mwt-nav-tabs">'+
                '<li>&nbsp;</li>'+
                tabcode.join('')+
                '<li style="float:right">'+
                    '<button id="newpr-'+domid+'" class="mwt-btn mwt-btn-primary mwt-btn-sm radius"'+
                        ' onclick="window.location=\'#/pr/form\'">'+
                        '<i class="sicon-plus" style="vertical-align:middle;"></i> 新建PR单'+
                    '</button>'+
                '</li>'+
            '</ul>'+
            '<div id="grid-'+domid+'" style="margin-top:10px;"></div>';
        jQuery('#'+domid).html(code);
		require('./grid').init('grid-'+domid,state);
	};

	return o;
});
