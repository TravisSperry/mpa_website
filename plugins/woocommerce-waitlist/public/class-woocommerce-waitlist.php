<?php
/**
 * Woocommerce Waitlist.
 *
 * @package   Woocommerce_Waitlist
 * @author    Makis Mourelatos <info@wpcream.com>
 * @license   GPL-2.0+
  * @link      http://wpcream.com
  * @copyright 2014 WPCream.com
 */

/**
 *
 * @package Woocommerce_Waitlist
 * @author  Your Name <email@example.com>
 */
class Woocommerce_Waitlist {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	const VERSION = '1.0.0';

	/**
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'woocommerce-waitlist';

	/**
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug_data = 'woocommerce-waitlist-data';
	
	/**
	 *
	 * @since    2.1.5
	 *
	 * @var      string or false
	 */
	public $subscribe_button_str = '';

	/**
	 *
	 * @since    2.0.0
	 *
	 * @var     array or false
	 */
	public 	$mail_product_glData = false;
	
	/**
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	public $hc = 0;

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		
		add_action('wp_head', array( $this, 'custom_css' ) );

		// Plugin shortcode
		add_shortcode( 'wew_unsubscribe_waitlist', array( $this, 'woowaitlist_unsubscribe_shortcode' ) );
		
		// Action triggered on products save
		add_action( 'save_post', array( $this, 'wew_on_product_save' ), 1000 );

		// Ajax functions
		add_action( 'wp_ajax_wew_save_to_db_callback', array( $this, 'wew_save_to_db_callback' ) );
		add_action( 'wp_ajax_nopriv_wew_save_to_db_callback', array( $this, 'wew_save_to_db_callback' ) );
		add_action( 'wp_ajax_woowaitlist_save_settings', array( $this, 'woowaitlist_save_settings' ) );
		add_action( 'wp_ajax_nopriv_woowaitlist_save_settings', array( $this, 'woowaitlist_save_settings' ) );

		// Check for products that are back in stock
		add_filter( 'woocommerce_get_availability', array( $this, 'wew_check_product_availability' ), 1, 2 );


		self::set_subscribe_button_str();
	}
	/**
	 * Save backend settings - Ajax call
	 *
	 * @since     1.0.0
	 * 
	 * @param     
	 *
	 * @return    json object 	Status and messages from save action
	 */
	public function woowaitlist_save_settings(){

		parse_str( $_POST['p'], $data);

		$ret = array();

		switch( $data['action'] ){

			case "update_woowaitlist_settings":

				$wew_subscribe_to_waitlist_str = trim( wp_unslash( $data['wew-subscribe-to-waitlist-str'] ) );
				update_option( 'wew-subscribe-to-waitlist-str', $wew_subscribe_to_waitlist_str );

				if( isset( $data['wew-subscribe-backorders'] )  ){

					$subscribe_backorders = $data['wew-subscribe-backorders'] == "on" ? "on" : "";
				}
				else{
					
					$subscribe_backorders = "";
				}

				update_option( 'wew-subscribe-backorders', $subscribe_backorders );


				$remove_onUninstall = isset($data['wew-remove-waitlist-on-uninstall']) && trim( $data['wew-remove-waitlist-on-uninstall'] ) != "" ? wp_unslash( $data['wew-remove-waitlist-on-uninstall']) : "";
				update_option( 'wew-remove-waitlist-on-uninstall', $remove_onUninstall );
				
				$unsubscribe_page = intval( $data['wew-unsubscribe-page'] );
				update_option( 'wew-unsubscribe-page', $unsubscribe_page );

				if( current_user_can( "manage_options" ) ){

					if( isset( $data['wew-enable-shop-manager-settings'] )  ){

						$shop_manager_settings = $data['wew-enable-shop-manager-settings'] == "on" ? "on" : "";
					}
					else{

						$shop_manager_settings = "";						
					}

					update_option( 'wew-enable-shop-manager-settings', $shop_manager_settings );
				}

				$notifyAdmin_onSubscription = isset($data['wew-admin-notification-on-subscription']) && trim( $data['wew-admin-notification-on-subscription'] ) == "on" ? "on" : "";
				update_option( 'wew-admin-notification-on-subscription', $notifyAdmin_onSubscription );

				$oos_message = trim( wp_unslash( $data['wew-out-of-stock-message'] ) );
				update_option( 'wew-out-of-stock-message', $oos_message );

				$voos_message = trim( wp_unslash( $data['wew-variations-out-of-stock-message'] ) );
				update_option( 'wew-variations-out-of-stock-message', $voos_message );

				$sproduct_success = trim( wp_unslash( $data['wew-subscribe-product-success-msg'] ) );
				update_option( 'wew-subscribe-product-success-msg', $sproduct_success );

				$svariation_success = trim( wp_unslash( $data['wew-subscribe-variation-success-msg'] ) );
				update_option( 'wew-subscribe-variation-success-msg', $svariation_success );

				$already_subscribed_product = trim( wp_unslash( $data['wew-already-product-subscribed'] ) );
				update_option( 'wew-already-product-subscribed', $already_subscribed_product );

				$already_subscribed_variation = trim( wp_unslash( $data['wew-already-variation-subscribed'] ) );
				update_option( 'wew-already-variation-subscribed', $already_subscribed_variation );

				$emptyEmail_alert_msg = trim( wp_unslash( $data['wew-no-email-alert'] ) );
				update_option( 'wew-no-email-alert', $emptyEmail_alert_msg );

				$custom_css_style = trim( wp_unslash( $data['wew-custom-css'] ) );
				update_option( 'wew-custom-css', $custom_css_style );

				$ret['message'] = '<div class="updated">'. __( "Settings saved successful.", $this->plugin_slug ) . '</div>';
				$ret['error'] = 0;

			break;
			case "update_woowaitlist_email_settings":
				
				$back_in_stock_quantity = intval( $data['wew-back-in-stock_quantity'] );
				update_option( 'wew-back-in-stock_quantity', $back_in_stock_quantity );

				$notifyAvail = trim( wp_unslash( $data['wew-notify-available-product'] ) );
				$r1 = update_option( 'wew-notify-available-product', $notifyAvail );

				$subMail_fromName = trim( wp_unslash( $data['wew-subscription-email-from-name'] ) );
				$r2 = update_option( 'wew-subscription-email-from-name', $subMail_fromName );

				$subMail_subject = trim( wp_unslash( $data['wew-subscription-email-subject'] ) );
				$r3 = update_option( 'wew-subscription-email-subject', $subMail_subject );

				$default_emailSettings = !isset( $data['woowaitilist-default-email-options'] ) || $data['woowaitilist-default-email-options'] != "on" ? "unselected" : "on";
				$r4 = update_option( 'woowaitilist-default-email-options', $default_emailSettings );

				$insertedSenderEmail = trim( $data['notifications-sender-email'] );
				$custom_senderEmail = $insertedSenderEmail ? $insertedSenderEmail : "";
				$r5 = update_option( 'notifications-sender-email', $custom_senderEmail );

				$ret['message'] = '<div class="updated">'. __( "Email settings saved successful.", $this->plugin_slug ) . '</div>';
				$ret['error'] = 0;

				if( $default_emailSettings == "unselected" && !is_email( $custom_senderEmail ) ){
					$ret['message'] = '<div class="updated error">'. __( "Please insert a valid email address.", $this->plugin_slug ) . '</div>';
					$ret['error'] = 1;
				}
			break;
		}

		$ret = json_encode( $ret );

		die( $ret );
	}

