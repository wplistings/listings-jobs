<?php

namespace Listings\Jobs;

use Listings\Jobs\Widgets\FeaturedJobs;
use Listings\Jobs\Widgets\RecentJobs;

class Plugin {
    public function __construct() {
        // Switch theme
        add_action( 'after_switch_theme', 'flush_rewrite_rules', 15 );

        add_action( 'widgets_init', array( $this, 'widgets_init' ) );

        // Actions
        add_action( 'after_setup_theme', array( $this, 'load_plugin_textdomain' ) );

        $this->shortcodes = new Shortcodes();
    }

    public function load_plugin_textdomain() {
        load_textdomain( 'listings-jobs', WP_LANG_DIR . "/listings-jobs/listings-jobs-" . apply_filters( 'plugin_locale', get_locale(), 'listings-jobs' ) . ".mo" );
        load_plugin_textdomain( 'listings-jobs', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Widgets init
     */
    public function widgets_init() {
        register_widget( RecentJobs::class );
        register_widget( FeaturedJobs::class );
    }
}