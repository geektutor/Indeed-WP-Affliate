<div class="uap-wrapper">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('AffiliateWP migration', 'uap');?></h3>
		<div class="inside">
		  <div class="row">
		  <div class="col-xs-5">
			<div class="uap-form-line">
            <h2><?php _e('Migrate from AffiliateWP script', 'uap');?></h2>
            <p><?php _e('Copy the Affiliate users and stored Referrals from AffiliateWP script. You can assign a specific UAP Rank to new copied affiliate users during this process.', 'uap'); ?></p>
            <br/>
            <p><strong><?php _e('Note: Affiliates and Referrals will not be removed after migration for safety reasons. You will have to manually remove them.', 'uap'); ?></strong></p>
        	</div>
            <div class="uap-line-break"></div>	
            <div class="uap-form-line">
            <h3><?php _e('Assign rank:', 'uap');?></h3>
            <select class="uap-migrate-assign-rank">
              <?php foreach ($data['ranks_available'] as $k=>$v):?>
                <?php $selected = ($k==$data['rank_id']) ? 'selected' : '';?>
                <option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $v;?></option>
              <?php endforeach;?>
            </select>
        	</div>
            <div class="uap-line-break"></div>	
            <div class="uap-form-line">
        <div style="margin: 5px 0px;">
            <div class="uap-progress-bar-wrapp"></div>
        </div>
        <span class="uap-trigger-event-migrate button button-primary button-large" data-type="affiliate-wp"><?php _e('Trigger', 'uap');?></span>
			</div>

		</div>
	 </div>
    </div>
	</div>

</div>

<script src="<?php echo UAP_URL . 'assets/js/migration-ajax.js';?>"></script>
<script>
UapMigrate.init({
		trigger: '.uap-trigger-event-migrate',
		rankSelector: '.uap-migrate-assign-rank',
		progressBarWrapp: '.uap-progress-bar-wrapp',
		completeDiv: '.uap-progress-bar-warning',
		completeMessage: '<?php _e('Process completed!', 'uap');?>',
		progressBarDiv: '.progress-bar',
});
</script>
