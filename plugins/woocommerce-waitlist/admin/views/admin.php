<?php
/**
 * Represents the view for the administration dashboard.
 *
 * @package   Plugin_Name
 * @author    Your Name <email@example.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 */

$plugin_slug = 'woocommerce-waitlist';

function get_pages_array(){
    
    $r = array();

    $args = array(
        'sort_order' => 'DESC',
        'orderby' => 'date',
        'hierarchical' => 1,
        'child_of' => 0,
        'parent' => -1,
        'offset' => 0,
        'post_type' => 'page',
        'post_status' => 'publish'
    ); 

    $pages = get_pages($args);    

    if( $pages && !empty( $pages ) ){

    	$r = $pages;
    }

    return $r;
}

function woowaitlist_admin_tabs( $current = 'woowaitlist_settings' ) {

	$plugin_slug = 'woocommerce-waitlist';

    $tabs = array( 
    	'woowaitlist_settings' 	=> __( 'WooWaitlist Settings', $plugin_slug ) , 
    	'email_settings' 		=> __( 'Email Settings', $plugin_slug ), 
    	'woowaitlist_data' 		=> __( 'WooWaitlist Data', $plugin_slug )
    );

    echo '<div id="icon-themes" class="icon32"><br></div>';

    echo '<h2 class="nav-tab-wrapper">';

    foreach( $tabs as $tab => $name ){

        $class = ( $tab == $current ) ? ' nav-tab-active' : '';

        if( $tab == "woowaitlist_settings" ){

        	$tab_str = "";
        }
        else{

        	$tab_str = "&tab=$tab";
        }

        echo "<a class='nav-tab$class' href='?page=" . $plugin_slug . $tab_str . "'>$name</a>";

    }
    echo '</h2>';
}

$a = Woocommerce_Waitlist_Admin::get_instance();

$message = $a->get_plugin_msg();
$errorMessage = $message['error'];
$message = $message['message'];

$updated_html = "";

if( $message ){

	$updated_classes = array('updated');

	if($errorMessage){

		$updated_classes[] = 'error';
	}

	$updated_html .= "<div class='" . implode( " ", $updated_classes ) . "'>";
	$updated_html .= $message;
	$updated_html .= "</div>";
}

?>

