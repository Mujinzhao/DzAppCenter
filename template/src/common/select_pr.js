define(function(require){
    /* PR单选择控件 */
    var store,grid,fd;


    function query() {
        store.baseParams = {
            key: mwt.get_value("so-key")
        };
        grid.load();
    }

    function create_grid(divid) {
        store = new mwt.Store({
    		proxy: new mwt.HttpProxy({
        		url: ajax.getAjaxUrl('pr&action=queryAuditSucc')
    		})
        });
        grid = new MWT.Grid({
            render  : divid,
            store   : store,
            noheader: true,
            pagebar : true, //!< false 表示不分页
            pageSize: 10,
            pagebarSimple: true,      //!< 简化版分页栏
            notoolbox: true,        //!< 不显示右下角工具箱
            multiSelect:false, 
            bordered : false,
            position : 'fixed',  //!< 固定位置显示
            striped  : true,
            tbarStyle: 'margin-bottom:-1px;',
            tbar: [
                {type:"search",id:"so-key",width:300,placeholder:'搜索PR单编号',handler:query}
            ],
            cm: new MWT.Grid.ColumnModel([
                {head:"", dataIndex:"prid", width:80,sort:true},
                {head:"", dataIndex:"prname", align:'left'}
            ]),
            rowclick: function(im) {
                fd.setText(im.prid);
                fd.setValue(im.prid);
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
            style    : 'width:100px;', 
            popWidth : 400,
            popHeight: 250,
            value    : '',
            empty    : false,
            errmsg   : '请选择...'
        });
        fd.on('pop',function(){
            if (!grid) {
                create_grid(fd.getPopDivId());
            } 
            else if (store.size()==0) {
                query();
            }
        });
        fd.create();
        return fd;
    };

    return o;
});
