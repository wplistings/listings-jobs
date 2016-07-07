<?php

namespace Listings\Jobs;

use Listings\Ajax\Handler;
use Listings\Jobs\Admin\Admin;
use Listings\Jobs\Ajax\Actions\GetListings;
use Listings\Jobs\Forms\EditJob;
use Listings\Jobs\Forms\SubmitJob;
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
        listings()->forms->register_form(new EditJob());
        listings()->forms->register_form(new SubmitJob());

        // Register Ajax actions
        listings()->ajax->registerAction(new GetListings() );

        $this->install = new Install();
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
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        add_action( 'admin_init', array( $this, 'updater' ) );
    }

    public function activate() {
        $this->post_types->register_post_types();
        Install::install();
        flush_rewrite_rules();
    }
    
    public function updater() {
        if ( version_compare( LISTINGS_JOBS_VERSION, get_option( 'listings_jobs_version' ), '>' ) ) {
            Install::install();
            flush_rewrite_rules();
        }
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

    public function enqueue_scripts() {
        $ajax_url         = Handler::get_endpoint();
        $ajax_filter_deps = array( 'jquery', 'jquery-deserialize' );
        $ajax_data 		  = array(
            'ajax_url'                => $ajax_url,
            'is_rtl'                  => is_rtl() ? 1 : 0,
            'i18n_load_prev_listings' => __( 'Load previous listings', 'listings' ),
        );

        wp_enqueue_style( 'listings-jobs', LISTINGS_JOBS_PLUGIN_URL . '/assets/css/frontend.css' );

        wp_register_script( 'listings-ajax-filters', LISTINGS_JOBS_PLUGIN_URL . '/assets/js/ajax-filters.min.js', $ajax_filter_deps, LISTINGS_JOBS_VERSION, true );
        wp_localize_script( 'listings-ajax-filters', 'listings_ajax_filters', $ajax_data );
        wp_enqueue_script( 'listings-job-application', LISTINGS_JOBS_PLUGIN_URL . '/assets/js/job-application.min.js', array( 'jquery' ), LISTINGS_JOBS_VERSION, true );
        wp_enqueue_script( 'listings-job-submission', LISTINGS_JOBS_PLUGIN_URL . '/assets/js/job-submission.min.js', array( 'jquery' ), LISTINGS_JOBS_VERSION, true );

        wp_register_script( 'listings-job-dashboard', LISTINGS_JOBS_PLUGIN_URL . '/assets/js/job-dashboard.min.js', array( 'jquery' ), LISTINGS_JOBS_VERSION, true );
        wp_localize_script( 'listings-job-dashboard', 'listings_job_dashboard', array(
            'i18n_confirm_delete' => __( 'Are you sure you want to delete this listing?', 'listings' )
        ) );
        wp_enqueue_script( 'listings-job-dashboard');
    }
}