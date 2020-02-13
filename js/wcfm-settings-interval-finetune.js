function wcfm_settings_interval_finetune() {
    
    var checkExist = setTimeout(function() {

        var $jq = jQuery.noConflict();
        
        $jq('img#list_banner_display.placeHolder').parent().css('display', 'none');
        $jq('p.list_banner.wcfm_title').css('display', 'none');
        $jq('p.list_banner.wcfm_title').next('label').css('display', 'none');
        $jq('#spiral-vendor-timezone').select2();
        
        // Add default blank option to custom currency selector
            // Create an Option object       
            var opt = document.createElement("option");        

            // Get selected currency
            var e = document.getElementById("scd-currency-list");
            var strSelected = e.options[e.selectedIndex].value;

            // Assign text and value to Option object
            opt.text = "";
            opt.value = "";

            // Select blank option if no saved selection exists
            if ( strSelected == "AED" ) { opt.selected = "selected"; }

            // Add an Option object to Drop Down List Box
            document.getElementById('scd-currency-list').options.add(opt);
        
    }, 1200);

}
    
/*
function spiral_save_timezone() {

    var $jq = jQuery.noConflict();
    
        $jq(document).on('click', '#wcfm_settings_save_button', function (e) {
            e.preventDefault();
            var selected_timezone = $jq('#spiral-vendor-timezone').val();
            if ( selected_timezone !== "" ) {
                $jq.post(
                        ajaxurl,
                        {
                            'action': 'update_vendor_timezone',
                            'selected_timezone': selected_timezone,
                        },
                        function(response) { alert(response); }
                );
            }
        });
}

spiral_save_timezone();
*/

wcfm_settings_interval_finetune();