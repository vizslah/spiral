
<?php
/**
 * Plugin Name: Spiral
 * Description: Custom modifications for the Spiral product.
 * Version: 2.4.0
 * Author: Mihaly Borbely
 * Author URI: http://mihalyborbely.com
 */


// Security check, don't allow running this outside WP
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Separate file for Spiral Settings menu in admin
include 'spiral-settings.php';



// ----------------------------------------------------
//
//                  Restrict Media Access
//
// ----------------------------------------------------

add_filter( 'ajax_query_attachments_args', 'get_current_user_uploads', 10,1);
		 
function get_current_user_uploads( $query ) {
    $user_id = get_current_user_id();
    if ( $user_id && !current_user_can('activate_plugins') && !current_user_can('edit_others_posts') ) {
        $query['author'] = $user_id;
    }
    return $query;
} 
        

/*
add_filter( 'wcfm_product_manage_fields_content', 'spiral_exclude_offering_description_from_edit' );
function spiral_exclude_offering_description_from_edit( $form ) {
    unset ( $form['description'] );
    return $form;
}
*/


//add_action( 'before_wcfm_products_manage_action', 'spiral_exclude_offering_description_from_edit');
/*
function spiral_exclude_offering_description_from_edit() {
    $description = $product->get_description();
}
*/

add_filter( 'wcfm_enquiry_reply_fields', 'spiral_disable_inquiry_publicize_checkbox' );
function spiral_disable_inquiry_publicize_checkbox( $form ) {
    unset ( $form['inquiry_stick'] );
    return $form;
}



// ----------------------------------------------------
//
//          Custom Post Type for Profile Pages
//
// ----------------------------------------------------

function spiral_create_profile_posttype() {
 
    register_post_type( 'profile',
        array(
            'labels' => array(
                'name' => __( 'profiles' ),
                'singular_name' => __( 'Profile' )
            ),

    // I haven't figured out how to do custom capabilities in a useful way yet
    //
    //        'capabilities' => array(
    //            'publish_products' => 'publish_profiles',
    //            'edit_products' => 'edit_profiles',
    //            'delete_products' => 'delete_profiles'
    //        ),

            'public' => true,
            'has_archive' => false,
            'rewrite' => array('slug' => 'profiles'),
        )
    );
    
}
add_action( 'init', 'spiral_create_profile_posttype' );



//  --------------- LEGACY ? CHECK IF DELETABLE --------------
//
// Add capability for coaches(vendors) to edit and save their profile page
// Grants general publishing rights, this is INSECURE
// Todo: define custom capabilities when creating post type.
//
/*
function spiral_vendors_can_publish_posts() {
    $role = get_role( 'wcfm_vendor' );
    $role->add_cap( 'publish_posts' );
}
add_action( 'wp_loaded', 'spiral_vendors_can_publish_posts' );
*/



function spiral_make_vendors_into_staff() {
    $membersArray = get_users( 'role=wcfm_vendor' );
    foreach($membersArray as $member){
       $member->add_role( 'shop_staff' );
    }
}
add_action( 'wp_loaded', 'spiral_make_vendors_into_staff' );

// auto-assign vendor as staff to all appointments where no staff is assigned
// custom post type: wc_appointment
// add_post_meta( $appointment->get_id(), '_appointment_staff_id', $staff_id );

function spiral_auto_assign_vendor_as_staff() {
    $appointmentsArray = get_posts( 'post_type=wc_appointment' );
    foreach($appointmentsArray as $appointment){
        $vendor_id=$appointment->post_author; // $vendor_id = get_post($appointment->get_product_id())->post_author;
        add_post_meta( $appointment->get_id(), '_appointment_staff_id', $vendor_id );
    }
}
//add_action( 'wp_loaded', 'spiral_auto_assign_vendor_as_staff' );




// --------------------------------------------------------------------
//
//                   Premium Advertisement Items
//
//
// --------------------------------------------------------------------


// Premium Features Tab in Offering Manager
//
add_action( 'end_wcfm_products_manage', 'spiral_add_premium_tab', 900 );

function spiral_add_premium_tab() {
    global $WCFM, $WCFMu;
    if ( spiral_is_user_on_free_plan() ) {
        ?>
        <div class="page_collapsible" id="spiral_premium_features_tab"><label class="wcfmfa fa-gem"></label>

            <?php _e('Pro Features', 'wc-frontend-manager-ultimate'); ?>

            <span></span></div>
            <div class="wcfm-container" id="spiral_nohide">

            <div class="wcfm-content">                
            <div class="spiral_premium_ad">
            <p class="wcfm_title"><strong>

            <?php _e('Get Pro Membership to unlock these features:', 'wc-frontend-manager-ultimate'); ?>

            </strong>

            <ul>

            <li>
            <?php _e('Unlimited Offerings', 'wc-frontend-manager-ultimate'); ?>
            </li>

            <li>
            <?php _e('Add-ons for offerings', 'wc-frontend-manager-ultimate'); ?>
            </li>

            <li>
            <?php _e('Downloadable offering type', 'wc-frontend-manager-ultimate'); ?>
            </li>

            <li>
            <?php _e('Extra page builder blocks', 'wc-frontend-manager-ultimate'); ?>
            </li>

            <li>
            <?php _e('Advanced page builder tools', 'wc-frontend-manager-ultimate'); ?>
            </li>

            <li>
            <?php _e('And much more...', 'wc-frontend-manager-ultimate'); ?>
            </li>

            </ul>
            <br>
            <b><a class="button" href='https://getspiral.com/upgrade'>➤ Upgrade to Pro!</a></b>
            </p>
            </div>
            </div></div>
        <?php
    }
}


// Diamond icon on Dashboard Header
//
add_action( 'wcfm_after_header_panel_item', 'spiral_add_premium_diamond_icon' );
function spiral_add_premium_diamond_icon() {
    if ( spiral_is_user_on_free_plan() ) {
        echo '<a href=" ' . site_url() . '/upgrade" data-tip="Upgrade to Pro" class="text_tip spiral-premium"><i class="wcfmfa fa-gem spiral-premium" style="font-size: 20px;"></i></a>';
    }
}



// --------------------------------------------------------------------
// 
//                   Vendor Timezone Setting
//
// --------------------------------------------------------------------


add_action( 'end_wcfm_marketplace_settings', 'spiral_vendor_timezone_settings' );

function spiral_vendor_timezone_settings() {
    global $WCFM, $WCFMu;
    ?>
    <div class="page_collapsible" id="wcfm_settings_form_timezone_head"><label class="wcfmfa fa-clock"></label>

        <?php _e('Timezone', 'wc-frontend-manager-ultimate'); ?>

        <span></span></div>
		<div class="wcfm-container">
        <div id="wcfm_settings_form_timezone_expander" class="wcfm-content">
        <div class="wcfm-content">                
                
        <p class="wcfm_title"><strong>

        <?php _e('Select your timezone', 'wc-frontend-manager-ultimate'); ?>

        </strong>
        <span class="img_tip wcfmfa fa-question" data-tip="This is important for clients outside your timezone." data-hasqtip="23" aria-describedby="qtip-23">
        </span>
        </p>
        <select id="spiral-vendor-timezone" name="spiral-vendor-timezone">
            <?php
            $timezone_list = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
            $user_data = get_user_meta( get_current_user_id(), 'wcfmmp_profile_settings', true );
            if( !$user_data ) $user_data = array();
            $user_timezone = isset( $user_data['spiral-vendor-timezone'] ) ? $user_data['spiral-vendor-timezone'] : '';
            if( !isset( $user_data['spiral-vendor-timezone'] ) ) {
                echo '<option></option>';
            }
            foreach($timezone_list as $key => $val) {
                if ($user_timezone==$val) {
                    echo '<option selected value="' . $val . '" >' . $val . '</option>';
                } else {
                echo '<option value="' . $val . '">' . $val . '</option>';
                }
            }
            ?>
        </select>

        <?php
        if ( isset( $user_data['spiral-vendor-timezone'] ) ) {
        ?>
            <br><br><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Important: Changing this setting is not recommended if you already have upcoming scheduled appointments.
        <?php
        }
        ?>

        </div></div>
        </div>
    <?php
}




// --------------------------------------------------------------------
// 
//                   Vendor Currency Setting
//
// --------------------------------------------------------------------


add_action( 'end_wcfm_marketplace_settings', 'spiral_vendor_currency_settings' );

function spiral_vendor_currency_settings() {
    global $WCFM, $WCFMu;
    ?>
    <div class="page_collapsible" id="wcfm_settings_form_currency_head"><label class="wcfmfa fa fa-euro-sign"></label>

        <?php _e('Currency', 'wc-frontend-manager-ultimate'); ?>

        <span></span></div>
		<div class="wcfm-container">
        <div id="wcfm_settings_form_currency_expander" class="wcfm-content">
        <div class="wcfm-content">                
                
        <p class="wcfm_title"><strong>

        <?php _e('Select your currency', 'wc-frontend-manager-ultimate'); ?>

        </strong>
        <span class="img_tip wcfmfa fa-question" data-tip="The currency in which you'll set the price of your offerings" data-hasqtip="23" aria-describedby="qtip-23">
        </span>
        </p>
        <select id="spiral-vendor-currency" name="spiral-vendor-currency">
            <?php
            $currency_list = array('EUR', 'USD', 'GBP', 'HUF');
            $user_data = get_user_meta( get_current_user_id(), 'wcfmmp_profile_settings', true );
            if( !$user_data ) $user_data = array();
            $user_currency = isset( $user_data['spiral-vendor-currency'] ) ? $user_data['spiral-vendor-currency'] : 'EUR';

            foreach($currency_list as $key => $val) {
                if ($user_currency==$val) {
                    echo '<option selected value="' . $val . '" >' . $val . '</option>';
                } else {
                echo '<option value="' . $val . '">' . $val . '</option>';
                }
            }
            ?>
        </select>

            <br><br><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
            <div>
            Experimental feature. Limitations apply if you switch from EUR to other currencies:<br>
            <br>
            - Prices are set and displayed in the chosen currency<br>
            - Paid add-ons can only be set in EUR, regardless of currency setting<br>
            </div>

        </div></div>
        </div>
    <?php
}

// Auto-update Currency Switcher
add_action( 'wcfm_vendor_settings_update', function( $vendor_id, $wcfm_settings_form ) {
	global $WCFM, $WCFMmp, $WOOCS;
	if( isset( $wcfm_settings_form['spiral-vendor-currency'] ) ) {
	    $WOOCS->set_currency( $wcfm_settings_form['spiral-vendor-currency'] );
    }
}, 500, 2 );



// -------------------------------------------
//
//    Custom Currency Field for Offerings
//
// -------------------------------------------


