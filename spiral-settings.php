<?php

add_action( 'admin_menu', 'spiral_menu' );

function spiral_menu() {
	add_menu_page( 'Spiral Plugin Settings', 'Spiral', 'manage_options', 'spiral', 'spiral_plugin_options', plugin_dir_url( __FILE__ ) . 'img/spiral_icon.png', '72' );
}

function spiral_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	echo '<div class="wrap">';
	screen_icon();
    echo '<h1>Spiral Settings</h1>';

    
	echo '</div>';
}

?>
    