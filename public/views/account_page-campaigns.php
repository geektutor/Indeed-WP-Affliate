<div class="uap-ap-wrap">

<?php if (!empty($data['title'])):?>
	<h3><?php echo $data['title'];?></h3>
<?php endif;?>
<?php if (!empty($data['message'])):?>
	<p><?php echo do_shortcode($data['message']);?></p>
<?php endif;?>

	<form method="post" action="" id="uap_campaign_form">
		<?php if (!empty($data['campaigns'])) : ?>
		<h4><?php _e('List Of Campaigns', 'uap');?></h4>
		<div class="uap-account-campaign-list-wrapper">
		<?php foreach ($data['campaigns'] as $value) : ?>
			<div class="uap-account-campaign-list">
				<div style="width:80%; display:inline-block; color: #21759b; font-weight:bold;"><?php echo $value;?></div><div style="width:20%; display:inline-block"><i class="fa-uap fa-trash-uap" onClick="jQuery('#uap_delete_campaign').val('<?php echo $value;?>');jQuery('#uap_campaign_form').submit();"></i></div>
			</div>
		<?php endforeach;?>
		</div>
			<input type="hidden" value="" name="uap_delete_campaign" id="uap_delete_campaign"/>
		<?php endif;?>
		<br/>
		<h5><?php _e('Add New Campaign', 'uap');?></h5>
		<div class="uap-ap-field">
			<label class="uap-ap-label uap-special-label">Name</label>
			<input type="text" name="campaign_name" value="" class="uap-public-form-control "/>
		</div>
		<div class="uap-ap-field">
			<input type="submit" name="save" value="<?php _e('Save', 'uap');?>" class="button button-primary button-large" />
		</div>	
	</form>
</div>