function spiral_wcfm_add_currency_field($output, $product_id) {

    global $WCFM, $WCFMu, $WOOCS;

    $currencies = $WOOCS->get_currencies();
    
    $user_data = get_user_meta( get_current_user_id(), 'wcfmmp_profile_settings', true );
    if( !$user_data ) $user_data = array();
    $user_currency = isset( $user_data['spiral-vendor-currency'] ) ? $user_data['spiral-vendor-currency'] : 'EUR';

    $rate = $currencies[$user_currency]['rate'];
    
    if ( get_post_meta($product_id, '_woocs_regular_price_' . $user_currency) ) {
        $alt_price = get_post_meta($product_id, '_woocs_regular_price_' . $user_currency);   
    } else {
        $alt_price = get_post_meta($product_id, '_regular_price');
        $alt_price[0] = round($alt_price[0] * $rate, 2);
    }
    
    if( $user_currency != 'EUR' ) {
        
        $output["spiral_price_".$user_currency] = array(
        'type' => 'text',
        'label' => __('Price('.$user_currency.')'),
        'id' => 'spiral_price_'.$user_currency,
        'value'=>$alt_price[0],
        'label_class' => 'wcfm_half_ele_title wcfm_title',
        'class' => 'wcfm-text wcfm_ele wcfm_non_negative_input wcfm_half_ele simple external non-subscription non-variable-subscription non-auction non-redq_rental non-accommodation-booking non-lottery'
        );
        $output[regular_price][type] = 'hidden';
    }
    
    return $output;
}
        
function spiral_wcfm_save_meta($post_id, $data) {
        
        global $WCFM, $WCFMu, $WOOCS;
    
        $currencies = $WOOCS->get_currencies();
        $reverse_rate = 1/$currencies[$user_currency]['rate'];

        $user_data = get_user_meta( get_current_user_id(), 'wcfmmp_profile_settings', true );
        if( !$user_data ) $user_data = array();
        $user_currency = isset( $user_data['spiral-vendor-currency'] ) ? $user_data['spiral-vendor-currency'] : 'EUR';

        $currencies = $WOOCS->get_currencies();
    
        // in_array($user_currency, $currencies)
    
        if (isset($data['spiral_price_'.$user_currency])) {
            if ($data['spiral_price_'.$user_currency] !== '') {

                // calculate rate for base price reverse conversion
                $reverse_rate = 1/$currencies[$user_currency]['rate'];
                // get alt price from form
                $spiral_alt_price = $data['spiral_price_'.$user_currency];
                // calculate base price from alt price
                $spiral_base_price = round($data['spiral_price_'.$user_currency] * $reverse_rate, 2);
            }
        } else {
            $spiral_alt_price = null;
            $spiral_base_price = $data['regular_price'];
        }

        // save alt price
        update_post_meta($post_id, '_woocs_regular_price_' . $user_currency, $spiral_alt_price);
        // save calculated base price (no idea why there are two meta tags for it)
        update_post_meta($post_id, '_regular_price', $spiral_base_price);
        update_post_meta($post_id, '_price', $spiral_base_price);

}

function spiral_wcfm_remove_description_field($output, $product_id) {

    unset($output['description']);
    return $output;
    
}


add_action('wcfm_product_manage_fields_content', 'spiral_wcfm_remove_description_field',10,2);
add_action('wcfm_product_manage_fields_pricing','spiral_wcfm_add_currency_field',10,2);
add_action('after_wcfm_products_manage_meta_save','spiral_wcfm_save_meta',10,2);

//$this->set_staff_id($new_staff_id);



/*
add_action( 'end_wcfm_marketplace_settings', 'spiral_add_currency_settings' );

function spiral_add_currency_settings() { 
// This is forked from SCD currency plugin and fixed to work properly inside WCFM
    global $WCFM, $WCFMu;
    ?>
    <div class="page_collapsible" id="wcfm_settings_form_currency_head"><label class="wcfmfa fa-eur"></label>

        <?php _e('Currency', 'wc-frontend-manager-ultimate'); ?>

        <span></span></div>
		<div class="wcfm-container">
        <div id="wcfm_settings_form_currency_expander" class="wcfm-content">

        <!--    <div class="scd-choose-curr" style="margin-left:15%;margin-top:70px; backgound-color:red;"> -->
            <div class="wcfm-content">                
                
        <p class="wcfm_title"><strong>

        <?php _e('Select your custom currency', 'wc-frontend-manager-ultimate'); ?>

        </strong>
        <span class="img_tip wcfmfa fa-question" data-tip="This is an Experimental feature, there might be bugs!<br><br>Learn more about how it works in our FAQ" data-hasqtip="23" aria-describedby="qtip-23">
        </span>
        </p>
        <select id="scd-currency-list" name="scd-currency-list">
            <?php
            $user_curr= get_user_meta(get_current_user_id(), 'scd-user-currency');
            if(count($user_curr)>0) $user_curr=$user_curr[0];
            foreach ($GLOBALS['currencies'] as $key => $val) {
                if($user_curr==$key){
                    echo '<option selected value="'.$key.'" >'.$val.'('.$key.')</option>';
                } else {
                    echo '<option value="'.$key.'" >'.$val.'('.$key.')</option>';
                }
             }
            ?>
        </select>

                <br><br><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Experimental feature
        
        </div></div>
        </div>
    <?php
}

*/






/* -------------------------

function wcfm_vendor_custom_0411_store_settings( $vendor_id ) {
	global $WCFM, $WCFMu;
	
	$wcfm_vendor_custom_options = (array) get_user_meta( $vendor_id, 'wcfm_vendor_custom_options', true );
	$wcfm_vendor_invoice_prefix = isset( $wcfm_vendor_custom_options['prefix'] ) ? $wcfm_vendor_custom_options['prefix'] : '';
	$wcfm_vendor_invoice_sufix = isset( $wcfm_vendor_custom_options['sufix'] ) ? $wcfm_vendor_custom_options['sufix'] : '';
	$wcfm_vendor_invoice_digit = isset( $wcfm_vendor_custom_options['digit'] ) ? $wcfm_vendor_custom_options['digit'] : '';
	$wcfm_vendor_invoice_disclaimer = isset( $wcfm_vendor_custom_options['disclaimer'] ) ? $wcfm_vendor_custom_options['disclaimer'] : '';
	$wcfm_vendor_invoice_signature = isset( $wcfm_vendor_custom_options['signature'] ) ? $wcfm_vendor_custom_options['signature'] : '';
	
	?>
	<!-- collapsible -->
	<div class="page_collapsible" id="wcfm_settings_form_vendor_custom_head">
		<label class="fa fa-file-pdf-o"></label>
		<?php _e('Custom Setting', 'wc-frontend-manager-ultimate'); ?><span></span>
	</div>
	<div class="wcfm-container">
		<div id="wcfm_settings_form_vendor_custom_expander" class="wcfm-content">
			<?php
			$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfmu_settings_fields_vendor_store_invoice', array( "wcfm_vendor_custom_prefix" => array('label' => __('Custom No Prefix', 'wc-frontend-manager-ultimate'), 'type' => 'select', 'name' => '', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $wcfm_vendor_invoice_prefix ),
																) ) );
			?>
		</div>
	</div>
	<div class="wcfm_clearfix"></div>
	<!-- end collapsible -->
	<?php
}
	

add_action( 'end_wcfm_vendor_settings', 'wcfm_vendor_custom_0411_store_settings', 20 );



function wcfmu_vendor_custom_0411_store_settings_update( $user_id, $wcfm_settings_form ) {
	global $WCFM, $WCFMu, $_POST;
	
	if( isset( $wcfm_settings_form['wcfm_vendor_custom_options'] ) ) {
		$wcfm_vendor_custom_options = $wcfm_settings_form['wcfm_vendor_custom_options'];
		update_user_meta( $user_id, 'wcfm_vendor_custom_options',  $wcfm_vendor_custom_options );
	}
}
add_action( 'wcfm_vendor_settings_update', 'wcfmu_vendor_custom_0411_store_settings_update', 20, 2 );


-------------------------------------  */




// ----------------------------------------------------------------------
//
//                  StoreFront Theme Customization
//
// ----------------------------------------------------------------------

function spiral_header_customization() {
    global $WCFM;
    //remove_action( 'storefront_header', 'storefront_secondary_navigation', 30 );
    remove_action( 'storefront_header', 'storefront_product_search', 40 );
    //remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper', 42 ); // why did I comment this out?
    //remove_action( 'storefront_header', 'storefront_primary_navigation', 50 ); // why did I comment this out?
    remove_action( 'storefront_header', 'storefront_header_cart', 60 );
    //remove_action( 'storefront_header', 'storefront_primary_navigation_wrapper_close', 68 ); // why did I comment this out?

    // Hide breadcrumbs when not needed. (todo: add more cases. or get rid of it entirely?)
    // 17250 = survey page id // 38 = checkout page id // 87959 = about page id
    $id = get_the_ID();
    if (
        is_account_page()
        or wcfm_is_store_page()
        or is_wcfm_membership_page()
        or is_wcfm_registration_page()
        or $id == '17250' // survey
        or $id == '38'    // checkout
        or $id == '87959' // about
       )
    {
        remove_action( 'storefront_before_content', 'woocommerce_breadcrumb', 10);
        if ( !wcfm_is_store_page() ) {
        add_action( 'storefront_before_content', function() { echo '<br>'; } );
        }
    }
    
    remove_action( 'storefront_footer', 'storefront_credit', 20 );
    
    // Hide page title on static pages
    remove_action( 'storefront_page', 'storefront_page_header', 10 );
    
    // Remove prev/next post navigation on blogs until I figure out how to restrict by author
    remove_action( 'storefront_single_post_bottom', 'storefront_post_nav');
    
    // Remove product page edit link
    remove_action( 'woocommerce_single_product_summary', 'storefront_edit_post_link', 60 );
    // https://getspiral.com/edit/post.php?post=4133&action=edit&post_type=product&vcv-action=frontend&vcv-source-id=4133
    
    // Remove Post Category
    remove_action( 'storefront_single_post_bottom', 'storefront_post_taxonomy', 5 );
    remove_action( 'storefront_loop_post', 'storefront_post_taxonomy', 5 );
    
}

add_action( 'wp_head', 'spiral_header_customization');


// Edit Button on Product Page
// wcfm product save process messes it up,
// needs to be sorted out before this feature can be activated again

function spiral_product_edit_button() {
    if ( get_the_author_meta('ID') == get_current_user_id() ) {
            echo "<input type=button onClick=\"location.href='" . get_admin_url() . "post.php?post=" . get_the_ID() . "&action=edit&post_type=product&vcv-action=frontend&vcv-source-id=" . get_the_ID() . "'\" value='Edit this'>";
        } // style='float: left; position: absolute; margin-top: 30px; margin-left: 10px; z-index: 99'
}
//add_action( 'woocommerce_product_meta_end', 'spiral_product_edit_button');


// ----------  Storefront custom post meta  --------------- /

function spiral_remove_default_post_meta() {
    remove_action( 'storefront_post_header_before', 'storefront_post_meta' );
}
add_action( 'wp_loaded', 'spiral_remove_default_post_meta' );


