<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

wp_clear_scheduled_hook( 'listings_jobs_check_for_expired_jobs' );

wp_trash_post( get_option( 'listings_jobs_submit_job_form_page_id' ) );
wp_trash_post( get_option( 'listings_jobs_job_dashboard_page_id' ) );
wp_trash_post( get_option( 'listings_jobs_jobs_page_id' ) );

$options = array(
    'listings_jobs_version',
    'listings_jobs_hide_filled_positions',
    'listings_jobs_enable_categories',
    'listings_jobs_enable_default_category_multiselect',
    'listings_jobs_category_filter_type',
    'listings_jobs_user_requires_account',
    'listings_jobs_enable_registration',
    'listings_jobs_registration_role',
    'listings_jobs_submission_requires_approval',
    'listings_jobs_user_can_edit_pending_submissions',
    'listings_jobs_submission_duration',
    'listings_jobs_allowed_application_method',
    'listings_jobs_installed_terms',
    'listings_jobs_submit_page_slug',
    'listings_jobs_dashboard_page_slug',
    'listings_jobs_submit_job_form_page_id',
    'listings_jobs_job_dashboard_page_id',
    'listings_jobs_jobs_page_id',
);

foreach ( $options as $option ) {
    delete_option( $option );
}