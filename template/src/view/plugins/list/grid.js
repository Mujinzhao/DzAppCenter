define(function(require){
    /* grid.js, (c) 2017 mawentao */
    var store,pagebar,gridid;

    function showList() {
        var n = store.size();
        if (n==0) {
            msg.showEmpty(gridid,'没有找到相关应用');
            return;
        }
        var lis = [];
        for (var i=0;i<n;++i) {
            var im = store.get(i);
            var installUrl = im.installUrl;
            var appname = im.appname;
            var code = '<li>'+
                '<img src="?_'+im.appkey+'" title="'+appname+'">'+
                '<div class="app-info">'+
                   '<span class="app-name">'+appname+' ('+im.appkey+')</span>'+
                   '<p class="app-desc">'+im.appdesc+'</p>'+
                '</div>'+
                '<a href="'+installUrl+'" class="mwt-btn mwt-btn-success mwt-btn-sm app-btn">'+
                    '<i class="icon icon-download"></i>&nbsp;&nbsp;安装</a>'+
            '</li>';
            lis.push(code);
        }
        var code = '<ul class="app-list-ul">'+lis.join('')+'</ul>';
        jQuery('#'+gridid).html(code);
    }

    var o={};
    
    o.init = function(domid){
        var code = '<div id="grid-'+domid+'"></div>'+
                   '<div id="pagebar-'+domid+'" style="margin:15px;"></div>';
        jQuery('#'+domid).html(code);
        gridid = 'grid-'+domid;
        store = new mwt.Store({
            proxy: new mwt.HttpProxy({
                //beforeLoad : store_before_load,
                //afterLoad  : store_after_load,
                url        : ajax.getAjaxUrl("plugin&action=query")
            })
        });
        pagebar = new MWT.PageBar({
            render    : 'pagebar-'+domid,
            pageStyle : 2,
            pageSize  : 30,
            store     : store
        });
        store.on('load',showList);

        o.query();
        jQuery('#sobtn').unbind('click').click(o.query);
        jQuery('#sokey').unbind('change').change(o.query);
        jQuery('[name=sort-sel]').unbind('click').click(function(){
            jQuery('[name=sort-sel]').removeClass('a');
            jQuery(this).addClass('a');
            o.query();
        });
    };

    o.query = function() {
        var sort = jQuery("[name=sort-sel][class=a]").data('value');
        store.baseParams = { 
            key  : mwt.get_value("sokey"),
            sort : sort ? sort : 'ctime',
            params: {
                siteuniqueid: dz.client_siteuniqueid,
                siteurl: dz.client_siteurl
            }
        };
        //store.load();
        pagebar.changePage(1);
    };
    
    return o;
});