function spiral_add_custom_post_meta() {
        global $WCFM;

        if ( 'post' !== get_post_type() ) {
			return;
		}

		// Posted on.
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf(
			$time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$output_time_string = sprintf( $time_string );

		$posted_on = '
			<span class="posted-on">' .
			sprintf( __( 'on %s', 'storefront' ), $output_time_string ) .
			'</span>';

		// Author.
        $store_link = wcfmmp_get_store_url( get_the_author_meta( 'ID' ) );
        $author = sprintf(
			'<span class="post-author"><a href="%1$s" class="url fn" rel="author">%2$s</a></span>',
			esc_url( $store_link ),
			esc_html( get_the_author() )
		);
    
        // display with html filter
		echo wp_kses(
			sprintf( '%1$s, %2$s', $author, $posted_on ), array(
				'span' => array(
					'class' => array(),
				),
				'a'    => array(
					'href'  => array(),
					'title' => array(),
					'rel'   => array(),
				),
				'time' => array(
					'datetime' => array(),
					'class'    => array(),
				),
			)
		);
}
add_action( 'storefront_post_header_before', 'spiral_add_custom_post_meta' );

add_filter( 'wcfmmp_is_allow_sold_by_logo', '__return_false' );

// ----------------------------------------------------------------------
//
//                  UI modifications (JS / CSS)
//                     for 3rd party plugins
//
// (app-frontend-vendor and app-frontend are Yobro(chat plugin) scripts)
//
// ----------------------------------------------------------------------

function spiral_enqueue_frontend_scripts() {
    
    global $WCFM;
    //$page_slug = $post->post_name;
    $url = $_SERVER["REQUEST_URI"];
    $is_settings_page = strpos($url, 'settings');
    $is_msg_page = strpos($url, 'msg');
    
    wp_register_script( 'yobro-finetune', plugins_url('js/yobro-finetune.js',__FILE__ ), array('app-frontend-vendor', 'app-frontend'), '', true);
    wp_register_script( 'wcfm_settings_interval_finetune', plugins_url('js/wcfm-settings-interval-finetune.js',__FILE__ ));
    
    // Timepicker
    wp_register_script( 'spiral_timepicker', plugins_url('js/jquery.timepicker.min.js',__FILE__ ), array('jquery'), '', true);
    wp_enqueue_script( 'spiral_timepicker' );
    wp_register_style( 'spiral_timepicker', plugins_url('css/jquery.timepicker.css',__FILE__ ));
    wp_enqueue_style( 'spiral_timepicker' );
    
    
    // Yobro
    if ( is_user_logged_in() ) {
        wp_enqueue_script('yobro-finetune' );
    }
    
    // todo: migrate to css and filter hooks if possible
    if ( is_user_logged_in() && !current_user_can('administrator') && $is_settings_page || is_user_logged_in() && !current_user_can('administrator') && $is_msg_page ) {
        wp_enqueue_script('wcfm_settings_interval_finetune' );
        wp_localize_script('wcfm_settings_interval_finetune', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
    }
    
    // WCFM fixes
    if ( !current_user_can('administrator') ) {
    wp_register_script( 'wcfm_finetune', plugins_url('js/wcfm-finetune.js',__FILE__ ), array('jquery'), '', true);
    wp_enqueue_script( 'wcfm_finetune' );
    
    wp_register_style( 'wcfm_fix', plugins_url('css/wcfm_fix.css',__FILE__ ));
    wp_enqueue_style( 'wcfm_fix' );

    wp_register_style( 'general_theme_fix', plugins_url('css/general_theme_fix.css',__FILE__ ));
    wp_enqueue_style( 'general_theme_fix' );
    }
    
    // Hotjar
    if ( !current_user_can('administrator') ) {
    wp_register_script( 'hotjar', plugins_url('js/hotjar.js',__FILE__ ), array('jquery'), '', false);
    wp_enqueue_script( 'hotjar' );
    }
    
    // Vendors only
    if ( is_user_logged_in() and wcfm_is_vendor() ) {
        wp_enqueue_script( 'tidio_chat', '//code.tidio.co/ognk7n10wgteilcaonwvh3nblzlcnh0t.js#asyncload' );
        wp_enqueue_style( 'providers', plugins_url( 'css/providers.css',__FILE__ ));
    }
    
    // Premium Lockdown
    if( spiral_is_user_on_free_plan() ) {
            wp_register_style( 'premium_lockdown', plugins_url('css/premium.css',__FILE__ ));
            wp_enqueue_style( 'premium_lockdown' );
    }
    
}

function spiral_enqueue_backend_scripts() {
        // VC customization JavaScript
        wp_register_script( 'visualcomposer-finetune', plugins_url('js/visualcomposer-finetune.js',__FILE__ ), array('jquery'), '', true);
        if ( is_user_logged_in() && !current_user_can('administrator') && $_GET['vcv-action'] == 'frontend' ) {
            wp_enqueue_script('visualcomposer-finetune' );        
        }
        // VC customization CSS
        wp_register_style('visualcomp_fix', plugins_url('css/visualcomp_fix.css',__FILE__ ));
        if ( is_user_logged_in() && !current_user_can('administrator') && $_GET['vcv-action'] == 'frontend' ) {
            wp_enqueue_style('visualcomp_fix');
        }
    
        wp_register_style('spiral_spinner', plugins_url('css/css-loader.css',__FILE__ ));
        if ( is_user_logged_in() && !current_user_can('administrator') && $_GET['vcv-action'] == 'frontend' ) {
            wp_enqueue_style('spiral_spinner');
        }

        // Premium Lockdown if on free plan
        //
        if( spiral_is_user_on_free_plan() ) {
            wp_register_style( 'premium_lockdown', plugins_url('css/premium.css',__FILE__ ));
            wp_enqueue_style( 'premium_lockdown' );
        }


}

add_action( 'wp_enqueue_scripts', 'spiral_enqueue_frontend_scripts' );
add_action( 'admin_enqueue_scripts', 'spiral_enqueue_backend_scripts' );


// -----------------------------------
//
//       Checkout customization 
//
// -----------------------------------


// Disable added to cart alert banner on checkout page
add_filter( 'wc_add_to_cart_message_html', '__return_false' );

//add_filter( 'wcfm_is_pref_stats_box', '__return_false' );

// phone not mandatory
add_filter( 'woocommerce_billing_fields', 'spiral_unrequire_wc_phone_field');
function spiral_unrequire_wc_phone_field( $fields ) {
$fields['billing_phone']['required'] = false;
return $fields;
}


// Async load
function spiral_ikreativ_async_scripts($url)
{
    if ( strpos( $url, '#asyncload') === false )
        return $url;
    else if ( is_admin() )
        return str_replace( '#asyncload', '', $url );
    else
	return str_replace( '#asyncload', '', $url )."' async='async"; 
    }
add_filter( 'clean_url', 'spiral_ikreativ_async_scripts', 11, 1 );


// ------------------------------------------------
//
//  Check membership helper function
//
// ------------------------------------------------
function spiral_is_user_on_free_plan() {
    global $WCFM, $WCFMmp, $WCFMvm;
    if( function_exists('get_wcfm_basic_membership') ) {
      $basic_membership = get_wcfm_basic_membership(); }
    $member_id = get_current_user_id();
    $membership_id = get_user_meta( $member_id, 'wcfm_membership', true );
    if( $basic_membership && ( $basic_membership == $membership_id ) ) {
        return true;
    } else {
        return false;
    }
}


// ----------------------------------------------------
//
//    Offering Limit override if not on Free plan
//
// ----------------------------------------------------
add_filter( 'wcfm_vendor_product_limit', 'spiral_override_offering_limit', 10, 2 );
function spiral_override_offering_limit( $productlimit, $vendor_id ) {
    if ( !spiral_is_user_on_free_plan() ) {
        return 0;
    } else {
        return $productlimit;
    }
}




// ---------------------------------------------------------------
//
//             Store Profile / Product Page Theme Modifications
//
// ---------------------------------------------------------------

add_action( 'before_wcfmmp_store_header_info', 'spiral_add_bio_to_header' );
function spiral_add_bio_to_header( $user_id ) {
    global $WCFM;
    echo wcfm_get_user_meta( $user_id, '_store_description', true );
}


function spiral_product_list_excerpt() {
    $excerpt = get_the_excerpt();
    $excerpt = substr($excerpt, 0, 90);
    if ( $excerpt != get_the_excerpt() ) {
        $excerpt = substr($excerpt, 0, strripos($excerpt, " "));
    }
    $excerpt = preg_replace(" ([.*?])",'',$excerpt);
    $excerpt = strip_shortcodes($excerpt);
    $excerpt = strip_tags($excerpt);
    if ( $excerpt ) $excerpt .= ' ...';
    echo $excerpt .'<br>';
    //the_excerpt();
}
add_action( 'woocommerce_after_shop_loop_item_title', 'spiral_product_list_excerpt', 40 );


// ---------------------------------------------------------------
//
//                   Custom Actions at Registration:
//                     Create Default Profile Page
//
// ---------------------------------------------------------------

function spiral_create_default_profile_page( $user_id ) {
    $new_profile = array(
    'post_content'  => '',
    'post_title'    => $user_id,
    'post_status'   => 'publish',
    'post_author'   => $user_id,
    'page_template' => 'page-templates/blank.php',
    'post_type' => 'profile'
    );
    wp_insert_post( $new_profile );
}
add_action( 'user_register', 'spiral_create_default_profile_page' );




// Check if user is admin
// Better native method exists, didn't know at the time
// Might no longer be used anywhere
function isAdmin(){
    return in_array('administrator',  wp_get_current_user()->roles);
}


// -----------------------------------------------------------------------------------
//
// Add shortcode for redirecting to logged in user's public profile (store page)
// Used to create the Profile dashboard menu item and to redirect from Onboarding
//
// ------------------------------------------------------------------------------------
add_shortcode('profile_redirect', 'get_user_profile_link');
function get_user_profile_link() {
    global $WCFM, $WCFMmp;
    //$store_user   = wcfmmp_get_store( get_query_var( 'author' ) );
    //$store_info   = $store_user->get_shop_info();
    if ( is_user_logged_in() ) {
        $store_user_id = get_current_user_id();
        $store_link = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( $store_user_id );
        preg_match_all('#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#', $store_link, $match);
        $redir = $match[0][0];
    } else {
        $redir = get_wcfm_url();
    }
    wp_redirect( $redir );
}



// -------------------------------------------------------------
//
//             Clean admin for non-admin users
//      since VC and WCFM onboarding both work from admin
//             TODO: add more and hide everything
//
// -------------------------------------------------------------
function hide_update_msg_non_admins(){
    if (!current_user_can( 'manage_options' )) { // non-admin users
        echo '<style>#setting-error-tgmpa>.updated settings-error notice is-dismissible, .update-nag, .updated { display: none; }</style>';
    }
}
add_action( 'admin_head', 'hide_update_msg_non_admins');

// Remove pointless widget from admin dashboard
function remove_dashboard_widgets() {
    global $wp_meta_boxes;
    unset($wp_meta_boxes['dashboard']['normal']['core']['so-dashboard-news']);
}

add_action('wp_dashboard_setup', 'remove_dashboard_widgets' );




// ---------------------------------------------------------------
//
//      Redirect front page to dashboard if user is logged in
//
// ----------------------------------------------------------------
function spiral_redirect_to_dashboard() {
	global $WCFM;
    if ( is_user_logged_in() && !isAdmin() && is_front_page() ) {
		wp_redirect( get_wcfm_url() );
        exit;
    }
}
add_action( 'template_redirect', 'spiral_redirect_to_dashboard');



// -----------------------------------------------------------------------
//
//             Hide wp-login.php, disable default signups
//         Nobody is supposed to see this page, but just in case
//              change the default links from wordpress
//
// -----------------------------------------------------------------------
add_filter('login_headertitle', create_function(false, "return site_url();"));
add_filter('login_headerurl', create_function(false, "return site_url();"));

// remove the register link from the wp-login.php script, disable signups
add_filter('option_users_can_register', function($value) {
    $script = basename(parse_url($_SERVER['SCRIPT_NAME'], PHP_URL_PATH));
 
    if ($script == 'wp-login.php') {
        $value = false;
    }
 
    return $value;
});


add_filter( 'wp_login_errors', 'my_login_form_lock_down', 90, 2 );
function my_login_form_lock_down( $errors, $redirect_to ){
  // This provides a secret way to show the login form as a url variable in case of emergencies.
  // access the login form like so:  https://getspiral.com/wp-login.php?iddqd=idkfa
  $secret_key = "iddqd";
  $secret_password = "idkfa";
  
  if ( !isset( $_GET[ $secret_key ] ) || $_GET[ $secret_key ] != $secret_password ) {
    //login_header(__('Log In'), '', $errors);
    echo '<div style=\'text-align: center; margin-top: 5em;\'>Ooops... Something went wrong.';
    echo '<p>You might need to log in again.';
    echo '<p><a target=\'_top\' href=\''. get_wcfm_url() .'\'>Back to Dashboard</a></div>';
    do_action( 'login_footer' );
    echo "</body></html>";
    exit;
  }
  
  return $errors;
}





// ------------------------------------------------------------------
//
//           --------------------------------------
//               Spiral V2 - WooCommerce + WCFM
//           --------------------------------------
//
// ------------------------------------------------------------------

// filters:
// login_form_top (before username)
// login_form_middle (between password and remember me)
// login_form_bottom (after login button)


// -------------------------------------------------------------
//
//   Offering Availability bookable days default
//
// -------------------------------------------------------------

add_filter( 'wcfm_wcappointments_availability_fields', function( $avalability_fields, $product_id ) {
	if( !$product_id ) {
		if( isset( $avalability_fields['_wc_appointment_has_restricted_days'] ) ) {
			$avalability_fields['_wc_appointment_has_restricted_days']['dfvalue'] = 'yes'; 
		}
		if( isset( $avalability_fields['_wc_appointment_restricted_days'] ) ) {
			$avalability_fields['_wc_appointment_restricted_days']['value'] = array(1,2,3,4,5); 
		}
	}
	return $avalability_fields;
}, 50, 2 );



// -------------------------------------------------------------
//
//   Onboarding Customization
//
// -------------------------------------------------------------
//add_filter( 'wcfm_is_allow_store_banner', false );

add_action('wcfm_page_heading', 'spiral_test_1');
function spiral_test_1() {
    $user_data = get_user_meta( get_current_user_id(), 'wcfmmp_profile_settings', true );
    if ( !isset( $user_data['spiral-vendor-timezone'] ) ) {
        echo '<div class="spiral-header-notice"><a href="' . get_wcfm_settings_url() . '#wcfm_settings_form_timezone_head"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Set your Timezone!</a></div>';
    }
}


// -------------------------------------------------------------
//
//   Skip Cart, instant checkout
//
// -------------------------------------------------------------
add_filter('woocommerce_add_to_cart_redirect', 'add_to_cart_redirect');
function add_to_cart_redirect() {
 global $woocommerce;
 $checkout_url = wc_get_checkout_url();
 return $checkout_url;
}

// -------------------------------------------------------------
//
//           Disable WCFM messed up features
//        password change, email verification, progress bar, etc
//
// -------------------------------------------------------------
add_filter( 'wcfm_is_allow_update_password', '__return_false' );
//add_action( 'begin_wcfm_wcvendors_profile_form', function() { echo '<a href="">Change password</a>'; } );
add_filter( 'wcfm_profile_fields_about', function() { return; });
add_filter( 'wcfm_is_allow_email_verification', '__return_false' );
add_filter( 'wcfm_is_allow_profile_complete_bar', '__return_false' );
add_filter( 'wcfm_is_allow_reports', '__return_false');
// in case off analytics problems, disable geolocate:
add_filter( 'wcfm_is_allow_wc_geolocate', '__return_false' );

add_filter( 'wcfm_is_allow_pm_variable', '__return_false' );
add_filter( 'wcfm_is_allow_linked', '__return_false' );


// -------------------------------------------------------------
//
//          Additional buttons and modifications
//          keywords: WCFM, Articles, Blog, Blogposts
//
// -------------------------------------------------------------
add_action( 'wcfm_products_quick_actions', 'add_blogposts_button');
function add_blogposts_button() {
    echo '<a id="see_blogposts_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_articles_url().'" data-tip="' . __('Switch to Blogpost Manager', 'wc-frontend-manager') . '"><span class="wcfmfa fa-edit"></span><span class="text">' . __( 'Blogposts', 'wc-frontend-manager') . '</span></a>';
}

// --------------------------------------------------------------
//
//                  Inquiry list design fix
//
// --------------------------------------------------------------
add_filter( 'wcfm_enquiry_message_display', 'spiral_enquiry_list_length_limit', 50, 2 );
function spiral_enquiry_list_length_limit( $text, $id ) {
    return substr(strip_tags($text), 0, 50);
}

// -------------------------------------------------------------
//
//                  Inquiry notification emails
//                  override hardcoded defaults
//
// -------------------------------------------------------------


add_action( 'wcfm_after_enquiry_submit', 'spiral_wcfm_inquiry_vendor_notification_email', 10, 6 );

function spiral_wcfm_inquiry_vendor_notification_email( $enquiry_id, $customer_id, $product_id, $vendor_id, $enquiry, $wcfm_enquiry_tab_form_data ) {
    
    if ( $customer_id ) {
        $first_name = get_user_meta( $customer_id, 'first_name' );
        $last_name = get_user_meta( $customer_id, 'last_name' );
        $subject = __( "New message from", "wc-frontend-manager" ) . " " . $first_name[0] . " " . $last_name[0];
    } else {
        $subject = __( "New message received", "wc-frontend-manager" );
    }
			$message =   $first_name[0] . ' ' . $last_name[0] . ' just sent you the following message:' .
												    '<br/><br/><strong><i>' . 
													'"{enquiry}"' . 
													'</i></strong><br/><br/>' .
													'{additional_info}' .
													'<a href="{enquiry_url}" class="button">Reply</a>' .
                                                    '</br></br>';
													 
			$message = str_replace( '{enquiry_url}', get_wcfm_enquiry_manage_url( $enquiry_id ), $message );
			$message = str_replace( '{enquiry}', $enquiry, $message );
			$message = str_replace( '{additional_info}', $additional_info, $message );
			$message = apply_filters( 'spiral_email_content_wrapper', $message, __( 'New Message', 'wc-frontend-manager' ) );
    
    if( wcfm_is_marketplace() && $vendor_id ) {

        $vendor_email = wcfm_get_vendor_store_email_by_vendor( $vendor_id );
        wp_mail( $vendor_email, $subject, $message );
                        
    }

}


add_action( 'wcfm_after_enquiry_reply_submit', 'spiral_wcfm_inquiry_reply_vendor_notification_email', 10, 5 );

function spiral_wcfm_inquiry_reply_vendor_notification_email( $enquiry_id, $customer_id, $product_id, $vendor_id, $enquiry ) {
    
    if ( $customer_id ) {
        $first_name = get_user_meta( $customer_id, 'first_name' );
        $last_name = get_user_meta( $customer_id, 'last_name' );
        $subject = __( "New reply from", "wc-frontend-manager" ) . " " . $first_name[0] . " " . $last_name[0];
    } else {
        $subject = __( "New reply received", "wc-frontend-manager" );
    }
			$message =   $first_name[0] . ' ' . $last_name[0] . ' just sent you the following message:' .
												    '<br/><br/><strong><i>' . 
													'{enquiry}' . 
													'</i></strong><br/><br/>' .
													'{additional_info}' .
													'<a href="{enquiry_url}" class="button">Reply</a>' .
                                                    '</br></br>';
													 
			$message = str_replace( '{enquiry_url}', get_wcfm_enquiry_manage_url( $enquiry_id ), $message );
			$message = str_replace( '{enquiry}', $enquiry, $message );
			$message = str_replace( '{additional_info}', $additional_info, $message );
			$message = apply_filters( 'spiral_email_content_wrapper', $message, __( 'New Reply', 'wc-frontend-manager' ) );
    
    if( wcfm_is_marketplace() && $vendor_id ) {

        $vendor_email = wcfm_get_vendor_store_email_by_vendor( $vendor_id );
        wp_mail( $vendor_email, $subject, $message );
                        
    }

}


add_filter( 'spiral_email_content_wrapper', 'spiral_email_content_wrapper', 10, 2 );
           
function spiral_email_content_wrapper( $content_body, $email_heading ) {
    global $WCFM;

    ob_start();
    wc_get_template( 'emails/email-header.php', array( 'email_heading' => $email_heading ) );
    $content_body_head = ob_get_clean();
    ob_start();
    wc_get_template( 'emails/email-footer.php' );
    $content_body_foot = ob_get_clean();

    $wcemail  = new WC_Email();
    $content_body  = $content_body_head . $content_body . $content_body_foot;
    $content_body  = apply_filters( 'woocommerce_mail_content', $wcemail->style_inline( $content_body ) );

    return $content_body;
}






// -------------------------------------------------------------
//
//           WC Appointments add Vendor Timezones feature
//
// -------------------------------------------------------------
add_filter( 'spiral_override_timezone_string', 'spiral_add_vendor_timezones', 10, 2 );
function spiral_add_vendor_timezones( $vendor_timezone, $product_obj ) {

    global $WCFM, $WCFMmp;

    if( is_object($product_obj) ) {
        $product_id = $product_obj->get_id();
        $vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );	
    } else {
        $user = wp_get_current_user();
        if ( in_array( 'wcfm_vendor', (array) $user->roles ) ) {
            $vendor_id = get_current_user_id();
        } else {
            $vendor_id = null;
        }
        
    }

    $vendor_settings = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
    if( !$vendor_settings ) $vendor_settings = array();
    $vendor_timezone = isset( $vendor_settings['spiral-vendor-timezone'] ) ? $vendor_settings['spiral-vendor-timezone'] : 'Europe/Berlin';
    
    return $vendor_timezone;
}




// --------------------------------------------------------------
//
//         WC Appointment notification email customization
//
// --------------------------------------------------------------

add_filter( 'woocommerce_email_recipient_admin_new_appointment', 'reminder_email_recipients', 10, 2 );
add_filter( 'woocommerce_email_recipient_admin_appointment_cancelled', 'reminder_email_recipients', 10, 2 );

function reminder_email_recipients( $recipient, $appointment ) {
        global $WCFM, $WCFMmp;

        if ( ! is_a( $appointment, 'WC_Appointment' ) ) {
			return $recipient;
		}

		$staff = $appointment->get_staff_members();

        $product_id = $appointment->get_product_id();
        $vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $product_id );	
        $vendor = get_user_by( 'ID', $vendor_id );
        if ( $vendor ) {
            $vendor_email = $vendor->user_email;
        }
    
		if ( $appointment->has_staff() && ( $staff ) ) {
			$staff_emails = array();
			foreach ( (array) $staff as $staff_member ) {
				$staff_emails[] = $staff_member->user_email;
			}
			$staff_emails[] = $vendor_email;
            $staff_emails = array_unique( $staff_emails );
			$recipient      = implode( ', ', $staff_emails );
		} else {
            $recipient = $vendor_email;
        }

		return $recipient;
}



