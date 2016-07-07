<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

wp_trash_post( get_option( 'listings_jobs_submit_job_form_page_id' ) );
wp_trash_post( get_option( 'listings_jobs_job_dashboard_page_id' ) );
wp_trash_post( get_option( 'listings_jobs_jobs_page_id' ) );

$options = array(
    'listings_jobs_submit_job_form_page_id',
    'listings_jobs_job_dashboard_page_id',
    'listings_jobs_jobs_page_id',
);

foreach ( $options as $option ) {
    delete_option( $option );
}