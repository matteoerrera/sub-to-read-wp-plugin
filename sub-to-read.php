<?php
/**
 * @package Sub-To-Read
 * @version 0.1
 */
/*
Plugin Name: Sub-To-Read
Description: This plugin allow to mask post content and show it only if a user is registered to a newsletter
Author: Matteo Errera
Version: 0.1
Author URI: https://github.com/matteoerrera
*/
define('SUB_TO_READ_SHORTCODE', 'STR_TRUNCATE_TEXT');

// Register style sheet.
add_action( 'wp_enqueue_scripts', 'str_register_plugin_styles' );
function str_register_plugin_styles() {
    wp_register_style( 'str-css', plugins_url( 'assets/style.css', __FILE__));
    wp_register_script( 'str-css', plugins_url( 'assets/ajax.js', __FILE__));
    wp_enqueue_style( 'str-css' );
    wp_enqueue_script( 'str-css' );
}

function str_load_txtDomain() {
    $locale = apply_filters( 'plugin_locale', get_locale(), 'strwp' );
    load_textdomain( 'strwp', plugin_dir_path( __FILE__ ) . '/languages/strwp-' . $locale . '.mo' );
}
add_action('plugins_loaded','str_load_txtDomain');


function str_shortcode_func($atts) {
    return "";
}

add_shortcode(SUB_TO_READ_SHORTCODE, 'str_shortcode_func');


function str_truncate_post_content($content) {
    $match = "[".SUB_TO_READ_SHORTCODE."]";
    ob_start();
    include( plugin_dir_path( __FILE__ ) . '/newsletter_template.php');
    return ob_get_clean();
}

function str_get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

add_filter('the_content','str_truncate_post_content');

add_action( 'wp_ajax_nopriv_str_form_submit', 'str_form_submit' );
add_action( 'wp_ajax_str_form_submit', 'str_form_submit' );


function str_form_submit() {
    str_subscribe_newsletter();
    wp_die();
}


function str_subscribe_newsletter() {
    $url = 'https://yourdomain.api-us1.com';
    $list_id = 0; //your list id
    $params = array(
        // the API Key can be found on the "Your Settings" page under the "API" tab.
        // replace this with your API Key
        'api_key'      => 'YOUR API KEY',

        // this is the action that adds a contact
        'api_action'   => 'contact_add',
        'api_output'   => 'json',
    );

    // here we define the data we are posting in order to perform an update
    $post = array(
        'email'                    => $_POST['email'],
        'first_name'               => $_POST['name'],
        'p[$list_id]'              => $list_id, // example list ID (REPLACE '123' WITH ACTUAL LIST ID, IE: p[5] = 5)
        'status[123]'              => 1, // 1: active, 2: unsubscribed (REPLACE '123' WITH ACTUAL LIST ID, IE: status[5] = 1)
    );

    // This section takes the input fields and converts them to the proper format
    $query = "";
    foreach( $params as $key => $value ) $query .= urlencode($key) . '=' . urlencode($value) . '&';
    $query = rtrim($query, '& ');

    // This section takes the input data and converts it to the proper format
    $data = "";
    foreach( $post as $key => $value ) $data .= urlencode($key) . '=' . urlencode($value) . '&';
    $data = rtrim($data, '& ');

    // clean up the url
    $url = rtrim($url, '/ ');

    // This sample code uses the CURL library for php to establish a connection,
    // submit your request, and show (print out) the response.
    if ( !function_exists('curl_init') ) die('CURL not supported. (introduced in PHP 4.0.2)');

    // If JSON is used, check if json_decode is present (PHP 5.2.0+)
    if ( $params['api_output'] == 'json' && !function_exists('json_decode') ) {
        die('JSON not supported. (introduced in PHP 5.2.0)');
    }
    // define a final API request - GET
    $api = $url . '/admin/api.php?' . $query;
    $request = curl_init($api); // initiate curl object
    curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
    curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
    curl_setopt($request, CURLOPT_POSTFIELDS, $data); // use HTTP POST to send form data
    //curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment if you get no gateway response and are using HTTPS
    curl_setopt($request, CURLOPT_FOLLOWLOCATION, true);
    $response = (string)curl_exec($request); // execute curl post and store results in $response
    curl_close($request); // close curl object
    if ( !$response ) {
        die('Nothing was returned. Do you have a connection to Email Marketing server?');
    }
    return $response;

    wp_die(); // this is required to terminate immediately and return a proper response
}