//
//   Don't send reminder email if booking was made
//     less than $max_hours before the appointment
//
//     not working, disabled until fixed
//
//add_filter( 'woocommerce_email_reminder_recipients', 'spiral_appointment_reminder_emails', 10, 2 );
function spiral_appointment_reminder_emails( $recipients, $appointment ) {
    $max_hours = 24;
    $booked = $appointment->get_date_created();
    $starts = $appointment->get_start();
    if ( $starts - $booked < $max_hours * 60 * 60 ) {
        $recipients = '';
    }
    return $recipients;
}



// -------------------------------------------------------------
//
//          Offering management customizations
//          - no free offerings in free plan
//          - auto-add vendor as staff (for calendar sync) - failed
//
// -------------------------------------------------------------

/*
add_filter( 'wcfm_form_custom_validation', function( $wcfm_products_manage_form_data, $form ) {
    $spiral_membership = wcfm_get_membership();
    if ($spiral_membership == '83254') {
    if( $form == 'product_manage' ) {
		$product_price = isset( $wcfm_products_manage_form_data['regular_price'] ) ? wc_clean( $wcfm_products_manage_form_data['regular_price'] ) : 0;
		$product_price = absint( $product_price );
		if( !$product_price || ( $product_price < 10 ) ) {
			$wcfm_products_manage_form_data['has_error'] = true;
			$wcfm_products_manage_form_data['message'] = 'Minimum price is 10 EUR. Upgrade to add free offerings! <a class=\'add_new_wcfm_ele_dashboard text_tip\' href=\''.get_wcfm_membership_page().'\'>&nbspLearn More&nbsp</a>';
		}
	}
	return $wcfm_products_manage_form_data;
    }
}, 50, 2);
*/

