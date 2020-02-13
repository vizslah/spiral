function wcfm_quicksetup_finetune() {
    
    var checkExist = setInterval(function() {
    
    var $jq = jQuery.noConflict();
    
    $jq('img#gravatar_display.placeHolder').attr('src', 'https://getspiral.com/library/2019/04/spiral_logo_square-100x100.jpg');
    $jq('img#banner_display.placeHolder').attr('src', 'https://getspiral.com/library/2019/02/background_1-100x100.png');

}, 250);
    
};

wcfm_quicksetup_finetune();
