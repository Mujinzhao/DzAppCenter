define(function(require){
    /* grid.js, (c) 2017 mawentao */
    var store,grid,gridid,appinfo;
    var formdg = require('./dialog');
    var o={};
    
    o.init = function(domid,_appinfo){
        gridid = domid;
        appinfo = _appinfo;
        store = new mwt.Store({
            proxy: new mwt.HttpProxy({
                beforeLoad : store_before_load,
                afterLoad  : store_after_load,
                url        : ajax.getAjaxUrl("admin&action=queryApppacks")
            })
        });
        grid = new MWT.Grid({
            render      : gridid,
            store       : store,
            pagebar     : false,     //!< 分页
            multiSelect : false,    //!< 多行选择
            bordered    : false,    //!< 单元格边框
            striped     : false,    //!< 斑马纹
            noheader    : false,    //!< 隐藏列头
            notoolbox   : false,    //!< 隐藏工具箱(刷新,斑马纹,导出Excel)
            bodyStyle   : '', 
            tbarStyle   : 'margin:15px 0 10px;',
            tbar: [
                {type:'search',id:'so-key-'+gridid,width:300,handler:o.query,placeholder:'查询版本'},
                '->',
                {label:'<i class="sicon-plus" style="vertical-align:middle;"></i> 添加分支版本',handler:function(){
                    formdg.open({rid:0,appkey:appinfo.appkey},o.query);
                }}  
            ],
            cm: new MWT.Grid.ColumnModel([
                {head:"版本名称",dataIndex:"appname",sort:true,render:function(v,item){
                    var code = '<div class="appname">'+item.packname+'</div>'+
                        '<div class="appdesc">'+item.appkey+'.'+appinfo.apptype+'.'+item.rid+'</div>';
                    return code;
                }},
                {head:"当前版本号",dataIndex:"version",sort:true,width:130,align:'center',render:function(v,item){
                    return '<b>'+v+'</b>';
                }},
                {head:"状态", dataIndex:"status",width:70,align:'center',sort:true,render:function(v,item){
                    switch (parseInt(v)) {
                        case 0: return '<b style="color:green;">已上架</b>';
                        case 1: return '<b style="color:#666;">新添加</b>';
                        case 9: return '<b style="color:red;">已下架</b>';
                    }
                    return v;
                }},
                {head:"最后发布", dataIndex:"mtime",width:80,align:'center',sort:true,render:function(v,item){
                    return v.substr(0,16);
                }},
                {head:'',dataIndex:"rid",align:'right',width:120,render:function(v,item){
                    var editbtn = '<a class="grida" name="editbtn-'+gridid+'" data-id="'+v+'" href="javascript:;">版本信息</a>';
                    var relasebtn = '<a class="grida" name="releasebtn-'+gridid+'" data-id="'+v+'" href="javascript:;">发布版本</a>';
                    var btns = [editbtn,relasebtn];
                    return btns.join("&nbsp;&nbsp;");
                }}
            ])
        });
        store.on('load',function(){
            mwt.popinit();
            // 编辑按钮
            jQuery('[name=editbtn-'+gridid+']').unbind('click').click(editbtnClick);
            // 发布
            jQuery('[name=releasebtn-'+gridid+']').unbind('click').click(releasebtnClick);
            // 删除按钮
            //jQuery('[name=delbtn-'+gridid+']').unbind('click').click(delbtnClick);
        });
        grid.create();
        o.query();
    };

    o.query = function() {
        store.baseParams = { 
            appkey: appinfo.appkey,
            key: mwt.get_value("so-key-"+gridid)
        };  
        grid.load();
    };

    //////////////////////////////////////////////////////

    // 编辑按钮点击事件
    function editbtnClick() {
        var id = jQuery(this).data('id');
        var idx = store.indexOf('rid',id);
        var record = store.get(idx);
        formdg.open(record,o.query);
    }

    // 发布按钮点击事件
    function releasebtnClick() {
        var id = jQuery(this).data('id');
        var idx = store.indexOf('rid',id);
        var record = store.get(idx);
        require('./release_dialog').open(record,o.query);
    }

    // 删除按钮点击事件
    function delbtnClick() {
        var id = jQuery(this).data('id');
        alert("TODO: delete "+id);
    }

    //////////////////////////////////////////////////////

    return o;
});