/*
// --------- trying to auto-include vendor as staff
// method 1 - FAIL :(
add_filter( 'wcfm_form_custom_validation', function( $wcfm_products_manage_form_data, $form ) {
    //if( $form == 'product_manage' && $wcfm_products_manage_form_data['product_type'] == 'appointment' ) {
       //&& !isset( $wcfm_products_manage_form_data['_wc_appointment_staffs[0][staff_id]'])
        $wcfm_products_manage_form_data['staff_id'] = 41;
    //}
	return $wcfm_products_manage_form_data;
}, 60, 2);
*/

//add_action( 'after_wcfm_products_manage_meta_save', 'spiral_auto_add_vendor_as_staff' );
//add_action( 'wp_loaded', 'spiral_auto_add_vendor_as_staff' );
function spiral_auto_add_vendor_as_staff( $post_ID ) {
    global $theappointment;
    $appointable_product = new WC_Product_Appointment( 100364 );
    //if ( is_wc_appointment_product( $appointable_product )) {
        $staff_ids = 39; //static id for testing
        $appointable_product->set_staff_ids( $staff_ids );
    //}
}

// -------------------------------------------------------------
//
//             Appointment Details Page Modifications
//
// Hooks list from view file:
// do_action( 'begin_wcfm_appointments_details' );
// do_action( 'begin_wcfm_appointments_details_overview', $appointment_id, $order->get_order_number() );
// do_action( 'before_wcfm_appointments_customer_details', $appointment_id, $order->get_order_number() );
// do_action( 'end_wcfm_appointments_details', $appointment_id, $order->get_order_number() );
// do_action( 'after_wcfm_appointments_details', $appointment_id, $order->get_order_number() );
//
// -------------------------------------------------------------

/*
function spiral_message_customer_button_appointments_page($appointment_id) {
    global $theappointment;
    $post = get_post($appointment_id);
    $appointment = new WC_Appointment( $post->ID );
    $customer_id = $appointment->get_customer_id( 'edit' );
        echo '<tr class="view">';
        echo '<th>&nbsp;</th>';
        echo '<td>';
        $shortcode = '[yobro_chat_new_message user_id=' . $customer_id . ' new_message=\'true\']';
        echo do_shortcode( $shortcode );
        //echo '<a class="button" target="_blank" href="' . get_wcfm_view_order_url( $order_id ) . '">' . __( $customer_id, 'wc-frontend-manager-ultimate' ) . '</a>';
        echo '</td>';
        echo '</tr>';
    return;
}
add_action( 'end_wcfm_appointments_details', 'spiral_message_customer_button_appointments_page');
*/

// ------------------------------------------------------------------------------
//
//   Hide my-account menu for vendors (they need to go there for password change)
//
// ------------------------------------------------------------------------------
add_filter( 'woocommerce_account_menu_items', 'spiral_remove_my_account_links' );
function spiral_remove_my_account_links( $menu_links ){

    $downloads     = WC()->customer->get_downloadable_products();
    $has_downloads = (bool) $downloads;
    if ( !$has_downloads ) {
        unset( $menu_links['downloads'] );
    }
    
    unset( $menu_links['dashboard'] );
    unset( $menu_links['orders'] );

//  unset( $menu_links['payment-methods'] ); // Remove Payment Methods
//	unset( $menu_links['customer-logout'] ); // Remove Logout link
    $user = wp_get_current_user();
    if ( !in_array( 'wcfm_vendor', (array) $user->roles ) ) {
        return $menu_links;
    }
}

add_filter( 'wcfm_is_allow_my_account_become_vendor', '__return_false' );

// -----------------------------------------------------------
//
//               Clean up Dashboard Menu
//
// -----------------------------------------------------------

add_action( 'wcfm_menus', 'spiral_remove_menu_items', 20);
function spiral_remove_menu_items( $menu ) {
    unset( $menu['wcfm-orders']);
    unset( $menu['wcfm-reports']);
    return $menu;
}


// ----------------------------------------------------------------
//
//   Disable pdf invoice attachments for free plan signup emails
//
// ----------------------------------------------------------------

add_filter( 'wcfm_is_allow_membership_subscription_invoice', function( $is_allow, $membership_id, $member_id ) {
	if( function_exists('get_wcfm_basic_membership') ) {
		$basic_membership = get_wcfm_basic_membership();
		if( $basic_membership && ( $basic_membership == $membership_id ) ) {
			$is_allow = false;
		}
	}
	return $is_allow;
}, 50, 3 );

// Legacy version of disabling free plan invoice
/*
add_filter( 'wcfm_is_allow_membership_subscription_invoice', function( $is_allow ) {
	if( function_exists('wcfm_get_membership')) {
        $active_membership = wcfm_get_membership();
		if ( $active_membership = '83254') {
            $is_allow = false;
        }
	}
	return $is_allow;
}, 50, 3 ); */



// -----------------------------------------------------------
//
//                WCFM copy modifications
//
// -----------------------------------------------------------

add_filter( 'gettext', 'customize_wcfm_copies', 20, 3 );
function customize_wcfm_copies( $modified_text, $text, $domain ) {
    switch ( $modified_text ) {
        case 'Store Setup' :
            $modified_text = __('Quick Setup', 'wc-multivendor-marketplace');
        break;
        case 'Thank you for choosing %s! This quick setup wizard will help you to configure the basic settings and you will have your store ready in no time.' :
            $modified_text = __('Let\'s quickly set some basic settings to prepare your landing page.', 'wc-multivendor-marketplace');
        break;
        case 'If you don\'t want to go through the wizard right now, you can skip and return to the dashboard. You may setup your store from dashboard &rsaquo; setting anytime!' :
            $modified_text = __('If you don\'t want to go through this setup right now, just skip it and set your preferences later in Settings.', 'wc-multivendor-marketplace');
        break;
        case 'Your store is ready. It\'s time to experience the things more Easily and Peacefully. Add your products and start counting sales, have fun!!' :
            $modified_text = __('Time to build your landing page! Remember to set your timezone in settings!', 'wc-multivendor-marketplace');
        break;
        case 'Store setup' :
            $modified_text = __('Brand Setup', 'wc-multivendor-marketplace');
        break;
        case 'Store settings' :
            $modified_text = __('Brand Settings', 'wc-multivendor-marketplace');
        break;
        case 'Store Settings':
            $modified_text = __('Settings');
        break;
        case 'Shop Name' :
            $modified_text = __('Brand Name', 'wc-multivendor-marketplace');
        break;
        case 'Store Name' :
            $modified_text = __('Brand Name', 'wc-multivendor-marketplace');
        break;
        case 'Shop Description' :
            $modified_text = __('Short Bio', 'wc-multivendor-marketplace');
        break;
        case 'Store' :
            $modified_text = __('Brand', 'wc-multivendor-marketplace');
        break;
        case 'Add-ons' :
            $modified_text = __('Extra Fields', 'wc-multivendor-marketplace');
        break;
        case 'Product' :
            $modified_text = __('Offering', 'wc-multivendor-marketplace');
        break;
        case 'Products' :
            $modified_text = __('Offerings', 'wc-multivendor-marketplace');
        break;
        case 'All product types' :
            $modified_text = __('All offering types', 'wc-multivendor-marketplace');
        break;
        case 'Appointable product' :
            $modified_text = __('Appointment offerings', 'wc-multivendor-marketplace');
        break;
        case 'Manage Product' :
            $modified_text = __('Manage Offering', 'wc-multivendor-marketplace');
        break;
        case 'Edit Product' :
            $modified_text = __('Edit Offering', 'wc-multivendor-marketplace');
        break;
        case 'Add Product' :
            $modified_text = __('Add Offering', 'wc-multivendor-marketplace');
        break;
        case 'Appointable' :
            $modified_text = __('Booking', 'wc-multivendor-marketplace');
        break;
        case 'Add fields to get additional information from customers' :
            $modified_text = __('Add upgrade options for extra charge, or ask additional customer information.<br>The fields you add will appear during the booking process.', 'wc-multivendor-marketplace');
        break;
        case 'Submit' :
            $modified_text = __('Save', 'wc-multivendor-marketplace');
        break;
        case 'Store Logo' :
            $modified_text = __('Logo', 'wc-multivendor-marketplace');
        break;
        case 'Shop Name not available.' :
            $modified_text = __('Brand name not available, please try another', 'wc-multivendor-marketplace');
        break;
        case 'Visit now ...' :
            $modified_text = __('Dashboard', 'wc-multivendor-membership');
        break;
        case 'Return to the WordPress Dashboard' :
            $modified_text = __('Return to the Dashboard', 'wc-frontend-manager');
        break;
        case 'Edit Inquiry' :
            $modified_text = __('Reply to Inquiry', 'wc-frontend-manager');
        break;
        case 'You are connected with Stripe' :
            $modified_text = __('<span class=\'spiral-check-big\'>✔</span> Successfully connected with Stripe', 'wc-frontend-manager');
        break;
        case 'Restricted days' :
            $modified_text = __('Bookable days', 'wc-frontend-manager-ultimate');
        break;
        case 'Thrusday' :
            $modified_text = __('Thursday', 'wc-frontend-manager-ultimate');
        break;
        case 'Recurring Time (all week)' :
            $modified_text = __('All bookable days', 'woocommerce-appointments');
        break;
        case 'Custom Availability' :
            $modified_text = __('Availability', 'woocommerce-appointments');
        break;
        case 'Two way - between Store and Google' :
            $modified_text = __('Both ways - between Spiral and Google', 'wc-frontend-manager-ultimate');
        break;
        case 'One way - from Store to Google' :
            $modified_text = __('One way - from Spiral to Google', 'wc-frontend-manager-ultimate');
        break;
        case 'gross sales in this month' :
            $modified_text = __('gross sales this month', 'wc-multivendor-marketplace');
        break;
        case 'received in this month' :
            $modified_text = __('received this month', 'wc-multivendor-marketplace');
        break;
        case 'Select intervals when each appointment slot is available for scheduling.' :
            $modified_text = __('Increment of timeslots. Example: 30 minutes means customer can pick time in 30 minute increments when booking', 'wc-multivendor-marketplace');
        break;
        case 'If not appointable, users won\'t be able to choose slots in this range for their appointment.' :
            $modified_text = __('Do you want to enable or disable appointment booking for the set timeframe? Setting Yes enables bookings.', 'wc-multivendor-marketplace');
        break;
        case 'There is no inquiry yet!!' :
            $modified_text = __('No new messages');
        break;
        case 'Inquiries' :
        case 'Enquiries' :
            $modified_text = __('Messages');
        break;
        case 'Inquiry' :
        case 'Enquiry' :
            $modified_text = __('Message');
        break;
        case 'Inquiry Board':
            $modified_text = __('Message Board');
        break;
        case 'FREE Plan!!! There is no payment option for this.':
            $modified_text = __('');
        break;
        case '%sChange or Upgrade your current membership plan >>%s':
            $modified_text = __('');
        break;
        case "Are you sure and want to 'Mark as Complete' this Appointment?":
            $modified_text = __('Are you sure you want to Confirm this Appointment?');
        break;
        case 'Sorry, your session has expired. %sReturn to shop%s':
            $modified_text = __('Sorry, your session has expired. %sReturn to Spiral%s');
        break;
        case 'Store Analytics':
            $modified_text = __('Profile Analytics', 'wc-frontend-manager-analytics');
        break;
        case 'Store Analytics BY':
            $modified_text = __('Profile Analytics', 'wc-frontend-manager-analytics');
        break;
        case 'Filter by product':
            $modified_text = __('Filter by Offering');
        break;
        case 'Related products':
            $modified_text = __('Other Offerings');
        break;
        case 'Payment setup':
            $modified_text = __('Payout setup');
        break;
        case 'You have reached your product limit!':
            $modified_text = __(' Offering limit reached. Please upgrade to Pro Plan for unlimited offerings');
        break;

    }
    return $modified_text;
}