	/**
	 * Check products availability that including in waitlist
	 *
	 * @since     1.0.0
	 */
	public function wew_on_product_save( $post_id ){

		if ( wp_is_post_revision( $post_id ) ){
			return;
		}

		$postType = get_post_type( $post_id );

		if( $postType == 'product' ){

			$this->checkGroupedStocks_toNotifyUsers( $post_id );			
			$this->checkStocks_toNotifyUsers( $post_id );
			$this->checkVariationsStocks_toNotifyUsers( $post_id );

			$disable_woowaitlist_subscription = isset( $_POST['disable_woowaitlist_subscription'] ) && $_POST['disable_woowaitlist_subscription'] === 'on' ? 'on' : false;
			update_post_meta( $post_id, 'disable_woowaitlist_subscription', $disable_woowaitlist_subscription );
		}

	}

	/**
	 * Send email to user that just added to waitlist
	 *
	 * @since     1.0.0
	 * 
	 * @param     string 	$user_email 	email address
	 *			  integer 	$product_id   	product id
	 *			  integer   $variation_id   variation id
	 *
	 */
	public function wew_email_onWaitlistAdd( $user_email, $product_id, $variation_id = 0, $gparent_id = 0 ){
		
		$unsubscribe_page_id = get_option('wew-unsubscribe-page') ? get_option('wew-unsubscribe-page') : 0 ;

		$wew_unsubscribe_page_id = false;

		if( $unsubscribe_page_id && get_page( $unsubscribe_page_id ) ){

			$unsp_data = get_page( $unsubscribe_page_id );

			if( $unsp_data->post_status == 'publish'){

				$wew_unsubscribe_page_id = absint( $unsubscribe_page_id );
			}
		}

		$parr = array(
			'product_id' => $product_id,
			'variation_id' => $variation_id
			);

		$this->set_mail_product_glData( $parr );

		$d = '<br/>';
		$d .= __( 'Email address: ', $this->plugin_slug ) ;
		$d .= $user_email;
		$d .= '<br/>';

		if( $gparent_id > 0 ){

			$d .= __( 'Product: ', $this->plugin_slug ) ;
			$d .= '<a href="' . esc_url( get_permalink( $gparent_id ) ) . '">' . esc_html( get_the_title( $gparent_id ) ) . '</a>';
			$d .= '<br/>';
			$d .= __( 'Child product: ', $this->plugin_slug ) ;
			$d .= '<a href="' . esc_url( get_permalink( $product_id ) ) . '">' . esc_html( get_the_title( $product_id ) ) . '</a>';
			$d .= '<br/>';
		}
		else{

			$d .= __( 'Product: ', $this->plugin_slug ) ;
			$d .= '<a href="' . esc_url( get_permalink( $product_id ) ) . '">' . esc_html( get_the_title( $product_id ) ) . '</a>';
			$d .= '<br/>';
			
			if( $variation_id > 0 ){

				$d .= __( 'Variation: ', $this->plugin_slug ) ;
				$d .= $this->get_variation_titles( $variation_id );
				$d .= '<br/>';
			}
		}
		
		$admin_d = $d;

		if( $wew_unsubscribe_page_id ){

			$unsubscribe_queryVars = array( 'wewmail' => $user_email, 'wewpid' => $product_id );

			if( $variation_id > 0 ){

				$unsubscribe_queryVars['wewvid'] = $variation_id;
			}

			if( $gparent_id > 0 ){

				$unsubscribe_queryVars['wewgpid'] = $gparent_id;
			}

			$d .= '<a href="' . add_query_arg( $unsubscribe_queryVars , esc_url( get_permalink( $wew_unsubscribe_page_id ) ) ) . '">' . __( 'Unsubscribe from waitlist', $this->plugin_slug ) . '</a>';
		}

		$d .= '<br/><br/><a href="' . esc_url( get_permalink( $product_id ) ) . '">' . get_the_post_thumbnail( $product_id, 'medium' ) . '</a>';
		$admin_d .= '<br/><br/><a href="' . esc_url( get_permalink( $product_id ) ) . '">' . get_the_post_thumbnail( $product_id, 'medium' ) . '</a>';

		$d .= '<br/><br/>';
		$admin_d .= '<br/><br/>';

		$mail_receiver = $user_email;
		$mail_content = $d;

		$email_subject = get_option('wew-subscription-email-subject') ? get_option('wew-subscription-email-subject') : "%product% added to your Waitlist";;
		
		$mail_title = $this->woowaitlist_shortcodes_filter( $email_subject, $product_id, $variation_id );
		
		$this->wew_send_email( $mail_receiver, $mail_title, $mail_content );

		$wew_admin_notification_on_subscription = get_option('wew-admin-notification-on-subscription') && get_option('wew-admin-notification-on-subscription') == "on" ? true : false;
		
		if( $wew_admin_notification_on_subscription ){

			$admin_mail_title = esc_html( get_the_title( $product_id ) ) . " " . __( "Product has been added to a Waitlist", $this->plugin_slug );
			$admin_email = get_option('woocommerce_email_from_address');

			$this->wew_send_email($admin_email, $admin_mail_title, $admin_d );
		}
	}

