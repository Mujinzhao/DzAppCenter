/* japp.js, (c) 2016 mawentao */
/* 全局变量 */
var ajax,log,frame,dict,uc,posnav,msg;
var conf = {
    // 日志级别 0:关闭;>=1:WARN;>=2:INFO;>=3DEBUG;
    loglevel: 3
};

/* ---------------- store加载消息 ---------------- */
var loadingdivid;
function store_before_load(){loadingdivid=mwt.notify("数据加载中...",10000,'loading');
    jQuery('body').css('cursor','wait');
}
function store_after_load(){mwt.notify_destroy(loadingdivid);
    jQuery('body').css('cursor','');
}

/* JApp定义 */
var JApp=function(baseUrl)
{
    this.init = function() {
		require.config({
			baseUrl: baseUrl,
			packages: [
				{name:'lang', location:'lang', main:'main'},
				{name:'frame', location:'frame', main:'main'}
			]
		});
        require(['jappengine','core/log','core/ajax','common/dict','common/uc','common/posnav','common/msg','frame'], 
          function(jappengine,corelog,coreajax,commonDict,commonUc,commonPosnav,commonMsg,fm){
			log   = corelog;
			ajax  = coreajax;
            dict  = commonDict;
            uc    = commonUc;
            posnav= commonPosnav;
            msg   = commonMsg;
			frame = fm;
			jappengine.start();
        });
    };
};


// 
function timeRender(v) {
    var st = strtotime(v);
    return '<span style="color:gray">'+date('Y/m/d H:i',st)+'</span>';
}

function prStateRender(v) {
    switch(parseInt(v)) {
        case 0: return '<span style="color:#333;">未保存</span>';
        case 1: return '<span style="color:#333;">未提交</span>';
        case 100: return '<span style="color:green;">审批通过</span>';
        case 101: return '<span style="color:blue;">审批中</span>';
        case 103: return '<span style="color:red;">驳回</span>';
        case 110: return '<span style="color:#333;">已撤销</span>';
    }
    return v;
}
