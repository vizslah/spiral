function fixVisualComposer() {

    var $jq = jQuery.noConflict();
    $jq( "div.vcv-error-screen button" ).text( "Return to Dashboard" );
    
    var checkExist = setInterval(function() {
   
        if ($jq('#vc-navbar-container').length) {
            
            // Drag handler
            $jq( "#vc-navbar-container > div > nav > div.vcv-ui-navbar-drag-handler.vcv-ui-drag-handler" ).hide();
            
            // Edit Menu Sections
            $jq( "div.vcv-ui-form-dependency:contains('Link selection')" ).hide();
            $jq( "div.vcv-ui-form-dependency:contains('Element ID')" ).hide();
            $jq( "div.vcv-ui-form-dependency:contains('Extra class name')" ).hide();
            $jq( "div.vcv-ui-form-dependency:contains('OnClick action')" ).hide();
            $jq( "div.vcv-ui-form-dependency:contains('Enter image size')" ).hide();
            
            $jq( "span.vcv-ui-form-group-heading:contains('Device type')" ).hide();
            $jq( "span.vcv-ui-form-group-heading:contains('Device type')" ).siblings().hide();

            // Row Layout Options
            $jq( "div.vcv-ui-edit-form-section:contains('Specify number of columns within row')" ).hide();
            
            // TODO: figure out how to remove dropdown option instead !!
            $jq( "span.vcv-ui-form-group-heading:contains('Background type')" ).siblings().hide();
            $jq( "span.vcv-ui-form-group-heading:contains('Background type')" ).text('Background');
            
            $jq( "span.vcv-ui-form-group-heading:contains('Content')" ).siblings().hide();
            $jq( "span.vcv-ui-form-group-heading:contains('Content')" ).html( "<h2>Click inside text elements for inline editing</h2>");

            $jq( "div.mce-widget[aria-label='Open Element in Edit Form']").hide();
            
            $jq( "div.vcv-ui-edit-form-section:contains('Sticky')" ).hide();
            $jq( "div.vcv-ui-edit-form-section:contains('Box Shadow')" ).hide();
            
            $jq( "div.vcv-ui-blank-row" ).hide();
            
            // Save button hover title change
            $jq( "#vc-navbar-container > div > nav > div.vcv-ui-navbar-controls-group.vcv-ui-pull-end > span" ).prop('title', 'Save and Exit');

            // Save button behavior (adding spinner + redirect to profile after save)
            $jq( "#vc-navbar-container > div > nav > div.vcv-ui-navbar-controls-group.vcv-ui-pull-end > span" ).click(function() {
                var base_url = window.location.protocol + "//" + window.location.host + "/p" ;
                
                $jq( "div.vcv-layout-overlay").addClass('loader loader-default is-active').attr('data-text', 'Saving');
                setTimeout(function(){
                    window.location.replace(base_url);                
                        }, 4000); 
                });

            $jq( "#vc-navbar-container > div > nav > dl.vcv-ui-navbar-dropdown.vcv-ui-pull-end.vcv-ui-navbar-sandwich > dt > span > i" ).removeClass("vcv-ui-icon-mobile-menu");

            // Menu button icon and tooltip -> Cancel
            $jq( "#vc-navbar-container > div > nav > dl.vcv-ui-navbar-dropdown.vcv-ui-pull-end.vcv-ui-navbar-sandwich > dt > span > i" ).addClass("vcv-ui-icon-close-thin");
            $jq( "#vc-navbar-container > div > nav > dl.vcv-ui-navbar-dropdown.vcv-ui-pull-end.vcv-ui-navbar-sandwich > dt" ).prop('title', 'Exit without saving');

            // Cancel button behavior (exit to dashboard)
            $jq( "dt.vcv-ui-navbar-dropdown-trigger[title='Exit without saving']" ).click(function() {
                var base_url = window.location.protocol + "//" + window.location.host + "/p" ;
                window.location.replace(base_url);
            });
            
            //var template_list_ul = $jq('#vcv-editor-end > div.vcv-ui-tree-view-content.vcv-ui-add-template-content > div > div.vcv-ui-tree-content-section > div.vcv-ui-scroll > div.vcv-ui-scroll-content > div > div.vcv-ui-editor-plates-container > div > div > div > ul');
            //var template_elements = $jq('#vcv-editor-end > div.vcv-ui-tree-view-content.vcv-ui-add-template-content > div > div.vcv-ui-tree-content-section > div.vcv-ui-scroll > div.vcv-ui-scroll-content > div > div.vcv-ui-editor-plates-container > div > div > div > ul li:contains("Premium")');
            //template_elements.each() {
            $jq('#vcv-editor-end > div.vcv-ui-tree-view-content.vcv-ui-add-template-content > div > div.vcv-ui-tree-content-section > div.vcv-ui-scroll > div.vcv-ui-scroll-content > div > div.vcv-ui-editor-plates-container > div > div > div > ul li').hide();
            
            // Notifications bars
            $jq( ".vcv-layout-notifications" ).hide();
            
            clearInterval(checkExist);
  
            }
        
        }, 250); // check every 250ms


        
};

fixVisualComposer();