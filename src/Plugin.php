<?php

namespace Listings\Jobs;

use Listings\Jobs\Admin\Admin;
use Listings\Jobs\Widgets\FeaturedJobs;
use Listings\Jobs\Widgets\RecentJobs;

class Plugin {
    public function __construct()
    {
        if (is_admin()) {
            new Admin();
        }

        // Register template path for this plugin
        listings()->template->register_template_path(LISTINGS_JOBS_PLUGIN_DIR . '/templates/');

        $this->post_types = new PostTypes();
        $this->shortcodes = new Shortcodes();
    }

    public function hooks()
    {
        // Activation - works with symlinks
        register_activation_hook( basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ), array( $this, 'activate' ) );

        // Switch theme
        add_action( 'after_switch_theme', 'flush_rewrite_rules', 15 );
        add_action( 'after_switch_theme', array( $this->post_types, 'register_post_types' ), 11 );

        add_action( 'widgets_init', array( $this, 'widgets_init' ) );

        // Actions
        add_action( 'after_setup_theme', array( $this, 'load_plugin_textdomain' ) );
    }

    public function activate() {
        $this->post_types->register_post_types();
        flush_rewrite_rules();
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