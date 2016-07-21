<?php

namespace Listings\Jobs\Admin;

use Listings\Jobs\Admin\Metaboxes\JobDetails;

class Admin
{
    public function __construct()
    {
        $this->setup = new Setup();
        $this->jobdetails = new JobDetails();
        $this->cpt = new Cpt();
        $this->settings = new Settings();
        $this->settings->hooks();

        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style( 'listings-jobs-admin', LISTINGS_JOBS_PLUGIN_URL . '/assets/css/admin.css' );
        wp_enqueue_script( 'listings-jobs-admin', LISTINGS_JOBS_PLUGIN_URL. '/assets/js/admin.min.js', array( 'jquery', 'jquery-tiptip', 'jquery-ui-datepicker' ), LISTINGS_VERSION, true );

        wp_localize_script( 'listings-jobs-admin', 'listings_jobs_admin', array(
            'date_format' => _x( 'yy-mm-dd', 'Date format for jQuery datepicker', 'listings-jobs' )
        ) );
    }
}