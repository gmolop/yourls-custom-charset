<?php
/*
Plugin Name: Custom charset in short URLs
Plugin URI: https://github.com/gmolop/yourls-custom-charset
Description: Allows custom charset in shorted URLs.
Version: 1.0
Author: gmolop
Author URI: https://github.com/search?q=user%3Agmolop+yourls
*/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

/**
 * Admin manage
 */
yourls_add_action( 'plugins_loaded', 'gmo_custom_charset_add_page' );
function gmo_custom_charset_add_page() {
    yourls_register_plugin_page( 'custom_charset', 'Custom charset', 'gmo_custom_charset_do_page' );
}

// Add filter
yourls_add_filter( 'get_shorturl_charset', 'gmo_custom_charset' );
function gmo_custom_charset( $in ) {

    // Get value from database
    $custom_charset_values         = yourls_get_option( 'custom_charset_values' );
    $custom_charset_values_json    = json_decode( $custom_charset_values);
    $custom_charset_values_charset = $custom_charset_values_json->charset;
    $custom_charset_values_values  = html_entity_decode($custom_charset_values_charset);

    return $in . $custom_charset_values_values;
}

// Display admin page
function gmo_custom_charset_do_page() {

    // Check if a form was submitted
    if( isset( $_POST['custom_charset_values'] ) ) {
        // Check nonce
        yourls_verify_nonce( 'custom_charset' );

        // Process form
        gmo_custom_charset_update_option();
    }

    // Get value from database
    $custom_charset_values         = yourls_get_option( 'custom_charset_values' );
    $custom_charset_values_json    = json_decode( $custom_charset_values);
    $custom_charset_values_charset = $custom_charset_values_json->charset;
    $custom_charset_values_values  = html_entity_decode($custom_charset_values_charset);

    // Create nonce
    $nonce = yourls_create_nonce( 'custom_charset' );
    $current_charset = yourls_get_shorturl_charset();

    echo <<<HTML
        <h2>Custom charset configuration</h2>
        <p>Enter here a list with the custom charset you want to allow in shorted URLs</p>
        <form method="post">
        <input type="hidden" name="nonce" value="$nonce" />
        <p><label for="custom_charset_values"><strong>Current charset</strong>: {$current_charset}</label></p>
        <p><label for="custom_charset_values"><strong>Custom charset</strong>: </label> <input type="text" name="custom_charset_values" value="{$custom_charset_values_values}"></p>
        <p><input type="submit" value="Update value" /></p>
        </form>

HTML;
}

// Update option in database
function gmo_custom_charset_update_option() {

    $in = $_POST['custom_charset_values'];


    if ( !empty($in) ) {

        $tr = trim( $in );
        $ht = htmlentities( $tr, ENT_QUOTES );
        $arr = array( 'charset' => $ht );
        $json = json_encode( $arr );
        yourls_update_option( 'custom_charset_values', $json );

    }

}

/**
 * User action
 */
if ( basename($_SERVER['PHP_SELF']) == 'index.php' ) yourls_add_action( 'admin_menu', 'gmo_custom_charset_add_menu' );

function gmo_custom_charset_add_menu() {
    echo '<li>Custom charset</li>';
}

