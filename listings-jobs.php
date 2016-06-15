<?php
/**
 * Plugin Name: Listings - Jobs
 * Description: Adds job board functionality to the Listings plugin.
 * Version: 1.0.0
 * Author: The Look and Feel
 * Text Domain: listings-jobs
 */

// Define constants
define( 'LISTINGS_JOBS_VERSION', '1.25.0' );
define( 'LISTINGS_JOBS_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'LISTINGS_JOBS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );

include('vendor/autoload.php');
$GLOBALS['listings_jobs'] = new \Listings\Jobs\Plugin();