add_filter( 'gettext', 'spiral_reschedule_coupon_notification', 20, 3 );
function spiral_reschedule_coupon_notification( $modified_text, $text, $domain ) {
    $url = $_SERVER["REQUEST_URI"];
    $is_reschedule_page = strpos($url, 'reschedule');
    if ( $is_reschedule_page ) {
        switch ( $modified_text ) {
            case 'Coupon code applied successfully.':
                $modified_text = __('Previous payment will be deducted at checkout.');
            break;
        }
    }
    return $modified_text;    
}


add_filter( 'gettext', 'customize_editor_welcome_page', 20, 3 );
function customize_editor_welcome_page( $modified_text, $text, $domain ) {
    switch ( $modified_text ) {
        case 'Name Your Page, Select' :
            $modified_text = 'Add Your First Block';
        break;
        case 'Layout and Start Building' :
            $modified_text = 'And Start Building';
        break;
        case 'Start by adding an element to your layout or select one of the pre-defined templates.' :
            $modified_text = 'Start by adding your first block template to your layout and customize it to your needs.';
        break;
        case 'Add Template' :
            $modified_text = 'Add Block';
        break;
        case 'Sorry! No Conversation Found.' :
            $modified_text = 'No Messages Yet';
        break;
    }
    return $modified_text;
}


// Payouts setup information
//
add_action( 'before_wcfm_payments', function() { echo '<div>Payouts are automatically sent on Mondays, for all 8+ days old transactions</div>'; } );

// -------------------------------------------------------------
//
//                  Fix WCFM notification emails
//
// -------------------------------------------------------------

// original code: wc-frontend-manager/core/class-wcfm-notification.php : 369+

add_filter( 'gettext', 'customize_wcfm_notification_emails', 20, 3 );
function customize_wcfm_notification_emails( $translated_text, $text, $domain ) {
    switch ( $translated_text ) {
        /*case 'Hi' :
            $translated_text = __('', 'wc-frontend-manager');
        break;*/
            
        case 'We recently have a inquiry from you regarding "%s". Please check below for our input for the same: ' :
            $translated_text = __('You just received the following message:', 'wc-frontend-manager');
        break;

        case 'Check more details %shere%s.' :
            $translated_text = __('You can reply on your %sDashboard%s', 'wc-frontend-manager');
        break;

        case 'Thank You' :
            $translated_text = __('', 'wc-frontend-manager');
        break;

        case 'Inquiry Reply' :
            $translated_text = 'New Message';
        break;

        /*case '<br/><br/>' :
            $translated_text = '';
        break;*/
        case 'Welcome to the store!' :
            $translated_text = 'Welcome to Spiral!';
        break;
        case 'Withdrawal Requests' :
            $translated_text = 'Payout Status';
        break;
        case 'Your withdrawal request #%s %s.' :
            $translated_text = __('The status of the payout #<strong>%s</strong> is currently <strong>%s</strong><br><br>Check out the payout details by clicking the payout number above. If you have any questions, feel free to contact us by replying to this email.<br/>', 'wc-multivendor-marketplace');
        break;
            
    }
    return $translated_text;
}

add_filter( 'wcfm_email_content_wrapper', 'email_line_break_cleanup' );
function email_line_break_cleanup($content) {
    
    $content = str_replace("<br /><br/>", "", $content);
    $content = str_replace("You have received a new notification:<br/><br/>", "", $content);
    if (strpos($content, 'The status of the payout') !== false) {
        $content = str_replace("Hi,<br/><br/>", "", $content);
        // couldn't remove link, the hack below makes it disappear
        $content = str_replace('You can reply on your', "", $content);
        $content = str_replace('Dashboard', "", $content);
    }
    
    return $content;
}

add_filter( 'wcfm_email_subject_wrapper', 'email_subject_cleanup' );
function email_subject_cleanup($subject) {
    $subject = str_replace("Spiral: ", "", $subject);
    $subject = str_replace(" - ", " ", $subject);

    if (strpos($subject, 'Reply for your Inquiry') !== false) {
        $subject = 'You got a new message';
    } elseif (strpos($subject, 'Notification Payout Status') !== false) {
        $subject = 'Payout Notification';
    }
    
    return $subject;
}

// don't include customer email address in vendor notification emails
add_filter( 'wcfm_is_allow_enquiry_customer_reply', '__return_false' );
//add_filter( 'wcfm_is_allow_new_appointments_vendor_notification', '__return_false' );
//add_filter( 'wcfm_is_allow_cancel_appointments_vendor_notification', '__return_false' );


//add_action( 'wcfm_after_dashboard_stats_box', 'spiral_dashboard_appointments');
function spiral_dashboard_appointments() {
    global $WCFMu;
    include_once( $WCFMu->plugin_path . 'includes/appointments_calendar/class-wcfm-appointments-calendar.php' );
    $cal = new WCFM_Appointments_Calendar();
    $cal->list_appointments( date(d), date(m), date(Y) );
}



// -------------------------------------------------------------------
//
//                      Restrict admin access
// (wcfm feature substitute with visual composer added as exception)
//
// -------------------------------------------------------------------


function spiral_restrict_admin_access() {
	  global $WCFM, $_GET;
			$is_vc = false;
            $is_setup  = false;
			$is_export = false;
			$is_import = false;
			if( isset($_GET['vcv-action']) && ( $_GET['vcv-action'] == 'frontend' ) ) { $is_vc = true; }
            //if( isset($_GET['action']) && ( $_GET['action'] == 'elementor' ) ) { $is_vc = true; }
            if( isset($_GET['page']) && ( $_GET['page'] == 'product_exporter' ) ) { $is_export = true; }
			if( isset($_GET['page']) && ( $_GET['page'] == 'product_importer' ) ) { $is_import = true; }
			//if( isset($_GET['page']) && ( $_GET['page'] == 'store-setup' ) ) { $is_setup = true; }
			if( wcfm_is_vendor() && !defined('DOING_AJAX') && !$is_vc && !$is_export && !$is_import && !$is_setup ) {
				if( isset( $_GET['wc_gcal_oauth'] ) || isset( $_GET['wc_gcal_logout'] ) ) {
					// WC Appointments Gcal OAuth support
					wp_redirect( get_wcfm_profile_url() . '#sm_profile_form_gcal_sync' );
				} else {
					wp_redirect( get_wcfm_url() );
				}
				exit;
			}
	}

add_action( 'admin_init', 'spiral_restrict_admin_access');



// --------------------------------------------------------------
//
//
//          Add order notes to Appointment detail page
//
//
// --------------------------------------------------------------


add_filter( 'wcfm_is_allow_order_note_attachments', false );

add_action( 'after_wcfm_appointments_details', 'order_notes_to_appointment_detail_page', 10, 2 );

