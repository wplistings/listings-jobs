<?php

namespace Listings\Jobs;

class Plugin {
    public function __construct() {
        // Switch theme
        add_action( 'after_switch_theme', 'flush_rewrite_rules', 15 );

        // Actions
        add_action( 'after_setup_theme', array( $this, 'load_plugin_textdomain' ) );
    }

    public function load_plugin_textdomain() {
        load_textdomain( 'listings-jobs', WP_LANG_DIR . "/listings-jobs/listings-jobs-" . apply_filters( 'plugin_locale', get_locale(), 'listings-jobs' ) . ".mo" );
        load_plugin_textdomain( 'listings-jobs', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }
}