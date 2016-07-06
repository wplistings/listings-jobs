<?php

namespace Listings\Jobs\Admin;

class Settings {
    public function hooks()
    {
        add_filter( 'listings_settings', array($this, 'settings' ) );
        add_action( 'listings_after_settings', array($this, 'after_settings') );
    }

    public function settings( $settings )
    {
        // Prepare roles option
        $roles         = get_editable_roles();
        $account_roles = array();

        foreach ( $roles as $key => $role ) {
            if ( $key == 'administrator' ) {
                continue;
            }
            $account_roles[ $key ] = $role['name'];
        }

        return array_merge( $settings, array(
            'job_listings' => array(
                __( 'Job Listings', 'listings_jobs' ),
                array(
                    array(
                        'name'        => 'listings_jobs_per_page',
                        'std'         => '10',
                        'placeholder' => '',
                        'label'       => __( 'Listings Per Page', 'listings-jobs' ),
                        'desc'        => __( 'How many listings should be shown per page by default?', 'listings-jobs' ),
                        'attributes'  => array()
                    ),
                    array(
                        'name'       => 'listings_jobs_hide_filled_positions',
                        'std'        => '0',
                        'label'      => __( 'Filled Positions', 'listings-jobs' ),
                        'cb_label'   => __( 'Hide filled positions', 'listings-jobs' ),
                        'desc'       => __( 'If enabled, filled positions will be hidden from archives.', 'listings-jobs' ),
                        'type'       => 'checkbox',
                        'attributes' => array()
                    ),
                    array(
                        'name'       => 'listings_jobs_hide_expired_content',
                        'std'        => '1',
                        'label'      => __( 'Expired Listings', 'listings-jobs' ),
                        'cb_label'   => __( 'Hide content within expired listings', 'listings-jobs' ),
                        'desc'       => __( 'If enabled, the content within expired listings will be hidden. Otherwise, expired listings will be displayed as normal (without the application area).', 'listings-jobs' ),
                        'type'       => 'checkbox',
                        'attributes' => array()
                    ),
                    array(
                        'name'       => 'listings_jobs_enable_categories',
                        'std'        => '0',
                        'label'      => __( 'Categories', 'listings-jobs' ),
                        'cb_label'   => __( 'Enable categories for listings', 'listings-jobs' ),
                        'desc'       => __( 'Choose whether to enable categories. Categories must be setup by an admin to allow users to choose them during submission.', 'listings-jobs' ),
                        'type'       => 'checkbox',
                        'attributes' => array()
                    ),
                    array(
                        'name'       => 'listings_jobs_enable_default_category_multiselect',
                        'std'        => '0',
                        'label'      => __( 'Multi-select Categories', 'listings-jobs' ),
                        'cb_label'   => __( 'Enable category multiselect by default', 'listings-jobs' ),
                        'desc'       => __( 'If enabled, the category select box will default to a multiselect on the [jobs] shortcode.', 'listings-jobs' ),
                        'type'       => 'checkbox',
                        'attributes' => array()
                    ),
                    array(
                        'name'       => 'listings_jobs_category_filter_type',
                        'std'        => 'any',
                        'label'      => __( 'Category Filter Type', 'listings-jobs' ),
                        'desc'       => __( 'If enabled, the category select box will default to a multiselect on the [jobs] shortcode.', 'listings-jobs' ),
                        'type'       => 'select',
                        'options' => array(
                            'any'  => __( 'Jobs will be shown if within ANY selected category', 'listings-jobs' ),
                            'all' => __( 'Jobs will be shown if within ALL selected categories', 'listings-jobs' ),
                        )
                    ),
                ),
            ),
            'job_submission' => array(
                __( 'Job Submission', 'listings-jobs' ),
                array(
                    array(
                        'name'       => 'listings_jobs_user_requires_account',
                        'std'        => '1',
                        'label'      => __( 'Account Required', 'listings-jobs' ),
                        'cb_label'   => __( 'Submitting listings requires an account', 'listings-jobs' ),
                        'desc'       => __( 'If disabled, non-logged in users will be able to submit listings without creating an account.', 'listings-jobs' ),
                        'type'       => 'checkbox',
                        'attributes' => array()
                    ),
                    array(
                        'name'       => 'listings_jobs_enable_registration',
                        'std'        => '1',
                        'label'      => __( 'Account Creation', 'listings-jobs' ),
                        'cb_label'   => __( 'Allow account creation', 'listings-jobs' ),
                        'desc'       => __( 'If enabled, non-logged in users will be able to create an account by entering their email address on the submission form.', 'listings-jobs' ),
                        'type'       => 'checkbox',
                        'attributes' => array()
                    ),
                    array(
                        'name'       => 'listings_jobs_generate_username_from_email',
                        'std'        => '1',
                        'label'      => __( 'Account Username', 'listings-jobs' ),
                        'cb_label'   => __( 'Automatically Generate Username from Email Address', 'listings-jobs' ),
                        'desc'       => __( 'If enabled, a username will be generated from the first part of the user email address. Otherwise, a username field will be shown.', 'listings-jobs' ),
                        'type'       => 'checkbox',
                        'attributes' => array()
                    ),
                    array(
                        'name'       => 'listings_jobs_registration_role',
                        'std'        => 'employer',
                        'label'      => __( 'Account Role', 'listings-jobs' ),
                        'desc'       => __( 'If you enable registration on your submission form, choose a role for the new user.', 'listings-jobs' ),
                        'type'       => 'select',
                        'options'    => $account_roles
                    ),
                    array(
                        'name'       => 'listings_jobs_submission_requires_approval',
                        'std'        => '1',
                        'label'      => __( 'Moderate New Listings', 'listings-jobs' ),
                        'cb_label'   => __( 'New listing submissions require admin approval', 'listings-jobs' ),
                        'desc'       => __( 'If enabled, new submissions will be inactive, pending admin approval.', 'listings-jobs' ),
                        'type'       => 'checkbox',
                        'attributes' => array()
                    ),
                    array(
                        'name'       => 'listings_jobs_user_can_edit_pending_submissions',
                        'std'        => '0',
                        'label'      => __( 'Allow Pending Edits', 'listings-jobs' ),
                        'cb_label'   => __( 'Submissions awaiting approval can be edited', 'listings-jobs' ),
                        'desc'       => __( 'If enabled, submissions awaiting admin approval can be edited by the user.', 'listings-jobs' ),
                        'type'       => 'checkbox',
                        'attributes' => array()
                    ),
                    array(
                        'name'       => 'listings_jobs_submission_duration',
                        'std'        => '30',
                        'label'      => __( 'Listing Duration', 'listings-jobs' ),
                        'desc'       => __( 'How many <strong>days</strong> listings are live before expiring. Can be left blank to never expire.', 'listings-jobs' ),
                        'attributes' => array()
                    ),
                    array(
                        'name'       => 'listings_jobs_allowed_application_method',
                        'std'        => '',
                        'label'      => __( 'Application Method', 'listings-jobs' ),
                        'desc'       => __( 'Choose the contact method for listings.', 'listings-jobs' ),
                        'type'       => 'select',
                        'options'    => array(
                            ''      => __( 'Email address or website URL', 'listings-jobs' ),
                            'email' => __( 'Email addresses only', 'listings-jobs' ),
                            'url'   => __( 'Website URLs only', 'listings-jobs' ),
                        )
                    )
                )
            ),
            'job_pages' => array(
                __( 'Pages', 'listings-jobs' ),
                array(
                    array(
                        'name' 		=> 'listings_jobs_submit_job_form_page_id',
                        'std' 		=> '',
                        'label' 	=> __( 'Submit Job Form Page', 'listings-jobs' ),
                        'desc'		=> __( 'Select the page where you have placed the [submit_job_form] shortcode. This lets the plugin know where the form is located.', 'listings-jobs' ),
                        'type'      => 'page'
                    ),
                    array(
                        'name' 		=> 'listings_jobs_job_dashboard_page_id',
                        'std' 		=> '',
                        'label' 	=> __( 'Job Dashboard Page', 'listings-jobs' ),
                        'desc'		=> __( 'Select the page where you have placed the [job_dashboard] shortcode. This lets the plugin know where the dashboard is located.', 'listings-jobs' ),
                        'type'      => 'page'
                    ),
                    array(
                        'name' 		=> 'listings_jobs_jobs_page_id',
                        'std' 		=> '',
                        'label' 	=> __( 'Job Listings Page', 'listings-jobs' ),
                        'desc'		=> __( 'Select the page where you have placed the [jobs] shortcode. This lets the plugin know where the job listings page is located.', 'listings-jobs' ),
                        'type'      => 'page'
                    ),
                )
            )
        ) );
    }

    public function after_settings()
    {
        ?>
        <script type="text/javascript">
        jQuery('.nav-tab-wrapper a:first').click();
			jQuery('#setting-listings_jobs_enable_registration').change(function(){
                if ( jQuery( this ).is(':checked') ) {
                    jQuery('#setting-listings_jobs_registration_role').closest('tr').show();
                    jQuery('#setting-listings_jobs_registration_username_from_email').closest('tr').show();
                } else {
                    jQuery('#setting-listings_jobs_registration_role').closest('tr').hide();
                    jQuery('#setting-listings_jobs_registration_username_from_email').closest('tr').hide();
                }
            }).change();
		</script>
        <?php
    }
}