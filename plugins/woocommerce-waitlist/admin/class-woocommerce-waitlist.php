<?php
/**
 * Woocommerce Waitlist Admin.
 *
 * @package   Woocommerce_Waitlist_Admin
 * @author    Makis Mourelatos <info@wpcream.com>
 * @license   GPL-2.0+
 * @link      http://wpcream.com
 * @copyright 2014 WPCream.com
 */

/**
 *
 * @package Woocommerce_Waitlist_Admin
 * @author  Your Name <email@example.com>
 */
class Woocommerce_Waitlist_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	public $plugin_has_msg = null;
	public $plugin_error_flag = null;
	

	/**
	 * Retrieve messages info
	 *
	 * @since    3.0.0
	 *
	 */

	public function get_plugin_msg() {

		$msg = $this->plugin_has_msg;
		$error = $this->plugin_error_flag;

		$ret = array();
		$ret["message"] = $msg;
		$ret["error"] = $error;

		return $ret;
	}

	/**
	 * Set messages info
	 *
	 * @since    3.0.0
	 *
	 */

	public function set_plugin_msg( $d, $er ) {

		$this->plugin_has_msg = $d;
		$this->plugin_error_flag = $er;
	}

	/**
	 * Export WooWaitlist Data
	 *
	 * @since    3.0.0
	 *
	 * @var      string
	 */
	public function export_woowaitilist_data( $ids ){

		if( $ids ){

			if( !is_array( $ids ) ){

				$ids = array( $ids );
			}

			$products_count = count( $ids );

			$filename = "woowaitlist-data-" . current_time('mysql') .".csv";

			$filename = str_replace( " ", "_", $filename);
			$filename = str_replace( ":", "-", $filename);

			$header_csv = array();
			$header_csv[] = "Email";
			$header_csv[] = "Product Title";
			$header_csv[] = "Variation";
			$header_csv[] = "Categories";
			$header_csv[] = "Date Added";
			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false);
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=" . $filename . ";" );
			header("Content-Transfer-Encoding: binary");

			$t = implode( ",", $header_csv );

			print $t . "\n";

			global $wpdb;

			$wew_DBtable_name = $wpdb->prefix . 'woocommerce_waitlist';

			$sql = "SELECT * FROM ".$wew_DBtable_name." WHERE id IN (" . implode( ",", $ids ) . ") ORDER BY date DESC";
			$ret = $wpdb->get_results( $sql );

			foreach ( $ret as $k => $v ) {
				
				if( in_array( $v->id, $ids ) ){

					$product_title = get_the_title( $v->productId );

					$subscription = array();
					$subscription[] = $v->email;
					$subscription[] = $product_title;

					if( $v->variationId == 0 ){

						$subscription[] = '-';
					}
					else{

						$pplg = Woocommerce_Waitlist::get_instance();

						$variationTitle = $pplg->get_variation_titles( $v->variationId );

						if( $variationTitle ){

							$subscription[] = $variationTitle ;	
						}

					}

					$terms = get_the_terms( $v->productId, 'product_cat' );
					
					if( $terms ){

						$counter = 0;

						$g = "";

						foreach ($terms as $term) {

							$g .= $term->name ;

						    $counter++;

						    if( $counter < count( $terms ) ){

						    	$g .= ' / ';
						    }
						}

						$subscription[] = $g;
					}	
					else{

						$subscription[] = "-";
					}

					$subscription[] = $v->date;

					$t = implode( ",", $subscription );
					print $t . "\n";
				}

			}

			exit;
		}
	}

	/**
	 * Hook - Execute before wp_header()
	 *
	 * @since    3.0.0
	 *
	 * @var      string
	 */
	public function execute_before_wp_header(){
				
		if( isset( $_GET['delete'] ) ){
				
			global $wpdb;	

			$wew_DBtable_name = $wpdb->prefix . 'woocommerce_waitlist';

			$_GET['delete'] = absint($_GET['delete']);

		    $wpdb->query( "DELETE FROM " . $wew_DBtable_name ." WHERE id='" .$_GET['delete']."'" );

		}
		elseif( isset( $_POST ) && !empty( $_POST ) ){

			if( isset( $_POST['action'] ) ){

				switch( $_POST['action'] ){
					case "update_woowaitlist_settings":

						$wew_subscribe_to_waitlist_str = trim( wp_unslash( $_POST['wew-subscribe-to-waitlist-str'] ) );
						update_option( 'wew-subscribe-to-waitlist-str', $wew_subscribe_to_waitlist_str );

						if( isset( $_POST['wew-subscribe-backorders'] )  ){

							$subscribe_backorders = $_POST['wew-subscribe-backorders'] == "on" ? "on" : "";
						}
						else{
							
							$subscribe_backorders = "";
						}

						update_option( 'wew-subscribe-backorders', $subscribe_backorders );

						$remove_onUninstall = trim( $_POST['wew-remove-waitlist-on-uninstall'] ) != "" ? wp_unslash( $_POST['wew-remove-waitlist-on-uninstall']) : "";
						update_option( 'wew-remove-waitlist-on-uninstall', $remove_onUninstall );
						
						$unsubscribe_page = intval( $_POST['wew-unsubscribe-page'] );
						update_option( 'wew-unsubscribe-page', $unsubscribe_page );

						if( current_user_can( "manage_options" ) ){

							if( isset( $_POST['wew-enable-shop-manager-settings'] )  ){

								$shop_manager_settings = $_POST['wew-enable-shop-manager-settings'] == "on" ? "on" : "";
							}
							else{
								
								$shop_manager_settings = "";
							}

							update_option( 'wew-enable-shop-manager-settings', $shop_manager_settings );
						}

						if( isset( $_POST['wew-admin-notification-on-subscription'] )  ){

							$admin_subscriptions_notifications = $_POST['wew-admin-notification-on-subscription'] == "on" ? "on" : "";
						}
						else{

							$admin_subscriptions_notifications = "";
						}

						update_option( 'wew-admin-notification-on-subscription', $admin_subscriptions_notifications );

						$oos_message = trim( wp_unslash( $_POST['wew-out-of-stock-message'] ) );
						update_option( 'wew-out-of-stock-message', $oos_message );

						$voos_message = trim( wp_unslash( $_POST['wew-variations-out-of-stock-message'] ) );
						update_option( 'wew-variations-out-of-stock-message', $voos_message );

						$sproduct_success = trim( wp_unslash( $_POST['wew-subscribe-product-success-msg'] ) );
						update_option( 'wew-subscribe-product-success-msg', $sproduct_success );

						$svariation_success = trim( wp_unslash( $_POST['wew-subscribe-variation-success-msg'] ) );
						update_option( 'wew-subscribe-variation-success-msg', $svariation_success );

						$already_subscribed_product = trim( wp_unslash( $_POST['wew-already-product-subscribed'] ) );
						update_option( 'wew-already-product-subscribed', $already_subscribed_product );

						$already_subscribed_variation = trim( wp_unslash( $_POST['wew-already-variation-subscribed'] ) );
						update_option( 'wew-already-variation-subscribed', $already_subscribed_variation );

						$emptyEmail_alert_msg = trim( wp_unslash( $_POST['wew-no-email-alert'] ) );
						update_option( 'wew-no-email-alert', $emptyEmail_alert_msg );

						$custom_css_style = trim( wp_unslash( $_POST['wew-custom-css'] ) );
						update_option( 'wew-custom-css', $custom_css_style );

						$a = Woocommerce_Waitlist_Admin::get_instance();

						$success_message = __( "Settings saved successful.", $a->plugin_slug );
						$a->set_plugin_msg( $success_message, 0 );

					break;
					case "update_woowaitlist_email_settings":

						$notifyAvail = trim( wp_unslash( $_POST['wew-notify-available-product'] ) );
						$r1 = update_option( 'wew-notify-available-product', $notifyAvail );

						$subMail_fromName = trim( wp_unslash( $_POST['wew-subscription-email-from-name'] ) );
						$r2 = update_option( 'wew-subscription-email-from-name', $subMail_fromName );

						$subMail_subject = trim( wp_unslash( $_POST['wew-subscription-email-subject'] ) );
						$r3 = update_option( 'wew-subscription-email-subject', $subMail_subject );

						$default_emailSettings = !isset( $_POST['woowaitilist-default-email-options'] ) || $_POST['woowaitilist-default-email-options'] != "on" ? "unselected" : "on";
						$r4 = update_option( 'woowaitilist-default-email-options', $default_emailSettings );

						$insertedSenderEmail = trim( $_POST['notifications-sender-email'] );
						$custom_senderEmail = $insertedSenderEmail ? $insertedSenderEmail : "";
						$r5 = update_option( 'notifications-sender-email', $custom_senderEmail );
						
						$back_in_stock_quantity = intval( $_POST['wew-back-in-stock_quantity'] );
						$r6 = update_option( 'wew-back-in-stock_quantity', $back_in_stock_quantity );

						$a = Woocommerce_Waitlist_Admin::get_instance();

						if( $default_emailSettings == "unselected" && !is_email( $custom_senderEmail ) ){

							$success_message = __( "Please insert a valid email address.", $a->plugin_slug );
							$a->set_plugin_msg( $success_message, 1 );
						}
						else{

							$success_message = __( "Email settings saved successful.", $a->plugin_slug );
							$a->set_plugin_msg( $success_message, 0 );
						}
					break;
					case "update_woowaitlist_data":

						global $wpdb;

						$wew_DBtable_name = $wpdb->prefix . 'woocommerce_waitlist';				

						if( isset( $_POST['wewd_id'] ) && !empty( $_POST['wewd_id'] )  ){

							$checked_elem = false;

							if( isset( $_POST['wew_data_actions_changes-2'] ) ){
								
								$checked_elem = $_POST['wew_data_form_actions-2'];
							}
							elseif( isset( $_POST['wew_data_actions_changes'] ) ){
								
								$checked_elem = $_POST['wew_data_form_actions'];
							}

							if( $checked_elem ){

					    		switch( $checked_elem ){
						    		case 'delete':
						    			if( is_array( $_POST['wewd_id'] ) ){

						    				$pids_array = $_POST['wewd_id'];

						    				foreach ( $pids_array as $key => $value) {

						    					$v = absint( $value );
					        					$wpdb->query( "DELETE FROM " . $wew_DBtable_name ." WHERE id='" . $v . "'" );
						    				}
						    			}
						    		break;
						    		case 'export':
						    			$this->export_woowaitilist_data( $_POST['wewd_id'] );
						    		break;
						    	}

					    	}
				    	}
				    	
					break;
				}
			}

		}
	}

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		$plugin = Woocommerce_Waitlist::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();
		$this->plugin_slug_data = $plugin->get_plugin_slug_data();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		add_action( 'wp_loaded', array( $this, 'execute_before_wp_header' ) );

		add_action( 'add_meta_boxes', array( $this, 'woowaitlist_add_product_meta_boxes' ) );
		add_action( 'add_meta_boxes', array( $this, 'woowaitlist_display_subscription_option' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( get_bloginfo( 'text_direction' ) == 'ltr' ) {
			
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Woocommerce_Waitlist::VERSION );
		}
		else{

			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin-rtl.css', __FILE__ ), array(), Woocommerce_Waitlist::VERSION );
		}
	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Woocommerce_Waitlist::VERSION );
		
			wp_localize_script( $this->plugin_slug . '-admin-script', 
								'woowaitlist_back_ajax_object', 
								array( 
									'ajax_url' 	=> admin_url( 'admin-ajax.php' )
								) 
				);
		}
	}

	/**
	 * Get Waitlist records from database
	 *
	 * @since    1.0.0
	 *
	 * @return   html
	 */

	public function get_wew_records(){

		global $wpdb;

		$wew_DBtable_name = $wpdb->prefix . 'woocommerce_waitlist';

		$ret = $wpdb->get_results("SELECT * FROM ".$wew_DBtable_name." ORDER BY date DESC");

		return $ret;
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {


		if (!current_user_can( 'manage_options' )){

			$editor = get_role('shop_manager');
			$editor->remove_cap('manage_options');

	        $this->plugin_screen_hook_suffix = add_menu_page(
											__( 'WooWaitlist', $this->plugin_slug ), 
											__( 'WooWaitlist', $this->plugin_slug ), 
											'manage_woocommerce',
											$this->plugin_slug, 
											array( $this, 'display_plugin_admin_page' ),
											plugin_dir_url( __FILE__ ) . 'images/woowaitlist-fav.png', 40
										);
	    }
	    else{

	    	$this->plugin_screen_hook_suffix = add_menu_page(
											__( 'WooWaitlist', $this->plugin_slug ), 
											__( 'WooWaitlist', $this->plugin_slug ), 
											'manage_options',
											$this->plugin_slug,
											array( $this, 'display_plugin_admin_page' ), 
											plugin_dir_url( __FILE__ ) . 'images/woowaitlist-fav.png', 40
										);
	    }

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);
	}

	/**
	 * Initialize WooWaitlist products metaboxes
	 *
	 * @since     2.1.5
	 */
	public function woowaitlist_add_product_meta_boxes(){

		add_meta_box( 
			'waitlist_product_subscribers_mbox', 
			__( "Waitlist subscribers", $this->plugin_slug ) , 
			array( $this, 'woowaitlist_display_subscribers_meta_box' ), 
			'product', 
			'side',
			'high'
		);
	}


	public function woowaitlist_display_subscription_option(){

		add_meta_box( 
			'woowaitlist_display_subscription_option_mbox', 
			__( "WooWaitlist subscription", $this->plugin_slug ) , 
			array( $this, 'woowaitlist_display_subscription_option_meta_box' ), 
			'product', 
			'normal',
			'high'
		);
	}

	/**
	 * Display Woowaitlist products subscribers
	 *
	 * @since     2.1.5
	 */
	public function woowaitlist_display_subscribers_meta_box( $post ){

		global $wpdb;

		$wew_DBtable_name = $wpdb->prefix . 'woocommerce_waitlist';

		$res = $wpdb->get_results("SELECT count(id) AS cnt FROM " . $wew_DBtable_name . " WHERE productId = " . $post->ID );

		$num_val = $res[0];

		$num = $num_val->cnt;

		echo '<p class="wew-subscr-num">' . $num . '</p>';
	}

	public function woowaitlist_display_subscription_option_meta_box( $post ){

		$disable_woowaitlist_subscription = get_post_meta( $post->ID, 'disable_woowaitlist_subscription', true );

		$disable_woowaitlist_subscription = $disable_woowaitlist_subscription == 'on' ? 1 : 0;

		$isChecked = $disable_woowaitlist_subscription ? ' checked="checked" ' : '';

		echo '<br/>';
		echo '<label for="disable_woowaitlist_subscription">';
		echo '<input type="checkbox" id="disable_woowaitlist_subscription" name="disable_woowaitlist_subscription" ' . $isChecked . '/>';
		echo __( 'Disable WooWaitist subscription', $this->plugin_slug );
		echo '</label>';
		echo '<br/>';
		echo '<br/>';
	}
}