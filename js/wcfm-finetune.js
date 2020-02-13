function wcfm_finetune() {

    var $jq = jQuery.noConflict();
    
    // Order Add Note (stolen from WCFMu )
	$jq('#wcfm_add_appointment_order_note').click(function(event) {
		event.preventDefault();
		addWCFMOrderNote();
		return false;
	});
    
    function addWCFMOrderNote() {
		$jq('#wcfm_order_notes_options').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action      : 'wcfm_add_order_note',
			note        : $jq('#add_order_note').val(),
			note_data   : $jq('#wcfm_add_order_note_form').serialize()
		}
        console.log(data);
		$jq.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				$jq('#notes_holder').append(response);
				$jq('#add_order_note').val('');
				$jq('#wcfm_order_notes_options').unblock();
			}
		});
	}
    
    // ...
    $jq('img#gravatar_display.placeHolder').attr('src', 'https://getspiral.com/library/2019/04/spiral_logo_square-100x100.jpg');
    $jq('img#banner_display.placeHolder').attr('src', 'https://getspiral.com/library/2019/02/background_1-100x100.png');

    $jq('img#mobile_banner_display.placeHolder').parent().css('display', 'none');
    $jq('p.mobile_banner.wcfm_title').css('display', 'none');
    $jq('p.mobile_banner.wcfm_title').next('label').css('display', 'none');
    
    $jq('p.list_banner_type.wcfm_title').css('display', 'none');
    $jq('p.list_banner_type.wcfm_title').next('label').css('display', 'none');
    $jq('select#list_banner_type').css('display', 'none');
    
    $jq('p.banner_type.wcfm_title').css('display', 'none');
    $jq('p.banner_type.wcfm_title').next('label').css('display', 'none');
    $jq('select#banner_type').css('display', 'none');
    
    $jq('p.wp_user_avatar.wcfm_title').css('display', 'none');
    $jq('p.wp_user_avatar.wcfm_title').next('label').css('display', 'none');
    $jq('div#wcfm_profile_personal_expander > span').css('display', 'none');
    
    $jq('input#wcfm_enquiry_submit_button').attr('value', 'Send');
    
    // Edit Offering Title Placeholder
    $jq('#pro_title').attr('placeholder', 'Offering Title');
    
    // Onboarding
    $jq('#wc-logo > a > img[alt="Spiral"]').attr('src', 'https://getspiral.com/library/2019/08/spiral_logo_small_transparent_black.png')
    
    $jq('div.wc-setup-content > h1:contains("Payout setup")').after('Connect or create a Stripe account to be able to receive your earnings.<br>You can start selling even if you skip this step now. Just remember to set it up before expecting payouts.');
    
    $jq('body > div.wc-setup-content > form > p.shop_description.wcfm_title > span').attr('data-tip', 'Displayed on landing page. 200 characters max recommended');
    
    $jq('body > div.wc-setup-content > p.wc-setup-actions.step > a[href="https://getspiral.com/dashboard/"]').attr('href', 'https://getspiral.com/p/');
    $jq('body > div.wc-setup-content > div.wc-setup-next-steps > p > a').attr('href', 'https://getspiral.com/p/');
    $jq('body > div.wc-setup-content > div.wc-setup-next-steps > p > a').text('Let\'s see Your Page!');
    
    $jq('div#products-wrapper > p.woocommerce-info:contains("Your Offerings will show up here.")').html('Your Offerings will show up here. <a href="https://getspiral.com/dashboard/offerings-manage/" class="button">Add your first Offering &nbsp&nbsp&nbsp</a>');
    
    // Setup menu bloat
    $jq('h3:contains("Store Visibility")').parent().css('display', 'none');
    $jq('div.store_address.store_visibility_wrap').css('display', 'none');
    
    $jq('#wcfm_settings_form_store_expander p.store_slug.wcfm_title.wcfm_ele').append('<span class="img_tip wcfmfa fa-question" data-tip="The ending of the URL of your Page, as seen in the browser\'s address bar" data-hasqtip="18" aria-describedby="qtip-18"></span>');
    
    $jq('#wcfm_settings_form_store_expander p.shop_description.wcfm_title > span').attr('data-tip', 'This is displayed on your profile page.');
    
    // New Message notification: hide if zero
    $jq('.unread_notification_count').css('display', function() {

        // don't display anything if there are zero new messages
        if ($jq(this).text().trim().match("^0")) {
        return 'none';
        }
        
        // don't display anything if we are on the messages page
        if (document.URL.indexOf("/dashboard/message") >= 0){
        return 'none';
        }
    });
    $jq('a.wcfm_header_panel_enquiry > i').removeClass('fa-question-circle');
    $jq('a.wcfm_header_panel_enquiry > i').addClass('fa-envelope');
    $jq('a.wcfm_header_panel_enquiry > i').css('font-size', '22px');
    
    $jq('#wcfm_profile_manage_form_apt_gcal_sync_expander p.last_synced').append(' (Berlin time)');
    
    //$jq('#wcfm_products_manage_form_appointment_availability_expander').append('<p><i class="fa fa-exclamation-triangle" aria-hidden=\'true\'> All timeslots are disabled by default. Set timeframes for which to enable appointment bookings.</p>');
    
    // Auto-enable canceling and timezones
    $jq('input#_wc_appointment_user_can_cancel').attr('checked', true);
    $jq('input#_wc_appointment_customer_timezones').attr('checked', true);
    
    // Vacation Mode
    $jq('input#wcfm_disable_vacation_purchase').attr('checked', true);
    $jq('select#wcfm_vacation_mode_type > option:eq(0)').attr('selected', true);
    $jq('p.wcfm_disable_vacation_purchase.wcfm_title').hide();
    $jq('p.wcfm_vacation_mode_type.wcfm_title').hide();
    $jq('#wcfm_disable_vacation_purchase').hide();
    $jq('#wcfm_vacation_mode_type').hide();
    
    // Pro Membership Payments
    $jq('#wcfm_membership_container input.wcfm_subscription_paymode[value="stripe"]').prop('checked', true);
    $jq('#wcfm-main-contentainer .wcfm_membership_review_pay .wcfm_membership_payment_form_non_free').show();
    $jq('div.wcfm_review_pay_welcome').text('Pay with Credit or Debit card (via Stripe)');
    
    if ( $jq('#wcfm_profile_manage_form_membership_expander div.wcfm_review_pay_free').length ) {
        $jq('#wcfm_profile_manage_form_membership_expander div.wcfm_membership_pay').append('<a href="https://getspiral.com/upgrade" class="button red">Upgrade to Pro</a>');
    }

    // Custom Availability Improvements
    $jq('#_wc_appointment_availability_rules input[name="_wc_appointment_availability_rules[0][qty]"]').attr('min', 1);
    if ( $jq('#_wc_appointment_availability_rules input[name="_wc_appointment_availability_rules[0][qty]"]').attr('value') < 1 ) { $jq('#_wc_appointment_availability_rules input[name="_wc_appointment_availability_rules[0][qty]"]').attr('value', 1);
    }
    
    // Offering manager - Tooltip fixes
    $jq('#_wc_appointment_availability_rules span[data-tip="The maximum number of appointments per slot. Overrides general product capacity."]').attr('data-tip', 'Max number of participants for group sessions. Keep it at 1 for one-on-one offerings');
    
    // Settings - Tooltip fixes
    if ( $jq('#wcfm_settings_form_store_expander p.banner.wcfm_title.banner_type_field.banner_type_single_img > span[data-tip*="Upload a banner for your store."]').length ) {
        var tooltip = $jq('#wcfm_settings_form_store_expander p.banner.wcfm_title.banner_type_field.banner_type_single_img > span[data-tip*="Upload a banner for your store."]').attr('data-tip');
        var tooltip_new = tooltip.replace("Upload a banner for your store.", "Background image on top of your page.");
        $jq('#wcfm_settings_form_store_expander p.banner.wcfm_title.banner_type_field.banner_type_single_img > span[data-tip*="Upload a banner for your store."]').attr('data-tip', tooltip_new);
    }
    
    // Offering manage - Default availability rule type UX fix
    $jq(document).ready(function () {
    if (window.location.href == "https://getspiral.com/dashboard/offerings-manage/") {
       $jq('#_wc_appointment_availability_rules_type_0 option[value="time"]').prop('selected', true);
        }
    });
    
    // Offering manager - Show limit warning if 0 or 1 remaining
    if ( $jq('span.wcfm_products_limit_label').text().indexOf("1 remaining") >= 0 ||
         $jq('span.wcfm_products_limit_label').text().indexOf("0 remaining") >= 0) {
        $jq('span.wcfm_products_limit_label').css('display', 'inline');
    }
    // Offering manager - Add Timepicker dropdown UX fix
    $jq('#wcfm_products_manage_form_appointment_availability_expander input[type="time"]').timepicker({ 'timeFormat': 'H:i' });
    
    // Special view if no service added yet
    var wait = setTimeout(function() {
    if ( $jq('.dataTables_empty').length ) {
        $jq("#wcfm-products_wrapper").html('<h3><b><br>You don\'t seem to have added any offerings yet.<br><br>Go ahead and add your first one!<br>:)</b></h3>');
    } }, 200);
    
    // Offering manager default image placeholder
    $jq('#featured_img_display').attr('src', 'https://getspiral.com/library/2019/02/mental-health-2313430_640.jpg')
    
    // Fix offering manager tabs
    var spiral = setTimeout(function() {
    $jq('#spiral_premium_features_tab').removeClass('wcfm_head_hide');
    $jq('#spiral_nohide').removeClass('wcfm_block_hide');
    $jq('#wcfm_products_manage_form_wcaddons_head > div > label').removeClass('fa-gem').addClass('fa-pencil-square-o');
    $jq('#wcfm_products_manage_form_location_head > div > label').removeClass('fa-snowflake').addClass('fa-map-marker');
    }, 1000);
    
    // Offering manager Title width fix
    $jq('#pro_title').removeClass('wcfm_full_ele');
    $jq('#pro_title').addClass('spiral_half_width');
    
    // Offering manager move short description into upper div
    $jq('#wcfm_products_manage_form_general_expander + div.wcfm-content').appendTo('div.wcfm_product_manager_general_fields');
    
    /* Legacy
    //If custom currency is not set, hide field
    if ($jq('#scd-wcv-select-currencies').length > 0) {
        $jq('#scd_regularPriceCurrency').css('display', 'none');
    }
    
    // Hide custom currency field if setting is same as original
    var origPrice = $jq('p.regular_price > strong').text();
    var customPrice = $jq('p.scd_salePriceCurrency').text();
    if ( origPrice.indexOf('€') >= 0 && customPrice.indexOf('EUR') >= 0 ) {
        $jq('p.scd_regularPriceCurrency').css('display', 'none');
        $jq('#scd_regularPriceCurrency').css('display', 'none'); }
    if ( origPrice.indexOf('$') >= 0 && customPrice.indexOf('USD') >= 0 ) {
        $jq('p.scd_regularPriceCurrency').css('display', 'none');
        $jq('#scd_regularPriceCurrency').css('display', 'none'); }
    */
    
    // Logo href to Dashboard instead of profile page.
    // (I might want to reconsider this)
    $jq("div.wcfm_menu_logo a").attr("href", "/dashboard/");
    $jq("div.wcfm_menu_logo a").removeAttr('target');
    
    // Add instructions to Calendar Sync
    //var text = document.createTextNode('Sync works both ways: It prevents booking appointments if your calendar is not free, and populates your calendar with new bookings. Only Google Calendar is supported at the moment, but you can integrate any calendar by syncing it with your Google account.');
    var text = document.createTextNode('Coming Soon...');
    var child = document.getElementById('wcfm_profile_manage_form_apt_gcal_sync_expander');
    if (!!child) {
    child.parentNode.insertBefore(text, child);
    }
    
    // Calendar View
    $jq('#mainform.wc_appointments_calendar_form.month_view a.prev').append('<<');
    $jq('#mainform.wc_appointments_calendar_form.month_view a.next').append('>>');
    
    // Offering Location field disclaimer
    $jq('#wcfm_products_manage_form_location_expander').append('Enter the street address, or service name / meeting url for online appointments. If clarification is needed, you can always message each other before the session.');
    
    // Rewrite custom currency copy
    $jq("p.scd_regularPriceCurrency").before('<br>');
    $jq('p.scd_regularPriceCurrency > strong')
    .contents()
    .filter(function() {
        return this.nodeType == Node.TEXT_NODE
            && this.nodeValue.indexOf('SCD Regular price') >= 0;
    }).each(function() {
        this.nodeValue = this.nodeValue.replace(/\bSCD Regular price\b/g, 'Price');
    });
    $jq('p.scd_regularPriceCurrency').addClass('wcfm_title wcfm_half_ele_title');
    $jq('p.scd_regularPriceCurrency').next().next('input').addClass('wcfm_text wcfm_half_ele');
    
    
    // Simplify Setup labels (hide the word Store)
    $jq('#wcfm_settings_form_store_expander, #wcfm_settings_form_store_expander *')
    .contents()
    .filter(function() {
        return this.nodeType == Node.TEXT_NODE
            && this.nodeValue.indexOf('Store') >= 0;
    }).each(function() {
        this.nodeValue = this.nodeValue.replace(/\Store \b/g, '');
    });
    
    
    // Product -> Offering where gettext doesn't work
    $jq('#add_new_product_dashboard').attr('data-tip', 'Add New Offering');
    $jq('#wcfm_products_listing, #wcfm_products_listing *')
    .contents()
    .filter(function() {
        return this.nodeType == Node.TEXT_NODE
            && this.nodeValue.indexOf('Products') >= 0;
    }).each(function() {
        this.nodeValue = this.nodeValue.replace(/\Products\b/g, 'Offerings');
    });

    // Offering Bookable Days force checked checkbox
    $jq('#_wc_appointment_has_restricted_days').prop('checked', true);    
    
    // Fixing currency bug for free offerings
    // Only runs if free product price is displayed
    var mainPrice = $jq('p.price > span.amount').text();
    if ( mainPrice.indexOf('Free!') >= 0 ) {
        var checkExist = setTimeout(function() {

            var $jq = jQuery.noConflict();
            $jq('p.price > span.amount:contains("NaN")').text('Free!');

        }, 1200);
    }
    
    // Text replace across the board (bad practice, replaced with gettext)
    /*
    $jq('body, body *')
    .contents()
    .filter(function() {
        return this.nodeType == Node.TEXT_NODE
            && this.nodeValue.indexOf('Store ') >= 0;
    }).each(function() {
        this.nodeValue = this.nodeValue.replace(/\bStore\b/g, 'Business');
    });
    */

    
};

wcfm_finetune();
