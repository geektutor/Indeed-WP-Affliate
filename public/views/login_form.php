<?php 

function uap_print_form_login($meta_arr){
	/*
	 * @param array
	 * @return string
	 */
	$str = '';
	if($meta_arr['uap_login_custom_css']){
		$str .= '<style>'. stripslashes($meta_arr['uap_login_custom_css']).'</style>';
	}
	
	$user_field_id = 'uap_login_username';
	$password_field_id = 'uap_login_password';
	
	$sm_string = '';
	$captcha = '';
	if (!empty($meta_arr['uap_login_show_recaptcha'])){
		$key = get_option('uap_recaptcha_public');
		if ($key){
			$captcha .= '<div class="g-recaptcha-wrapper">';
			$captcha .= '<div class="g-recaptcha" data-sitekey="' . $key . '"></div>';
			$captcha .= '<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=en"></script>';				
			$captcha .= '</div>';
		}	
	}	
	
	$str .= '<div class="uap-login-form-wrap '.$meta_arr['uap_login_template'].'">'
			.'<form action="" method="post" id="uap_login_form">'
			. '<input type="hidden" name="uapaction" value="login" />';
	
	switch ($meta_arr['uap_login_template']){
	
	case 'uap-login-template-2':
		//<<<< FIELDS		
		$str .= '<div class="uap-form-line-fr">' . '<span class="uap-form-label-fr uap-form-label-username">'.__('Username', 'uap').':</span>'
				. '<input type="text" value="" name="log" id="' . $user_field_id . '" />'
				. '</div>'
				. '<div class="uap-form-line-fr">' . '<span class="uap-form-label-fr uap-form-label-pass">'.__('Password', 'uap').':</span>'
				. '<input type="password" value="" name="pwd" id="' . $password_field_id . '" />'
				. '</div>';
		//>>>>
		$str .= $sm_string;			
		//<<<< REMEMBER ME			
		if($meta_arr['uap_login_remember_me']){
			$str .= '<div class="uap-form-line-fr uap-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="uap-form-input-remember" /><span class="uap-form-label-fr uap-form-label-remember">'.__('Remember Me', 'uap').'</span> </div>';
		}
		//>>>>
		
		//<<<< ADDITIONAL LINKS
		if($meta_arr['uap_login_register'] || $meta_arr['uap_login_pass_lost']){
		$str .= '<div  class="uap-form-line-fr uap-form-links">';
			if($meta_arr['uap_login_register']){
				$pag_id = get_option('uap_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="uap-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'uap').'</a></div>';
				}
			}
			if($meta_arr['uap_login_pass_lost']){
				$pag_id = get_option('uap_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="uap-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'uap').'</a></div>';
				}
			}
		$str .= '</div>';
		}
		//>>>>
		
		$str .= $captcha;
		
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}
		$str .=    '<div class="uap-form-line-fr uap-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'uap').'" name="Submit" '.$disabled.'/>'
				 . '</div>';
		//>>>>
	break;
		
	case 'uap-login-template-3':
		//<<<< FIELDS		
		$str .= '<div class="uap-form-line-fr">'
				. '<input type="text" value="" id="' . $user_field_id . '" name="log" placeholder="'.__('Username', 'uap').'"/>'
				. '</div>'
				. '<div class="uap-form-line-fr">'
				. '<input type="password" value="" name="pwd" id="' . $password_field_id . '" placeholder="'.__('Password', 'uap').'"/>'
				. '</div>';
		//>>>>
		$str .= $sm_string;
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}

		$str .= $captcha;

		$str .=    '<div class="uap-form-line-fr uap-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'uap').'" name="Submit" '.$disabled.'/>'
				 . '</div>';
		
		$str .=    '<div class="uap-temp3-bottom">';		 
		//<<<< REMEMBER ME			
		if($meta_arr['uap_login_remember_me']){
			$str .= '<div class="uap-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="uap-form-input-remember" /><span class="uap-form-label-remember">'.__('Remember Me', 'uap').'</span> </div>';
		}
		//>>>>
		
		//<<<< ADDITIONAL LINKS
		if($meta_arr['uap_login_register'] || $meta_arr['uap_login_pass_lost']){
		$str .= '<div  class="uap-form-links">';
			if ($meta_arr['uap_login_register']){
				$pag_id = get_option('uap_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="uap-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'uap').'</a></div>';
				}
			}
			if ($meta_arr['uap_login_pass_lost']){
				$pag_id = get_option('uap_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="uap-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'uap').'</a></div>';
				}
			}
		$str .= '</div>';
		}
		//>>>>	
		$str .= '<div class="uap-clear"></div>';
		$str .= '</div>';
		
		break;
		
	case 'uap-login-template-4':
		//<<<< FIELDS		
		$str .= '<div class="uap-form-line-fr">'
				. '<i class="fa-uap fa-username-uap"></i><input type="text" id="' . $user_field_id . '" value="" name="log" placeholder="'.__('Username', 'uap').'"/>'
				. '</div>'
				. '<div class="uap-form-line-fr">'
				. '<i class="fa-uap fa-pass-uap"></i><input type="password" id="' . $password_field_id . '" value="" name="pwd" placeholder="'.__('Password', 'uap').'"/>'
				. '</div>';
		//>>>>
		$str .= $sm_string;
		//<<<< REMEMBER ME			
		if($meta_arr['uap_login_remember_me']){
			$str .= '<div class="uap-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="uap-form-input-remember" /><span class="uap-form-label-remember">'.__('Remember Me', 'uap').'</span> </div>';
		}
		//>>>>
		
		$str .= $captcha;
		
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}
		$str .=    '<div class="uap-form-line-fr uap-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'uap').'" name="Submit" '.$disabled.' />'
				 . '</div>';
				 
		
		
		//<<<< ADDITIONAL LINKS
		if($meta_arr['uap_login_register'] || $meta_arr['uap_login_pass_lost']){
		$str .= '<div  class="uap-form-links">';
			if($meta_arr['uap_login_register']){
				$pag_id = get_option('uap_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="uap-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'uap').'</a></div>';
				}
			}
			if($meta_arr['uap_login_pass_lost']){
				$pag_id = get_option('uap_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="uap-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'uap').'</a></div>';
				}
			}
		$str .= '</div>';
		}
		//>>>>
		
		break;
	case 'uap-login-template-5':	
		//<<<< FIELDS		
		$str .= '<div class="uap-form-line-fr">' . '<span class="uap-form-label-fr uap-form-label-username">'.__('Username', 'uap').':</span>'
				. '<input id="' . $user_field_id . '" type="text" value="" name="log" />'
				. '</div>'
				. '<div class="uap-form-line-fr">' . '<span class="uap-form-label-fr uap-form-label-pass">'.__('Password', 'uap').':</span>'
				. '<input type="password" id="' . $password_field_id . '" value="" name="pwd" />'
				. '</div>';
		//>>>>
		$str .= $sm_string;
		$str .=    '<div class="uap-temp5-row">';	
		$str .=    '<div class="uap-temp5-row-left">';		
		//<<<< REMEMBER ME			
		if($meta_arr['uap_login_remember_me']){
			$str .= '<div class="uap-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="uap-form-input-remember" /><span class="uap-form-label-fr uap-form-label-remember">'.__('Remember Me', 'uap').'</span> </div>';
		}
		//>>>>
		//<<<< ADDITIONAL LINKS
		if($meta_arr['uap_login_register'] || $meta_arr['uap_login_pass_lost']){
		$str .= '<div  class="uap-form-line-fr uap-form-links">';
			if($meta_arr['uap_login_register']){
				$pag_id = get_option('uap_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="uap-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'uap').'</a></div>';
				}
			}
			if($meta_arr['uap_login_pass_lost']){
				$pag_id = get_option('uap_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="uap-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'uap').'</a></div>';
				}
			}
		$str .= '</div>';
		}
		//>>>>
		$str .= '</div>';
		
		$str .= $captcha;
		
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}
		$str .=    '<div class="uap-form-line-fr uap-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'uap').'" name="Submit" '.$disabled.'/>'
				 . '</div>';
		//>>>>
		$str .= '<div class="uap-clear"></div>';
		$str .= '</div>';	
		
		break;
		case 'uap-login-template-6':	
		//<<<< FIELDS		
		$str .= '<div class="uap-form-line-fr">' . '<span class="uap-form-label-fr uap-form-label-username"><b>'.__('Username', 'uap').':</b></span>'
				. '<input type="text" id="' . $user_field_id . '" value="" name="log" />'
				. '</div>'
				. '<div class="uap-form-line-fr">' . '<span class="uap-form-label-fr uap-form-label-pass"><b>'.__('Password', 'uap').':</b></span>'
				. '<input type="password" id="' . $password_field_id . '" value="" name="pwd" />'
				. '</div>';
		//>>>>
		$str .= $sm_string;
		//<<<< ADDITIONAL LINKS
		if($meta_arr['uap_login_register'] || $meta_arr['uap_login_pass_lost']){
		$str .= '<div  class="uap-form-links">';
			if($meta_arr['uap_login_register']){
				$pag_id = get_option('uap_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="uap-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'uap').'</a></div>';
				}
			}
			if($meta_arr['uap_login_pass_lost']){
				$pag_id = get_option('uap_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="uap-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'uap').'</a></div>';
				}
			}
		$str .= '</div>';
		}
		//>>>>
		$str .=    '<div class="uap-temp6-row">';	
		$str .=    '<div class="uap-temp6-row-left">';		
		//<<<< REMEMBER ME			
		if($meta_arr['uap_login_remember_me']){
			$str .= '<div class="uap-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="uap-form-input-remember" /><span class="uap-form-label-fr uap-form-label-remember">'.__('Remember Me', 'uap').'</span> </div>';
		}
		//>>>>
		
		$str .= '</div>';
		
		$str .= $captcha;
		
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}
		$str .=    '<div class="uap-form-line-fr uap-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'uap').'" name="Submit" '.$disabled.'/>'
				 . '</div>';
		//>>>>
		$str .= '<div class="uap-clear"></div>';
		$str .= '</div>';	
		
		break;	
		
		case 'uap-login-template-7':	
		//<<<< FIELDS		
		$str .= '<div class="uap-form-line-fr">' . '<span class="uap-form-label-fr uap-form-label-username">'.__('Username', 'uap').':</span>'
				. '<input type="text" value="" id="' . $user_field_id . '" name="log" />'
				. '</div>'
				. '<div class="uap-form-line-fr">' . '<span class="uap-form-label-fr uap-form-label-pass">'.__('Password', 'uap').':</span>'
				. '<input type="password" id="' . $password_field_id . '" value="" name="pwd" />'
				. '</div>';
		//>>>>
		$str .= $sm_string;
		$str .=    '<div class="uap-temp5-row">';	
		$str .=    '<div class="uap-temp5-row-left">';		
		//<<<< REMEMBER ME			
		if($meta_arr['uap_login_remember_me']){
			$str .= '<div class="uap-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="uap-form-input-remember" /><span class="uap-form-label-fr uap-form-label-remember">'.__('Remember Me', 'uap').'</span> </div>';
		}
		//>>>>
		//<<<< ADDITIONAL LINKS
		if($meta_arr['uap_login_register'] || $meta_arr['uap_login_pass_lost']){
		$str .= '<div  class="uap-form-links">';
			if($meta_arr['uap_login_register']){
				$pag_id = get_option('uap_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="uap-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'uap').'</a></div>';
				}
			}
			if($meta_arr['uap_login_pass_lost']){
				$pag_id = get_option('uap_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="uap-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'uap').'</a></div>';
				}
			}
		$str .= '</div>';
		}
		//>>>>
		$str .= '</div>';
		
		$str .= $captcha;
		
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}
		$str .=    '<div class="uap-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'uap').'" name="Submit" '.$disabled.'/>'
				 . '</div>';
		//>>>>
		$str .= '<div class="uap-clear"></div>';
		$str .= '</div>';	
		
		break;
		
		case 'uap-login-template-8':
			//<<<< FIELDS		
			$str .= '<div class="uap-form-line-fr">' 
					. '<i class="fa-uap fa-username-uap"></i><input type="text" id="' . $user_field_id . '" value="" name="log" placeholder="' . __('Username', 'uap') . '" />'
					. '</div>'
					. '<div class="uap-form-line-fr">' 
					. '<i class="fa-uap fa-pass-uap"></i><input type="password" id="' . $password_field_id . '" value="" placeholder="'.__('Password', 'uap').'" name="pwd" />'
					. '</div>';
			//>>>>
			$str .= $sm_string;	
			//<<<< REMEMBER ME			
			if($meta_arr['uap_login_remember_me']){
				$str .= '<div class="uap-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="uap-form-input-remember" /><span class="uap-form-label-fr uap-form-label-remember">'.__('Remember Me').'</span> </div>';
			}
			//>>>>

			
			$str .= $captcha;
			
			//SUBMIT BUTTON
			$disabled = '';
			if(isset($meta_arr['preview']) && $meta_arr['preview']){
				$disabled = 'disabled';
			}
			$str .=    '<div class="uap-form-line-fr uap-form-submit">'
						. '<input type="submit" value="'.__('Log In', 'uap').'" name="Submit" '.$disabled.' class=""/>'
					 . '</div>';
			//>>>>		
			
			//<<<< ADDITIONAL LINKS
			if($meta_arr['uap_login_register'] || $meta_arr['uap_login_pass_lost']){
				$str .= '<div  class="uap-form-line-fr uap-form-links">';
					if($meta_arr['uap_login_register']){
						$pag_id = get_option('uap_general_register_default_page');
						if($pag_id!==FALSE){
							$register_page = get_permalink( $pag_id );
							if (!$register_page) $register_page = get_home_url();
							$str .= '<div class="uap-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'uap').'</a></div>';
						}
					}
					if($meta_arr['uap_login_pass_lost']){
						$pag_id = get_option('uap_general_lost_pass_page');
						if($pag_id!==FALSE){
							$lost_pass_page = get_permalink( $pag_id );		
							if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
							$str .= '<div class="uap-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'uap').'</a></div>';
						}
					}
				$str .= '</div>';
			}
			//>>>>
								
			break;	

		case 'uap-login-template-9':
			//<<<< FIELDS		
			$str .= '<div class="uap-form-line-fr">' 
					. '<i class="fa-uap fa-username-uap"></i><input type="text" id="' . $user_field_id . '" value="" name="log" placeholder="' . __('Username', 'uap') . '" />'
					. '</div>'
					. '<div class="uap-form-line-fr">' 
					. '<i class="fa-uap fa-pass-uap"></i><input type="password" id="' . $password_field_id . '" value="" placeholder="'.__('Password', 'uap').'" name="pwd" />'
					. '</div>';
			//>>>>
			$str .= $sm_string;	
			//<<<< REMEMBER ME			
			if($meta_arr['uap_login_remember_me']){
				$str .= '<div class="uap-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="uap-form-input-remember" /><span class="uap-form-label-fr uap-form-label-remember">'.__('Remember Me').'</span> </div>';
			}
			//>>>>
			
			//<<<< ADDITIONAL LINKS
			if($meta_arr['uap_login_pass_lost']){
			$str .= '<div  class="uap-form-links">';
				if($meta_arr['uap_login_pass_lost']){
					$pag_id = get_option('uap_general_lost_pass_page');
					if($pag_id!==FALSE){
						$lost_pass_page = get_permalink( $pag_id );		
						if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
						$str .= '<div class="uap-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'uap').'</a></div>';
					}
				}
			$str .= '</div>';
			$str .= '<div class="uap-clear"></div>';
			}
			//>>>>
			
			$str .= $captcha;
			
			//SUBMIT BUTTON
			$disabled = '';
			if(isset($meta_arr['preview']) && $meta_arr['preview']){
				$disabled = 'disabled';
			}
			$str .=    '<div class="uap-form-line-fr uap-form-submit">'
						. '<input type="submit" value="'.__('Log In', 'uap').'" name="Submit" '.$disabled.' class=""/>'
					 . '</div>';
			//>>>>
				if($meta_arr['uap_login_register']){
					$pag_id = get_option('uap_general_register_default_page');
					if($pag_id!==FALSE){
						$register_page = get_permalink( $pag_id );
						if (!$register_page) $register_page = get_home_url();
						$str .= '<div  class="uap-form-links">';
						$str .= '<div class="uap-form-links-reg">' . __('Dont have an account?', 'uap') . '<a href="'.$register_page.'">'.__('Sign Up', 'uap').'</a></div>';
						$str .= '</div>';
						$str .= '<div class="uap-clear"></div>';
					}
				}								
			break;

		case 'uap-login-template-10':
			//<<<< FIELDS		
			$str .= '<div class="uap-form-line-fr">'
				. '<i class="fa-uap fa-username-uap"></i><input type="text" id="' . $user_field_id . '" value="" id="" name="log" placeholder="'.__('Username', 'uap').'"/>'
				. '</div>'
				. '<div class="uap-form-line-fr">'
				. '<i class="fa-uap fa-pass-uap"></i><input type="password" id="' . $password_field_id . '" value="" id="" name="pwd" placeholder="'.__('Password', 'uap').'"/>'
				. '</div>';
			//>>>>
			$str .= $sm_string;	
			//<<<< REMEMBER ME			
			if($meta_arr['uap_login_remember_me']){
				$str .= '<div class="uap-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="uap-form-input-remember" /><span class="uap-form-label-fr uap-form-label-remember">'.__('Remember Me').'</span> </div>';
			}
			//>>>>
			
			//<<<< ADDITIONAL LINKS
			if($meta_arr['uap_login_pass_lost']){
			$str .= '<div  class="uap-form-links">';
				if($meta_arr['uap_login_pass_lost']){
					$pag_id = get_option('uap_general_lost_pass_page');
					if($pag_id!==FALSE){
						$lost_pass_page = get_permalink( $pag_id );		
						if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
						$str .= '<div class="uap-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'uap').'</a></div>';
					}
				}
			$str .= '</div>';
			$str .= '<div class="uap-clear"></div>';
			}
			//>>>>
			
			$str .= $captcha;
			
			//SUBMIT BUTTON
			$disabled = '';
			if(isset($meta_arr['preview']) && $meta_arr['preview']){
				$disabled = 'disabled';
			}
			$str .=    '<div class="uap-form-line-fr uap-form-submit">'
						. '<input type="submit" value="'.__('Log In', 'uap').'" name="Submit" '.$disabled.' class=""/>'
					 . '</div>';
			//>>>>	
				if($meta_arr['uap_login_register']){
					$pag_id = get_option('uap_general_register_default_page');
					if($pag_id!==FALSE){
						$register_page = get_permalink( $pag_id );
						if (!$register_page) $register_page = get_home_url();
						$str .= '<div  class="uap-form-links">';
						$str .= '<div class="uap-form-links-reg">' . __('Dont have an account?', 'uap') . '<a href="'.$register_page.'">'.__('Sign Up', 'uap').'</a></div>';
						$str .= '</div>';
						$str .= '<div class="uap-clear"></div>';
					}
				}							
			break;
			
	case 'uap-login-template-11':
			//<<<< FIELDS		
			$str .= '<div class="uap-form-line-fr">' 
					. '<i class="fa-uap fa-username-uap"></i><input type="text" id="' . $user_field_id . '" value="" name="log" placeholder="' . __('Username', 'uap') . '" />'
					. '</div>'
					. '<div class="uap-form-line-fr">' 
					. '<i class="fa-uap fa-pass-uap"></i><input type="password" id="' . $password_field_id . '" value="" placeholder="'.__('Password', 'uap').'" name="pwd" />'
					. '</div>';
			//>>>>
			$str .= $sm_string;	
			//<<<< REMEMBER ME			
			if($meta_arr['uap_login_remember_me']){
				$str .= '<div class="uap-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="uap-form-input-remember" /><span class="uap-form-label-fr uap-form-label-remember">'.__('Remember Me').'</span> </div>';
			}
			//>>>>
			
			//<<<< ADDITIONAL LINKS
			if($meta_arr['uap_login_pass_lost']){
			$str .= '<div  class="uap-form-links">';
				if($meta_arr['uap_login_pass_lost']){
					$pag_id = get_option('uap_general_lost_pass_page');
					if($pag_id!==FALSE){
						$lost_pass_page = get_permalink( $pag_id );		
						if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
						$str .= '<div class="uap-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'uap').'</a></div>';
					}
				}
			$str .= '</div>';
			$str .= '<div class="uap-clear"></div>';
			}
			//>>>>
			
			$str .= $captcha;
			
			//SUBMIT BUTTON
			$disabled = '';
			if(isset($meta_arr['preview']) && $meta_arr['preview']){
				$disabled = 'disabled';
			}
			$str .=    '<div class="uap-form-line-fr uap-form-submit">'
						. '<input type="submit" value="'.__('Log In', 'uap').'" name="Submit" '.$disabled.' class=""/>'
					 . '</div>';
			//>>>>
				if($meta_arr['uap_login_register']){
					$pag_id = get_option('uap_general_register_default_page');
					if($pag_id!==FALSE){
						$register_page = get_permalink( $pag_id );
						if (!$register_page) $register_page = get_home_url();
						$str .= '<div  class="uap-form-links">';
						$str .= '<div class="uap-form-links-reg">' . __('Dont have an account?', 'uap') . '<a href="'.$register_page.'">'.__('Sign Up', 'uap').'</a></div>';
						$str .= '</div>';
						$str .= '<div class="uap-clear"></div>';
					}
				}								
			break;
			
	case 'uap-login-template-12':
			//<<<< FIELDS		
			$str .= '<div class="uap-form-line-fr">' 
					. '<i class="fa-uap fa-username-uap"></i><input type="text" id="' . $user_field_id . '" value="" name="log" placeholder="' . __('Username', 'uap') . '" />'
					. '</div>'
					. '<div class="uap-form-line-fr">' 
					. '<i class="fa-uap fa-pass-uap"></i><input type="password" id="' . $password_field_id . '" value="" placeholder="'.__('Password', 'uap').'" name="pwd" />'
					. '</div>';
			//>>>>
			$str .= $sm_string;	
			//<<<< REMEMBER ME			
			if($meta_arr['uap_login_remember_me']){
				$str .= '<div class="uap-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="uap-form-input-remember" /><span class="uap-form-label-fr uap-form-label-remember">'.__('Remember Me').'</span> </div>';
			}
			//>>>>
			
			//<<<< ADDITIONAL LINKS
			if($meta_arr['uap_login_pass_lost']){
			$str .= '<div  class="uap-form-links">';
				if($meta_arr['uap_login_pass_lost']){
					$pag_id = get_option('uap_general_lost_pass_page');
					if($pag_id!==FALSE){
						$lost_pass_page = get_permalink( $pag_id );		
						if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
						$str .= '<div class="uap-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'uap').'</a></div>';
					}
				}
			$str .= '</div>';
			$str .= '<div class="uap-clear"></div>';
			}
			//>>>>
			
			$str .= $captcha;
			
			//SUBMIT BUTTON
			$disabled = '';
			if(isset($meta_arr['preview']) && $meta_arr['preview']){
				$disabled = 'disabled';
			}
			$str .=    '<div class="uap-form-line-fr uap-form-submit">'
						. '<input type="submit" value="'.__('Log In', 'uap').'" name="Submit" '.$disabled.' class=""/>'
					 . '</div>';
			//>>>>
				if($meta_arr['uap_login_register']){
					$pag_id = get_option('uap_general_register_default_page');
					if($pag_id!==FALSE){
						$register_page = get_permalink( $pag_id );
						if (!$register_page) $register_page = get_home_url();
						$str .= '<div  class="uap-form-links">';
						$str .= '<div class="uap-form-links-reg">' . __('Dont have an account?', 'uap') . '<a href="'.$register_page.'">'.__('Sign Up', 'uap').'</a></div>';
						$str .= '</div>';
						$str .= '<div class="uap-clear"></div>';
					}
				}								
			break;
	
	case 'uap-login-template-13':
		//<<<< FIELDS		
		$str .= '<div class="uap-form-line-fr">' . '<span class="uap-form-label-fr uap-form-label-username">'.__('Username', 'uap').':</span>'
				. '<input id="' . $user_field_id . '" type="text" value="" name="log" />'
				. '</div>'
				. '<div class="uap-form-line-fr" style="margin-bottom:30px;">' . '<span class="uap-form-label-fr uap-form-label-pass">'.__('Password', 'uap').':</span>'
				. '<input type="password" id="' . $password_field_id . '" value="" name="pwd" />'
				. '</div>';
		//>>>>
		
				
		//<<<< REMEMBER ME			
		if($meta_arr['uap_login_remember_me']){
			$str .=    '<div class="uap-temp5-row">';	
			$str .= '<div class="uap-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="uap-form-input-remember" /><span class="uap-form-label-fr uap-form-label-remember">'.__('Remember Me', 'uap').'</span> </div>';
			$str .= '</div>';
		}
		//>>>>
		$str .=    '<div class="uap-temp5-row">';
			
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}
		$str .= '<div class="uap-temp5-row-left">';
		$str .=    '<div class="uap-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'uap').'" name="Submit" '.$disabled.'/>'
				 . '</div>';
		$str .= '</div>';		 
		//>>>>
		if($meta_arr['uap_login_register']){
			$str .= '<div class="uap-temp5-row-right">';
				$pag_id = get_option('uap_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="uap-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'uap').'</a></div>';
				}
			$str .= '</div>';	
			}
		$str .= '<div class="uap-clear"></div>';
		$str .= '</div>';	
		
		//<<<< ADDITIONAL LINKS
			
		if($meta_arr['uap_login_pass_lost']){
			$str .= '<div class="uap-temp5-row">';
			$pag_id = get_option('uap_general_lost_pass_page');
			if($pag_id!==FALSE){
				$lost_pass_page = get_permalink( $pag_id );		
				if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
				$str .= '<div class="uap-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'uap').'</a></div>';
			}
			$str .= '</div>';
		}
	
	
		
		$str .= $captcha;
		$str .= $sm_string;
		
		
		break;
												
	default:			
		//<<<< FIELDS		
		$str .= '<div class="uap-form-line-fr">' . '<span class="uap-form-label-fr uap-form-label-username">'.__('Username', 'uap').':</span>'
				. '<input type="text" value="" name="log" id="' . $user_field_id . '" />'
				. '</div>'
				. '<div class="uap-form-line-fr">' . '<span class="uap-form-label-fr uap-form-label-pass">'.__('Password', 'uap').':</span>'
				. '<input type="password" value="" name="pwd" id="' . $password_field_id . '" />'
				. '</div>';
		//>>>>
		$str .= $sm_string;	
		//<<<< REMEMBER ME			
		if($meta_arr['uap_login_remember_me']){
			$str .= '<div class="uap-form-line-fr uap-remember-wrapper"><input type="checkbox" value="forever" name="rememberme" class="uap-form-input-remember" /><span class="uap-form-label-fr uap-form-label-remember">'.__('Remember Me').'</span> </div>';
		}
		//>>>>
		
		//<<<< ADDITIONAL LINKS
		if($meta_arr['uap_login_register'] || $meta_arr['uap_login_pass_lost']){
		$str .= '<div  class="uap-form-line-fr uap-form-links">';
			if($meta_arr['uap_login_register']){
				$pag_id = get_option('uap_general_register_default_page');
				if($pag_id!==FALSE){
					$register_page = get_permalink( $pag_id );
					if (!$register_page) $register_page = get_home_url();
					$str .= '<div class="uap-form-links-reg"><a href="'.$register_page.'">'.__('Register', 'uap').'</a></div>';
				}
			}
			if($meta_arr['uap_login_pass_lost']){
				$pag_id = get_option('uap_general_lost_pass_page');
				if($pag_id!==FALSE){
					$lost_pass_page = get_permalink( $pag_id );		
					if (!$lost_pass_page) $lost_pass_page = get_home_url(); 
					$str .= '<div class="uap-form-links-pass"><a href="'.$lost_pass_page.'">'.__('Lost your password?', 'uap').'</a></div>';
				}
			}
		$str .= '</div>';
		}
		//>>>>
		
		$str .= $captcha;
		
		//SUBMIT BUTTON
		$disabled = '';
		if(isset($meta_arr['preview']) && $meta_arr['preview']){
			$disabled = 'disabled';
		}
		$str .=    '<div class="uap-form-line-fr uap-form-submit">'
					. '<input type="submit" value="'.__('Log In', 'uap').'" name="Submit" '.$disabled.' class="button button-primary button-large"/>'
				 . '</div>';
		//>>>>
		break;
		
		
		
	}
	
	$str .=   '</form>'
			.'</div>';
	$err_msg = __('Please complete all require fields!', 'uap');
	$custom_err_msg = get_option('uap_login_error_ajax'); 
	if ($custom_err_msg){
		$err_msg = $custom_err_msg;
	}		
	/// JAVASCRIPT 
	$str .= "<script>
		jQuery(document).ready(
			function(){
				jQuery('#$user_field_id').on('blur', function(){
					uap_check_login_field('log', '$err_msg');
				});	
				jQuery('#$password_field_id').on('blur', function(){
					uap_check_login_field('pwd', '$err_msg');
				});		
				jQuery('#uap_login_form').on('submit', function(e){
					e.preventDefault();
					var u = jQuery('#uap_login_form [name=log]').val();
					var p = jQuery('#uap_login_form [name=pwd]').val();
					if (u!='' && p!=''){
						jQuery('#uap_login_form').unbind('submit').submit();
					} else {
						uap_check_login_field('log', '$err_msg');
						uap_check_login_field('pwd', '$err_msg');
						return FALSE;
					}
				});		
			}
		);
	</script>";	
			
	return $str;
}

