<?php
/**********************************************************************************************************
 Extends America Base Themes
  
 Plugin Name: 	  America Page Protection
 Description:     This plugin handles authentication for password protected pages and adds basic error messaging.
 				  Needed due to enhanced security on production
 Version:         1.0.1
 Author:          Office of Design, Bureau of International Information Programs
 License:         GPL-2.0+
 Text Domain:     america
 Domain Path:     /languages
 
 ************************************************************************************************************/

//* Prevent loading this file directly
defined( 'ABSPATH' ) || exit;  

add_action( 'init', 'iip_init' );
add_filter( 'post_password_expires', 'iip_post_password_expires' );
add_filter( 'the_password_form', 'iip_page_password_form' ); 


function iip_init () {
	load_plugin_textdomain ( 'america', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

function iip_post_password_expires( $expires ) { // make cookie a session cookies
 	return 0;
 }

function iip_page_password_form() {
	global $post;

 	$msg = '';
 	$url = plugin_dir_url( __FILE__ ) . 'iip-check.php?action=postpass';
    $label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
   
    $form = '<form action="' . esc_url( $url ) . '" method="post"> <p>' . __( "To view this protected post, enter the password below:" ) . '</p>
    <label for="' . $label . '">' . __( "Password:" ) . ' </label><input name="post_password" id="' . $label . '" type="password" size="20" maxlength="20" /><input type="submit" name="Submit" value="' . esc_attr__( "Submit" ) . '" />
    </form>'; 


    return $msg . $form;
}