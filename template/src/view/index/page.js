define(function(require){
	/* 首页 */
    var o={};

	o.execute=function(domid){
        var prdiv = '<div id="pr-'+domid+'" class="wall" style="min-height:100px;"></div>';
        var podiv = '<div id="po-'+domid+'" class="wall" style="min-height:100px;"></div>';
        var divs = [prdiv];
        if (dz.auth==2) divs.push(podiv);
        var width = 100/divs.length
        var tds = [];
        for (var i=0;i<divs.length;++i) {
            if (i!=0) tds.push('<td>&nbsp;&nbsp;</td>');
            tds.push('<td width="'+width+'%" style="vertical-align:top;">'+divs[i]+'</td>');
        }
		var code = '<div class="wp">'+
            '<div id="flow-'+domid+'" class="wall" style="min-height:100px;"></div>'+
            '<table width="100%" style="margin:10px 0;">'+
              '<tr>'+tds.join('')+'</tr>'+
            '</table>'+
		'</div>'+
		require('common/copyright').get();
		jQuery('#'+domid).html(code);

		require('./flow').init('flow-'+domid);
		require('./pr').init('pr-'+domid);
        if (dz.auth==2) {
            require('./po').init('po-'+domid);
        }
	};

	return o;
});
