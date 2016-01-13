<?php
/*
Plugin Name: InfusedWoo Basic
Plugin URI: http://woo.infusedaddons.com
Description: Integrates WooCommerce with Infusionsoft CRM. You need an Infusionsoft account to make this plugin work.
Version: 1.0
Author: Mark Joseph
Author URI: http://www.infusedmj.com
*/

/**
 * Plugin updates
 * */
 
add_action('init', 'ia_woocommerce_autoupdate');  
function ia_woocommerce_autoupdate()  
{  
    require_once ('autoupdate.php');  
    $plugin_current_version = '1.0';  
    $plugin_remote_path = 'http://infusedmj.com/plugins/ia_woocommerce_update_checker.php';  
    $plugin_slug = plugin_basename(__FILE__);  
    new ia_auto_update ($plugin_current_version, $plugin_remote_path, $plugin_slug);  
}   

 
/**
 * Include Infusionsoft SDK Libraries
 * */ 
 

if( !class_exists("iaSDK")) include 'sdk/iasdk.php';
add_action('plugins_loaded', 'ia_woocommerce_init', 0);

function ia_woocommerce_init() {

	if (!class_exists('WC_Integration')) return;
	
	define('IA_WOOCOMMERCE_DIR', WP_PLUGIN_DIR . "/" . plugin_basename( dirname(__FILE__) ) . '/');

	/**
	 * Integration Class
	 **/
	class IA_Woocommerce extends WC_Integration {
	
		public function __construct() { 
			$this->id					= 'infusionsoft';
	        $this->method_title			= __( 'Infusionsoft', 'woocommerce' );
	        $this->method_description	= __( 'Integrates WooCommerce with Infusionsoft CRM. You need an Infusionsoft account to make this plugin work.', 'woocommerce' );
	        $this->icon 		= WP_PLUGIN_URL . "/" . plugin_basename( dirname(__FILE__)) . '/images/cards.png';
	        $this->has_fields 	= false;
				
			// Load the form fields
			$this->init_form_fields();
			
			// Load the settings.
			$this->init_settings();

			// Get setting values
			$this->enabled 		= $this->settings['enabled'];
			$this->title 		= $this->settings['title'];
			$this->description	= $this->settings['description'];
			$this->machine_name	= $this->settings['machinename'];
			$this->apikey		= $this->settings['apikey'];
			$this->success_as   = $this->settings['success_as'];
			
			// Hooks
			add_action('admin_notices', array(&$this,'ia_woocommerce_notices'));
			add_action('woocommerce_update_options_integration_infusionsoft', array(&$this, 'process_admin_options'));
			add_action('woocommerce_update_options_integration_infusionsoft', array(&$this, 'ia_woocommerce_update_product_options'));
			
			$plugin = plugin_basename(__FILE__); 
			add_filter("plugin_action_links_$plugin", array(&$this, 'ia_woocommerce_settings_link') );
	
			
			if(!empty($this->apikey) && !empty($this->apikey) && $this->enabled) {
				$this->ia_app_connect();
				if(!$this->is_error) {
					add_action('wp_login', array(&$this, 'ia_woocommerce_refresh_admin'), 10, 2);		
					add_action('wp_login', array(&$this, 'ia_woocommerce_refresh_customer'), 10, 2);	
					add_action('woocommerce_product_write_panels', array(&$this, 'ia_woocommerce_options'));
					add_action('woocommerce_product_write_panel_tabs', array(&$this,'ia_woocommerce_tab'));
					add_action( 'admin_enqueue_scripts', array(&$this,'ia_searchable') );
					add_action('wp_insert_post', array(&$this, 'ia_woocommerce_process_product'), 10, 2 );
					add_action('woocommerce_checkout_process' , array(&$this, 'ia_woocommerce_checkout_process'),10,1 );
					add_action('woocommerce_payment_complete', array(&$this, 'ia_woocommerce_payment_complete'), 10, 1);
					add_action( 'init', array(&$this, 'ia_ls_save'), 10, 2 );
				}				
			}
			
			
	    } 
		
		

		function ia_ls_save() {
			$siteurl = $_SERVER['HTTP_HOST'];
			$siteurl = str_replace("http://","",$siteurl);
			$siteurl = str_replace("https://","",$siteurl);
			$siteurl = str_replace("www.","",$siteurl);

			if(!empty($_GET['leadsource'])) {
				setcookie("ia_leadsource", $_GET['leadsource'], (time()+31*24*3600), "/", $siteurl, 0); 
				$_SESSION['leadsource'] = $_GET['leadsource'];
			} else if(!empty($_COOKIE['ia_leadsource'])) {
				$_SESSION['leadsource'] = $_COOKIE['ia_leadsource'];				
			}
		}
		
		 
		function ia_woocommerce_settings_link($links) { 
		  $settings_link = '<a href="admin.php?page=woocommerce_settings&tab=integration&section=infusionsoft">Settings</a>'; 
		  array_unshift($links, $settings_link); 
		  return $links; 
		}
		 
		/**
	  	 * Error Notices
	 	 **/	 
		 
		function ia_woocommerce_notices() {
		     
			$app_name 	= $this->machine_name;
			$app_apikey = $this->apikey;

			if((empty($app_name) || empty($app_apikey)) && $this->enabled=='yes') {
				echo '<div class="error"><p>'.sprintf(__('To enable infusionsoft, you need to <a href="%s">input</a> your Infusionsoft application name and API key.', 'woothemes'), admin_url('admin.php?page=woocommerce_settings&tab=integration&section=infusionsoft')).'</p></div>';
			} else {
				if(!empty($app_name) && !empty($app_apikey)) {
					$this->ia_app_connect();
					if($this->is_error) {
						echo '<div class="error"><p><strong>' . sprintf(__('FATAL ERROR: Problem connecting to infusionsoft. Please <a href="%s">check your Infusionsoft API Credentials</a>.', 'woothemes'), admin_url('admin.php?page=woocommerce_settings&tab=integration&section=infusionsoft')) . '</strong></p></div>';		
					}
				}
			}			 
		}
		

		/**
	  	 * Infusionsoft Connector
	 	 **/
		 		 

		function ia_app_connect() {
			$app_name 	= $this->machine_name;
			$app_apikey = $this->apikey;
			
			if(!empty($app_name) && !empty($app_apikey)) {
				$this->app = new iaSDK;
				$this->app->configCon($app_name, $app_apikey);				
				
				$checker = $this->app->dsGetSetting('Contact', 'optiontypes');
	
				//VALIDATE CREDENTIALS
				$pos = strrpos($checker, "ERROR");				
				if ($pos === false)  $this->is_error = 0;
				else $this->is_error = 1;			
				
			} else {
				$this->is_error = 1;
			}
		}
		
		function ia_woocommerce_update_product_options() {
			$this->ia_app_connect();
				
			if(!$this->is_error) {
				update_option('ia_app_data', array());
				$tags		= $this->ia_get_tags();
				$emails		= $this->ia_get_emails();
				$actions	= $this->ia_get_actions();		

				$app_data['tags'] 		= $tags;
				$app_data['emails'] 	= $emails;
				$app_data['actions'] 	= $actions;

				update_option('ia_app_data', $app_data);
			}
		
		}
		
		/**
	  	 * Refresh Admin Options on Admin Login
	 	 **/
		 
		function ia_woocommerce_refresh_admin($user_login, $user) {						
			if (user_can( $user, 'publish_posts' )) {
				$this->ia_woocommerce_update_product_options();
			} 
		}
		
		/**
	  	 * Get Email Templates
	 	 **/		
		
		function ia_get_emails() {
			sleep(0.2);
			$returnFields = array('Id','PieceTitle');
			$results = array();
			$page = 0;
			do {
				$bucket = $this->app->dsFind('Template',1000,$page,'PieceType','Email',$returnFields);
				$results = array_merge($results, $bucket);
				$page++;
			}
			while( count($bucket) == 1000 );
			return $results;
		}
	  
	  
	  	/**
	  	 * Get Tags
	 	 **/
		function ia_get_tags() {
			sleep(0.2);
			$returnFields = array('Id','GroupName');
			$results = array();
			$page = 0;
			do {
				$bucket = $this->app->dsFind('ContactGroup',1000,$page,'Id','%',$returnFields);
				$results = array_merge($results, $bucket);
				$page++;
			}
			while( count($bucket) == 1000 );
			return $results;
		}

		
	  	/**
	  	 * Get Actions
	 	 **/
	  
		function ia_get_actions() {
			sleep(0.2);
			$returnFields = array('Id','TemplateName');
			$results = array();
			$page = 0;
			do {
				$bucket = $this->app->dsFind('ActionSequence',1000,$page,'Id','%',$returnFields);
				$results = array_merge($results, $bucket);
				$page++;
			}
			while( count($bucket) == 1000 );
			return $results;
		}
		
		
		/**
	  	 * Refresh Customer info on login
	 	 **/
		 
	   function ia_woocommerce_refresh_customer($user_login, $user) {
			global $woocommerce;
			$this->ia_app_connect();

			$userEmail  = $user->user_email;	
			$countries 	= new WC_Countries();
			$countries 	= array_flip($countries->countries);

			if(!empty($userEmail)) {
				$contactinfo = array(
					'Id'				,
					'FirstName' 		,
					'LastName' 			,
					'Phone1' 			,
					'StreetAddress1' 	,
					'StreetAddress2' 	,
					'City' 				,
					'State' 			,
					'Country' 			,
					'PostalCode' 		,
					'Company'
				);
				
				$billing_fname  = get_user_meta($user->ID, 'billing_first_name', true);
				
				if(empty($billing_fname)) {
					$contact = $this->app->dsFind('Contact',5,0,'Email',$userEmail,$contactinfo);
					$contact = $contact[0];
					
					$countryfull    = $contact['Country'];
					$country 		= $countries[$countryfull];
					
					if(!empty($contact['Id'])) { 
						update_user_meta( $user->ID, 'billing_first_name', 	$contact['FirstName']  );
						update_user_meta( $user->ID, 'billing_last_name', 	$contact['LastName']  );
						update_user_meta( $user->ID, 'billing_email', 		$userEmail  );
						update_user_meta( $user->ID, 'billing_phone', 		$contact['Phone1']  );
						update_user_meta( $user->ID, 'billing_address_1', 	$contact['StreetAddress1']  );
						update_user_meta( $user->ID, 'billing_address_2', 	$contact['StreetAddress2']  );
						update_user_meta( $user->ID, 'billing_city', 		$contact['City']  );
						update_user_meta( $user->ID, 'billing_state', 		$contact['State']  );
						update_user_meta( $user->ID, 'billing_country', 	$country );
						update_user_meta( $user->ID, 'billing_postcode', 	$contact['PostalCode']  );
						update_user_meta( $user->ID, 'billing_company', 	$contact['Company']  );
					}
				}			
			}	
						
		}
		

		/**
		 * Product Infusionsoft Tab
		 **/
		
		function ia_woocommerce_tab() {
			?>
			<li class="custom_tab"><a href="#infusionsoft_tab"><?php _e('Infusionsoft', 'woothemes'); ?></a></li>
			<?php
		}
		
		
		
		function ia_searchable($hook) {
			if( 'post.php' != $hook && 'post-new.php' != $hook ) return;
			wp_enqueue_script( 'ia_searchable', plugins_url('/chosen.jquery.min.js', __FILE__), array('jquery') ); 
			wp_enqueue_script( 'ia_admin_scripts', plugins_url('/admin_scripts.js', __FILE__) ); 
		} 

		
		/**
		 * Product Settings
		 **/
		
		function ia_woocommerce_options() {
			global $post;
			
			if($_GET['ia'] == 'refresh') {
				$this->ia_woocommerce_update_product_options();
			}
			
			$this->ia_app_connect();			
			$app_data 	= get_option('ia_app_data');		
		
			$tags 		= $app_data['tags'];
			$emails 	= $app_data['emails'];	
			$actions 	= $app_data['actions'];	
			

			$tid = (int) get_post_meta($post->ID, 'infusionsoft_tag', 	true);
			$eid = (int) get_post_meta($post->ID, 'infusionsoft_email', 	true);
			$aid = (int) get_post_meta($post->ID, 'infusionsoft_action', 	true);
			
			$tag_select			= array(
									'id' 			=> 'infusionsoft_tag', 
									'class'			=> 'chzn-select',
									'value' 		=> $tid, 
									'label' 		=> 'Tag to apply upon successful purchase',
									'desc_tip' 	=> 'If you want to apply more than one tags, create an action set in infusionsoft
														and use the action set option below.'
									);	
			$email_select		= array(
									'id' 			=> 'infusionsoft_email', 
									'class'			=> 'chzn-select',
									'value' 		=> $eid, 
									'label' 		=> 'Email Template to Send upon successful purchase',
									'desc_tip' 	=> 'If you want to send more than one email template, create an action set in 		
															infusionsoft and use the action set option below.'
									);	
			$action_select		=  array(
									'id' 			=> 'infusionsoft_action', 
									'class'			=> 'chzn-select',
									'value' 		=> $aid, 
									'label' 		=> 'Action set to run upon successful purchase',
									'desc_tip' 	=> 'You can start follow up sequence, subscriptions, send HTTP post, etc using this
															option.'
									);

			$tag_select		['options']['0'] = 'Please select tag';
			$email_select	['options']['0'] = 'Please select email';
			$action_select	['options']['0'] = 'Please select action set';

			
			if(count($tags) > 0) {
				foreach($tags as $tag) {
					$value = $tag['Id'];
					$text = "{$tag['Id']} => {$tag['GroupName']}";
					$tag_select['options'][$value] = $text;
				}
			}

			if(count($emails) > 0) {
				foreach($emails as $email) {
					$value = $email['Id'];
					$text = "{$email['Id']} => {$email['PieceTitle']}";
					$email_select['options'][$value] = $text;
				}
			}

			if(count($actions) > 0) {
				foreach($actions as $action) {
					$value = $action['Id'];
					$text = "{$action['Id']} => {$action['TemplateName']}";
					$action_select['options'][$value] = $text;
				}
			}
			
			
			?>
			<div id="infusionsoft_tab" class="panel woocommerce_options_panel">
			<div class="options_group">
			<?php
				woocommerce_wp_select( $tag_select );
				woocommerce_wp_select( $email_select );
				woocommerce_wp_select( $action_select );
				
			?>
			<a href="<?php echo $_SERVER['REQUEST_URI']; ?>&ia=refresh" style="margin: 10px;">Not seeing a specific action, tag or template? Click here to Refresh</a>
			</div></div>
			<?php
		}
		
		/**
		 * Process Product Settings
		 **/		
		
		function ia_woocommerce_process_product( $post_id, $post = null  ) {
			global $woocommerce; 
			if ( $post->post_type == "product" ) {
				update_post_meta( $post_id, 'infusionsoft_tag', 	$_POST['infusionsoft_tag']);
				update_post_meta( $post_id, 'infusionsoft_email', 	$_POST['infusionsoft_email']);
				update_post_meta( $post_id, 'infusionsoft_action', 	$_POST['infusionsoft_action']);
			}
		}	
		
		/**
	     * Initialize Gateway Settings Form Fields
	     */

	    function init_form_fields() {
	    
	    	$this->form_fields = array(
				'enabled' => array(
								'title' => __( 'Enable/Disable', 'woothemes' ), 
								'label' => __( 'Enable Infusionsoft', 'woothemes' ), 
								'type' => 'checkbox', 
								'description' => __( 'Once enabled, woocommerce will be automatically integrated to infusionsoft to create/update contact record','woothemes' ), 
								'default' => 'no'
							), 
				'machinename' => array(
								'title' => __( 'Application Name', 'woothemes' ), 
								'type' => 'text', 
								'description' => __( 'Your Infusionsoft Application Name', 'woothemes' ), 
								'default' => ''
							), 
				'apikey' => array(
								'title' => __( 'API Key', 'woothemes' ), 
								'type' => 'password', 
								'description' => __( 'This is the API Key supplied by Infusionsoft.', 'woothemes' ), 
								'default' => ''
							),
							
				'success_as' => array(
								'title' => __( 'Action Set # to Run After Successful Purchase', 'woothemes' ), 
								'type' => 'text', 
								'description' => __( 'This action set will be triggered to the customer for every successful purchase.', 'woothemes' ), 
								'default' => '0'
							)

			);

	    }		 
		
		/**
		 * Admin Panel Options 
		 **/
		public function admin_options() {
			?>
			<h3><?php _e('Infusionsoft','woothemes'); ?></h3>	    	
	    	<p><?php _e( 'Update the settings below to integrate woocommerce with Infusionsoft\'s CRM', 'woothemes' ); ?></p>
	    	<table class="form-table">
	    		<?php $this->generate_settings_html(); ?>
			</table><!--/.form-table-->    	
	    	<?php
	    }
		

		/**
		 * Add/Update Contact to infusionsoft after checkout form submission.
		**/
		
		function ia_woocommerce_checkout_process() {
			$this->ia_app_connect();			
			$countries 		= new WC_Countries();
			
			$returnFields 	= array('Id');	
			$shiptobilling 	= (int) $this->get_post('shiptobilling');
			
			// GET COUNTRY
			$email			= $this->get_post('billing_email');
			$contact 		= $this->app->dsFind('Contact',5,0,'Email',$email,$returnFields); 
			$contact 		= $contact[0];
 			
			$firstName		= $this->get_post('billing_first_name');
			$lastName		= $this->get_post('billing_last_name');
			$phone			= $this->get_post('billing_phone');
			
			$b_address1		= $this->get_post('billing_address_1');
			$b_address2		= $this->get_post('billing_address_2');
			$b_city			= $this->get_post('billing_city');
			$b_state		= $this->get_post('billing_state');
			$b_country		= $countries->countries[$this->get_post('billing_country')];
			$b_zip			= $this->get_post('billing_postcode');
			$b_company		= $this->get_post('billing_company');
			
			$s_address1		= $shiptobilling ?	$b_address1 : $this->get_post('shipping_address_1');
			$s_address2		= $shiptobilling ? 	$b_address2	: $this->get_post('shipping_address_2');
			$s_city			= $shiptobilling ? 	$b_city		: $this->get_post('shipping_city');
			$s_state		= $shiptobilling ? 	$b_state	: $this->get_post('shipping_state');
			$s_country		= $shiptobilling ? 	$b_country	: $countries->countries[$this->get_post('shipping_country')];
			$s_zip			= $shiptobilling ? 	$b_zip		: $this->get_post('shipping_postcode');
			
			// Company Selector
			$compId = 0;
			if(!empty($b_company)) {
				$company 		= $this->app->dsFind('Company',5,0,'Company',$b_company,array('Id')); 
				$company 		= $company[0];
				
				if ($company['Id'] != null && $company['Id'] != 0 && $company != false){		
					$companyinfo = array('FirstName' => $b_company,'Company' => $b_company);
					$compId = $this->app->dsAdd("Company", $companyinfo);
				} else {
					$compId = $company['Id'];
				}
			}
			
			// CONTACT INFO
			$contactinfo = array(
				'FirstName' 		=> $firstName,
				'LastName' 			=> $lastName,
				'Phone1' 			=> $phone,
				'StreetAddress1' 	=> $b_address1,
				'StreetAddress2' 	=> $b_address2,
				'City' 				=> $b_city,
				'State' 			=> $b_state,
				'Country' 			=> $b_country,
				'PostalCode' 		=> $b_zip,
				'Address2Street1' 	=> $s_address1,
				'Address2Street2' 	=> $s_address2,
				'City2' 			=> $s_city,
				'State2' 			=> $s_state,
				'Country2' 			=> $s_country,
				'PostalCode2' 		=> $s_zip,
				'Leadsource' 		=> $_SESSION['leadsource'],
				'CompanyID'			=> $compId,
				'ContactType'		=> 'Customer'
				);
				
		
			// GET CONTACT ID
			if ($contact['Id'] != null && $contact['Id'] != 0 && $contact != false){
				   $contactId = (int) $contact['Id']; 
				   $contactId = $this->app->updateCon($contactId, $contactinfo);
			} else {
				$contactinfo['Email'] = $email;
				$contactId  = $this->app->addCon($contactinfo);
				$this->app->optIn($email,"API Override.");
			}
		
			$_SESSION['ia_contactId']  = $contactId;	
			
		}

		/**
		 * Run actions upon successful purchase
		**/
		function ia_woocommerce_payment_complete( $order_id ) {
			global $woocommerce;			
			$this->ia_app_connect();		
			$order = new WC_Order( $order_id );		
			
			$contactId = (int) $_SESSION['ia_contactId'];
			
			$products = $order->get_items(); 
			
			$as = (int) $this->success_as;
			$this->app->runAS($contactId, $as);	
			
			foreach($products as $product) {
				$id =  (int) $product['id'];				
				$tag    = (int) get_post_meta($id, 'infusionsoft_tag', 	true);
				$email  = (int) get_post_meta($id, 'infusionsoft_email', 	true);
				$action = (int) get_post_meta($id, 'infusionsoft_action', 	true);				

				if(!empty($action)) $this->app->runAS($contactId, $action);
				if(!empty($tag)) 	$this->app->grpAssign($contactId, $tag);	
				if(!empty($email))	$this->app->sendTemplate(array($contactId), $email);		
				
			}
		}
		
		/**
		 * For Debugging Purposes
		 **/
	
		function testmailme($msg) {
			$to      = 'admin@infusedaddons.com';
			$subject = 'debug msg';
			$message = $msg;
			$headers = 'From: webmaster@infusedmj.com' . "\r\n" .
				'Reply-To: webmaster@example.com' . "\r\n" .
				'X-Mailer: PHP/' . phpversion();

			mail($to, $subject, $message, $headers);
		}  
		

		/**
		 * Get post data if set
		 **/
		private function get_post($name) {
			if(isset($_POST[$name])) {
				return $_POST[$name];
			}
			return NULL;
		}

	}
	
	function add_infusionsoft_gateway( $methods ) {
		$methods[] = 'IA_Woocommerce'; return $methods;
	}
	
	add_filter('woocommerce_integrations', 'add_infusionsoft_gateway' );

}