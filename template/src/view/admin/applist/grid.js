define(function(require){
    /* grid.js, (c) 2017 mawentao */
    var store,grid,gridid;
    var formdg = require('./dialog');
    var o={};
    
    o.init = function(domid){
        gridid = domid;
        store = new mwt.Store({
            proxy: new mwt.HttpProxy({
                beforeLoad : store_before_load,
                afterLoad  : store_after_load,
                url        : ajax.getAjaxUrl("admin&action=queryApplist")
            })
        });
        grid = new MWT.Grid({
            render      : gridid,
            store       : store,
            pagebar     : true,     //!< 分页
            pageSize    : 20,       //!< 每页大小
            multiSelect : false,    //!< 多行选择
            bordered    : false,    //!< 单元格边框
            striped     : false,    //!< 斑马纹
            noheader    : false,    //!< 隐藏列头
            notoolbox   : false,    //!< 隐藏工具箱(刷新,斑马纹,导出Excel)
            bodyStyle   : '', 
            tbarStyle   : 'margin:15px 0 10px;',
            tbar: [
                {type:'search',id:'so-key-'+gridid,width:300,handler:o.query,placeholder:'查询应用'},
                '->',
                {label:'<i class="sicon-plus" style="vertical-align:middle;"></i> 添加应用',handler:function(){
                    formdg.open({appid:0},o.query);
                }}  
            ],
            cm: new MWT.Grid.ColumnModel([
                {head:"", dataIndex:"appkey", width:80,align:'center',render:function(v){
                    var imgurl = dz.siteurl+'?_'+v;
                    var code = '<img src="'+imgurl+'" class="applogo" name="applogo" data-appkey="'+v+'">';
                    return code;
                }},
                {head:"应用名称",dataIndex:"appname",sort:true,style:'vertical-align:top;',render:function(v,item){
                    var code = '<div class="appname">'+item.appname+' ('+item.appkey+')</div>'+
                        '<div class="appdesc">'+item.appdesc+'</div>';
                    return code;
                }},
                {head:"状态", dataIndex:"status",width:70,align:'center',sort:true,render:function(v,item){
                    switch (parseInt(v)) {
                        case 0: return '<b style="color:green;">已上架</b>';
                        case 1: return '<b style="color:#333;">新添加</b>';
                        case 9: return '<b style="color:red;">已下架</b>';
                    }
                    return v;
                }},
                {head:"作者", dataIndex:"author",width:100,align:'center',sort:true,render:function(v,item){
                    return v;
                }},
                {head:'',dataIndex:"appid",align:'right',width:120,render:function(v,item){
                    var editbtn = '<a class="grida" name="editbtn-'+gridid+'" data-id="'+v+'" href="javascript:;">应用信息</a>';
                    var viewbtn = '<a class="grida" href="#/adminapp/packs~appkey='+item.appkey+'">版本管理</a>';
                    var btns = [editbtn,viewbtn];
                    return btns.join("&nbsp;&nbsp;");
                }}
            ])
        });
        store.on('load',function(){
            mwt.popinit();
            // 更改applogo
            jQuery('[name=applogo]').unbind('click').click(function(){
                var jimg = jQuery(this);
                var appkey = jimg.data('appkey');
                require("common/applogo_upload").upload(appkey,function(res){
                    if (res.retcode!=0) { mwt.alert(res.retmsg); }
                    else {
                        //print_r(res);
                        var imgurl = res.data.imgurl;
                        jimg.attr("src",imgurl);
                    }   
                }); 
            });
            // 编辑按钮
            jQuery('[name=editbtn-'+gridid+']').unbind('click').click(editbtnClick);
            // 删除按钮
            //jQuery('[name=delbtn-'+gridid+']').unbind('click').click(delbtnClick);
        });
        grid.create();
        o.query();
    };

    o.query = function() {
        store.baseParams = { 
            key: mwt.get_value("so-key-"+gridid)
        };  
        grid.load();
    };

    //////////////////////////////////////////////////////

    // 编辑按钮点击事件
    function editbtnClick() {
        var id = jQuery(this).data('id');
        var idx = store.indexOf('appid',id);
        var record = store.get(idx);
        formdg.open(record,o.query);
    }

    // 删除按钮点击事件
    function delbtnClick() {
        var id = jQuery(this).data('id');
        alert("TODO: delete "+id);
    }

    //////////////////////////////////////////////////////

    return o;
});
