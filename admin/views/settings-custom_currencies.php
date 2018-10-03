<div class="uap-wrapper">
			<form action="" method="post">
				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Add new Currency', 'uap');?></h3>
					<div class="inside">		
						<div class="uap-inside-item">
							<label class="iump-labels-special"><?php _e('Code:', 'uap');?></label>
							<input type="test" value="" name="new_currency_code" />
							<p><?php _e('Insert a valid Currency Code, ex: ', 'uap');?><span style="font-weight:bold;"><?php _e('USD, EUR, CAD.', 'uap');?></span></p>
						</div>
						<div class="uap-inside-item">
							<label class="iump-labels-special"><?php _e('Name:', 'uap');?></label>
							<input type="test" value="" name="new_currency_name" />
						</div>
						<div class="uap-submit-form">
							<input type="submit" value="<?php _e('Save', 'uap');?>" name="uap_save" class="button button-primary button-large" />
						</div>							
					</div>
				</div>
				
				<?php if ($currencies!==FALSE && count($currencies)>0): ?>
					<div style="width: 98%;">
						<table class="wp-list-table widefat fixed tags" style="margin-bottom: 20px;">
							<thead>
								<tr>
									<th class="manage-column">Code</th>
									<th class="manage-column">Name</th>
									<th class="manage-column" style="width:80px; text-align: center;">Delete</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($currencies as $code=>$name):?>
									<tr id="uap_div_<?php echo $code;?>">
										<td><?php echo $code;?></td>
										<td><?php echo $name;?></td>
										<td style="text-align: center;"><i class="fa-uap uap-icon-remove-e" onClick="uap_remove_currency('<?php echo $code;?>');" style="cursor: pointer;"></i></td>
									</tr>						
								<?php endforeach;?>		
							</tbody>
						</table>				
					</div>
				<?php endif;?>			
			</form>
</div>			