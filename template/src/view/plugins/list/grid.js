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
            var code = '<li>'+im.appname+
                '<a href="'+installUrl+'" class="mwt-btn mwt-btn-success mwt-btn-sm">安装</a>'+
            '</li>';
            lis.push(code);
        }
        var code = '<ul>'+lis.join('')+'</ul>';
        jQuery('#'+gridid).html(code);
    }

    var o={};
    
    o.init = function(domid){
        var code = '<div id="grid-'+domid+'"></div>'+
                   '<div id="pagebar-'+domid+'"></div>';
        jQuery('#'+domid).html(code);
        gridid = 'grid-'+domid;
        store = new mwt.Store({
            proxy: new mwt.HttpProxy({
                beforeLoad : store_before_load,
                afterLoad  : store_after_load,
                url        : ajax.getAjaxUrl("plugin&action=query")
            })
        });
        pagebar = new MWT.PageBar({
            render: 'pagebar-'+domid,
            store: store
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
