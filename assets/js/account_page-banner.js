var UapAccountPageBanner = {
    triggerId       : '',
    saveImageTarget : '',
    cropImageTarget : '',
    bannerClass     : '',

    init: function(args){
        var obj = this
        obj.setAttributes(obj, args)
        jQuery(document).ready(function(){
            var options = {
              uploadUrl                 : obj.saveImageTarget,
              cropUrl                   : obj.cropImageTarget,
              modal                     : true,
              fileNameInput             : 'uap_upload_image_top_banner',
              imgEyecandyOpacity        : 0.4,
              loaderHtml                : '<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div>',
              onBeforeImgUpload         : function(){},
              onAfterImgUpload          : function(){},
              onImgDrag                 : function(){},
              onImgZoom                 : function(){},
              onBeforeImgCrop           : function(){},
              onAfterImgCrop            : function(response){ obj.handleAfterImageCrop(obj, response) },
              onAfterRemoveCroppedImg   : function(){ obj.handleRemove(obj) },
              onError                   : function(e){ console.log('onError:' + e) }
            }
            var cropperHeader = new Croppic(obj.triggerId, options)
        })
    },

    setAttributes: function(obj, args){
        for (var key in args) {
          obj[key] = args[key]
        }
    },

    handleAfterImageCrop: function(obj, response){
        if (response.status=='success'){
            jQuery('.'+obj.bannerClass).css('background-image', response.url)
        }
    },

    handleRemove: function(obj){
        var old = jQuery('.' + obj.bannerClass).attr('data-banner')
        jQuery.ajax({
            type : "post",
            url : decodeURI(ajax_url),
            data : {
                       action: "uap_ap_reset_custom_banner",
                       oldBanner: old,
                   },
            success: function (data) {
            	jQuery('.' + obj.bannerClass).css('background-image', old)
            }
       	});
    }


}
