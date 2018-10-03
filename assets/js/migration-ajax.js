UapMigrate = {
    trigger: '',
    rankSelector: '',
    progressBarWrapp: '',
    progressBarDiv: '',
    completeDiv: '',
    completeMessage: '',

    init: function(args){
        var obj = this;
        obj.setAttributes(obj, args);

        jQuery(document).ready(function(){
            obj.handleTriggerAction(obj);
        });
    },

    handleTriggerAction: function(obj){
      jQuery(obj.trigger).on('click', function(){
        obj.insertProgressBar(obj);
        jQuery.ajax({
              type : 'post',
              url : window.uap_url + '/wp-admin/admin-ajax.php',
              data : {
                         action: 'uap_trigger_migration',
                         serviceType: jQuery(obj.trigger).attr('data-type'),
                         assignRank: jQuery(obj.rankSelector).val(),
               },
              success: function (response) {}
         });
      });
    },

    insertProgressBar: function(obj){
        jQuery.ajax({
              type : 'post',
              url : window.uap_url + '/wp-admin/admin-ajax.php',
              data : {
                         action: 'uap_get_empty_progress_bar',
               },
              success: function (response) {
                  jQuery(obj.progressBarWrapp).html(response);
                  obj.updateProgressBar(obj);
              }
         });
    },

    updateProgressBar: function(obj){
      jQuery.ajax({
            type : 'post',
            url : window.uap_url + '/wp-admin/admin-ajax.php',
            data : {
                       action: 'uap_migrate_get_status',
                       serviceType: jQuery(obj.trigger).attr('data-type'),
            },
            success: function (response) {
                //console.log('update response: ' + response);

                    if (response>-1){
                        jQuery(obj.progressBarDiv).attr('aria-valuenow', response);
                        jQuery(obj.progressBarDiv).css('width', response + '%');
                        jQuery(obj.progressBarDiv).html(response + '%');
                    }

                    if (response==100){
                        obj.resetLog(obj);
                        return false;
                    }
                    setTimeout(
                        function(){
                           obj.updateProgressBar(obj);
                    }, 1000);

            }
       });
    },

    resetLog: function(obj){
      jQuery(obj.completeDiv).html(obj.completeMessage);
      jQuery.ajax({
            type : 'post',
            url : window.uap_url + '/wp-admin/admin-ajax.php',
            data : {
                       action: 'uap_migrate_reset_log',
                       serviceType: jQuery(obj.trigger).attr('data-type'),
            },
            success: function(response){
                location.reload();
            },
      });
    },

    setAttributes: function(obj, args){
        for (var key in args) {
          obj[key] = args[key];
        }
    },

};