	/**
	 * Filter content with WooWaitlist shortcodes
	 *
	 * @since    3.0.0
	 *
	 */
	public function woowaitlist_shortcodes_filter( $content, $productId, $variation_id ){

		$c = $content;
		$pid = $productId;
		$vid = $variation_id;

		// Site title - shortcode

		$site_title = get_settings('woocommerce_email_from_name') ? get_settings('woocommerce_email_from_name') : false;

		if( !$site_title ){

			$site_title = get_bloginfo( 'name' );
		}

		// Product title - shortcode

		if( $vid > 0 ){

			$product_title = get_the_title($pid) . "( " . __( "variation:", $this->plugin_slug ) . " " . $this->get_variation_titles( $vid ) . " )";
		}
		else{

			$product_title = get_the_title($pid);
		}

		// Product page (link) - shortcode

		$product_page_link = '<a href="' . esc_url( get_permalink($pid) ) .'">' . esc_html( get_the_title($pid) ) . '</a>';


		$c = str_replace( '%product%', $product_title, $c );

		$c = str_replace( '%product page%', $product_page_link, $c );

		$c = str_replace( '%site title%', $site_title, $c );

		return $c;
	}

	/**
	 * Remove record from waitlist
	 *
	 * @since     1.0.0
	 * 
	 * @param     string 	$user_email 	email address
	 *			  integer 	$product_id   	product id
	 *			  integer   $variation_id   variation id
	 *
	 */
	public function remove_record_from_waitlist( $user_email, $product_id, $variation_id = 0 ){

		global $wpdb;

		$wew_DBtable_name = $wpdb->prefix . 'woocommerce_waitlist';

		if( $variation_id > 0 ){

			$sql = $wpdb->prepare( "DELETE FROM " . $wew_DBtable_name . " WHERE email = %s AND productId = %d AND variationId = %d LIMIT 1;", $user_email, $product_id, $variation_id );
		}
		else{
			
			$sql = $wpdb->prepare( "DELETE FROM " . $wew_DBtable_name . " WHERE email = %s AND productId = %d LIMIT 1;", $user_email, $product_id );
		}

		$delete = $wpdb->query( $sql );

		return $delete;
	}

	/**
	 * Unsubscribe page shortcode
	 *
	 * @since     1.0.0
	 *
	 * @return    Unsubscribe HTML content | Redirects to home page if user email and product id is not in waitlist
	 *
	 */
	public function woowaitlist_unsubscribe_shortcode(){
		
		$url_email = false;
		$url_productId = false;

		if( get_query_var( 'wewmail' ) ){

			$url_email = urldecode( get_query_var( 'wewmail' ) );
		}
		elseif( isset( $_GET['wewmail'] ) ){

			$url_email = urldecode( $_GET['wewmail'] );
		}

		if( get_query_var( 'wewpid' ) ){
			
			$url_productId = intval( get_query_var( 'wewpid' ) );
		}
		elseif( isset( $_GET['wewpid'] ) ){

			$url_productId = intval( $_GET['wewpid'] );
		}

		$url_variationId = 0;

		if( get_query_var( 'wewvid' ) ){
			
			$url_variationId = intval( get_query_var( 'wewvid' ) );
		}
		elseif( isset( $_GET['wewvid'] ) ){

			$url_variationId = intval( $_GET['wewvid'] );
		}

		if( get_query_var( 'wewgpid' ) ){
			
			$url_gparentId = intval( get_query_var( 'wewgpid' ) );
		}
		elseif( isset( $_GET['wewgpid'] ) ){

			$url_gparentId = intval( $_GET['wewgpid'] );
		}

		if( $url_email && is_email( $url_email ) && $url_productId ){

			$delete = $this->remove_record_from_waitlist( $url_email, $url_productId, $url_variationId );
			
			if( $delete ){
				
				$productLink .= '<a href="' . esc_url( get_permalink( $url_productId ) ) . '" target="_blank">' . esc_html( get_the_title( $url_productId ) ) . '</a>';

				$d = '<div class="wew-unsubscribe-wrapper">';

				if( $url_gparentId ){

					$parent_title = get_the_title( $url_gparentId );
					$parentLink .= '<a href="' . esc_url( get_permalink( $url_gparentId ) ) . '" target="_blank">' . esc_html( $parent_title ) . '</a>';

					$d .= '<p>Your email <strong>' . $url_email . '</strong> removed from notification list for grouped product ' . $parentLink;
					$d .= ' ( ' . __('child product:', $this->plugin_slug ) . ' ' . $productLink . ' )';
				}
				else{

					$d .= '<p>Your email <strong>' . $url_email . '</strong> removed from notification list for product ' . $productLink;

					if( $url_variationId ){

						$d .= ' ( ' . __('variation:', $this->plugin_slug ) . ' ' . $this->get_variation_titles( $url_variationId ) . ' )';
					}
				}
				

				$d .= '.</p>';
				$d .= '</div>';

				return $d;
			}
		}
		
		wp_safe_redirect( get_home_url() );
	}

