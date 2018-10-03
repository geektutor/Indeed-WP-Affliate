<?php 
if (!class_exists('Uap_Listing_Affiliates')){
	class Uap_Listing_Affiliates{
		private $args = array();
		private $total_pages = 0;
		private $users = array();
		private $div_parent_id = '';
		private $li_width = '';
		private $user_fields = array();
		private $total_users;
		private $single_item_template = '';
		private $general_settings = array();
		private $link_user_page = '';
		private $fields_label = array();
		private $permalink_type = '';
		private $currency = '';
		
		public function __construct($input=array()){
			/*
			 * @param array
			 * @return none
			 */
			if (empty($input)){
				return;
			} else {
				global $indeed_db;
				$this->args = $input;
				$this->general_settings = $indeed_db->return_settings_from_wp_option('top_affiliate_list');
			}
		}
		
		public function run(){
			/*
			 * @param none
			 * @return string
			 */
			if (empty($this->args)){				
				return;
			}
			
			$this->currency = get_option('uap_currency');
			$output = '';
			$html = '';
			$js = '';
			$css = '';
			$js_after_html = '';
			$pagination = '';
			if (empty($this->args['entries_per_page'])) $this->args['entries_per_page'] = 25;
			
			////// FILTER BY RANKS
			$ranks_list = array();
			if (!empty($this->args['filter_by_rank']) && !empty($this->args['ranks_in'])){
				if (strpos($this->args['ranks_in'], ',')!==FALSE){
					$ranks_list = explode(',', $this->args['ranks_in']);
				} else {
					$ranks_list = array($this->args['ranks_in']);
				}
			}
			
			////////// ORDER
			$order_by = $this->args['order_by'];
			$order_type = $this->args['order_type'];
			
			//////////TOTAL USERS
			$this->total_users = $this->get_total_affiliates($ranks_list);
			if ($this->total_users>$this->args['num_of_entries']){
				$this->total_users = $this->args['num_of_entries'];
			}
			
			//limit && offset
			if (empty($this->args['slider_set'])){
				//// NO SLIDER + PAGINATION
				if (!empty($this->args['current_page'])){
					$current_page = $this->args['current_page'];
					$offset = ( $current_page - 1 ) * $this->args['entries_per_page']; //start from
				} else {
					$offset = 0;
				}
				$limit = $this->args['entries_per_page'];
				if ($offset + $limit>$this->total_users){
					$limit = $this->total_users - $offset;
				}			
			} else {
				////SLIDER
				$offset = 0;
				$limit = $this->args['num_of_entries'];				
			}
			
			///GETTING USER IDS
			$user_ids = $this->get_affiliates($order_by, $order_type, (int)$offset, (int)$limit, $ranks_list);
			if (empty($user_ids)){
				return;//no users available
			}
			
			////SET USERS DATA
			$this->set_users_data($user_ids);
			
			$this->single_item_template = UAP_PATH .'public/listing_users/themes/' . $this->args['theme'] . "/index.php";
			
			///SET FIELDS LABEL
			$this->set_fields_label();
			
			if (!empty($this->users) && file_exists($this->single_item_template)){
				$html .= $this->create_the_html();
				$js .= $this->create_the_js();
				$css .= $this->create_the_css();
				$js_after_html .= $this->create_the_js_after_html();
			}				
			
			if (empty($this->args['slider_set']) && $this->args['entries_per_page']<$this->total_users){
				///adding pagination
				$pagination .= $this->print_pagination();
			}
			
			$output = $css . $js . $pagination . $html . $js_after_html;
			return $output;
		}
		
		private function set_users_data($user_ids){
			/*
			 * @param array
			 * @return none
			 */
			$this->user_fields = explode(',', $this->args['user_fields']);
			if ($this->args['order_by']=='random'){
				shuffle($user_ids);
			}
			
			foreach ($user_ids as $k=>$id){
				foreach ($this->user_fields as $field){
					if (empty($users[$id][$field])){
						$user_data = get_userdata($id);
						if (isset($user_data->$field)){
							$this->users[$id][$field] = $user_data->$field;
						} else {
							@$this->users[$id][$field] = get_user_meta($id, $field, TRUE);
						}
					}
				}
				$this->users[$id]['earnings'] = $this->get_earnings_by_wpuid($id);
				$this->users[$id]['visits'] = $this->get_visits_by_wpuid($id);
				$this->users[$id]['referrals'] = $this->get_referrals_by_wpuid($id);
			}
		}
		
		private function get_earnings_by_wpuid($uid=0){
			/*
			 * @param int
			 * @return float
			 */
			 if ($uid){
			 	 global $wpdb;
				 $a = $wpdb->prefix . 'uap_affiliates';
			 	 $r = $wpdb->prefix . 'uap_referrals';
				 $q = "SELECT SUM(r.amount) as amount
							FROM 
							$r r 
							INNER JOIN $a a 
								ON r.affiliate_id=a.id
							WHERE 1=1
							AND a.uid=$uid
				 ";
				 $data = $wpdb->get_row($q);
				 if ($data && isset($data->amount)){
				 	return $data->amount;	
				 }								 				 
			 }
			 return 0;
		}
		
		private function get_visits_by_wpuid($uid=0){
			/*
			 * @param int
			 * @return float
			 */
			 if ($uid){
			 	 global $wpdb;
				 $a = $wpdb->prefix . 'uap_affiliates';
			 	 $v = $wpdb->prefix . 'uap_visits';
				 $q = "SELECT COUNT(v.id) as visits
							FROM 
							$v v 
							INNER JOIN $a a 
								ON v.affiliate_id=a.id
							WHERE 1=1
							AND a.uid=$uid
				 ";
				 $data = $wpdb->get_row($q);
				 if ($data && isset($data->visits)){
				 	return $data->visits;	
				 }								 				 
			 }
			 return 0;
		}		

		private function get_referrals_by_wpuid($uid=0){
			/*
			 * @param int
			 * @return float
			 */
			 if ($uid){
			 	 global $wpdb;
				 $a = $wpdb->prefix . 'uap_affiliates';
			 	 $r = $wpdb->prefix . 'uap_referrals';
				 $q = "SELECT COUNT(r.id) as referrals
							FROM 
							$r r 
							INNER JOIN $a a 
								ON r.affiliate_id=a.id
							WHERE 1=1
							AND a.uid=$uid
				 ";
				 $data = $wpdb->get_row($q);
				 if ($data && isset($data->referrals)){
				 	return $data->referrals;	
				 }								 				 
			 }
			 return 0;
		}	
				
		private function set_fields_label(){
			/*
			 * @param none
			 * @return none
			 */
			global $indeed_db;
			$fields_data = $indeed_db->register_get_custom_fields();
			foreach ($this->user_fields as $field){
				$key = uap_array_value_exists($fields_data, $field, 'name');
				if ($key!==FALSE && !empty($fields_data[$key]) && !empty($fields_data[$key]['label'])){
					$this->fields_label[$field] = $fields_data[$key]['label'];
				}				
			}
		}
		
		
		private function get_total_affiliates($ranks=array()){
			/*
			 * @param array
			 * @return int
			 */
			global $wpdb;
			$ranks_str = (empty($ranks)) ? '' : implode(',', $ranks);
			$a = $wpdb->prefix . 'uap_affiliates';
			$u = $wpdb->base_prefix . 'users';
			$q = "SELECT COUNT(a.id) as v FROM 
					$a a 
					INNER JOIN $u u
					ON a.uid=u.ID
					WHERE 1=1";
			if ($ranks){
				$q .= " AND a.rank_id IN($ranks_str)";
			}
			$data = $wpdb->get_row($q);
			if ($data && isset($data->v)){
				return $data->v;
			}
			return 0;
		}
		
		private function get_affiliates($order_by='', $order_type='', $offset=0, $limit=5, $ranks_str=''){
			/*
			 * @param string, string, int, int, string
			 * @return array
			 */
			 global $wpdb;
			 $a = $wpdb->prefix . 'uap_affiliates';
			 $v = $wpdb->prefix . 'uap_visits';
			 $r = $wpdb->prefix . 'uap_referrals';
			 $u = $wpdb->base_prefix . 'users';	
			 $um = $wpdb->base_prefix . 'usermeta';
			 $ranks = (empty($ranks_str)) ? '' : implode(',', $ranks_str);
	 		 $q = '';
			 
			 switch ($order_by){
			 	case 'visits':
					$q = "SELECT COUNT(v.id) as visits_count, 
										a.uid as id
										FROM 
										$a a  
										LEFT JOIN $v v
										ON v.affiliate_id=a.id
										WHERE 1=1";
					if ($ranks){
						$q .= " AND rank_id IN ($ranks)";	
					}
					$q .= " GROUP BY a.id
						    ORDER BY visits_count $order_type
						    LIMIT $limit OFFSET $offset";
					$data = $wpdb->get_results($q);		
					break;
				case 'referrals':
					$q = "SELECT COUNT(r.id) as referrals_count, 
										a.uid as id
										FROM 
										$a a 
										LEFT JOIN $r r 
										ON r.affiliate_id=a.id
										WHERE 1=1";
					if ($ranks){
						$q .= " AND rank_id IN ($ranks)";	
					}					
					$q .= " GROUP BY a.id
							ORDER BY referrals_count $order_type
							LIMIT $limit OFFSET $offset
					;";
					$data = $wpdb->get_results($q);												
					break;
				case 'earnings':
					$q = "SELECT SUM(r.amount) as referrals_amount, 
										a.uid as id
										FROM 
										$a a 
										LEFT JOIN $r r 
										ON r.affiliate_id=a.id
										WHERE 1=1";
					if ($ranks){
						$q .= " AND rank_id IN ($ranks)";	
					}											
					$q .= " GROUP BY a.id
										ORDER BY referrals_amount $order_type
										LIMIT $limit OFFSET $offset										
					;";				
					break;
				case 'user_registered':
				case 'user_login':
				case 'user_email':
					if ($order_type=='random'){
						$order_type = '';
					}
					$q = "SELECT u.ID as id
							FROM $u u 
							INNER JOIN $a a ON a.uid=u.ID 
							WHERE 1=1";
					if ($ranks){
						$q .= " AND a.rank_id IN ($ranks)";	
					}			
					if ($order_type && $order_by){
						$q .= " ORDER BY u." . $order_by . " " . $order_type;		
					}
					$q .= " LIMIT $limit OFFSET $offset";
					break;		
				case 'random':
					if ($order_type=='random'){
						$order_type = '';
					}
					$q = "SELECT a.id 
							FROM $u u 
							INNER JOIN $a a ON a.uid=u.ID 
							WHERE 1=1";
					if ($ranks){
						$q .= " AND a.rank_id IN ($ranks)";	
					}			
					if ($order_type && $order_by){
						$q .= " ORDER BY RAND() ";		
					}
					$q .= " LIMIT $limit OFFSET $offset";					
					break;							
			 }
			 if ($q){
				 $data = $wpdb->get_results($q);			 	
			 }
			 
			 $array = array();
			 if (!empty($data)){
				foreach ($data as $object){
					if (isset($object->id)){
						$array[] = $object->id;						
					}
				}			 		 	
			 }
			 return $array;
		}

		
		private function create_the_js_after_html(){
			/*
			 * @param
			 * @return string
			 */
			$str = '';
			if (!empty($this->args['slider_set'])){
				$total_pages = count($this->users) / $this->args['items_per_slide'];
					
				if ($total_pages>1){
					$navigation = (empty($this->args['nav_button'])) ? 'false' : 'true';
					$bullets = (empty($this->args['bullets'])) ? 'false' : 'true';
					if (empty($this->args['autoplay'])){
						$autoplay = 'false';
						$autoplayTimeout = 5000;
					} else {
						$autoplay = 'true';
						$autoplayTimeout = $this->args['speed'];
					}
					$autoheight = (empty($this->args['autoheight'])) ? 'false' : 'true';
					$stop_hover = (empty($this->args['stop_hover'])) ? 'false' : 'true';
					$loop = (empty($this->args['loop'])) ? 'false' : 'true';
					$responsive = (empty($this->args['responsive'])) ? 'false' : 'true';
					$lazy_load = (empty($this->args['lazy_load'])) ? 'false' : 'true';
					$animation_in = (($this->args['animation_in'])=='none') ? 'false' : "'{$this->args['animation_in']}'";
					$animation_out = (($this->args['animation_out'])=='none') ? 'false' : "'{$this->args['animation_out']}'";
					$slide_pagination_speed = $this->args['pagination_speed'];
						
					$str .= "<script>
												jQuery(document).ready(function() {
													var owl = jQuery('#" . $this->div_parent_id . "');
													owl.owluapCarousel({
															items : 1,
															mouseDrag: true,
															touchDrag: true,
													
															autoHeight: $autoheight,
													
															animateOut: $animation_out,
															animateIn: $animation_in,
													
															lazyLoad : $lazy_load,
															loop: $loop,
													
															autoplay : $autoplay,
															autoplayTimeout: $autoplayTimeout,
															autoplayHoverPause: $stop_hover,
															autoplaySpeed: $slide_pagination_speed,
													
															nav : $navigation,
															navSpeed : $slide_pagination_speed,
															navText: [ '', '' ],
													
															dots: $bullets,
															dotsSpeed : $slide_pagination_speed,
													
															responsiveClass: $responsive,
															responsive:{
																0:{
																	nav:false
																},
																450:{
																	nav : $navigation
																}
															}
													});	
												});
					</script>";
				}
			}
			return $str;
		}
	
		private function create_the_css(){
			/*
			 * @param none
			 * @return string
			 */
			//add the themes and the rest of CSS here...
			$str = '';			
			if (!empty($this->args['slider_set']) && !defined('UAP_SLIDER_LOAD_CSS')){
				///// SLIDER CSS
				$str .= '<link rel="stylesheet" type="text/css" href="' . UAP_URL . 'public/listing_users/assets/css/owl.carousel.css">';
				$str .= '<link rel="stylesheet" type="text/css" href="' . UAP_URL . 'public/listing_users/assets/css/owl.theme.css">';
				$str .= '<link rel="stylesheet" type="text/css" href="' . UAP_URL . 'public/listing_users/assets/css/owl.transitions.css">';
				define('UAP_SLIDER_LOAD_CSS', TRUE);
			}
			if (!empty($this->args['theme'])){
				///// THEME
				$str .= '<link rel="stylesheet" type="text/css" href="' . UAP_URL . 'public/listing_users/themes/' . $this->args['theme'] . '/style.css">';
			}
			if (!defined('UAP_COLOR_CSS_FILE')){
				////// COLOR EXTERNAL CSS
				$str .= '<link rel="stylesheet" type="text/css" href="' . UAP_URL . 'public/listing_users/assets/css/layouts.css">';
				define('UAP_COLOR_CSS_FILE', TRUE);
			}			
			$str .= '<style>';
			///// SLIDER COLORS
			if (!empty($this->args['color_scheme']) && !empty($this->args['slider_set'])){
				$str .= '
							.style_'.$this->args['color_scheme'].' .owl-uap-theme .owl-uap-dots .owl-uap-dot.active span, .style_'.$this->args['color_scheme'].'  .owl-uap-theme .owl-uap-dots .owl-uap-dot:hover span { background: #'.$this->args['color_scheme'].' !important; }
							.style_'.$this->args['color_scheme'].' .pag-theme1 .owl-uap-theme .owl-uap-nav [class*="owl-uap-"]:hover{ background-color: #'.$this->args['color_scheme'].'; }
							.style_'.$this->args['color_scheme'].' .pag-theme2 .owl-uap-theme .owl-uap-nav [class*="owl-uap-"]:hover{ color: #'.$this->args['color_scheme'].'; }
							.style_'.$this->args['color_scheme'].' .pag-theme3 .owl-uap-theme .owl-uap-nav [class*="owl-uap-"]:hover{ background-color: #'.$this->args['color_scheme'].';}
						';
			}		
			////// ALIGN CENTER
			if (!empty($this->args['align_center'])) {
				$str .= '#'.$this->div_parent_id.' ul{text-align: center;}';
			}
			///// CUSTOM CSS
			if (!empty($this->general_settings['uap_listing_users_custom_css'])){
				$str .= stripslashes($this->general_settings['uap_listing_users_custom_css']);
			}
			//// RESPONSIVE 
			if (!empty($this->general_settings['uap_listing_users_responsive_small'])){
				$width = 100 / $this->general_settings['uap_listing_users_responsive_small'];
				$str .= '
						@media only screen and (max-width: 479px){
							#' . $this->div_parent_id . ' ul li{
								width: calc(' . $width . '% - 1px) !important;
							}
						}
				';				
			}
			if (!empty($this->general_settings['uap_listing_users_responsive_medium'])){
				$width = 100 / $this->general_settings['uap_listing_users_responsive_medium'];
				$str .= '
						@media only screen and (min-width: 480px) and (max-width: 767px){
							#' . $this->div_parent_id . ' ul li{
								width: calc(' . $width . '% - 1px) !important;
							}
						}
				';				
			}
			if (!empty($this->general_settings['uap_listing_users_responsive_large'])){
				$width = 100 / $this->general_settings['uap_listing_users_responsive_large'];
				$str .= '
						@media only screen and (min-width: 768px) and (max-width: 959px){
							#' . $this->div_parent_id . ' ul li{
								width: calc(' . $width . '% - 1px) !important;
							}
						}
				';				
			}
			$str .= '</style>';
			return $str;		
		}	

		private function create_the_js(){
			/*
			 * @param
			 * @return string
			 */
			$str = '';
			if (!empty($this->args['slider_set']) && !defined('UAP_SLIDER_LOAD_JS')){
				$str .= '<script src="' . UAP_URL . 'public/listing_users/assets/js/owl.carousel.js" ></script>';
				define('UAP_SLIDER_LOAD_JS', TRUE);
			}				
			return $str;
		}
		
		private function print_pagination(){
			/*
			 * @param none
			 * @return string
			 */
			$str = '';
			$current_page = (empty($this->args['current_page'])) ? 1 : $this->args['current_page'];
			$this->total_pages = ceil($this->total_users/$this->args['entries_per_page']);
			$url = UAP_PROTOCOL . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; /// $_SERVER['SERVER_NAME']
			$str = '';
			
			if ($this->total_pages<=5){
				//show all the links
				for ($i=1; $i<=$this->total_pages; $i++){
					$show_links[] = $i;
				}
			} else {
				// we want to show only first, last, and the first neighbors of current page
				$show_links = array(1, $this->total_pages, $current_page, $current_page+1, $current_page-1);
			}
			
			for ($i=1; $i<=$this->total_pages; $i++){
				if (in_array($i, $show_links)){
					$href = (defined('IS_PREVIEW')) ? '#' : add_query_arg('uapUserList_p', $i, $url);
					$selected = ($current_page==$i) ? '-selected' : '';
					$str .= "<a href='$href' class='uap-user-list-pagination-item" . $selected . "'>" . $i . '</a>';		
					$dots_on = TRUE;
				} else {
					if (!empty($dots_on)){
						$str .= '<span class="uap-user-list-pagination-item-break">...</span>';
						$dots_on = FALSE;
					}
				}
			}
			/// Back link
			if ($current_page>1){
				$prev_page = $current_page - 1;
				$href = (defined('IS_PREVIEW')) ? '#' : add_query_arg('uapUserList_p', $prev_page, $url);
				$str = "<a href='" . $href . "' class='uap-user-list-pagination-item'> < </a>" . $str;
			}
			///Forward link
			if ($current_page<$this->total_pages){
				$next_page = $current_page + 1;
				$href = (defined('IS_PREVIEW')) ? '#' : add_query_arg('uapUserList_p', $next_page, $url);
				$str = $str . "<a href='" . $href . "' class='uap-user-list-pagination-item'> > </a>";
			}
						
			//Wrappers
			$str = "<div class='uap-user-list-pagination'>" . $str . "</div><div class='uap-clear'></div>";
			return $str;
		}

		private function create_the_html(){
			/*
			 * @param
			 * @return string
			 */
			$str = '';
			$total_items = count($this->users);

			$items_per_slide = (empty($this->args['slider_set'])) ? $total_items : $this->args['items_per_slide'];
			
			include $this->single_item_template;
			if (empty($list_item_template)){
				return '';
			}
			
			$this->li_width = 'calc(' . 100/$this->args['columns'] . '% - 1px)';
			$i = 1;
			$breaker_div = 1;
			$new_div = 1;
			$color_class = (empty($this->args['color_scheme'])) ? 'style_0a9fd8' : 'style_' . $this->args['color_scheme'];
			$parent_class = (empty($this->args['slider_set'])) ? 'uap-content-user-list' : 'uap-carousel-view';//carousel_view
			$num = rand(1, 10000);
			$this->div_parent_id = 'indeed_carousel_view_widget_' . $num;
			$arrow_wrapp_id = 'wrapp_arrows_widget_' . $num;
			$ul_id = 'uap_list_users_ul_' . rand(1, 10000);
				
			///// WRAPPERS
			$extra_class = (empty($this->args['pagination_theme'])) ? '' : $this->args['pagination_theme'];
			$str .= "<div class='' id='uap_public_list_users_" . rand(1, 10000) . "'>";
			$str .= "<div class='$color_class'>";
			$str .= "<div class='" . $this->args['theme'] . " " . $extra_class . "'>";
			$str .= "<div class='uap-wrapp-list-users'>";
			$str .= "<div class='$parent_class' id='$this->div_parent_id' >";
			
			////// ITEMS
			foreach ($this->users as $uid=>$arr){
				if (!empty($new_div)){
					$div_id = $ul_id . '_' . $breaker_div;
					$str .= "<ul id='$div_id' class=''>"; /////ADDING THE UL
				}
			
				$str .= $this->print_item($uid, $list_item_template);///// PRINT SINGLE ITEM
			
				if ($i % $items_per_slide==0 || $i==$total_items){
					$breaker_div++;
					$new_div = 1;
					$str .= "<div class='uap-clear'></div></ul>";
				} else {
					$new_div = 0;
				}
				$i++;
			}
				
			///// CLOSE WRAPPERS
			$str .= '</div>'; /// end of $parent_class
			$str .= '</div>'; /// end of uap-wrapp-list-users
			$str .= '</div>'; /// end of $args['theme'] . " " . $args['pagination_theme']
			$str .= '</div>'; /// end of $color_class
			$str .= '</div>'; //// end of uap_public_list_users_
			
			return $str;
		}

		private function print_item($uid, $template){
			/*
			 * SINGLE ITEM
			 * @param int, string
			 * @return string
			 */
			$fields = $this->user_fields;
			
			$str = '';
			$str .= "<li style='width: $this->li_width' >";
			
			//AVATAR
			$this->users[$uid]['uap_avatar'] = uap_get_avatar_for_uid($uid);
			
			///STANDARD FIELDS
			$standard_fields = array(
										"user_login" => "UAP_USERNAME",
										"first_name" => "UAP_FIRST_NAME",
										"last_name" => "UAP_LAST_NAME",
										"user_email" => "UAP_EMAIL",
										"uap_avatar" => "UAP_AVATAR",
 			);

			foreach ($standard_fields as $k=>$v){
				$data = '';
				if (in_array($k, $fields)){
					$data = $this->users[$uid][$k];
				}
				$template = str_replace($v, $data, $template);
				$key = array_search($k, $fields);
				if ($key!==FALSE){
					unset($fields[$key]);					
				}
			}


			///AFFILIATES SPECIAL DATA EARNINGS, REFERRALS AND VISITS
			if ($key=array_search('earnings', $fields)){
				unset($fields[$key]);
				$html_str = '<div class="uap-top-counts">' .  $this->currency .$this->users[$uid]['earnings'] . ' ' .  __(' Earnings ', 'uap') .'</div>';
				$template = str_replace('EARNINGS', $html_str, $template);
			} else {
				$template = str_replace('EARNINGS', '', $template);
			}
			if ($key=array_search('referrals', $fields)){
				unset($fields[$key]);
				$html_str = '<div class="uap-top-counts">' . $this->users[$uid]['referrals'] .__(' Referrals', 'uap') . '</div>';
				$template = str_replace('REFERRALS', $html_str, $template);
			} else {
				$template = str_replace('REFERRALS', '', $template);
			}
			if ($key=array_search('visits', $fields)){
				unset($fields[$key]);
				$html_str = '<div class="uap-top-counts">' . $this->users[$uid]['visits'] . __(' Visits', 'uap') .'</div>';
				$template = str_replace('VISITS', $html_str, $template);
			} else {
				$template = str_replace('VISITS', '', $template);
			}			
			///AFFILIATES SPECIAL DATA EARNINGS, REFERRALS AND VISITS
			

			/// SOME EXTRA FIELDS			
			$extra_fields = '';
			if ($fields){				
				foreach ($fields as $value){
					$extra_fields_str = '';
					if (!empty($this->users[$uid][$value])){
						if (!empty($this->args['include_fields_label']) && !empty($this->fields_label[$value])){
							$extra_fields_str .= '<span class="uap-user-list-label">' . $this->fields_label[$value] . ' </span>';
							$extra_fields_str .= '<span class="uap-user-list-label-result">';
						}else{
							$extra_fields_str .= '<span class="uap-user-list-result">';
						}
						if (is_array($this->users[$uid][$value])){
							$extra_fields_str .= implode(',', $this->users[$uid][$value]);
						} else {
							$extra_fields_str .= $this->users[$uid][$value];
						}
						$extra_fields_str .= '</span>';
						$extra_fields_str .= '<div class="uap-clear"></div>';
						if (!empty($extra_fields_str)){
							$extra_fields .= '<div class="member-extra-single-field">' . $extra_fields_str . '</div>';
						}					
					}					
				}
			}
			$template = str_replace('UAP_EXTRA_FIELDS', $extra_fields, $template);
			
			$str .= $template;
			$str .= '</li>';
			return $str;
		}
		
		
	}
}