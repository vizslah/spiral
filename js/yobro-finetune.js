function fixYoBro() {

    // fixing new message counter
    var $jq = jQuery.noConflict();
    $jq('.totalUnseenMsgCounter span').css('display', function() {

        // don't display anything if there are zero new messages
        if ($jq(this).text().trim().match("^0")) {
        return 'none';
        }
        
        // don't display anything if we are on the messages page
        if (document.URL.indexOf("/dashboard/msg") >= 0){
        return 'none';
        }
    });
    //$jq('.totalUnseenMsgCounter').addClass('wcfm_header_panel_messages text_tip');
};

fixYoBro();

function yobro_new_account_view() {
    
    var checkExist2 = setTimeout(function() {

        var $jq = jQuery.noConflict();
        
        // Yobro New message
        $jq('a.new-msg').css('display', 'none');
        
        // Yobro empty message box
        if ( $jq('div.yoBro-chatHistory' ).is(':empty')){
        $jq('div.yoBro-chatHistory').append('<div class="yoBro-singleChatMsg active active"><h4>You don\'t seem to have any messages yet.<br></h4></div>');
        }
        
    }, 10);
    
}

yobro_new_account_view();