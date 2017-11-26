define(function(require){
    /* 消息 */
    var o={};

    o.showEmpty=function(domid,msg) {
         var code = '<div class="mwt-wall" style="background:none;border:none;color:#999;">'+
            '<i class="sicon-cup"></i> '+msg+
        '</div>';
        jQuery('#'+domid).html(code);
    };

    o.showException=function(domid,msg) {
        var code = '<div class="mwt-wall mwt-wall-danger">'+msg+'</div>';
        jQuery('#'+domid).html(code);
    };

    o.showLoading=function(domid,msg) {
        var code = '<div class="mwt-wall" style="background:none;border:none;">'+
            '<i class="fa fa-refresh fa-spin"></i> '+msg+
        '</div>';
        jQuery('#'+domid).html(code);
    };

    return o;
});
