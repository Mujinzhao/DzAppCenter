define(function(require){
    /* 采购项列表 */
    var poid,gridid,readonly,store,data;
    var prSelect;
    var o={};


	var columns = [
		{header:'产品',dataIndex:'prod_info',align:'left'},
		{header:'品牌',dataIndex:'prod_brand',width:70,align:'center'},
		{header:'规格',dataIndex:'prod_style',width:70,align:'center'},
		{header:'备注',dataIndex:'use_info',align:'left',width:100},
		{header:'数量',dataIndex:'item_num',width:60,align:'right'},
		{header:'单位',dataIndex:'item_unit',width:50,align:'center'},
		{header:'单价',dataIndex:'item_unit_price',width:80,align:'right',field:'number',defaultValue:'0.00'},
		{header:'总价',dataIndex:'item_num',width:105,align:'right',render:function(v,item){
			return number_format(item.item_num * item.item_unit_price,2)+'元';
		}}
	];

	function getTdStyle(col) {
		var styles = [];
		if (col.width) styles.push('width:'+col.width+'px');
		if (col.align) styles.push('text-align:'+col.align);
		var style = styles.length ? ' style="'+styles.join(';')+'"' : '';
		return style;
	}

    function showList() {
        var cols = ['<td width="40" style="text-align:right;">序号</td>'];
		for (var i=0;i<columns.length;++i) {
			var cim = columns[i];
			var style = getTdStyle(cim);
			var td = '<td'+style+'>'+cim.header+'</td>';
			cols.push(td);
		}
        if (!readonly){ cols.push('<td style="width:60px;"></td>'); }
        
        var rows = [];
        var totalPrice = 0;
        for (var i=0;i<store.size();++i) {
            var col = ['<td style="text-align:right;">'+(i+1)+'.</td>'];
            var item = store.get(i);
            for (var c=0;c<columns.length;++c) {
                var cim = columns[c];
                var k = cim['dataIndex'];
                var v = cim.render ? cim.render(item[k],item) : item[k];
                var style = getTdStyle(cim);
                col.push('<td'+style+'>'+v+'</td>');
            }
            if (!readonly) {
                var editbtn = '<a name="edbtn-'+gridid+'" href="javascript:;" data-id="'+item.item_id+'" pop-title="编辑">'+
                                '<i class="icon icon-edit"></i></a>';
                var delbtn = '<a name="delbtn-'+gridid+'" href="javascript:;" data-id="'+item.item_id+'" pop-title="删除">'+
                                '<i class="icon icon-trash"></i></a>';
                var btsn = [editbtn,delbtn];
                col.push('<td style="text-align:right;">'+btsn.join('&nbsp;&nbsp;')+'</td>');
            }
            rows.push('<tr>'+col.join('')+'</tr>');
            totalPrice += item.item_unit_price * item.item_num;
        }

        var code = '<table class="listtab">'+
            '<thead><tr class="head">'+cols.join('')+'</tr></thead>'+
            rows.join('')+
            '<thead><tr class="foot">'+
                '<td></td>'+
                '<td></td>'+
                '<td></td>'+
                '<td></td>'+
                '<td></td>'+
                '<td></td>'+
                '<td></td>'+
                '<td style="text-align:right;font-weight:bold;">总计:</td>'+
                '<td style="text-align:right;color:red;">'+number_format(totalPrice,2)+'元</td>'+
                (readonly ? '' : '<td></td>')+
            '</tr></thead>'+
        '</table>';
        jQuery('#'+gridid).html(code);
        mwt.popinit();
        // 点击编辑按扭
        jQuery('[name=edbtn-'+gridid+']').unbind('click').click(function(){
            var item_id=jQuery(this).data('id');
            var idx = store.indexOf('item_id',item_id);
            var item = store.get(idx);
            require('./dialog').open(item);
        });
        // 点击删除按扭
        jQuery('[name=delbtn-'+gridid+']').unbind('click').click(function(){
            var item_id=jQuery(this).data('id');
            mwt.confirm('确定删除吗?',function(res){
                if (res) removeItem(item_id);
            });
        });
    }

    function removeItem(item_id) {
        ajax.post('po&action=removeItem',{item_id:item_id},function(res){
            if (res.retcode!=0) mwt.notify(res.retmsg,1500,'danger');
            else { o.query(); }
        });
    };


    // init
	o.init=function(domid,_data,_readonly){
        data = _data;
        poid = data.poid;
        gridid = 'grid-'+domid;
        readonly = _readonly;
		var opbtn = '';
		if (!readonly) {
			var syncBtn = '<button class="mwt-btn mwt-btn-danger mwt-btn-sm radius" style="float:right;margin-left:5px;"'+
				'id="syncbtn-'+domid+'" >'+
				'<i class="icon icon-transfer" style="vertical-align:middle;"></i> 导入PR单采购列表</button>';
            var prBtn = '<span style="float:right">关联PR单: '+
                    '<div id="prbtn-'+domid+'" style="display:inline-block;"></div></span>';
            btns = [syncBtn,prBtn];
            opbtn = btns.join('');
		}
		var code = '<thead><tr><th class="partition">采购项列表(Purchasing Product List)'+
			opbtn+'</th></tr></thead>'+
            '<tr><td id="'+gridid+'" style="padding:5px;"></td></tr>';
		jQuery('#'+domid).html(code);

        if (!readonly) {
            // PR单关联选择控件
            prSelect = require('common/select_pr').create('prbtn-'+domid);
            if (data.prid!=0) {
                prSelect.setText(data.prid);
            }
            prSelect.on('change',setPrid);
            // 导入PR单采购列表
            jQuery('#syncbtn-'+domid).unbind('click').click(syncPr);
        }

        store = new mwt.Store({
            proxy: new mwt.HttpProxy({
                url: ajax.getAjaxUrl('po&action=queryItems')
            })
        });
        store.on('load',showList);
        o.query();
        // 添加项
		jQuery('#addbtn-'+domid).unbind('click').click(function(){
            var item = {prid:data.prid,item_id:0};
            require('./dialog').open(item);
		});
	};

    o.query=function() {
		store.baseParams = {
            poid: poid
        };
        store.load();
    };

    // 设置关联的PR单
    function setPrid() 
    {
        var prid = prSelect.getValue();
        if (prid!=0) {
            ajax.post('po&action=setPrid',{poid:data.poid,prid:prid},function(res){
                        if (res.retcode!=0) {
                            mwt.notify(res.retmsg,1500,'danger');
                        } else {
                            data.prid = prid;
                            mwt.notify('已关联PR单['+prid+']',1500,'success');
                        }
                    });
        }       
    }

    // 导入PR单采购列表
    function syncPr() {
        var prid = data.prid;
        if (prid==0) {
            mwt.notify('请先关联PR单',1500,'danger');
            return;
        }
        ajax.post('po&action=syncPr',{poid:data.poid},function(res){
            if (res.retcode!=0) { mwt.notify(res.retmsg,1500,'danger'); }
            else {
                o.query();
            }
        });
    }

	return o;
});
