<?php

/*
Plugin Name: Hello Trumpy
Plugin URI: https://wordpress.org/plugins/hello-trumpy/
Description: Randomly see a quote from President Donald Trump
Version: 1.0
Author: LJerez
Author URI: http://LeoJerez.com
License: GPLv2+
*/

function get_hello_trumpy_quote() {
    $url = "https://api.whatdoestrumpthink.com/api/v1/quotes/random";

    // Use WP Http to pull API data
    if( !class_exists( 'WP_Http' ) ) {
        include_once( ABSPATH . WPINC. '/class-http.php' );
    }

    $result = wp_remote_request ($url);

    if (!is_wp_error($result)){       

        // We want to work with a PHP object form the Json to get the message
        $result = json_decode($result);
        $result = $result->message;

        // If the message is long (Trump is long-winded), split into two lines at the nearest word near the middle
        if(strlen($result) >= 120){
            $halfWayPos = strlen($result) / 2;
            $nextWordPos = strpos($result, " ", $halfWayPos) + 1;
            $result = substr_replace($result, "<br />",$nextWordPos, 0);
        }
    }    

    // Returns text with quote transformations
    return wptexturize( $result );
}

// Calls API Get function and prints html, positioned/styled separately.
function hello_trumpy() {
    $quote = get_hello_trumpy_quote();
    echo "<p id='trumpy'><em>$quote</em></p>";
}

// Hook into admin_notices to display the quote.
add_action( 'admin_notices', 'hello_trumpy' );

// Account for right-to-left languages and call different CSS appropriately.

function trumpy_css()
{
    if (is_rtl()) {
        wp_enqueue_style('trumpy-rtl', plugins_url('css/trumpy-rtl.css', __FILE__));
    } else {
        wp_enqueue_style('trumpy', plugins_url('css/trumpy.css', __FILE__));
    }
}

// Hook into admin head to include trumpy css
add_action( 'admin_head', 'trumpy_css' );

?>
