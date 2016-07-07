<?php
/**
 * Plugin Name: Listings - Jobs
 * Description: Adds job board functionality to the Listings plugin.
 * Version: 1.0.0
 * Author: The Look and Feel
 * Text Domain: listings-jobs
 */

// Define constants
define( 'LISTINGS_JOBS_VERSION', '1.0.0' );
define( 'LISTINGS_JOBS_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'LISTINGS_JOBS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
define( 'LISTINGS_JOBS_PLUGIN_FILE', __FILE__ );

/**
 * @return \Listings\Jobs\Plugin
 */
function listings_jobs() {
    static $instance;
    if ( is_null( $instance ) ) {
        $instance = new \Listings\Jobs\Plugin();
        $instance->hooks();
    }
    return $instance;
}

function __load_listings_jobs() {
    $GLOBALS['listings_jobs'] = listings_jobs();
}

// autoloader
require 'vendor/autoload.php';

// create plugin object
add_action( 'listings_init', '__load_listings_jobs', 10 );