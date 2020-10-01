<?php
/**
 * The Wine Box Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package The Wine Box
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_THE_WINE_BOX_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'the-wine-box-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_THE_WINE_BOX_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );

/*** DIGITAL TANGENT ***/

add_action( 'woocommerce_shop_loop_item_title', 'show_tags', 0 );
function show_tags() {
  global $product;
  // get the product_tags of the current product
  $current_tags = get_the_terms( get_the_ID(), 'product_tag' );
  // only start if we have some tags
  if ( $current_tags && ! is_wp_error( $current_tags ) ) { 
    //create a list to hold our tags
    echo '<div class="product_tags">';
    // for each tag we create a list item
    foreach ( $current_tags as $tag ) {
      $tag_title = $tag->name; // tag name
      $tag_link = get_term_link( $tag ); // tag archive link
      echo '<a class="tag_link" href="'.$tag_link.'">'.$tag_title.'</a>';
    }
    echo '</div>';
  }
}

/** Remove the taxonomy from archive titles **/
add_filter( 'get_the_archive_title', function ($title) {    
        if ( is_category() ) {    
                $title = single_cat_title( '', false );    
            } elseif ( is_tag() ) {    
                $title = single_tag_title( '', false );    
            } elseif ( is_author() ) {    
                $title = '<span class="vcard">' . get_the_author() . '</span>' ;    
            } elseif ( is_tax() ) { //for custom post types
                $title = sprintf( __( '%1$s' ), single_term_title( '', false ) );
            } elseif (is_post_type_archive()) {
                $title = post_type_archive_title( '', false );
            }
        return $title;    
    });

/**
 * @snippet       Rename "My Account" Link @ WooCommerce/WP Nav Menu

 */
 
add_filter( 'wp_nav_menu_items', 'dynamic_label_change', 10, 2 ); 
 
function dynamic_label_change( $items, $args ) { 
   if ( ! is_user_logged_in() ) { 
      $items = str_replace( "My Account", "Login / Register", $items ); 
   } 
	
	if ( is_user_logged_in() ) {
    $user_info = wp_get_current_user();
    $user_name = $user_info->user_login;
    $items = str_replace( "My Account", "Hi " . $user_name, $items ); 
}
   return $items; 
} 

/** Add text under place order button **/
add_action( 'woocommerce_review_order_after_submit', 'bbloomer_privacy_message_below_checkout_button' );
 
function bbloomer_privacy_message_below_checkout_button() {
   echo '<p><small>* Click and collect orders that include any alcohol will only be available for collection from 10am due to alcohol restrictions.</small></p>';
}

/**
 * Remove product data tabs
 */
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

function woo_remove_product_tabs( $tabs ) {

    unset( $tabs['additional_information'] );  	// Remove the additional information tab
	$tabs['description']['title'] = __( 'More Information' );		// Rename the description tab

    return $tabs;
}

/**
* Change text strings for Related Products
*/
 
function custom_related_products_text( $translated_text, $text, $domain ) {
  switch ( $translated_text ) {
    case 'Related products' :
      $translated_text = __( 'You May Also Like', 'woocommerce' );
      break;
  }
  return $translated_text;
}
add_filter( 'gettext', 'custom_related_products_text', 20, 3 );

/**
 * To display additional field at My Account page 
 * Once member login: edit account
 */
add_action( 'woocommerce_edit_account_form', 'my_woocommerce_edit_account_form' );
 
function my_woocommerce_edit_account_form() {
 
	$user_id = get_current_user_id();
	$user = get_userdata( $user_id );
 
	if ( !$user )
		return;
 
	$birthdate = get_user_meta( $user_id, 'birthdate', true );
 
?>
	<fieldset>
		<legend>Additional Information</legend>
    
		<p class="form-row form-row-thirds">
			<label for="birthdate">Birth date:</label>
			<input type="text" name="birthdate" value="<?php echo esc_attr( $birthdate ); ?>" class="input-text" />
			<br />
			<span style="font-size: 12px;">(Birth date format: YYYY-MM-DD. eg: 1980-12-31)</span>
		</p>
	</fieldset>
 
<?php
 
} // end func
 
 
/**
 * This is to save user input into database
 * hook: woocommerce_save_account_details
 */
add_action( 'woocommerce_save_account_details', 'my_woocommerce_save_account_details' );
 
function my_woocommerce_save_account_details( $user_id ) {
	update_user_meta( $user_id, 'birthdate', htmlentities( $_POST[ 'birthdate' ] ) ); 
} // end func

add_filter( 'woocommerce_billing_fields' , 'ced_remove_billing_fields' );
function ced_remove_billing_fields( $fields ) {
         unset($fields['billing_last_name']);
         return $fields;
}

add_filter( 'woocommerce_checkout_fields' , 'ced_rename_checkout_fields' );
// Change placeholder and label text
function ced_rename_checkout_fields( $fields ) {
$fields['billing']['billing_first_name']['placeholder'] = 'Full Name';
$fields['billing']['billing_first_name']['label'] = 'Full Name';
return $fields;
}

//Change the 'Billing details' checkout label to 'Contact Information'
function wc_billing_field_strings( $translated_text, $text, $domain ) {
switch ( $translated_text ) {
case 'Billing details' :
$translated_text = __( 'Client Details', 'woocommerce' );
break;
}
return $translated_text;
}
add_filter( 'gettext', 'wc_billing_field_strings', 20, 3 );



