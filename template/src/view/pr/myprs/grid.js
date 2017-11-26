define(function(require){
	/* grid.js, (c) 2017 mawentao */
    var prproc = require('./prproc');
	var store,grid,gridid;
    var state;

    var o={};

	o.init=function(_gridid,_state){
        state = _state;
		gridid = _gridid;
		store = new mwt.Store({
    		proxy: new mwt.HttpProxy({
        		url: ajax.getAjaxUrl('pr&action=queryMine')
    		})
		});
		grid = new MWT.Grid({
			render   : gridid,
    		store    : store,
			pagebar  : true,  // false 表示不分页
			bordered : false,  // false 不显示列边框
			pageSize : 10,    // 默认每页大小
			multiSelect : false,
            striped  : true,
			tbarStyle: 'margin-bottom:-1px;',
			tbar: [
				{type:"search",id:"so-key-"+gridid,width:300,placeholder:'查询PR单',handler:o.query}
			],
            bodyStyle: 'min-height:300px;',
			cm: new MWT.Grid.ColumnModel([
				{head:"PR单号", dataIndex:"prid",width:100,align:'left',sort:true,render:function(v,item){
					return v;
				}},
				{head:"Title",dataIndex:"prname",align:'left',render:function(v,item){
                    var url = '#/pr/form~prid='+item.prid;
					return '<a href="'+url+'">'+v+'</a>';
				}},
				{head:"关联流程",dataIndex:"progress_title",align:'left',render:function(v,item){
                    if (!v) return '';
                    return '<a class="grida" href="#/flow~f='+item.pgid+'">'+v+'</a>';
                }},
				{head:"创建时间",dataIndex:"ctime",width:120,align:'center',sort:true,render:timeRender},
				{head:"状态",dataIndex:"status",width:100,align:'center',render:prStateRender},
				{head:"", dataIndex:"prid",width:160,align:'right',render:function(v,item){
					var editbtn = '<a class="mwt-btn mwt-btn-default mwt-btn-xs" href="#/pr/form~prid='+item.prid_code+'" '+
							'name="editbtn" data-id="'+v+'">编辑</a>';
                    var submitbtn = '<a class="mwt-btn mwt-btn-default mwt-btn-xs" href="javascript:;" '+
							'name="submitbtn" data-id="'+v+'">提交</a>';
                    var flowbtn = '<a class="mwt-btn mwt-btn-default mwt-btn-xs" href="#/flow~f='+item.pgid+'" '+
							'name="submitbtn" data-id="'+v+'">审批详情</a>';
                    var cancelbtn = '<a class="mwt-btn mwt-btn-default mwt-btn-xs" href="javascript:;"'+
							'name="cancelbtn" data-id="'+v+'">撤销</a>';
                    var delbtn = '<a class="mwt-btn mwt-btn-default mwt-btn-xs" href="javascript:;" '+
                            'name="delbtn" data-id="'+v+'">删除</a>';
                    var btns = [editbtn,submitbtn];
                    switch (parseInt(item.status)) {
                        case 0:
                        case dict.PRO_AUDIT_FAIL  :
                        case dict.PRO_AUDIT_CANCEL:
                        case dict.PRO_STATE_EDIT  : btns=[editbtn,submitbtn,delbtn]; break;
                        case dict.PRO_AUDIT_SUCC  : btns=[flowbtn]; break;
                        case dict.PRO_AUDIT_TODO  : btns=[cancelbtn]; break;
                    }
                    return btns.join("&nbsp;&nbsp;");
				}}
			])
		});
		grid.create();
		store.on('load',function(){
			mwt.popinit();
			// 提交PR单
			jQuery('[name=submitbtn]').unbind('click').click(function(){
                var prid = jQuery(this).data('id');
                mwt.confirm('确定要提交此PR单吗？',function(res){
                    if (res) {
                        prproc.submit(prid,o.query);               
                    }
                });
			});
            // 撤销PR单
            jQuery('[name=cancelbtn]').unbind('click').click(function(){
                var prid = jQuery(this).data('id');
                mwt.confirm('确定要撤销此PR单吗？',function(res){
                    if (res) {
                        prproc.cancel(prid,o.query);               
                    }
                });
            });
            // 删除PR单
            jQuery('[name=delbtn]').unbind('click').click(function(){
                var prid = jQuery(this).data('id');
                mwt.confirm('确定要删除此PR单吗？',function(res){
                    if (res) {
                        prproc.remove(prid,o.query);               
                    }
                });
            });
		});
		o.query();
	};

	o.query=function() {
		store.baseParams = {
			status: state,
            key: mwt.get_value("so-key-"+gridid)
        };  
        grid.load();
	};

	return o;
});
