<div class="uap-wrapper">
<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('Account Page - REST API', 'uap');?></h3>
		<div class="inside">

			<div class="uap-form-line">
				<h2><?php _e('Activate/Hold Customize Menu', 'uap');?></h2>
				<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['uap_rest_api_enabled']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_rest_api_enabled');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
				</label>
				<input type="hidden" name="uap_rest_api_enabled" value="<?php echo $data['metas']['uap_rest_api_enabled'];?>" id="uap_rest_api_enabled" />
			</div>




			<div class="uap-submit-form" style="margin-top: 20px;">
				<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>

		</div>
	</div>


</form>

<?php
$endpoints = [
		'getAffiliates'						=> [
						'title' 	=> __('List Affiliates', 'uap'),
						'link'		=> 'affiliates',
						'method'	=> 'GET',
						'args'		=> 'page, limit',
						'extra'		=> '',
		],
		'approveAffiliate'				=> [
						'title' 	=> __('Approve affiliate', 'uap'),
						'link'		=> 'approve-affiliate/{affiliateId}',
						'method'	=> 'POST',
						'args'		=> 'affiliateId',
						'extra'		=> '',
		],
		'updateAffiliateRank'			=> [
						'title' 	=> __('Update affiliate rank', 'uap'),
						'link'		=> 'update-affiliate-rank/{affiliateId}/{newRankId}',
						'method'	=> 'POST',
						'args'		=> 'affiliateId, rankId',
						'extra'		=> '',
		],
		'getAllUserData'					=> [
						'title' 	=> __('Get user data', 'uap'),
						'link'		=> 'get-user-data/{affiliateId}',
						'method'	=> 'GET',
						'args'		=> 'affiliateId',
						'extra'		=> '',
		],
		'getUserFieldValue'				=> [
						'title' 	=> __('Get user field value', 'uap'),
						'link'		=> 'get-user-field-value/{affiliateId}/{fieldName}',
						'method'	=> 'GET',
						'args'		=> 'affiliateId, fieldName',
						'extra'		=> '',
		],
		'getAffiliateRank'				=> [
						'title' 	=> __('Get affiliate rank', 'uap'),
						'link'		=> 'get-affiliate-rank/{affiliateId}',
						'method'	=> 'GET',
						'args'		=> 'affiliateId',
						'extra'		=> '',
		],
		'getAffiliateRankDetails' => [
						'title' 	=> __('Get affiliate rank details', 'uap'),
						'link'		=> 'get-affiliate-rank-details/{affiliateId}',
						'method'	=> 'GET',
						'args'		=> 'affiliateId',
						'extra'		=> '',
		],
		'searchAffiliate'					=> [
						'title' 	=> __('Search Affiliate', 'uap'),
						'link'		=> 'search-affiliate/{search}',
						'method'	=> 'GET',
						'args'		=> 'search',
						'extra'		=> '',
		],
		'listRanks'								=> [
						'title' 	=> __('List Ranks', 'uap'),
						'link'		=> 'list-ranks',
						'method'	=> 'GET',
						'args'		=> '',
						'extra'		=> '',
		],
		'getAffiliatesByRank'			=> [
						'title' 	=> __('Get affiliates by rank', 'uap'),
						'link'		=> 'list-affiliates-by-rank/{rankId}',
						'method'	=> 'GET',
						'args'		=> 'rankId',
						'extra'		=> '',
		],
		'makeUserAffiliate'				=> [
						'title' 	=> __('Make user affiliate', 'uap'),
						'link'		=> 'make-user-affiliate/{userId}',
						'method'	=> 'PUT',
						'args'		=> 'userId',
						'extra'		=> '',
		],
		'listReferrals'						=> [
						'title' 	=> __('List Referrals', 'uap'),
						'link'		=> 'list-referrals',
						'method'	=> 'GET',
						'args'		=> '',
						'extra'		=> '',
		],
		'createReferral'							=> [
						'title' 	=> __('Add Referral', 'uap'),
						'link'		=> 'add-referral',
						'method'	=> 'PUT',
						'args'		=> 'json with all referral details',
						'extra'		=> 'Example: {
														"refferal_wp_uid": 2,
														"campaign": "",
														"affiliate_id": "",
														"visit_id": "",
														"description": "test",
														"source": "restapi",
														"reference": "q",
														"reference_details": "test",
														"parent_referral_id": "test",
														"child_referral_id": "",
														"amount": 10,
														"currency": "usd",
														"date": "12-02-2018",
														"status": 1,
														"payment": 0
					}'
		],

];
?>
			<?php foreach ($endpoints as $array):?>
					<div class="uap-stuffbox">
						<h3 class="uap-h3"><?php echo $array['title'];?></h3>
						<div class="inside">
							<div class="uap-form-line">
								<p class="uap-api-link"><?php echo $data['base_url'] . '/wp-json/ultimate-affiliates-pro/v1/' . $array['link'];?></p>
								<p><?php echo '<strong>' . __('Method: ', 'uap') . '</strong>' . $array['method'];?></p>
								<p><?php echo '<strong>' . __('Arguments: ', 'uap') . '</strong>' . $array['args'];?></p>
								<p><?php echo $array['extra'];?></p>
							</div>
						</div>
					</div>
			<?php endforeach;?>

</div>
