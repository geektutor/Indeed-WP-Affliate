<div class="uap-wrapper">
			<form action="" method="post">

				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Opt-In Settings', 'uap');?></h3>
					<div class="inside">
						<div class="uap-form-line">
							<h4><?php _e('Enable Opt-In', 'uap');?></h4>
							<label class="uap_label_shiwtch" style="margin:10px 0 10px -10px;">
								<?php $checked = ($data['metas']['uap_register_opt-in']) ? 'checked' : '';?>
								<input type="checkbox" class="uap-switch" onClick="uap_check_and_h(this, '#uap_register_opt-in');" <?php echo $checked;?> />
								<div class="switch" style="display:inline-block;"></div>
							</label>
							<input type="hidden" name="uap_register_opt-in" value="<?php echo $data['metas']['uap_register_opt-in'];?>" id="uap_register_opt-in" />
						</div>
						<div class="uap-form-line">
								<h4><?php _e('Opt-In Destination:', 'uap');?></h4>
			                	<select name="uap_register_opt-in-type">
			                    	    <?php
			                        	    $subscribe_types = array(
			                                                                    'aweber' => 'AWeber',
																																					'active_campaign' => 'Active Campaign',
			                                                                    'campaign_monitor' => 'CampaignMonitor',
			                                                                    'constant_contact' => 'Constant Contact',
			                                                                    'email_list' => __('E-mail List', 'uap'),
			                                                                    'get_response' => 'GetResponse',
			                                                                    'icontact' => 'IContact',
			                                                                    'madmimi' => 'Mad Mimi',
			                                                                    'mailchimp' => 'MailChimp',
			                                                                    'mymail' => 'Mailster (MyMail)',
			                                                                    'wysija' => 'Wysija',
			                                                                 );
			                                foreach ($subscribe_types as $k=>$v){
			                                    $selected = ($data['metas']['uap_register_opt-in-type']==$k) ? 'selected' : '';
			                                    ?>
			                                    <option value="<?php echo $k;?>" <?php echo $selected;?> ><?php
			                            	     	echo $v;
			                                    ?></option>
			                                    <?php
			                                }
			                                ?>
			                    </select>
						</div>
						<div><?php _e('During the registration process, the affiliate email address is sent to your OptIn destination.', 'uap');?></div>
						<div style="margin-top: 15px;">
							<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>
					</div>
				</div>

				<div class="uap-stuffbox">
					<h3 class="uap-h3">Active Campaign</h3>
					<div class="inside">
					    <table id="active_campaign_table">
					      <tbody>
					        <tr>
					          <td>
					            <?php _e('Api URL', 'uap');?>
					          </td>
					          <td>
					            <input type="text" value="<?php echo $data['metas']['uap_active_campaign_apiurl'];?>" name="uap_active_campaign_apiurl" style="min-width: 260px;">
					          </td>
					        </tr>
					        <tr>
					          <td>
					            <?php _e('Api Key:', 'uap');?>
					          </td>
					          <td>
					            <input type="text" value="<?php echo $data['metas']['uap_active_campaign_apikey'];?>" name="uap_active_campaign_apikey" style="min-width: 260px;">
					          </td>
					        </tr>
					        <tr>
					          <td>
					            <?php _e('List ID:', 'uap');?>
					          </td>
					          <td>
					            <input type="text" value="<?php echo $data['metas']['uap_active_campaign_listId'];?>" name="uap_active_campaign_listId" style="min-width: 260px;">
					          </td>
					        </tr>
					      </tbody>
					    </table>
						<div style="margin-top: 15px;">
							<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>
					</div>
				</div>

				<div class="uap-stuffbox">
					<h3 class="uap-h3"><?php _e('Additional Main E-Mail', 'uap');?></h3>
					<div class="inside">
						<input type="text" name="uap_main_email" value="<?php echo $data['metas']['uap_main_email'];?>" style="min-width: 300px;" />
						<div style="margin-top: 15px;">
							<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>
					</div>
				</div>


				<div class="uap-stuffbox">
					<h3 class="uap-h3">Aweber</h3>
					<div class="inside">
					    <table>
					      <tbody>
					        <tr>
					          <td>
					            <?php _e('Auth Code', 'uap');?>
					          </td>
					          <td>
					            <textarea id="uap_aweber_auth_code" name="uap_aweber_auth_code" style="min-width: 375px;"><?php
					            	echo $data['metas']['uap_aweber_auth_code'];
					            ?></textarea>
					          </td>
					        </tr>
					        <tr>
					          <td>
					          </td>
					          <td>
					            <a href="https://auth.aweber.com/1.0/oauth/authorize_app/751d27ee" target="_blank" class="uap-info-link">
					              <?php _e('Get Your Auth Code From Here', 'uap');?>
					            </a>
					          </td>
					        </tr>
					        <tr>
					          <td>
					            <?php _e('Unique List ID:', 'uap');?>
					          </td>
					          <td>
					            <input type="text" value="<?php echo $data['metas']['uap_aweber_list'];?>" name="uap_aweber_list" style="min-width: 375px;">
					          </td>
					        </tr>
					        <tr>
					          <td>
					          </td>
					          <td>
					            <a href="https://www.aweber.com/users/settings/" target="_blank" class="uap-info-link">
					              <?php _e('Get Unique List ID', 'uap');?>
					            </a>
					          </td>
					        </tr>
					        <tr>
					          <td>
					          </td>
					          <td>
					            <div onclick="uap_connect_aweber( '#uap_aweber_auth_code' );" class="button button-primary button-large">
					              <?php _e('Connect', 'uap');?>
					            </div>
					          </td>
					        </tr>
					      </tbody>
					    </table>
					    <div style="margin-top: 15px;">
							<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>
					</div>
				</div>

				<div class="uap-stuffbox">
					<h3 class="uap-h3">Mailchimp</h3>
					<div class="inside">
					    <table>
					      <tbody>
					        <tr>
					          <td>
					            <?php _e('API Key', 'uap');?>
					          </td>
					          <td>
					            <input type="text" value="<?php echo $data['metas']['uap_mailchimp_api'];?>" name="uap_mailchimp_api" style="min-width: 375px;">
					          </td>
					        </tr>
					        <tr>
					          <td>
					          </td>
					          <td>
					            <a href="http://kb.mailchimp.com/article/where-can-i-find-my-api-key" target="_blank" class="uap-info-link">
					              <?php _e('Where can I find my API Key?', 'uap');?>
					            </a>
					          </td>
					        </tr>
					        <tr>
					          <td>
					            <?php _e('ID List', 'uap');?>
					          </td>
					          <td>
					            <input type="text" value="<?php echo $data['metas']['uap_mailchimp_id_list'];?>" name="uap_mailchimp_id_list" style="min-width: 375px;">
					          </td>
					        </tr>
					        <tr>
					          <td>
					          </td>
					          <td>
					            <a href="http://kb.mailchimp.com/article/how-can-i-find-my-list-id/" target="_blank" class="uap-info-link">
					              <?php _e('Where can I find List ID?', 'uap');?>
					            </a>
					          </td>
					        </tr>
					      </tbody>
					    </table>
					    <div style="margin-top: 15px;">
							<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>
					</div>
				</div>

				<div class="uap-stuffbox">
					<h3 class="uap-h3">Get Response</h3>
					<div class="inside">
					    <table>
					      <tbody>
					        <tr>
					          <td>
					            GetResponse <?php _e('API Key', 'uap');?>
					          </td>
					          <td>
					            <input type="text" value="<?php echo $data['metas']['uap_getResponse_api_key'];?>" name="uap_getResponse_api_key" style="min-width: 240px;">
					          </td>
					        </tr>
					        <tr>
					          <td>
					          </td>
					          <td>
					            <a href="http://www.getresponse.com/learning-center/glossary/api-key.html" target="_blank" class="uap-info-link">
					              <?php _e('Where can I find my API Key?', 'uap');?>
					            </a>
					          </td>
					        </tr>
					        <tr>
					          <td>
					            GetResponse <?php _e('Campaign Token', 'uap');?>
					          </td>
					          <td>
					            <input type="text" value="<?php echo $data['metas']['uap_getResponse_token'];?>" name="uap_getResponse_token" style="min-width: 240px;">
					          </td>
					        </tr>
					        <tr>
					          <td>
					          </td>
					          <td>
					            <a href="https://app.getresponse.com/campaign_list.html " target="_blank" class="uap-info-link">
					              <?php _e('Where can I find Campaign Token?', 'uap');?>
					            </a>
					          </td>
					        </tr>
					      </tbody>
					    </table>
					    <div style="margin-top: 15px;">
							<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>
					</div>
				</div>

				<div class="uap-stuffbox">
					<h3 class="uap-h3">Campaign Monitor</h3>
					<div class="inside">
					    <table>
					      <tbody>
					        <tr>
					          <td>
					            CampaignMonitor <?php _e('API Key', 'uap');?>
					          </td>
					          <td>
					            <input type="text" value="<?php echo $data['metas']['uap_cm_api_key'];?>" name="uap_cm_api_key" style="min-width: 270px;">
					          </td>
					        </tr>
					        <tr>
					          <td>
					          </td>
					          <td>
					            <a href="https://www.campaignmonitor.com/api/getting-started/#apikey" target="_blank" class="uap-info-link">
					              <?php _e('Where can I find API Key ?', 'uap');?>
					            </a>
					          </td>
					        </tr>
					        <tr>
					          <td>
					            CampaignMonitor <?php _e('List ID', 'uap');?>
					          </td>
					          <td>
					            <input type="text" value="<?php echo $data['metas']['uap_cm_list_id'];?>" name="uap_cm_list_id" style="min-width: 270px;">
					          </td>
					        </tr>
					        <tr>
					          <td>
					          </td>
					          <td>
					            <a href="https://www.campaignmonitor.com/api/clients/#subscriber_lists" target="_blank" class="uap-info-link">
					              <?php _e('Where can I find List ID?', 'uap');?>
					            </a>
					          </td>
					        </tr>
					      </tbody>
					    </table>
					    <div style="margin-top: 15px;">
							<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>
					</div>
				</div>

				<div class="uap-stuffbox">
					<h3 class="uap-h3">IContact</h3>
					<div class="inside">
					    <table>
					      <tbody>
					        <tr>
					          <td>
					            iContact <?php _e('Username', 'uap');?>
					          </td>
					          <td>
					            <input type="text" value="<?php echo $data['metas']['uap_icontact_user'];?>" name="uap_icontact_user" style="min-width: 280px;">
					          </td>
					        </tr>
					        <tr>
					          <td>
					            iContact <?php _e('App ID', 'uap');?>
					          </td>
					          <td>
					            <input type="text" value="<?php echo $data['metas']['uap_icontact_appid'];?>" name="uap_icontact_appid" style="min-width: 280px;">
					          </td>
					        </tr>
					        <tr>
					          <td>
					          </td>
					          <td>
					            <a href="http://www.icontact.com/developerportal/documentation/register-your-app/" target="_blank" class="uap-info-link">
					              <?php _e('Where can I get my App ID?', 'uap');?>
					            </a>
					          </td>
					        </tr>
					        <tr>
					          <td>
					            iContact <?php _e('App Password', 'uap');?>
					          </td>
					          <td>
					            <input type="text" value="<?php echo $data['metas']['uap_icontact_pass'];?>" name="uap_icontact_pass" style="min-width: 280px;">
					          </td>
					        </tr>
					        <tr>
					          <td>
					            iContact <?php _e('List ID', 'uap');?>
					          </td>
					          <td>
					            <input type="text" value="<?php echo $data['metas']['uap_icontact_list_id'];?>" name="uap_icontact_list_id" style="min-width: 280px;">
					          </td>
					        </tr>
					        <tr>
					          <td>
					          </td>
					          <td>
					            <div>
					              <a href="https://app.icontact.com/icp/core/mycontacts/lists" target="_blank" class="uap-info-link">
					                <?php _e('Click on the list name:', 'uap');?>
					              </a>
					            </div>
					            <div>
					            	<?php _e('Click on the list name and get the ID from the URL', 'uap');?> (ex:  https://app.icontact.com/icp/core/mycontacts/lists/edit/
					              <b>
					                ID_LIST
					              </b>
					              /?token=f155cba025333b071d49974c96ae0894 )
					            </div>
					          </td>
					        </tr>
					      </tbody>
					    </table>
					    <div style="margin-top: 15px;">
							<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
						</div>
					</div>
				</div>

			<div class="uap-stuffbox">
				<h3 class="uap-h3">Constant Contact</h3>
				<div class="inside">
				    <table>
				      <tbody>
				        <tr>
				          <td>
				            Constant Contact <?php _e('Username', 'uap');?>
				          </td>
				          <td>
				            <input type="text" value="<?php echo $data['metas']['uap_cc_user'];?>" id="uap_cc_user" name="uap_cc_user" style="min-width: 260px;">
				          </td>
				        </tr>
				        <tr>
				          <td>
				            Constant Contact <?php _e('Password', 'uap');?>
				          </td>
				          <td>
				            <input type="password" value="<?php echo $data['metas']['uap_cc_pass'];?>" id="uap_cc_pass" name="uap_cc_pass" style="min-width: 260px;">
				          </td>
				        </tr>
				        <tr>
				          <td>
				          </td>
				          <td>
				            <div onclick="uap_get_cc_list( '#uap_cc_user', '#uap_cc_pass' );" class="button button-primary button-large">
				              <?php _e('Get Lists', 'uap');?>
				            </div>
				          </td>
				        </tr>
				        <tr>
				          <td>
				            Constant Contact <?php _e('List', 'uap');?>
				          </td>
				          <td>
				            <select id="uap_cc_list" name="uap_cc_list" style="min-width: 260px;">
				            	<?php
				            		$list_name = '';
				            		if (isset($data['metas']['uap_cc_list']) && $data['metas']['uap_cc_list']){
				            			//getting list name by id
				            			include_once UAP_PATH . 'classes/email_services/constantcontact/class.cc.php';
				            			$cc = new cc($data['metas']['uap_cc_user'], $data['metas']['uap_cc_pass']);
				            			@$list_arr= $cc->get_list($data['metas']['uap_cc_list']);
				            			if(isset($list_arr['Name'])) $list_name = $list_arr['Name'];
				            		}
				            	?>
				            	<option value="<?php echo $data['metas']['uap_cc_list'];?>"><?php echo $list_name;?></option>
				            </select>
				          </td>
				        </tr>
				      </tbody>
				    </table>
					<div style="margin-top: 15px;">
						<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
					</div>
				</div>
			</div>

			<div class="uap-stuffbox">
				<h3 class="uap-h3">Wysija Contact</h3>
				<div class="inside">
				    <table>
				      <tbody>
				        <tr>
				          <td>
				            <?php _e('Select Wysija List:', 'uap');?>
				          </td>
				          <td>
		                  	<?php
		                        @$wysija_list = $obj->indeed_returnWysijaList();
		                        if ($wysija_list && count($wysija_list)>0){
		                        	?>
		                            <select name="uap_wysija_list_id">
		                            	<?php
		                                	foreach ($wysija_list as $k=>$v){
		                                		$selected = '';
		                                		if($data['metas']['uap_wysija_list_id']==$k) $selected = 'selected="selected"';
		                                        ?>
		                                        	<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
		                                        <?php
		                                    }
		                                ?>
		                            </select>
		                     <?php
		                     	}else echo __("No List available ", 'uap') . "<input type='hidden' name='uap_wysija_list_id' value=''/> ";
		                     ?>
				          </td>
				        </tr>
				      </tbody>
				    </table>
					<div style="margin-top: 15px;">
						<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
					</div>
				</div>
			</div>

			<div class="uap-stuffbox">
				<h3 class="uap-h3">Mailster (MyMail)</h3>
				<div class="inside">
				    <table>
				      <tbody>
				        <tr>
				          <td>
				            <?php _e('Select MyMail List:', 'uap');?>
				          </td>
				          <td>
							<?php
		                    	$mymailList = $obj->indeed_getMyMailLists();
		                        if ($mymailList){
		                        	?>
		                            <select name="uap_mymail_list_id">
		                            	<?php
		                                foreach ($mymailList as $k=>$v){
		                                	$selected = '';
		                                	if ($data['metas']['uap_mymail_list_id']==$k) $selected = 'selected="selected"';
		                                    ?>
		                                    	<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
		                                <?php
		                                }
		                                ?>
		                            </select>
		                    <?php
		                    	}else echo __('No List available', 'uap') . " <input type='hidden' name='uap_mymail_list_id' value=''/> ";
				          	?>
				          </td>
				        </tr>
				      </tbody>
				    </table>
					<div style="margin-top: 15px;">
						<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
					</div>
				</div>
			</div>

			<div class="uap-stuffbox">
				<h3 class="uap-h3">Mad Mimi</h3>
				<div class="inside">
				    <table>
				      <tbody>
				        <tr>
				          <td>
				            <?php _e('Username Or Email:', 'uap');?>
				          </td>
				          <td>
				            <input type="text" value="<?php echo $data['metas']['uap_madmimi_username'];?>" name="uap_madmimi_username" style="min-width: 260px;">
				          </td>
				        </tr>
				        <tr>
				          <td>
				            <?php _e('Api Key:', 'uap');?>
				          </td>
				          <td>
				            <input type="text" value="<?php echo $data['metas']['uap_madmimi_apikey'];?>" name="uap_madmimi_apikey" style="min-width: 260px;">
				          </td>
				        </tr>
				        <tr>
				          <td>
				            <?php _e('List Name:', 'uap');?>
				          </td>
				          <td>
				            <input type="text" value="<?php echo $data['metas']['uap_madmimi_listname'];?>" name="uap_madmimi_listname" style="min-width: 260px;">
				          </td>
				        </tr>
				      </tbody>
				    </table>
					<div style="margin-top: 15px;">
						<input type="submit" value="<?php _e('Save Changes', 'uap');?>" name="save" class="button button-primary button-large" />
					</div>
				</div>
			</div>

			<div class="uap-stuffbox">
				<h3 class="uap-h3"><?php _e('Saved E-mail List', 'uap');?></h3>
				<div class="inside">
				    <textarea disabled style="width: 450px;height: 100px;"><?php
				    	echo $data['metas']['uap_email_list'];
				    ?></textarea>
				</div>
			</div>
		</form>
</div>