<div id="woowaitlist-top" class="woowaitlist-settings-container">

	<h1 class="wew-settings-title"><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php

	$current_tab = "woowaitlist_settings";
	$current_tabURL_slug = "?page=" . $plugin_slug;

	if ( isset ( $_GET['tab'] ) ){

		woowaitlist_admin_tabs( $_GET['tab'] ); 

		if( $_GET['tab'] != "woowaitlist_settings" ){

			$current_tab = $_GET['tab'];	
			$current_tabURL_slug .= "&tab=" . $current_tab;
		}
	}
	else{

		woowaitlist_admin_tabs('woowaitlist_settings');
	}

	echo $updated_html;

	wp_nonce_field('woowaitlist-update-settings'); ?>

		<?php
		
		switch( $current_tab ){
			case "woowaitlist_settings":

				$wew_subscribe_to_waitlist_str = get_option('wew-subscribe-to-waitlist-str') ? get_option('wew-subscribe-to-waitlist-str') : __('Subscribe to waitlist', $plugin_slug );
				$subscibe_backorders = get_option('wew-subscribe-backorders') && get_option('wew-subscribe-backorders') == "on" ? 'checked="checked"' : "";
				
				$wew_enable_shop_manager_settings = get_option('wew-enable-shop-manager-settings') && get_option('wew-enable-shop-manager-settings') == "on" ? 'checked="checked"' : "";
				$wew_admin_notification_on_subscription = get_option('wew-admin-notification-on-subscription') && get_option('wew-admin-notification-on-subscription') == "on" ? 'checked="checked"' : "";
				$wew_unsubscribe_page = get_option('wew-unsubscribe-page') ? get_option('wew-unsubscribe-page') : 0 ;
				$wew_remove_data_on_uninstall = get_option('wew-remove-waitlist-on-uninstall') && get_option('wew-remove-waitlist-on-uninstall') == "on" ? 'checked="checked"' : "";
				$sites_allPages = get_pages_array();
				$out_of_stock_message = get_option('wew-out-of-stock-message') ? get_option('wew-out-of-stock-message') : __('Notify me when item is back in stock', $plugin_slug );
				$out_variation_of_stock_message = get_option('wew-variations-out-of-stock-message') ? get_option('wew-variations-out-of-stock-message') : __('Notify me when item variation is back in stock', $plugin_slug );
				$sproduct_success = get_option('wew-subscribe-product-success-msg') ? get_option('wew-subscribe-product-success-msg') : __( 'Your email address has been saved and you will be notified when the product is back in stock', $plugin_slug );
				$svariation_success = get_option('wew-subscribe-variation-success-msg') ? get_option('wew-subscribe-variation-success-msg') : __( "Your email address has been saved and you will be notified when the product variation is back in stock", $plugin_slug );
				$already_subscribed_product = get_option('wew-already-product-subscribed') ? get_option('wew-already-product-subscribed') : __( "You have already subscribed to this product waitlist", $plugin_slug );
				$already_subscribed_variation = get_option('wew-already-variation-subscribed') ? get_option('wew-already-variation-subscribed') : __( "You have already subscribed to this product variation waitlist", $plugin_slug );
				$emptyEmail_alert = get_option('wew-no-email-alert') ? get_option('wew-no-email-alert') : __( "Email Address is Required", $plugin_slug );
				$custom_css = get_option('wew-custom-css') ? get_option('wew-custom-css') : "";

				?>

				<form method="post" action="<?php echo $current_tabURL_slug; ?>" class="woowaitlist-settings-form">

					<div class="woowaitilist-sw">
						<label><?php _e('Subscribe to waitlist', $plugin_slug ); ?></label>&nbsp;
						<input type="text" name="wew-subscribe-to-waitlist-str" id="wew-subscribe-to-waitlist-str" value="<?php echo $wew_subscribe_to_waitlist_str; ?>" />
					</div>

					<hr/>

					<div class="woowaitilist-sw">
						<label><?php _e('Simple Product "Out of stock" Notification', $plugin_slug ); ?></label>&nbsp;
						<input type="text" name="wew-out-of-stock-message" id="wew-out-of-stock-message" value="<?php echo $out_of_stock_message; ?>" />
					</div>

					<hr/>

					<div class="woowaitilist-sw">
						<label for="wew-variations-out-of-stock-message"><?php _e('Product Variable "Out of Stock" Notification', $plugin_slug ); ?></label>&nbsp;
						<input type="text" name="wew-variations-out-of-stock-message" id="wew-variations-out-of-stock-message" value="<?php echo $out_variation_of_stock_message; ?>" />
					</div>

					<hr/>

					<div class="woowaitilist-sw">
						<label for="wew-subscribe-product-success-msg"><?php _e('Subscribed Success Message for Simple Product', $plugin_slug ); ?></label>&nbsp;
						<input type="text" name="wew-subscribe-product-success-msg" id="wew-subscribe-product-success-msg" value="<?php echo $sproduct_success; ?>" />
					</div>

					<hr/>

					<div class="woowaitilist-sw">
						<label for="wew-subscribe-variation-success-msg"><?php _e('Subscribed Success Message for Product Variation', $plugin_slug ); ?></label>&nbsp;
						<input type="text" name="wew-subscribe-variation-success-msg" id="wew-subscribe-variation-success-msg" value="<?php echo $svariation_success; ?>" />
					</div>

					<hr/>

					<div class="woowaitilist-sw">
						<label for="wew-already-product-subscribed"><?php _e('Already Subscribed Message for Simple Product', $plugin_slug ); ?></label>&nbsp;
						<input type="text" name="wew-already-product-subscribed" id="wew-already-product-subscribed" value="<?php echo $already_subscribed_product; ?>" />
					</div>

					<hr/>

					<div class="woowaitilist-sw">
						<label for="wew-already-variation-subscribed"><?php _e('Already Subscribed Message for Product Variation', $plugin_slug ); ?></label>&nbsp;
						<input type="text" name="wew-already-variation-subscribed" id="wew-already-variation-subscribed" value="<?php echo $already_subscribed_variation; ?>" />
					</div>

					<hr/>

					<div class="woowaitilist-sw">

						<label for="wew-enable-shop-manager-settings"><?php _e( 'Display subscribe field in case of enabled "backorder" for products', $plugin_slug ); ?></label>&nbsp;
						<input type="checkbox" id="wew-subscribe-backorders" name="wew-subscribe-backorders" <?php echo $subscibe_backorders; ?> />
					
					</div>

					<hr/>

					<?php

					if( current_user_can( "manage_options" ) ){

						?>

						<div class="woowaitilist-sw">

							<label for="wew-enable-shop-manager-settings"><?php _e( 'Enable/Disable Access to Shop Manager', $plugin_slug ); ?></label>&nbsp;
							<input type="checkbox" id="wew-enable-shop-manager-settings" name="wew-enable-shop-manager-settings" <?php echo $wew_enable_shop_manager_settings; ?> />

						</div>

						<hr>

						<?php
					}

					?>

					<div class="woowaitilist-sw">

						<label for="wew-admin-notification-on-subscription"><?php _e( 'Enable/Disable Administrator notifications on products subscriptions', $plugin_slug ); ?></label>&nbsp;
						<input type="checkbox" id="wew-admin-notification-on-subscription" name="wew-admin-notification-on-subscription" <?php echo $wew_admin_notification_on_subscription; ?> />

					</div>

					<hr>

					<div class="woowaitilist-sw">
						<label for="wew-no-email-alert"><?php _e( 'Empty Email Address Notification', $plugin_slug ); ?></label>&nbsp;
						<input type="text" name="wew-no-email-alert" id="wew-no-email-alert" value="<?php echo $emptyEmail_alert; ?>" />
					</div>

					<hr>
					
					<div class="woowaitilist-sw">

						<label for="wew-unsubscribe-page"><strong><?php _e( 'Select Unsubscribe Page', $plugin_slug ); ?></strong></label>&nbsp;

						<select name="wew-unsubscribe-page">
							<option value="0" <?php echo $wew_unsubscribe_page == 0 ? 'selected="selected"' : ""; ?>><?php _e( "-- Select page --", $plugin_slug ); ?></option>
							
							<?php
							foreach( $sites_allPages as $spk => $spv ){

								$selected_val = "";

								if( $wew_unsubscribe_page == $spv->ID ){

									$selected_val = ' selected="selected" ';
								}

								echo '<option value="' . $spv->ID . '" ' . $selected_val . '>' . $spv->post_title . '</option>';
							}
							?>
						</select>
					
					</div>
					
					<hr>				

					<div class="woowaitilist-sw">
		
						<label for="wew-remove-waitlist-on-uninstall"><?php _e( "Remove WooWaitlist Data on Plugin Uninstall", $plugin_slug ); ?></label>&nbsp;
						<input type="checkbox" id="wew-remove-waitlist-on-uninstall" name="wew-remove-waitlist-on-uninstall" <?php echo $wew_remove_data_on_uninstall; ?> />

					</div>

					<hr>

					<div class="woowaitilist-sw">
						<label for="wew-custom-css"><?php _e( "Add Custom CSS", $plugin_slug ); ?></label>&nbsp;
						<br/>
						<textarea name="wew-custom-css" id="wew-custom-css"><?php echo $custom_css; ?></textarea>
					</div>

					<hr>

					<input type="hidden" name="action" value="update_woowaitlist_settings" />

					<p class="submit">
						<input type="submit" class="woowaitlist-settings-save button-primary" value="<?php _e('Save Changes', $plugin_slug) ?>" />
					</p>

				</form>

				<?php
			break;
			case "email_settings":

				$subscription_email_from = get_option('wew-subscription-email-from-name') ? get_option('wew-subscription-email-from-name') : "%site title% Waitlist";
				$subscription_email_subject = get_option('wew-subscription-email-subject') ? get_option('wew-subscription-email-subject') : "%product% added to your Waitlist";
				$notify_available_product = get_option('wew-notify-available-product') ? get_option('wew-notify-available-product') : __("Your product %product% is back in stock, don't miss out and visit the %product page%.", $plugin_slug );			
				$wew_notifications_senderEmail = !get_option('woowaitilist-default-email-options') || get_option('woowaitilist-default-email-options') == "on" ? 'checked="checked"' : '';
				$custom_sender_email_address = get_option('notifications-sender-email') ? get_option('notifications-sender-email') : get_settings('woocommerce_email_from_address') ;
				$back_in_stock_quantity = get_option('wew-back-in-stock_quantity') ? intval( get_option('wew-back-in-stock_quantity') ) : 0;
				$custom_email_options_class = array( 'woowaitilist-custom-email' );

				if( $wew_notifications_senderEmail ){

					$custom_email_options_class[] = 'hidden';
				}					
				
				?>

				<form method="post" action="<?php echo $current_tabURL_slug; ?>" class="woowaitlist-settings-form">

					<div class="woowaitilist-sw">
						<label for="wew-subscription-email-from-name"><?php _e('Subscription From', $plugin_slug ); ?></label>&nbsp;
						<input type="text" name="wew-subscription-email-from-name" id="wew-subscription-email-from-name" value="<?php echo $subscription_email_from; ?>">
					</div>

					<hr/>

					<div class="woowaitilist-sw">
						<label><?php _e('Subscription Email Subject', $plugin_slug ); ?></label>&nbsp;
						<input type="text" name="wew-subscription-email-subject" id="wew-subscription-email-subject" value="<?php echo $subscription_email_subject; ?>">
					</div>

					<hr/>

					<div class="woowaitilist-sw">
						<label><?php _e('Send "Back in stock" email, when product quantity is more than', $plugin_slug ); ?></label>&nbsp;
						<input type="text" name="wew-back-in-stock_quantity" id="wew-back-in-stock_quantity" value="<?php echo $back_in_stock_quantity; ?>" />
					</div>

					<hr/>

					<div class="woowaitilist-sw">

						<label><?php _e('"Back in stock" Email Content', $plugin_slug ); ?></label>&nbsp;
						<input type="text" name="wew-notify-available-product" id="wew-notify-available-product" value="<?php echo $notify_available_product; ?>">

					</div>

					<hr>

					<div class="woowaitilist-sw">

						<label class="woowaitilist-default-email-options"><?php _e("Back In Stock Notifications: Use WooCommerce 'From' Email Address Option", $plugin_slug ); ?></label>&nbsp;
						<input type="checkbox" id="woowaitilist-default-email-options" name="woowaitilist-default-email-options" <?php echo $wew_notifications_senderEmail; ?> />
						
						<div class="<?php echo implode( ' ', $custom_email_options_class ); ?>">
							<label for="notifications-sender-email"><?php _e( "Insert Custom 'From' Email Address", $plugin_slug ); ?></label>&nbsp;
							<input type="text" id="notifications-sender-email" name="notifications-sender-email" value="<?php echo $custom_sender_email_address; ?>" />
						</div>
					</div>

					<hr>

					<div class="woowaitilist-sw">

						<p><?php echo __( 'You can use following shortcodes:', $plugin_slug ); ?></p>
						<ul>
							<li><strong>%site title%</strong> : <?php _e("Display Site Title as Shown in WooCommerce 'From Name' Email Settings", $plugin_slug ); ?></li>
							<li><strong>%product%</strong> : <?php _e("Display Product Title", $plugin_slug ); ?></li>
							<li><strong>%product page%</strong> : <?php _e("Display Linked Product Title", $plugin_slug ); ?></li>
						</ul>

					</div>					

					<input type="hidden" name="action" value="update_woowaitlist_email_settings" />

					<p class="submit">
						<input type="submit" class="woowaitlist-settings-save button-primary" value="<?php _e('Save Changes', $plugin_slug) ?>" />
					</p>

				</form>

				<?php
			break;
			case "woowaitlist_data":
				?>

				<form method="post" action="<?php echo $current_tabURL_slug; ?>" id="wew_data_form">
				    <p>
				        <select name="wew_data_form_actions" class="wew_data_form_actions">
				            <option value="actions"><?php _e( 'Actions', $plugin_slug )?></option>
				            <option value="delete"><?php _e( 'Delete', $plugin_slug )?></option>
				            <option value="export"><?php _e( 'Export', $plugin_slug )?></option>
				      </select>
				      <input type="submit" name="wew_data_actions_changes" class="button-secondary" value="<?php _e( 'Apply', $plugin_slug )?>" />
				    </p>

				    <table class="widefat page fixed" cellpadding="0">
				      <thead>
				        <tr>
				        <th id="cb" class="manage-column column-cb check-column" style="" scope="col">
				          <input type="checkbox"/>
				        </th>
				          <th class="manage-column"><?php _e( 'Email', $plugin_slug )?></th>
				          <th class="manage-column"><?php _e( 'Thumb', $plugin_slug )?></th>
				          <th class="manage-column"><?php _e( 'Product Title', $plugin_slug )?></th>
				          <th class="manage-column"><?php _e( 'Variation', $plugin_slug )?></th>
				          <th class="manage-column"><?php _e( 'Grouped parent', $plugin_slug )?></th>
				          <th class="manage-column"><?php _e( 'Categories', $plugin_slug )?></th>
				          <th class="manage-column"><?php _e( 'Date Added', $plugin_slug )?></th>
				        </tr>
				      </thead>
				      <tfoot>
				        <tr>
				        <th id="cb" class="manage-column column-cb check-column" style="" scope="col">
				          <input type="checkbox"/>
				        </th>
				          <th class="manage-column"><?php _e( 'Email', $plugin_slug )?></th>
				          <th class="manage-column"><?php _e( 'Thumb', $plugin_slug )?></th>
				          <th class="manage-column"><?php _e( 'Product Title', $plugin_slug )?></th>
				          <th class="manage-column"><?php _e( 'Variation', $plugin_slug )?></th>
				          <th class="manage-column"><?php _e( 'Grouped parent', $plugin_slug )?></th>
				          <th class="manage-column"><?php _e( 'Categories', $plugin_slug )?></th>
				          <th class="manage-column"><?php _e( 'Date Added', $plugin_slug )?></th>
				        </tr>
				      </tfoot>
				      <tbody><?php

				      	function get_wew_records(){

							global $wpdb;

							$wew_DBtable_name = $wpdb->prefix . 'woocommerce_waitlist';

							$ret = $wpdb->get_results("SELECT * FROM " . $wew_DBtable_name . " ORDER BY date DESC");

							return $ret;
						}

						$wewData = get_wew_records();

						if( $wewData ){

							$i=0;

							foreach( $wewData as $d ) { 

								$i++;

								$product_permalink = get_permalink( $d->productId );
								$product_title = get_the_title( $d->productId );
								?>
								<tr class="<?php echo (ceil($i/2) == ($i/2)) ? "" : "alternate"; ?>">
									<th class="check-column" scope="row">
										<input type="checkbox" value="<?php echo $d->id?>" name="wewd_id[]" />
									</th>
									<td>
										<strong><?php echo $d->email; ?></strong>
										<div class="row-actions-visible">
										<span class="delete"><a href="<?php echo $current_tabURL_slug; ?>&amp;delete=<?php echo $d->id?>" onclick="return confirm('Are you sure you want to delete this record from WooWaitlist database?');"><?php _e( "Delete", $this->plugin_slug ); ?></a></span>
										</div>
									</td>
									<td>
										<a href="<?php echo $product_permalink; ?>" title="<?php echo $product_title; ?>" target="_blank">
										<?php
										
										echo get_the_post_thumbnail( $d->productId, array(75,75) );

										?>
										</a>
									</td>
									<td><strong><a href="<?php echo $product_permalink; ?>" title="<?php echo $product_title; ?>" target="_blank"><?php echo $product_title; ?></a></strong></td>
									<td><?php

										if( $d->variationId == 0 ){

											echo '-';
										}
										else{

											$pplg = Woocommerce_Waitlist::get_instance();

											$variationTitle = $pplg->get_variation_titles( $d->variationId );

											if( $variationTitle ){

												echo '<strong>' . $variationTitle . '</strong>';	
											}

										}

									?></td>



									<td><?php

										if( $d->grouped_parentId == 0 ){

											echo '-';
										}
										else{
											
											$parent_product = get_post( absint( $d->grouped_parentId ) );

											$parent_title = $parent_product->post_title;
											$parent_permalink = get_permalink(  $parent_product->ID );
											?>

											<strong><a href="<?php echo $parent_permalink; ?>" title="<?php echo $parent_title; ?>" target="_blank"><?php echo $parent_title; ?></a></strong>

											<?php
										}

									?></td>





									<td>
										<?php

										$terms = get_the_terms( $d->productId, 'product_cat' );
										
										$counter = 0;

										foreach ($terms as $term) {

											echo '<a href="' . admin_url( 'edit.php?post_type=product&product_cat=' . $term->slug ) . '" title="' . $term->name .'">' . $term->name .'</a>' ;

										    $counter++;

										    if( $counter < count( $terms ) ){

										    	echo ', ';
										    }
										}

										?>
									</td>
									<td><?php echo $d->date; ?></td>
								</tr><?php
							}
						}
						else{
							?><tr><td colspan="4"><?php _e( 'No subscribers.', $plugin_slug )?></td></tr><?php
						}
						?>
						</tbody>
				    </table>

				    <p>
				        <select name="wew_data_form_actions-2" class="wew_data_form_actions">
				            <option value="actions"><?php _e( 'Actions', $plugin_slug )?></option>
				            <option value="delete"><?php _e( 'Delete', $plugin_slug )?></option>
				            <option value="export"><?php _e( 'Export', $plugin_slug )?></option>
				        </select>
				        <input type="submit" name="wew_data_actions_changes-2" class="button-secondary" value="<?php _e( 'Apply', $plugin_slug )?>" />
				    </p>

				    <input type="hidden" name="action" value="update_woowaitlist_data" />

			  	</form>

				<?php
			break;
		}

		?>
</div>