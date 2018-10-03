<div class="uap-popup-wrapp" id="uap_admin_popup_box">
	<div class="uap-the-popup">
        <div class="uap-popup-top">
        	  <div class="title"><?php _e('Ultimate Affiliate Pro - Send Direct Email', 'uap');?></div>
            <div class="close-bttn" id="uap_send_email_via_admin_close_popup_bttn"></div>
            <div class="clear"></div>
        </div>
        <div class="uap-popup-content uap-send-email">
         <div class="uap-inside-item">
           <div class="row">
             <div class="col-xs-6">
            	<div class="input-group">
             		 <span class="input-group-addon"><?php _e('From', 'uap');?></span>
	           		 <input type="text" class="form-control" id="indeed_admin_send_mail_from" value="<?php echo $fromEmail;?>"/>
        		</div>
            </div>
           </div>
           <div class="row">
             <div class="col-xs-6">
            	<div class="input-group">
              <span class="input-group-addon"><?php _e('To', 'uap');?></span>
	            <input type="text"  class="form-control" id="indeed_admin_send_mail_to" value="<?php echo $toEmail;?>" disabled />
        		</div>
            </div>
           </div>
           <div class="row">
             <div class="col-xs-8">
            	<div class="input-group">
              <span class="input-group-addon"><?php _e('Subject', 'uap');?></span>
	            <input type="text" class="form-control" id="indeed_admin_send_mail_subject" value="<?php echo $website . __(' Notification', 'uap');?>" />
        		</div>
            </div>
           </div>
           <div class="row">
             <div class="col-xs-12">
              <h4><?php _e('Message:', 'uap');?></h4>
              <textarea id="indeed_admin_send_mail_content"><?php echo 'Hi ' . $fullName . ", ";?></textarea>
            </div>
           </div>
           <div class="row">
             <div class="col-xs-8">
            	<div class="input-group">
          			<div class="input-group-btn">
              			<button class="btn btn-primary pointer" type="button" id="indeed_admin_send_mail_submit_bttn"><?php _e('Send Email', 'uap');?></button>
          			</div>
        		</div>
            </div>
           </div>
         </div>
    	</div>
    </div>
</div>