function order_notes_to_appointment_detail_page($appointment_id, $order_id) {
    global $wp, $WCFM, $WCFMu, $wp_query;
    if( apply_filters( 'wcfm_allow_order_notes', true ) ) {
        ?>
        <div class="wcfm-clearfix"></div>
        <br />
        <!-- collapsible -->
        <div class="page_collapsible orders_details_notes" id="wcfm_order_notes_options"><?php _e('Messages', 'wc-frontend-manager-ultimate'); ?><span></span></div>
        <div class="wcfm-container">
            <div id="orders_details_notes_expander" class="wcfm-content">

                <?php if( $view_add_order_notes = apply_filters( 'wcfm_add_order_notes', true ) ) { ?>
                    <div class="add_note">
                      <form name="wcfm_add_order_note_form" id="wcfm_add_order_note_form" action="" method="POST">
                            <h2><?php _e( 'Send Message', 'wc-frontend-manager-ultimate' ); ?> <span class="wcfmfa fa-question-circle img_tip" data-tip="<?php _e( 'Send a message to the client', 'wc-frontend-manager-ultimate' ); ?>"></span></h2>
                            <div class="wcfm-clearfix"></div>
                            <p>
                                <textarea type="text" name="order_note" id="add_order_note" class="input-text wcfm-textarea wcfm_full_ele" cols="20" rows="5"></textarea>
                            </p>
                            <p>
                            <?php
                            if( apply_filters( 'wcfm_is_allow_order_note_attachments', true ) ) {
                                $WCFM->wcfm_fields->wcfm_generate_form_field( array( "order_note_attachments"  => array( 'label' => __( 'Attachment(s)', 'wc-frontend-manager'), 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele wcfm_non_sortable', 'label_class' => 'wcfm_title', 'value' => array(), 'options' => array(
                                                                                                                                                                                                                                                                                    "name" => array('label' => __('Name', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_ele wcfm_title' ),
                                                                                                                                                                                                                                                                                    "file" => array('label' => __('File', 'wc-frontend-manager'), 'type' => 'upload', 'mime' => 'Uploads', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_ele wcfm_title' ),
                                                                                                                                                                                                                                                                         ), 'desc' => sprintf( __( 'Please upload any of these file types: %1$s', 'wc-frontend-manager' ), '<b style="color:#f86c6b;">' . implode( ', ', array_keys( wcfm_get_allowed_mime_types() ) ) . '</b>' ) )
                                                                                                                        ) );
                            }

                            do_action( 'wcfm_order_add_note_form_end', $wp->query_vars['wcfm-orders-details'] );
                            ?>
                            </p>
                            <p>
                                <input type="hidden" name="order_note_type" value="customer">
                                <div class="wcfm-clearfix"></div>
                                <input type="hidden" name="add_order_note_id" value="<?php echo $order_id; ?>">
                                <a href="#" class="add_note button" id="wcfm_add_appointment_order_note" data-orderid="<?php echo $wp->query_vars['wcfm-orders-details']; ?>"><?php _e( 'Send', 'wc-frontend-manager-ultimate' ); ?></a>
                                <div class="wcfm-clearfix"></div>
                            </p>
                        </form>
                    </div>
                <?php } ?>
                
                <?php
                    if( $view_view_order_notes = apply_filters( 'wcfm_view_order_notes', true ) ) {
                        $args = array(
                            'post_id'   => $order_id,
                            'orderby'   => 'comment_ID',
                            'order'     => 'ASC',
                            'approve'   => 'approve',
                            'type'      => 'order_note'
                        );

                        $args = apply_filters( 'wcfm_order_notes_args', $args );

                        remove_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

                        $notes = apply_filters( 'wcfm_order_notes', get_comments( $args ), $order_id/*$wp->query_vars['wcfm-orders-details']*/ );

                        add_filter( 'comments_clauses', array( 'WC_Comments', 'exclude_order_comments' ), 10, 1 );

                        echo '<table id="notes_holder"><tbody>';

                        if ( $notes ) {

                            foreach( $notes as $note ) {

                                $note_classes   = array( 'note' );
                                $note_classes[] = get_comment_meta( $note->comment_ID, 'is_customer_note', true ) ? 'customer-note' : '';
                                $note_classes[] = $note->comment_author === __( 'WooCommerce', 'wc-frontend-manager-ultimate' ) ? 'system-note' : '';
                                $note_classes   = apply_filters( 'woocommerce_order_note_class', array_filter( $note_classes ), $note );
                                ?>
                                <tr class="<?php echo esc_attr( implode( ' ', $note_classes ) ); ?>">
                                    <td>
                                        <?php echo wpautop( wptexturize( wp_kses_post( $note->comment_content ) ) ); ?>
                                    </td>
                                    <td>
                                        <span style="cursor: help;" class="exact-date text_tip" data-tip="<?php echo $note->comment_date; ?>"><?php printf( __( 'added on %1$s at %2$s', 'wc-frontend-manager-ultimate' ), date_i18n( wc_date_format(), strtotime( $note->comment_date ) ), date_i18n( wc_time_format(), strtotime( $note->comment_date ) ) ); ?></span>
                                        <?php if ( $note->comment_author !== __( 'WooCommerce', 'wc-frontend-manager-ultimate' ) ) printf( ' ' . __( 'by %s', 'wc-frontend-manager-ultimate' ), $note->comment_author ); ?>
                                    </td>
                                </tr>
                                <?php
                            }

                        } else {
                            //echo '<li>' . __( 'There are no notes yet.', 'wc-frontend-manager-ultimate' ) . '</li>';
                        }

                        echo '</tbody></table>';
                    }
                ?>

            </div>
        </div>
        <!-- end collapsible -->
        <?php
    }
}


// -------------------------------------------------------------
//
//             Appointment Rescheduling Feature
//
// -------------------------------------------------------------

add_filter( 'appointments_cancel_appointment_url', 'custom_cancel_appointment_url', 10, 2 );
function custom_cancel_appointment_url( $url, $appointment ) {
	if (strpos($url, 'reschedule') !== false) {
        $cancel_page = get_permalink( get_option('woocommerce_myaccount_page_id') ) . 'reschedule/';
        $url = wp_nonce_url(
            add_query_arg(
                array(
                    'cancel_appointment' => 'true',
                    'appointment_id' => $appointment->get_id(),
                    'redirect' => '',
                ),
                $cancel_page
                ),
        'woocommerce-appointments-cancel_appointment'
        );
    }
	return $url;
}

add_action( 'init', 'spiral_reschedule_my_account_endpoint' );
function spiral_reschedule_my_account_endpoint() {
    add_rewrite_endpoint( 'reschedule', EP_PAGES );
}

function spiral_reschedule_endpoint_content() {

    global $woocommerce;
    
    $appointment_id = $_GET['appointment_id'];
    $appointment = get_wc_appointment( $appointment_id );
    if ($appointment) $customer_email = $appointment->get_customer()->email;
    $current_user = wp_get_current_user();
    $user_email = $current_user->user_email;    
    
    if ( $customer_email == $user_email and wp_verify_nonce( $_REQUEST['_wpnonce'], 'woocommerce-appointments-cancel_appointment' ) ) {

        $product_id = $appointment->get_product_id();
        $expiry_date = date("Y-m-d", strtotime('+2 days'));
        $order_amount = $appointment->get_order()->get_total();
    
        // Get discount amount
        if ( sizeof($appointment->get_order()->get_items()) == 1 ) {
            $amount = $order_amount;
            
        } else {
            $order = $appointment->get_order();
            $items = $order->get_items(); 
            foreach ( $items as $item_id => $item ) {
                $order_item_id = $item->get_variation_id() ? $item->get_variation_id() : $item->get_product_id();
                if ( $product_id == $order_item_id ) {
                    $amount = $item->get_total();
                }
            }
        }
        
        $coupon_code = $appointment_id . time();
        $discount_type = 'fixed_cart'; // Type: fixed_cart, percent, fixed_product, percent_product

        $coupon = array(
            'post_title' => $coupon_code,
            'post_content' => '',
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type'     => 'shop_coupon'
        );    

        $new_coupon_id = wp_insert_post( $coupon );

        // Add meta
        update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
        update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
        update_post_meta( $new_coupon_id, 'individual_use', 'yes' );
        //update_post_meta( $new_coupon_id, 'product_ids', $product_id );
        update_post_meta( $new_coupon_id, 'usage_limit', '1' );
        update_post_meta( $new_coupon_id, 'expiry_date', $expiry_date );
        update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
        update_post_meta( $new_coupon_id, 'email_restrictions', array($customer_email) );
        
        //WC()->cart->add_discount( $coupon_code );
        WC()->cart->remove_coupons();
        if (!$woocommerce->cart->add_discount( sanitize_text_field( $coupon_code )))
        $woocommerce->show_messages();
        
        echo '<div><h4>Pick a new timeslot for your session of ' . $appointment->get_product_name() . '.</h4></div>';
        $product_id = $appointment->get_product_id();
        echo do_shortcode('[appointment_form id="' . $product_id . '" show_title="0" show_price="0" show_rating="0" show_excerpt="0" show_meta="0" show_sharing="0"]');

    } else {
        echo 'These are not the droids you are looking for...';
    }
}
add_action( 'woocommerce_account_reschedule_endpoint', 'spiral_reschedule_endpoint_content' );


// -- Hide Blue Coupon banner from Checkout page -- //
add_action( 'woocommerce_before_checkout_form', 'spiral_remove_checkout_coupon_form', 9 );
function spiral_remove_checkout_coupon_form(){
    remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
    add_action( 'woocommerce_before_checkout_form', function() { echo '<br>'; } );
}




// -------------------------------------------------------------
//
//                     Visual Composer:
//           Allow vendors to access all templates
//
// -------------------------------------------------------------

function spiral_vendor_template_access() {
    $vendor_role = get_role( 'wcfm_vendor' );
    $vendor_role->add_cap( 'read_vcv_templates' );
    $vendor_role->remove_cap( 'read_private_vcv_templates' );
    $vendor_role->remove_cap( 'read_private_vcv_templatess' );
    $vendor_role->remove_cap( 'read_others_vcv_templates' );
    $vendor_role->remove_cap( 'edit_others_vcv_templatess' );
    $vendor_role->remove_cap( 'edit_vcv_templatess' );

    $vendor_role->remove_cap( 'edit_others_posts' );
    $vendor_role->remove_cap( 'edit_others_pages' );
}
    
add_action( 'plugins_loaded', 'spiral_vendor_template_access' );


// -------------------------------------------------------------
//                        section:
//
//      Add Visual Composer Landing Page into Profile Page
//
// -------------------------------------------------------------


//
// Find the profile post that belongs to the current user
// 
function spiral_get_profile_id_by_author( $author ) {    

    $args = array(
    'posts_per_page' => 1,
    'author' => $author,
    'post_type' => 'profile'
    );
    $posts = get_posts($args);
    $post_id = $posts[0]->ID;
    return $post_id;
    
}

//
//  Help VisualComposer enqueue assets on store page
//
function spiral_store_detection_for_vc() {
    global $WCFM, $WCFMmp, $wp, $WCFM_Query, $post;

    if ( wcfm_is_store_page() ) {
        
        $wcfm_store_url = get_option( 'wcfm_store_url', 'store' );
        $author         = get_query_var( $wcfm_store_url );
		$seller_info    = get_user_by( 'slug', $author );
		$author_id      = $seller_info->data->ID;
        $sourceId       = spiral_get_profile_id_by_author( $author_id );         
 
        return $sourceId;
        
    } else {

        $sourceId = get_the_ID();
        return $sourceId;
        
    }
}
add_filter( 'vc_before_enqueue_assets', 'spiral_store_detection_for_vc');


//
// Display edit button + profile page
//
function spiral_add_landing_page( $author ) {

    $post_id = spiral_get_profile_id_by_author( $author );

    $queried_post = get_post($post_id);    
    $content = apply_filters('the_content', $queried_post->post_content);
    $content = str_replace( ']]>', ']]&gt;', $content );

    // Add Edit button if user owns the page
    //
    if ( $author == get_current_user_id() and $content != '' ) {
        echo "<input type=button onClick=\"location.href='" . get_admin_url() . "post.php?post=" . $post_id . "&action=edit&vcv-action=frontend&vcv-source-id=" . $post_id . "'\" value='Edit this' style='float: left; position: absolute; margin-top: 30px; margin-left: 10px; z-index: 99'>";
    
    // Onboarding welcome screen!
    //
    }  else if ( $author == get_current_user_id() and $content == '' ) {
        echo "<div style='text-align: center; margin-top: 50px; margin-bottom: 60px'>
        <h2>This is your Introduction Page!</h2>
        <p>Pick from our pre-defined template blocks and build a great page in minutes!</p>
        <input type=button onClick=\"location.href='" . get_admin_url() . "post.php?post=" . $post_id . "&action=edit&vcv-action=frontend&vcv-source-id=" . $post_id . "'\" value='Start Building' style='margin-top: 30px'></div>";
    }

    // Display profile post inside store template (Method 2.0)
    echo $content;
    
    if ( $author == get_current_user_id() ) {
    add_filter( 'gettext', 'spiral_before_first_offering_user', 20, 3 );
    } else {
    add_filter( 'gettext', 'spiral_before_first_offering_visitor', 20, 3 );
    }
    function spiral_before_first_offering_user( $modified_text, $text, $domain ) {
        switch ( $modified_text ) {
            case 'No products were found matching your selection.' :
                $modified_text = __('Your Offerings will show up here.');
            break;
        }
        return $modified_text;
    }
    function spiral_before_first_offering_visitor( $modified_text, $text, $domain ) {
        switch ( $modified_text ) {
            case 'No products were found matching your selection.' :
                $modified_text = __('No offerings available yet');
            break;
        }
        return $modified_text;
    }

    
}
//add_action( 'wcfmmp_before_store_artworks', 'spiral_add_landing_page');
add_action( 'wcfmmp_store_before_products', 'spiral_add_landing_page');



// Remove Inquiry button on user's own profile
function spiral_no_self_inquiry( $author ) {
if ($author == get_current_user_id()) {
    add_filter('wcfmmp_is_allow_store_header_enquiry', false );
    }
}

add_action( 'wcfmmp_store_before_header', 'spiral_no_self_inquiry');



// ----------------------------------------------------------------
//
//                      Customize Blog Tab
//
// ----------------------------------------------------------------
add_action('wcfmmp_store_before_articles', 'spiral_add_new_post_button');
function spiral_add_new_post_button( $owner_id ) {
    if ( $owner_id == get_current_user_id() and have_posts() ) {
        echo '<a href=\'' . site_url() . '/edit/post-new.php\' class=\'button\'>Create new Post</a>';
        echo ' <a href=\'' . get_wcfm_url() . 'articles\' class=\'button alt\'>Manage posts and drafts</a>';
    } else if ( $owner_id == get_current_user_id() and !have_posts() ) {
        echo '<a href=\'' . site_url() . '/edit/post-new.php\' class=\'button\'>Create your first Blogpost!</a>';
        echo ' <a href=\'' . get_wcfm_url() . 'articles\' class=\'button alt\'>Manage drafts</a>';
    }
}


// ----------------------------------------------------------------
//
//                 Customize Tabs on Store Page
//
// ----------------------------------------------------------------


add_action( 'wcfmmp_after_store_article_loop_start', function( $store_id, $store_info ) {
	echo '<div>';
}, 50, 2);

add_action( 'wcfmmp_store_article_template', function() {
	get_template_part( 'content', '' );
});

add_action( 'wcfmmp_store_article_template_none', function() {
	get_template_part( 'content', 'none' );
});

add_action( 'wcfmmp_before_store_article_loop_end', function( $store_id, $store_info ) {
	the_posts_pagination();
	echo '</div>';
}, 50, 2);
add_filter( 'wcfm_is_allow_store_articles', '__return_true' );

// Rename tab to Blog
add_filter( 'wcfmmp_store_tabs', function( $store_tabs, $store_id ) {
  $store_tabs['articles'] = 'Blog';
  return $store_tabs;
}, 50, 2 );

add_filter( 'wcfmp_store_tabs_url', function( $store_tab_url, $tab ) {
    if( $tab == 'articles' ) {
        $store_tab_url = str_replace('articles', 'blog', $store_tab_url);
    }
    return $store_tab_url;
}, 50, 2 );

add_action( 'wcfmmp_rewrite_rules_loaded', function( $wcfm_store_url ) {
    add_rewrite_rule( $wcfm_store_url.'/([^/]+)/blog?$', 'index.php?'.$wcfm_store_url.'=$matches[1]&articles=true', 'top' );
    add_rewrite_rule( $wcfm_store_url.'/([^/]+)/blog/page/?([0-9]{1,})/?$', 'index.php?'.$wcfm_store_url.'=$matches[1]&paged=$matches[2]&articles=true', 'top' );
}, 50 );

// Rename Products tab to Offerings
add_filter( 'wcfmmp_store_tabs', function( $store_tabs, $store_id ) {
  $store_tabs['products'] = 'Offerings';
  return $store_tabs;
}, 50, 2 );

add_filter( 'wcfmmp_store_tabs', function( $store_tabs, $store_id ) {
  unset($store_tabs['about']);
  return $store_tabs;
}, 50, 2 );




// ----------------------------------------------------------------
//
//                Conditional Extra Tab Experiment
//
// ----------------------------------------------------------------


add_action( 'wcfmmp_rewrite_rules_loaded', function( $wcfm_store_url ) {
    add_rewrite_rule( $wcfm_store_url.'/([^/]+)/1?$', 'index.php?'.$wcfm_store_url.'=$matches[1]&1=true', 'top' );
    add_rewrite_rule( $wcfm_store_url.'/([^/]+)/1/page/?([0-9]{1,})/?$', 'index.php?'.$wcfm_store_url.'=$matches[1]&paged=$matches[2]&1=true', 'top' );
}, 50 );

add_filter( 'wcfmp_store_default_template', function( $template, $tab ) {
  if( $tab == '1' ) {
    $template = 'store/wcfmmp-view-store-reviews.php';
  }
  return $template;
}, 50, 2);

add_filter( 'query_vars', function( $vars ) {
    $vars[] = '1';
    return $vars;
}, 50 );

add_filter( 'wcfmp_store_default_query_vars', function( $query_var ) {
    global $WCFM, $WCFMmp;

    if ( get_query_var( '1' ) ) {
        $query_var = '1';
    }
    return $query_var;
}, 50 );


add_action( 'wcfmmp_store_before_header', 'spiral_eextra_tab');
function spiral_eextra_tab( $author ) {

    global $WCFM, $WCFMu;
    
    $user_data = get_user_meta( $author, 'wcfmmp_profile_settings', true );
    if( !$user_data ) $user_data = array();
    $user_timezone = isset( $user_data['spiral-vendor-timezone'] ) ? $user_data['spiral-vendor-timezone'] : '';
    
    if ( $user_timezone == 'Europe/London' ) {

        add_filter( 'wcfmmp_store_tabs', function( $store_tabs, $store_id ) {
            global $WCFM, $WCFMu;
    
            $user_data = get_user_meta( $store_id, 'wcfmmp_profile_settings', true );
            if( !$user_data ) $user_data = array();
            $user_timezone = isset( $user_data['spiral-vendor-timezone'] ) ? $user_data['spiral-vendor-timezone'] : '';
    
            $store_tabs['1'] = $user_timezone;
          return $store_tabs;
        }, 50, 2 );

        add_filter( 'wcfmp_store_tabs_url', function( $store_tab_url, $tab ) {
            if( $tab == '1' ) {
                $store_tab_url .= '1';
            }
            return $store_tab_url;
        }, 50, 2 );

    }}

/* -------------------------------------------------------
//                YoBro section (DEPRECATED)
// -------------------------------------------------------


// -----------------------------------------------------------------------
//
//        Add YoBro Message notification icon to Dashboard header
//
// -----------------------------------------------------------------------

function spiral_messaging_notification() {
    echo "<div class=\"wcfm_header_panel_messages text_tip\" data-tip=\"New Messages\">";
    echo do_shortcode('[yobro_chat_notification]');
    echo "</div>";
}
//add_action( 'wcfm_after_header_panel_item', 'spiral_messaging_notification' );


// -----------------------------------------------------------------------
//
//                Add YoBro Message button on profile pages
//
// -----------------------------------------------------------------------
function spiral_message_button( $owner_id ) {
    $shortcode = '[yobro_chat_new_message user_id=' . $owner_id . ' new_message=\'true\']';
    echo do_shortcode( $shortcode );
}
//add_action( 'after_wcfmmp_store_header_actions', 'spiral_message_button');


// --------------------------------------------------------------------
//
//                   YoBro Chat notification emails
//           (needs two methods because YoBro has shit code)
//
// --------------------------------------------------------------------


// Case 1.
// For messages sent from the coach profile page with the Send Message button
//
function spiral_profile_side_notification( $yobro_conv ) {
    $recipent_id = $yobro_conv['conversation']['reciever'];
    $sender_id = $yobro_conv['conversation']['sender'];
    $sender = get_user_by( 'id', $sender_id );
    $sender_name = $sender->first_name . ' ' . $sender->last_name;
    $recipent = get_user_by( 'id', $recipent_id );
    $recipent_email = $recipent->user_email;
    $message_text = $sender_name . " sent you a Message:" . "\r\n\"" . wp_strip_all_tags($yobro_conv['conversation']['message']) . "\"\r\n" . "Head over to <a href=\"" . get_home_url() . "/dashboard/msg\">Spiral</a> to reply.";

    $message_text = apply_filters( 'wcfm_email_content_wrapper', $message_text, 'New Message' );

    function set_html_content_type() {
        return 'text/html';
    }
    
    if ( !empty($yobro_conv['conversation']['message']) ) {
        add_filter( 'wp_mail_content_type', 'set_html_content_type' );
        wp_mail( $recipent_email, 'You got a new message on Spiral', $message_text );
        remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
    }
    return;
}
add_action('yobro_new_conversation_created', 'spiral_profile_side_notification');


// Case 2.
// For messages sent from the Messages page where real time chat happens
//
function spiral_message_page_notification( $yobro_msg ) {
    $recipent_id = $yobro_msg['reciever_id'];
    $sender_id = $yobro_msg['sender_id'];
    $sender = get_user_by( 'id', $sender_id );
    $sender_name = $sender->first_name . ' ' . $sender->last_name;
    $recipent = get_user_by( 'id', $recipent_id );
    $recipent_email = $recipent->user_email;
    $message_text = $sender_name . " sent you a Message:" . "\r\n\"" . wp_strip_all_tags($yobro_msg['message']) . "\"\r\n" . "Head over to <a href=\"" . get_home_url() . "/dashboard/msg\">Spiral</a> to reply.";

    $message_text = apply_filters( 'wcfm_email_content_wrapper', $message_text, 'New Message' );

    function set_html_content_type() {
        return 'text/html';
    }
    
    if ( !empty($yobro_msg['message']) ) {
        add_filter( 'wp_mail_content_type', 'set_html_content_type' );
        wp_mail( $recipent_email, 'You got a new message on Spiral', $message_text );
        remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
    }
    return;
}
add_action('yobro_after_store_message', 'spiral_message_page_notification');

// 
//                             End of YoBro section                         
// -------------------------------------------------------------------------*/






?>