	/**
	 * Save to waitlist - Ajax call
	 *
	 * @since     1.0.0
	 * 
	 * @param     string 		$user_email 	email address
	 *			  integer 		$product_id   	product id	
	 *
	 * @return    json object 	Status and messages from save action
	 */
	public function wew_save_to_db_callback(){

		$ret = array();
		$ret['error'] = false;
		$ret['message'] = "";


		if( isset( $_POST['pid'] ) && ( isset( $_POST['uemail'] ) || is_user_logged_in() ) && isset( $_POST['is_variation'] ) && isset( $_POST['variation_id'] ) ){

			global $wpdb, $current_user;

			$product_id = absint( $_POST['pid'] );

			$user_email = is_user_logged_in() ? $current_user->user_email : trim( $_POST['uemail'] );

			if( absint( $_POST['is_variation'] ) == 1 ){

				$product_variation_id = absint( $_POST['variation_id'] );
			}
			else{

				$product_variation_id = 0;
			}

			if( absint( $_POST['is_grouped'] ) == 1 ){

				$grouped_parent_id = absint( $_POST['parent_id'] );
			}
			else{

				$grouped_parent_id = 0;
			}

			if( is_email( $user_email ) ){

				$wew_DBtable_name = $wpdb->prefix . 'woocommerce_waitlist';

				if( $product_variation_id ){

					$exists_sql = $wpdb->prepare( "SELECT * FROM " . $wew_DBtable_name . " WHERE email = %s AND productId = %d AND variationId = %d LIMIT 1;", $user_email, $product_id, $product_variation_id );
				}
				else if( $grouped_parent_id ){

					$exists_sql = $wpdb->prepare( "SELECT * FROM " . $wew_DBtable_name . " WHERE email = %s AND productId = %d AND grouped_parentId = %d LIMIT 1;", $user_email, $product_id, $grouped_parent_id );
				}
				else{

					$exists_sql = $wpdb->prepare( "SELECT * FROM " . $wew_DBtable_name . " WHERE email = %s AND productId = %d LIMIT 1;", $user_email, $product_id );
				}

				$exists = $wpdb->get_row( $exists_sql );

				if( !$exists ){

					if( $product_variation_id > 0 ){

						$sql = $wpdb->prepare( "INSERT INTO " . $wew_DBtable_name . "( email, productId, variationId ) VALUES ( %s, %d, %d )", $user_email, $product_id, $product_variation_id );
					}
					else if( $grouped_parent_id > 0 ){
						
						$sql = $wpdb->prepare( "INSERT INTO " . $wew_DBtable_name . "( email, productId, grouped_parentId ) VALUES ( %s, %d, %d )", $user_email, $product_id, $grouped_parent_id );
					}
					else{

						$sql = $wpdb->prepare( "INSERT INTO " . $wew_DBtable_name . "( email, productId ) VALUES ( %s, %d )", $user_email, $product_id );
					}

					$exe = $wpdb->query( $sql );

					if( $exe ){

						$ret['send_email'] = self::wew_email_onWaitlistAdd( $user_email, $product_id, $product_variation_id, $grouped_parent_id );

						$ret['code'] = 1;

						if( $product_variation_id ){
							
							$svariation_success = get_option('wew-subscribe-variation-success-msg') ? get_option('wew-subscribe-variation-success-msg') : __( "Your email address has been saved and you will be notified when the product variation is back in stock", $plugin_slug );

							$ret['message'] = $svariation_success;
						}
						else{

							$sproduct_success = get_option('wew-subscribe-product-success-msg') ? get_option('wew-subscribe-product-success-msg') : __( 'Your email address has been saved and you will be notified when the product is back in stock', $plugin_slug );

							$ret['message'] = $sproduct_success;
						}
					}
					else{

						$ret['code'] = 2;
						$ret['error'] = true;

						if( $product_variation_id ){
							
							$ret['message'] = __( "An error occured on saving product's variation notification data. Try again or contact site's administrator", $this->plugin_slug );
						}
						else{

							$ret['message'] = __( "An error occured on saving product's notification data. Try again or contact site's administrator", $this->plugin_slug );
						}
						
					}
				}
				else{

					$ret['code'] = 3;
					$ret['error'] = true;

					if( $product_variation_id ){
						
						$already_subscribed_variation = get_option('wew-already-variation-subscribed') ? get_option('wew-already-variation-subscribed') : __( "You have already subscribed to this product variation waitlist", $plugin_slug );

						$ret['message'] = $already_subscribed_variation;
					}
					else{

						$already_subscribed_product = get_option('wew-already-product-subscribed') ? get_option('wew-already-product-subscribed') : __( "You have already subscribed to this product waitlist", $plugin_slug );

						$ret['message'] = $already_subscribed_product;
					}
					
				}
				
			}
			else{

				$ret['code'] = 4;
				$ret['error'] = true;
				$emptyEmail_alert = get_option('wew-no-email-alert') ? get_option('wew-no-email-alert') : __( "Email Address is Required", $plugin_slug );
				$ret['message'] = $emptyEmail_alert;
			}
		}
		else{

			$ret['code'] = 5;
			$ret['error'] = true;
			$ret['message'] = __( "Not acceptable data.", $this->plugin_slug );
		}

		$ret = json_encode( $ret );

		die( $ret );
	}

	/**
	 * Save to waitlist - Ajax call
	 *
	 * @since     1.1.0
	 * 
	 * @param     integer 		$vid   	variation id
	 *
	 * @return    string 	variation title(s)
	 */
	public function get_variation_titles( $vid ){

		$r = false;

		$display_variations = array();

		$variation_meta = get_post_meta( $vid );

		if( $variation_meta ){

			foreach ( $variation_meta as $x => $y ) {
				
				if( strpos( $x, "attribute_" ) === 0 ){

					$display_variations[] = $y[0];
				}
			}
		}

		if( !empty( $display_variations ) ){

			foreach ( $display_variations as $a => $b ) {

				$display_variations[$a] = ucfirst( str_replace('-', " ", $b ) );
			}

			$r = implode( ' - ', $display_variations );	
		}
		
		return $r;
	}

