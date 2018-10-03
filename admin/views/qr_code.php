<form action="" method="post">
	<div class="uap-stuffbox">
		<h3 class="uap-h3"><?php _e('QR Codes', 'uap');?></h3>
		<div class="inside">
			
			<div class="row">
				<div class="col-xs-7">
					<h3><?php _e('Activate/Hold QR Codes into Account Page', 'uap');?></h3>
					<p><?php _e('Affiliates may download and share their QR codes anywhere out of the website.', 'uap');?></p>
					<label class="woo_account_page_enable" style="margin:10px 0 10px -10px;">
					<?php $checked = ($data['metas']['uap_qr_code_enable']) ? 'checked' : '';?>
					<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_qr_code_enable');" <?php echo $checked;?> />
					<div class="switch" style="display:inline-block;"></div>
					</label>
					<input type="hidden" name="uap_qr_code_enable" value="<?php echo $data['metas']['uap_qr_code_enable'];?>" id="uap_qr_code_enable" /> 
				</div>
			</div>
			<div class="uap-line-break"></div>
			<div class="row">
				<div class="col-xs-6">
					<h3><?php _e('Additional Settings', 'uap');?></h3>
					<br/>
					<h4><?php _e(' QRCode Image Size', 'uap');?></h4>
					<p><?php _e('Decides the image size for the QR code. Bigger is much easier to scan, but has an increased load time.', 'uap');?></p>
					<input type="number" value="<?php echo $data['metas']['uap_qr_code_size'];?>" name="uap_qr_code_size" min="1" max="10" style="min-width:150px;" />
				</div>
			</div>

			<div class="row" style="margin-top:30px;">
				<div class="col-xs-6">
					<h4><?php _e('ECC Data Level', 'uap');?></h4>
					<p><?php _e('Error Code Correction is their ability to sustain "damage" and continue to function even when a part of the QR code image is obscured. Level L or Level M represent the best compromise between density and ruggedness for general marketing use. ', 'uap');?></p>
					<select name="uap_qr_code_ecc_level"  style="min-width:150px;">
						<?php foreach (array('l' => 'L', 'm' => 'M', 'q' => 'Q', 'h' => 'H') as $k=>$v): ?>
							<?php $selected = ($k==$data['metas']['uap_qr_code_ecc_level']) ? 'selected' : '';?>
							<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
						<?php endforeach;?>
					</select>
				</div>
			</div>			
			
			
			<div class="uap-submit-form" style="margin-top: 20px;"> 
				<input type="submit" value="<?php _e('Save', 'uap');?>" name="save" class="button button-primary button-large" />
			</div>	
						
		</div>
	</div>
</form>