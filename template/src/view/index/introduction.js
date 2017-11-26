define(function(require){
	/* 公司简介 */

    var o={};

	o.init=function(domid){
		var code = '<div class="panel">'+
			'<div class="panel-body">'+
				'<img src="'+setting.introduction_img+'" class="introduction-img">'+
				'<div class="content" style="margin-top:5px;">'+setting.introduction+'</div>'+
			'</div>'+
		'</div>';
		jQuery('#'+domid).html(code);
	};

	return o;
});