	/**
	 * Display custom "Out of stock" message - WordPress/WooCommerce Hook 
	 *
	 * @since     1.0.0
	 * 
	 * @param     array 	$availability
	 *			  object 	$_product
	 *
	 * @return    html 		"Out of stock" html
	 */
	public function wew_check_product_availability( $availability, $_product ){

		$disable_woowaitlist_subscription = get_post_meta( $_product->id, 'disable_woowaitlist_subscription', true );

		$disable_woowaitlist_subscription = $disable_woowaitlist_subscription == 'on' ? 1 : 0;

		if( !$disable_woowaitlist_subscription ){

			$plugin_slug = $this->get_plugin_slug();

			global $post, $wp_query;

			$subscribe_backorders = false;

			$isVariation = $_product->variation_id ? true : false;

			$isGrouped = intval( $_product->post->post_parent ) > 0 && $wp_query->queried_object->ID == $_product->post->post_parent ? true : false;

			$_productID = $isVariation ? $_product->post->ID : $_product->id ;
			$_productTitle = get_the_title( $_productID );
			$_productPermalink = esc_url( get_permalink( $_productID ) );
			$display_outOfStock = ( !$this->productStock_quantity( $_productID, false, false ) ) ? true : false;	
			
			$display_variationOutOfStock = ( $isVariation && intval( $_product->total_stock ) == 0 && $_product->total_stock != null ) ? true : false ;
		
			$bo = false;
			$bo = get_post_meta( $_productID, '_backorders', true );

			if( $bo != 'no' ){

				$quantity = 1;

				if( $isVariation ){

					$quantity = intval( $_product->total_stock );
				}
				else{

					$quantity = intval( get_post_meta($_productID, '_stock',true) );
				}

				if( $quantity == 0 ){
						
					$subscribe_backorders = true;
				}
			}

			if( $subscribe_backorders ){

				$subscibe_backorders_setting = get_option('wew-subscribe-backorders') && get_option('wew-subscribe-backorders') == "on" ? true : false;
				
				if( !$subscibe_backorders_setting || $bo == 'yes' ){
					
					$subscribe_backorders = false;
				}
			}

			$display_msg = "";
			$display_classes = "";
			$notify_msg = "";

			if( !$isVariation || ( $display_outOfStock && $this->get_hc() == 0 ) || ( $display_outOfStock && $this->get_hc() > 0 && $isGrouped ) ){

				if( $this->get_hc() == 0 || ( $this->get_hc() > 0 && $isGrouped ) ){
					
					if( $isGrouped ){

						$oosType = "grouped";
					}
					else{

						$oosType = "stock";
					}

					?>
					
					<input type="hidden" class="wew-id-for-notify" value="<?php echo $_productID; ?>" />
					
					<?php

					if( $this->get_hc() == 0 ){

						if( $isGrouped ){ ?>
							
							<input type="hidden" id="oos-parentid" value="<?php echo esc_attr( $_product->post->post_parent ); ?>" /><?php
						}

						?>
						<input type="hidden" id="oos-type" value="<?php echo esc_attr( $oosType ); ?>" />
						<input type="hidden" name="wew-is-logged-in" id="wew-is-logged-in" value="<?php echo is_user_logged_in(); ?>" /><?php
					}

				}

				if( $display_outOfStock || $subscribe_backorders ){	// product has NOT stock
					
					if( $subscribe_backorders ){

						$display_msg = $availability['availability'];

						$display_classes = " stock in-msg " . $availability['class'] . " ";
					}
					else{

						$display_msg = __( 'Out of stock', $plugin_slug );

						$display_classes = " stock out-msg " . $availability['class'] . " ";
					}

					$notify_msg = get_option('wew-out-of-stock-message') ? get_option('wew-out-of-stock-message') : __('Notify me when item is back in stock', $plugin_slug );

					echo '<p class="'.$display_classes.'">'.$display_msg.'</p>';

					echo '<p class="oos-message">' . $notify_msg . '</p>';

					$display_form = '<div class="wew-notification-action_wrapper">';					

					if( is_user_logged_in() ){

						$display_form .= '<button id="wew-submit-email-to-notify" class="add_to_cart_button logged button">' . $this->get_subscribe_button_str() . '</button>';
					}
					else{

						$display_form .= '<input type="text" name="wew-email-to-notify" class="wew-email-to-notify" placeholder="' . __( "Insert email address", $this->plugin_slug ) . '" />';

						$display_form .= '<button id="wew-submit-email-to-notify" class="add_to_cart_button button">' . __( "Subscribe", $this->plugin_slug ) . '</button>';
					}

					$display_form .= '</div>';

					echo $display_form;

					$this->set_hc( intval( $this->get_hc() ) + 1 );

					remove_filter( 'woocommerce_get_availability', array( $this, 'wew_check_product_availability' ) );

					return;
				}
				else{ 	// product HAS stock

					$display_msg = $availability['availability'];

					$display_classes = " stock in-msg " . $availability['class'] . " ";
				}

			}
			elseif( $isVariation && !$display_outOfStock ){

				if( $this->get_hc() == 0 ){
					
					$oosType = "variation";

					?>
					
					<input type="hidden" id="oos-type" value="<?php echo esc_attr( $oosType ); ?>" />
					<input type="hidden" class="wew-id-for-notify" value="<?php echo $_productID; ?>" />
					<input type="hidden" name="wew-is-logged-in" id="wew-is-logged-in" value="<?php echo is_user_logged_in(); ?>" /><?php
				}

				if( $display_variationOutOfStock || $subscribe_backorders ){	 	// variation has NOT stock

					$cntv = 0;
					$variation_title = "";

					if( $_product->variation_data ){

						foreach ( $_product->variation_data as $a => $b ) {

							$cntv++;

							if( trim($b) != "" ){

								if( $cntv > 1 && $cntv <= count( $_product->variation_data ) ){

									$variation_title .= " - ";
								}

								$variation_title .= '<strong>' . $b . '</strong> ' ;
							}

						}

					}

					if( $bo == 'no' ){
					
						$notify_msg = get_option('wew-variations-out-of-stock-message') ? get_option('wew-variations-out-of-stock-message') : __('Notify me when item variation is back in stock', $plugin_slug ) ;

						$display_msg = __( 'Out of stock', $plugin_slug );
						$display_classes = " stock out-msg " . $availability['class'] . " ";

						$display_msg .= '<p class="oos-message">' . $notify_msg . '</p>';
						$display_msg .= '<div class="wew-notification-action_wrapper variations"></div>';
					}
					else{

						$display_msg = trim( $availability['availability'] ) != '' ? $availability['availability'] : '';

						if( $subscribe_backorders ){

							$notify_msg = get_option('wew-variations-out-of-stock-message') ? get_option('wew-variations-out-of-stock-message') : __('Notify me when item variation is back in stock', $plugin_slug ) ;

							$display_msg .= '<p class="oos-message">' . $notify_msg . '</p>';

							$display_msg .= '<div class="wew-notification-action_wrapper variations"></div>';
						}

						
						$display_classes = $availability['class'];
					}
				}
				else{ 	// variation HAS stock
					$display_msg = $availability['availability'];
					$display_classes = " stock in-msg " . $availability['class'] . " ";
				}

			}

			$availability['availability'] = $display_msg ;
			$availability['class'] = $display_classes;

			$this->set_hc( intval( $this->get_hc() ) + 1 );

		}

		remove_filter( 'woocommerce_get_availability', array( $this, 'wew_check_product_availability' ) );

		return $availability;
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {

		return $this->plugin_slug;
	}

	/**
	 * Return waitlist data page url slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Waitlist data page url slug.
	 */
	public function get_plugin_slug_data() {
		
		return $this->plugin_slug_data;
	}

	/**
	 * Return waitlist data page url slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Waitlist data page url slug.
	 */
	public function get_mail_product_glData() {

		return $this->mail_product_glData;
	}

	/**
	 * Set waitlist data page url slug.
	 *
	 * @since    1.0.0
	 *
	 */

	public function set_mail_product_glData( $d ) {

		$this->mail_product_glData = $d;
	}

	/**
	 * Return subscribe button string.
	 *
	 * @since    2.1.5
	 *
	 * @return    string.
	 */
	public function get_subscribe_button_str() {

		return $this->subscribe_button_str;
	}

	/**
	 * Set subscribe button string.
	 *
	 * @since    2.1.5
	 *
	 */

	public function set_subscribe_button_str() {
		
		$saved_str = get_option('wew-subscribe-to-waitlist-str') ? get_option('wew-subscribe-to-waitlist-str') : '_undefined_';

		if( $saved_str != '_undefined_' ){
			
			$this->subscribe_button_str = trim( $saved_str );
		}
	}

	/**
	 * Return waitlist data page url slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Waitlist data page url slug.
	 */
	public function get_hc() {

		return $this->hc;
	}

	/**
	 * Set waitlist data page url slug.
	 *
	 * @since    1.0.0
	 *
	 */
	public function set_hc( $d ) {

		$this->hc = $d;
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
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();
				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );
	}

