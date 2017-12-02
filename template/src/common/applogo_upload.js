define(function(require){
    /* AppLogo上传控件 */
    var callbackfun,filesel,sid,o={};
	var domid = "image-upload-div";

    function create() {
		var code = '<form method="POST" enctype="multipart/form-data">'+
                     '<input type="file" id="imgfile" name="imgfile" accept="image/*" style="display:none;"/>'+
                   '</form>';
        jQuery("#"+domid).html(code);

		////////////////////////////////////////////////////
		// 上传进度
		/*jQuery('#imgfile').fileupload({
        		dataType: 'json',

                progressall: function (e, data) {
                    /*var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('.progress .bar').css(
                        'width',
                        progress + '%'
                    );
                    $('.progress .bar').text(progress + '%');
					console.log(data);
                },

                done: function(e, data) {
					console.log("upload done");
                    //$('.progress .bar').text("done");
                }
            });*/
		////////////////////////////////////////////////////

        filesel = jQuery('#imgfile');    
        filesel.change(do_upload);
    }

    function do_upload() {
        var imgfile = filesel.val();
        if (imgfile=="") return;
        //alert(imgfile);
        var upurl= ajax.getAjaxUrl("upload&action=applogo&fileElementId=imgfile&appkey="+sid);
        jQuery.ajaxFileUpload({
            url: upurl,
            secureuri: false,
            fileElementId: 'imgfile',
            dataType: 'json',
            timeout: 30000,
            complete: function(data) {
                console.log(data);
                create();
            },  
            success: function(data,status) {
                callbackfun(data);  
            },  
            error: function (data, status, e) {
                alert("Error: "+e);
            }
        });
    };

    o.init = function() {
        if(!document.getElementById(domid)) { 
            var onediv = document.createElement('div');
            onediv.id=domid;
            document.body.appendChild(onediv);
        }
        create();
    };

    o.upload = function(_sid,callfun) {
        if (!filesel) {
            o.init();
        }
        sid = _sid;
        callbackfun = callfun;
        filesel.val("");
        filesel.click();
    };

    return o;
});
