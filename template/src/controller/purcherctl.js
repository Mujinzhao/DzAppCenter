define(function(require){
/**
 * 采购管理后台控制器
 **/
	var posnav = require('common/posnav');
    var o={};
	var control='purcherctl';

	o.conf = {
		controller: control,
		path: [
			'/'+control+'/index',
//			'/'+control+'/profile',
			'/'+control+'/my_suppliers',
			'/'+control+'/supplier_audit',
		],
		// 左部菜单
		menu: [
			{name:'我的资料', icon:'sicon-grid', submenu:[
				{name:'我的资料',icon:'icon icon-log',action:'index'},
				{name:'修改密码',icon:'icon icon-lock',action:'changepass'}
			]},
/*
			{name:'采购项目', icon:'sicon-grid', submenu:[
				{name:'新建项目',icon:'icon icon-log',action:'profile'},
				{name:'项目列表',icon:'icon icon-log',action:'profile'},
				{name:'进度管理',icon:'icon icon-lock',action:'changepass'}
			]},
*/
			{name:lang.supplier_manage, icon:'sicon-users', submenu:[
				{name:lang.supplier_list,icon:'sicon-users',action:'my_suppliers'},
				{name:lang.supplier_audit,icon:'sicon-user-following',action:'supplier_audit'}
			]}/*,
			{name:'统计报表', icon:'sicon-grid', submenu:[
				{name:'项目报表',icon:'icon icon-log',action:'profile'},
				{name:'供应商报表',icon:'icon icon-lock',action:'changepass'}
			]}*/
		]
	};

	function before_action() {
		require('./login').check();
		if (dz.groupid!=1) {
			frame.showpage("没有权限");
			throw new Error("不是采购员");
		}
	}

	// 默认action
	o.indexAction=function() {
		before_action();
		frame.showpage('welcome');
	};

	// 我的供应商
	o.my_suppliersAction=function() {
		before_action();
		frame.showpage('我的供应商');
	};

	// 供应商审核
	o.supplier_auditAction=function() {
		before_action();
		var code = posnav.get(lang.supplier_audit)+
			'<div id="supplier_audit_div" class="ctlgrid"></div>';
		frame.showpage(code);
		require('view/purcherctl/supplier_audit/page').execute('supplier_audit_div');
	};

	return o;
});