	/**
	 * Create plugin database table
	 *
	 * @since    1.0.0
	 *
	 */
	private static function createPlugin_databaseTable(){

		global $wpdb, $charset_collate;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$wew_DBtable_name = $wpdb->prefix . 'woocommerce_waitlist';


		$sql = "show tables like '" . $wew_DBtable_name . "' ;";

		$res = $wpdb->get_results( $sql );

		if($res){

			$sql2 = "SHOW COLUMNS FROM " . $wew_DBtable_name . " LIKE 'variationId' ;";

			$res2 = $wpdb->get_results( $sql2 );

			if( !$res2 ){

				$variationColumn_sql = "ALTER TABLE " . $wew_DBtable_name . " ADD variationId BIGINT(20) NOT NULL DEFAULT 0;";

				$exe = $wpdb->query( $variationColumn_sql );
			}

			$sql3 = "SHOW COLUMNS FROM " . $wew_DBtable_name . " LIKE 'email_productId' ;";
			$res3 = $wpdb->get_results( $sql3 );

			if( $res3 ){
				// Remove table index from previous plugin version
				$sql4 = "DROP INDEX `email_productId` ON " . $wew_DBtable_name . " ;";
				$update_exe = $wpdb->query( $sql4 );
			}

			$sql5 = "SHOW COLUMNS FROM " . $wew_DBtable_name . " LIKE 'grouped_parentId' ;";

			$res5 = $wpdb->get_results( $sql5 );

			if( !$res5 ){

				$variationColumn_sql = "ALTER TABLE " . $wew_DBtable_name . " ADD grouped_parentId BIGINT(20) NOT NULL DEFAULT 0;";

				$exe = $wpdb->query( $variationColumn_sql );
			}

		}
		else{

			$sql = "CREATE TABLE IF NOT EXISTS " . $wew_DBtable_name . " (
						`id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
						`email` VARCHAR(100) NOT NULL,
						`productId` BIGINT(20) NOT NULL,
						`variationId`  BIGINT(20) NOT NULL DEFAULT 0,
						`grouped_parentId`  BIGINT(20) NOT NULL DEFAULT 0,
						`date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
						PRIMARY KEY (`id`)
					)" . $charset_collate . " ; ";
			
			dbDelta( $sql );			
		}
	}
	
	/**
	 * Return product "in stock"
	 *
	 * @since    1.0.0
	 *
	 * @param    integer 	$pid 	product id
	 *  	     integer 	$vid 	variation id
	 * @return   boolean	
	 */
	public function productStock_quantity( $pid, $vid = 0, $email_notification = false ){
		
		$r = false;

		$back_in_stock_quantity = get_option('wew-back-in-stock_quantity') ? intval( get_option('wew-back-in-stock_quantity') ) : 0;

		if( $vid > 0 ){

			$variation_meta = get_post_meta( $vid );

			$quantity = isset( $variation_meta['_stock'] ) && isset( $variation_meta['_stock'][0] ) ? intval( $variation_meta['_stock'][0] ) : 0;

			if( $variation_meta && 
				isset( $variation_meta['_stock'] ) && 
				isset( $variation_meta['_stock'][0] ) && 
				( $variation_meta['_stock'][0] == null || $quantity > 0 ) 
				)
			{

				if( get_post_meta($pid, '_stock_status',true) == 'instock' ){

					if( $email_notification ){

						if( $quantity >= $back_in_stock_quantity ){
						
							$r = true;
						}
					}
					else{
						
						$r = true;
					}
				}
			}

		}
		else{

			$quantity = intval( get_post_meta($pid, '_stock',true) );
			
			if( get_post_meta($pid, '_manage_stock',true) == 'yes' && $quantity > 0 ){

				if( $email_notification ){

					if( $quantity >= $back_in_stock_quantity ){
					
						$r = true;
					}
				}
				else{
				
					$r = true;
				}
			}
			elseif( get_post_meta($pid, '_stock_status',true) == 'instock' ){

				$r = true;
			}

		}
		
		return $r;
	}

	/**
	 * Return all products that are in waitlist
	 *
	 * @since     1.0.0
	 *
	 * @return    array|false
	 */
	private static function getProducts_inWaitlist(){

		global $wpdb;

		$ret = false;

		$wew_DBtable_name = $wpdb->prefix . 'woocommerce_waitlist';

		$sql = "SELECT DISTINCT productId FROM " . $wew_DBtable_name . " WHERE variationId = 0 ; ";

		$results = $wpdb->get_results( $sql, ARRAY_A );

		if( $results ){

			$t = array();

			foreach( $results as $k=>$v ){

				$t[] = $v['productId'] ;
			}

			$ret = $t;
		}

		return $ret;
	}

	/**
	 * Return all products that are in waitlist and are in grouped product
	 *
	 * @since     1.0.0
	 *
	 * @return    array|false
	 */
	private static function getGroupedProducts_inWaitlist(){

		global $wpdb;

		$ret = false;

		$wew_DBtable_name = $wpdb->prefix . 'woocommerce_waitlist';

		$sql = "SELECT DISTINCT productId FROM " . $wew_DBtable_name . " WHERE grouped_parentId <> 0 ; ";

		$results = $wpdb->get_results( $sql, ARRAY_A );

		if( $results ){

			$t = array();

			foreach( $results as $k=>$v ){

				$t[] = $v['productId'] ;
			}

			$ret = $t;
		}

		return $ret;
	}

	/**
	 * Return all products with variations that are in waitlist
	 *
	 * @since     1.0.0
	 *
	 * @return    array|false
	 */
	private static function getProducts_inwaitlist_forVariations(){

		global $wpdb;

		$ret = false;

		$wew_DBtable_name = $wpdb->prefix . 'woocommerce_waitlist';

		$sql = "SELECT DISTINCT productId, variationId FROM " . $wew_DBtable_name . " WHERE variationId != 0 ; ";

		$results = $wpdb->get_results( $sql, ARRAY_A );

		if( $results ){

			$t = array();

			foreach( $results as $k=>$v ){

				$t['product'][$k] = $v['productId'] ;
				$t['variation'][$k] = $v['variationId'] ;
			}

			$ret = $t;
		}

		return $ret;

	}

	/**
	 * Send "back in stock" email to users
	 *
	 * @since     1.0.0
	 * 
	 * @param     string 	$email 	email address
	 *			  integer 	$pid 	product id
	 *			  integer 	$vid 	variable id
	 *			  integer 	$gparent_id 	grouped parent id
	 */
	private function send_backInStock_email( $email, $pid, $vid = 0, $gparent_id = 0 ){

		if( is_email( $email ) ){

			$notify_available_product = get_option('wew-notify-available-product') ? get_option('wew-notify-available-product') : __("Your product %product% is back in stock, don't miss out and visit the %product page% .", $plugin_slug );

			$d = $this->woowaitlist_shortcodes_filter( $notify_available_product, $pid, $vid );

			$d = '<br/>' . $d;

			$d .= '<br/><br/><a href="' . esc_url( get_permalink( $product_id ) ) . '">' . get_the_post_thumbnail( $pid, 'medium' ) . '</a><br/><br/>';

			$mail_receiver = $email;

			if( $vid > 0 ){

				$mail_title = __( "Product variation is available", $this->plugin_slug );
			}
			elseif( $gparent_id > 0 ){

				$mail_title = __( "Grouped product is available", $this->plugin_slug );

				$productObj = get_post( $pid );
				$product_title = $productObj->post_title;
				$parentObj = get_post( $gparent_id );
				$parent_title = $parentObj->post_title;
				$parent_permalink = get_permalink( $gparent_id );

				$d = __( 'Your Grouped Child Product' );
				$d .= ' – ' . $parent_title . '( ' . $product_title . ' ) ';
				$d .= __( 'is back in stock', $this->plugin_slug );
				$d .= ', ' . __( "don't miss out and visit the Grouped Product", $this->plugin_slug ) . " – ";
				$d .= '<a href="' . esc_url( $parent_permalink ) . '">' . esc_html( $parent_title ) . '</a>';
			}
			else{

				$mail_title = __( "Product is available", $this->plugin_slug );
			}
			
			$mail_content = $d;

			if( $this->remove_record_from_waitlist( $email, $pid, $vid ) ){
				
				$this->wew_send_email( $mail_receiver, $mail_title, $mail_content );
			}
			
		}
	}

	/**
	 * Set plugin sender email - Wordpress Hook
	 *
	 * @since    1.0.0
	 *
	 * @param    integer 	$email
	 *
	 * @return   string 	email	
	 */
	public function wew_wp_mail_from( $email ){
		
		$use_custom_sender_email = !get_option('woowaitilist-default-email-options') || get_option('woowaitilist-default-email-options') == "on" ? true : false;
		
		if( !$use_custom_sender_email ){

			$inserted_email = get_option('notifications-sender-email') && is_email( get_option('notifications-sender-email') ) ? get_option('notifications-sender-email') : false;	
		}
		else{

			$inserted_email = false;
		}

	    $email = $inserted_email ? $inserted_email : get_settings('woocommerce_email_from_address') ;
	    $email = is_email($email);
	    return $email;
	}

	/**
	 * Set plugin email sender name - Wordpress Hook
	 *
	 * @since    1.0.0
	 *
	 * @param    integer 	$email
	 *
	 * @return   string 	email	
	 */
	public function wew_wp_mail_from_name( $from ){
		
		$email_from = get_option('wew-subscription-email-from-name') ? get_option('wew-subscription-email-from-name') : "%site title% Waitlist";

		$t = $this->get_mail_product_glData();

		$product_id = $t['product_id'];
		$variation_id = $t['variation_id'];

		$email_from = $this->woowaitlist_shortcodes_filter( $email_from, $product_id, $variation_id );

		return $email_from;
	}

	/**
	 * Send html format email
	 *
	 * @since    1.0.0
	 *
	 * @param    string 	$receiver 	email receiver email
	 * 			 string 	$title      email subject
	 *		     string 	$content    email content in html format
	 *
	 */
	public function wew_send_email( $receiver, $title, $content ){

		$message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <html>
                    <head>
                    <title>' . esc_html( $title ) . '</title>
                    </head>
                    <body>'
                    . $content . 
                    '</body>
                    </html>';

        add_filter( 'wp_mail_from', array( $this, 'wew_wp_mail_from' ) );
        add_filter( 'wp_mail_from_name', array( $this, 'wew_wp_mail_from_name' ) );
	    
	    add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );

	    wp_mail( $receiver, $title, $message );

	    remove_filter( 'wp_mail_from', array( $this, 'wew_wp_mail_from' ) );
        remove_filter( 'wp_mail_from_name', array( $this, 'wew_wp_mail_from_name' ) );
	}	

	/**
	 * Check if product exists in waitlist
	 *
	 * @since     1.0.0
	 * 
	 * @param     integer 	$pid 	product id
	 *			  integer 	$vid 	variable id
	 *			  integer 	$gparent_id 	grouped parent id
	 *
	 */
	public function backInStock_usersNotification( $pid, $vid = 0, $gparent_id = false ){

		global $wpdb;

		$wew_DBtable_name = $wpdb->prefix . 'woocommerce_waitlist';

		if( $gparent_id  ){

			$sql = "SELECT DISTINCT email, grouped_parentId FROM " . $wew_DBtable_name . " WHERE productId = %d AND grouped_parentId <> 0; ";
			$sql = $wpdb->prepare( $sql, $pid);
		}
		elseif( $vid > 0 ){

			$sql = "SELECT DISTINCT email FROM " . $wew_DBtable_name . " WHERE productId = %d AND variationId = %d ; ";
			$sql = $wpdb->prepare( $sql, $pid, $vid );
		}
		else{
			
			$sql = "SELECT DISTINCT email FROM " . $wew_DBtable_name . " WHERE productId = %d AND variationId = 0 ; ";
			$sql = $wpdb->prepare( $sql, $pid );
		}

		$results = $wpdb->get_results( $sql );

		if( $results ){

			if( $this ){

				$this_instance = $this;
			}
			else{

				$this_instance = self::get_instance();
			}

			foreach( $results as $k=>$v ){

				$this_instance->send_backInStock_email( $v->email, $pid, $vid, $v->grouped_parentId );
			}

		}
	}
	

	/**
	 * Check if products stock changed and notify user
	 *
	 * @since     1.0.0
	 * 
	 * @param     integer 	$product_id		product id
	 *			  integer 	$gparent_id 	grouped parent id
	 *
	 */
	public function checkGroupedStocks_toNotifyUsers( $product_id = false, $gparent_id = false ){
		
		$waitlistPorducts = self::getGroupedProducts_inWaitlist();

		if( $waitlistPorducts ){

			$args = array(
			    'posts_per_page' => -1,
			    'post_type' => 'product',
			    'orderby' => 'title',
			    'post__in' => $waitlistPorducts
			);

			if( $product_id ){

				$args['p'] = $product_id;
			}

			$the_query = new WP_Query( $args );

			if( $the_query->posts ){

				foreach( $the_query->posts as $product ){

					$ins = Woocommerce_Waitlist::get_instance();
						
					$pInStock = $ins->productStock_quantity( $product->ID, false, true );

					if( $pInStock ){

						$ins->backInStock_usersNotification( $product->ID, 0, true );
					}

				}

			}

		}
	}

	/** 
	 * Check if products stock changed and notify user 
	 * 
	 * @since     1.0.0 
	 * 
	 * @param     integer 	$product_id		product id 
	 * 
	 */
	public function checkStocks_toNotifyUsers( $product_id = false ){
		
		$waitlistPorducts = self::getProducts_inwaitlist();

		if( $waitlistPorducts ){

			$args = array(
			    'posts_per_page' => -1,
			    'post_type' => 'product',
			    'orderby' => 'title',
			    'post__in' => $waitlistPorducts
			);

			if( $product_id ){

				$args['p'] = $product_id;
			}

			$the_query = new WP_Query( $args );

			if( $the_query->posts ){
				
				foreach( $the_query->posts as $product ){

					if($this){

						$pInStock = $this->productStock_quantity( $product->ID, false, true );

						if( $pInStock ){

							$this->backInStock_usersNotification( $product->ID, false );
						}
					}
					else{

						$pInStock = self::productStock_quantity( $product->ID, false, true );

						if( $pInStock ){

							self::backInStock_usersNotification( $product->ID, false );
						}

					}

				}

			}

		}
	}

	/**
	 * Check if products with variations stock changed and notify user
	 *
	 * @since     1.1.0
	 * 
	 * @param     integer 	$product_id		product id
	 *
	 */
	public function checkVariationsStocks_toNotifyUsers( $product_id = false ){

		$waitlistPorducts = self::getProducts_inwaitlist_forVariations();

		if( $waitlistPorducts ){
		
			$args = array(
			    'posts_per_page' => -1,
			    'post_type' => 'product',
			    'orderby' => 'title',
			    'post__in' => $waitlistPorducts['product']
			);

			if( $product_id ){

				$args['p'] = $product_id;
			}

			$the_query = new WP_Query( $args );

			if( $the_query->posts ){

				foreach( $the_query->posts as $product ){

					$product_variation_id = false;

					foreach ( $waitlistPorducts['product'] as $s => $f ) {

						if( intval( $f ) == $product->ID ){

							$product_variation_id = $waitlistPorducts['variation'][$s];
						}
					}

					if( $product_variation_id ){

						if($this){

							$pInStock = $this->productStock_quantity( $product->ID, $product_variation_id, true );

							if( $pInStock ){

								$this->backInStock_usersNotification( $product->ID, $product_variation_id );
							}

						}
						else{

							$pInStock = self::productStock_quantity( $product->ID, $product_variation_id, true );

							if( $pInStock ){

								self::backInStock_usersNotification( $product->ID, $product_variation_id );
							}

						}
					
					}

				}

			}

		}
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {

		$ins = Woocommerce_Waitlist::get_instance();

		$ins->createPlugin_databaseTable();
		$ins->checkGroupedStocks_toNotifyUsers();
		$ins->checkStocks_toNotifyUsers();
		$ins->checkVariationsStocks_toNotifyUsers();
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;

		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		$path = ABSPATH . 'wp-content/plugins/'.$domain.'/languages/'.$domain.'-'.$locale.'.mo';

		load_textdomain( $domain, $path );
    	load_plugin_textdomain( $domain, FALSE, dirname(plugin_basename(__FILE__)).'/languages/' );
	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( get_bloginfo( 'text_direction' ) == 'ltr' ) {
			
			wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
		}
		else{

			wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public-rtl.css', __FILE__ ), array(), self::VERSION );
		}
	}

	/**
	 * Add custom style.
	 *
	 * @since    3.0.0
	 */
	public function custom_css(){

		$custom_css = get_option('wew-custom-css') ? trim( get_option('wew-custom-css') ) : "";
		
		if( $custom_css != "" ) {
		
			$output = "<style type='text/css'>" . $custom_css . "</style>";

			echo $output;
		}
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		
		wp_localize_script( $this->plugin_slug . '-plugin-script', 
							'wew_ajax_object', 
							array( 
								'ajax_url' 	=> admin_url( 'admin-ajax.php' ),
								'texts'		=> array(
									'subscribe' => $this->get_subscribe_button_str(),
									'insert' 	=> __( "Insert email address", $this->plugin_slug ),
									'proceed' 	=> __( "Subscribe", $this->plugin_slug )
								)
							) 
			);
	}

}