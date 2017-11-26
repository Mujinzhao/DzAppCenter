define(function(require){
    /* 送货地址信息 */
    var domid,data,addrid,readonly;
    var addrSelect,dateFd;

    function show() {
        try {
            if (addrid==0) throw '请选择送货地址';
            var addressInfo = require('cache/address').get(addrid);
            if (!addressInfo) throw '地址[#'+addrid+']不存在或已删除';
            // show
            var riqifd = data.arrival_date;
            var code = '<p>'+
                '地址: '+addressInfo.address+
                '<br>邮编: '+addressInfo.zipcode+
                '<br>联系人: '+addressInfo.contact+
                '<br>电话: '+addressInfo.tel+
                '<br><br>送货日期: <span id="date-'+domid+'" style="display:inline-block;">'+riqifd+'</span>'+
            '</p>';
            jQuery('#body-'+domid).html(code);

            if (!readonly) {
                dateFd.create();
                dateFd.setValue(data.arrival_date);
            }
        } catch (e) {
            msg.showException('body-'+domid,e);
        }
    }

    var o={};

    o.init=function(_domid,_data,_readonly) {
        domid = _domid;
        data = _data;
        readonly = _readonly;
        addrid = data.addrid;
		var code = '<div class="title">'+
                '<label>送货地址</label>'+
                '<span style="float:right"><div id="addrsel-'+domid+'" style="display:inline-block;"></div></span>'+
            '<div>'+
            '<div id="body-'+domid+'" class="infodiv"></div>';
		jQuery('#'+domid).html(code);

        if (!readonly) {
            // 地址选择控件
            addrSelect = require('common/select_address').create('addrsel-'+domid);
            if (addrid!=0) {
                //addrSelect.setText(addrid);
            }
            addrSelect.on('change',setAddr);
            //
            dateFd = new MWT.DatepickerField({
                render   : 'date-'+domid,
                style    : 'width:150px;',
                value    : ''
            });
            dateFd.on('change',setArrivalDate);
        }

        show();
    };

    function setAddr() {
        addrid = addrSelect.getValue();
        if (addrid!=0) {
            ajax.post('po&action=setAddr',{poid:data.poid,addrid:addrid},function(res){
                if (res.retcode!=0) { mwt.notify(res.retmsg,1500,'danger'); } 
                else {
                    data.addrid = addrid;
                    show();
                }
            });
        }
    }

    function setArrivalDate() {
        var dt = dateFd.getValue();
        ajax.post('po&action=setArrivalDate',{poid:data.poid,arrival_date:dt},function(res){
            if (res.retcode!=0) { mwt.notify(res.retmsg,1500,'danger'); } 
            else {
                data.arrival_date = dt;
                mwt.notify('已保存',1500,'success');
            }
        });
    }

	return o;
});
