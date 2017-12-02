define(function(require){
	/* 插件列表页 */
    var o={};

	o.execute=function(domid,query){
        var sortopts = [
            {text:'最新排序',value:'ctime'},
            {text:'更新排序',value:'mtime'}
        ];
        var bar = '<div class="pxl cl" style="margin-bottom:0px;"><span>';
        for (var i=0;i<sortopts.length;++i) {
            var im = sortopts[i];
            var a = i==0 ? ' class="a"' : '';
            bar += '<a href="javascript:;" name="sort-sel" data-value="'+im.value+'"'+a+'>'+im.text+'</a>';
        }
        bar += '</span></div>';

        var color = '#777';
        var code = '<div class="cl">'+
            '<h2>插件</h2>'+
            '<div class="mwt-search-bar mwt-search-bar-black" style="width:230px;border-radius:2px;border:solid 1px '+color+';">'+
              '<div class="text">'+
              '  <input type="text" id="sokey" style="width:150px;font-size:12px;" placeholder="搜索插件">'+
              '</div>'+
              '<button class="submit" id="sobtn" style="background:'+color+';border-color:'+color+';font-size:13px;">搜索</button>'+
            '</div>'+
          '</div>'+bar+
        '<div id="grid-'+domid+'" style="margin-top:-1px;border:solid 1px #ddd;min-height:350px;margin-bottom:15px;"></div>';
        jQuery('#'+domid).html(code);
        require('./grid').init('grid-'+domid);
	};

	return o;
});
