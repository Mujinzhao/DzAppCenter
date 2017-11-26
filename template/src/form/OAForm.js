/**
 * OA表单
 **/
mwt.OAForm=function(opt)
{
    this.render = '';
    this.edit = 0;  //!< 是否可编辑
    this.title = '采购申请单';
    this.print = false;
    this.construct=function(opt){
        if (opt){
            if(opt.render) this.render=opt.render;
            if(opt.print) this.print=opt.print;
        }
    };
    this.beforeInit=function(){}
    this.init=function(){
        try {
            this.beforeInit();
            var barid = this.render+'-bar';
            var bodyid = this.render+'-body';
            // 编辑模式
            if (this.edit) {
                new mwt.BorderLayout({
                    render: this.render,
                    items : [
                        {id:barid, region:'north',height:40,style:''},
                        {id:bodyid,region:'center',style:'background:#fff;'}
                    ]
                }).init();
                // bar
                var thiso=this;
                new mwt.ToolBar({
                    render : barid,
                    style  : 'border:none;border-bottom:solid 1px #ccc;',
                    items  : [
                        '<label>'+this.title+'</label>','->',
                        {label:'<i class="fa fa-save"></i> 保存待发',cls:'mwt-btn-success',handler:thiso.save},
                        {label:'<i class="fa fa-share"></i> 提交发送',cls:'mwt-btn-success',handler:thiso.submit}//,
//                      {label:'<i class="fa fa-print"></i> 打印',cls:'mwt-btn-success',handler:thiso.print}
                    ]
                }).create();
            }
            // 只读模式 
            else {
                /*
                new mwt.BorderLayout({
                    render: this.render,
                    items : [
                        {id:bodyid,region:'center',style:'background:#fff;'}
                    ]
                }).init();
                */
                bodyid = this.render;
            }
            // body
            this.showForm(bodyid);

            if (this.print) {
                var code = '<div style="position:absolute;right:10px;top:10px;" id="print-btn-div">'+
                    '<button id="print-btn" class="mwt-btn mwt-btn-default mwt-btn-xs">打印</button>'+
                '</div>';
                jQuery('#'+bodyid).append(code);
                jQuery('#print-btn').unbind('click').click(function(){
                    jQuery('.formdiv').print({
                        iframe:false
                    });
                });
            }

        } catch (e) {
            require('common/msg').showException(this.render,e);
        }  
    };

    // 以下是需要重写的函数
    this.showForm=function(domid){};
    this.save=function(){};
    this.submit=function(){};
};
MWT.extends(MWT.OAForm, MWT.Event);
