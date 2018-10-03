<div class="uap-wrapper">
	<div class="uap-page-title">Ultimate Affiliate Pro - <span class="second-text"><?php _e('Offers', 'uap');?></span></div>
		<a href="<?php echo $data['url-add_edit'];?>" class="uap-add-new-like-wp"><i class="fa-uap fa-add-uap"></i><span><?php _e('Add New Offer', 'uap');?></span></a>
		<span class="uap-top-message"><?php _e('...create your Offers for specific Products or Affiliates', 'uap');?></span>
		<?php if (!empty($data['errors'])) : ?>
			<div class="uap-wrapp-the-errors"><?php echo $data['errors'];?></div>
		<?php endif;?>
		<?php if (!empty($data['listing_items'])) : ?>
			<form action="" method="post" id="form_offers">
				<div class="uap-offer-items-wrap">
					<?php foreach ($data['listing_items'] as $arr) : ?>
						<?php 	
							$inside_data = unserialize($arr['settings']);
							$color = (empty($inside_data['color']))	? '#000' : '#' . $inside_data['color'];
							$disabled = (empty($arr['status'])) ? 'uap-disabled-box' : '';
						?>
					   <div class="uap-admin-dashboard-offer-box-wrap <?php echo $disabled;?>">
					      <div class="uap-admin-dashboard-offer-box" id="uap-b-item-1" style="background-color: <?php echo $color;?>">
					         <div class="uap-admin-dashboard-offer-box-main">
					            <div class="uap-admin-dashboard-offer-box-title"><?php echo $arr['name']?></div>
					            <div class="uap-admin-dashboard-offer-box-content">
								<?php _e('Target Affiliates:', 'uap');?>
								<?php echo $arr['affiliates'];?>
								</div>
					            <div class="uap-admin-dashboard-offer-box-links-wrap">
					               <div class="uap-admin-dashboard-offer-box-links">
					                  <a href="<?php echo $data['url-add_edit'] . '&id=' . $arr['id'];?>" class="uap-admin-dashboard-offer-box-link"><?php _e('Edit', 'uap');?></a>
					                  <div onclick="uap_delete_from_table(<?php echo $arr['id'];?>, 'Offer', '#delete_offer_id', '#form_offers');" class="uap-admin-dashboard-offer-box-link"><?php _e('Delete', 'uap');?></div>
					               </div>
					            </div>
					         </div>
					         <div class="uap-admin-dashboard-offer-box-bottom">
					            <div class="uap-admin-dashboard-offer-box-files">
									<?php switch ($arr['amount_type']){
										case 'flat' : echo uap_format_price_and_currency($currency, $arr['amount_value']); break;
										case 'percentage' : echo $arr['amount_value'] . '%'; break;	
									} ?>
					               <div class="uap-admin-dashboard-offer-box-dest">&nbsp;</div>
					            </div>
					            <div class="uap-admin-dashboard-offer-box-date"><span style="font-weight:200;"><?php _e('From', 'uap');?> </span><span><?php echo $arr['start_date'];?></span><br/><span style="font-weight:200;"><?php _e('to', 'uap');?> </span><span><?php echo $arr['end_date'];?></span></div>
					            <div class="clear"></div>
					         </div>
					      </div>
					   </div>
					<?php endforeach;?>
					<div class="uap-clear"></div>
				</div>
				<input type="hidden" name="delete_offers[]" value="" id="delete_offer_id" />
			</form>
		<?php else : ?>
			<h4 style="margin-top:50px;"><?php _e('No Offers to show. Please, add your first Offer. ', 'uap');?></h4>
		<?php endif;?>	
</div>
</div><!-- end of uap-dashboard-wrap -->
							
