define(function(require){
    /* 地址选择控件 */
    var store,grid,gridid,fd;

    function query() {
        store.baseParams = {
            key: mwt.get_value("sokey-"+gridid)
        };
        grid.load();
    }

    function create_grid(divid) {
        gridid = divid;
        store = new mwt.Store({
    		proxy: new mwt.HttpProxy({
        		url: ajax.getAjaxUrl('address&action=queryEffect')
    		})
        });
        grid = new MWT.Grid({
            render  : gridid,
            store   : store,
            noheader: true,
            pagebar : true, //!< false 表示不分页
            pageSize: 20,
            pagebarSimple: true,      //!< 简化版分页栏
            notoolbox: true,        //!< 不显示右下角工具箱
            multiSelect:false, 
            bordered : false,
            position : 'fixed',  //!< 固定位置显示
            striped  : true,
            tbarStyle: 'margin-bottom:-1px;',
            tbar: [
                {type:"search",id:"sokey-"+gridid,width:200,placeholder:'搜索地址',handler:query},
                '->',
                '<label><a href="#/purcherctl/my_address" target="_blank">添加新的地址</a></label>'
            ],
            cm: new MWT.Grid.ColumnModel([
                {head:"", dataIndex:"addrid",render:function(v,item){
                    var code = item.address+'<br>'+
                        item.zipcode+'<span style="float:right">'+item.contact+'</span>';
                    return code;
                }}
            ]),
            rowclick: function(im) {
                var t = im.address;
                var width = 15;
                if (t.length>width) {
                    t = t.substr(0,width)+'...';
                }
                fd.setText(t);
                fd.setValue(im.addrid);
                fd.fire('change');
            }
        });
        grid.create();
    }

    var o={};
    o.create=function(domid) {
        fd = new MWT.ComboxField({
            render   : domid,
            cls      : 'radius',
            style    : 'width:200px;', 
            popWidth : 300,
            popHeight: 250,
            value    : '',
            empty    : false,
            errmsg   : '请选择...'
        });
        fd.on('pop',function(){
            if (!grid) { create_grid(fd.getPopDivId()); } 
            else if (store.size()==0) { query(); }
        });
        fd.create();
        return fd;
    };

    return o;
});
