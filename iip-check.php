<?php
/**
 * Process page protection
 *
 * Handles authentication for password protected pages. 
 * Needed due to enhanced security on production (wp_login.php is blocked)
 * Adapted from wp-login.php v 3.8.10
 *
 * @package WordPress
 */
/** Make sure that the WordPress bootstrap has run before continuing. */
require(  $_SERVER['DOCUMENT_ROOT'] . '/wp/wp-load.php' );

// Redirect to https login if forced to use SSL
if ( force_ssl_admin() && ! is_ssl() ) {
	if ( 0 === strpos($_SERVER['REQUEST_URI'], 'http') ) {
		wp_redirect( set_url_scheme( $_SERVER['REQUEST_URI'], 'https' ) );
		exit();
	} else {
		wp_redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		exit();
	}
}

nocache_headers();

header('Content-Type: '.get_bloginfo('html_type').'; charset='.get_bloginfo('charset'));

if ( defined( 'RELOCATE' ) && RELOCATE ) { // Move flag is set
	if ( isset( $_SERVER['PATH_INFO'] ) && ($_SERVER['PATH_INFO'] != $_SERVER['PHP_SELF']) )
		$_SERVER['PHP_SELF'] = str_replace( $_SERVER['PATH_INFO'], '', $_SERVER['PHP_SELF'] );

	$url = dirname( set_url_scheme( 'http://' .  $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] ) );
	if ( $url != get_option( 'siteurl' ) )
		update_option( 'siteurl', $url );
}

// Set a cookie now to see if they are supported by the browser.
setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);
if ( SITECOOKIEPATH != COOKIEPATH )
	setcookie( TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN );

require_once ABSPATH . 'wp-includes/class-phpass.php';
$hasher = new PasswordHash( 8, true );

/**
 * Filter the life span of the post password cookie.
 *
 * By default, the cookie expires 10 days from creation. To turn this
 * into a session cookie, return 0.
 *
 * @since 3.7.0
 *
 * @param int $expires The expiry time, as passed to setcookie().
 */
$expire = apply_filters( 'post_password_expires', time() + 10 * DAY_IN_SECONDS );
//$secure = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
setcookie( 'wp-postpass_' . COOKIEHASH, $hasher->HashPassword( wp_unslash( $_POST['post_password'] ) ), $expire, COOKIEPATH );

/**
 * Append query string to use for error handling
 */
$url = '';
$q = '?auth=1';
if( empty($_SERVER['QUERY_STRING']) ) {
	$url = wp_get_referer() . $q; 
} else {
	$url .= strtok( wp_get_referer(), '?' ) . $q; 
}

wp_safe_redirect( $url );
exit();

