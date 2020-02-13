function fixVisualComposerBooted() {

    var $jq = jQuery.noConflict();
    $jq( "#vcv-ui-blank-row" ).hide();
    $jq( "div.vcv-ui-blank-row-drag-overlay").hide();
    $jq( "div.vcv-ui-blank-row-controls-container").hide();

};

fixVisualComposerBooted();