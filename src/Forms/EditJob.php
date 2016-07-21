<?php

namespace Listings\Jobs\Forms;

class EditJob extends SubmitJob {

	public $form_name           = 'edit-job';

	/** @var EditJob The single instance of the class */
	protected static $_instance = null;

	/**
	 * Main Instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->job_id = ! empty( $_REQUEST['job_id'] ) ? absint( $_REQUEST[ 'job_id' ] ) : 0;

		if  ( ! listings_user_can_edit_listing( $this->job_id ) ) {
			$this->job_id = 0;
		}
	}

	/**
	 * Get the submitted job ID
	 * @return int
	 */
	public function get_job_id() {
		return absint( $this->job_id );
	}

	/**
	 * output function.
	 */
	public function output( $atts = array() ) {
		$this->submit_handler();
		$this->submit();
	}

	/**
	 * Submit Step
	 */
	public function submit() {
		$job = get_post( $this->job_id );

		if ( empty( $this->job_id  ) || ( $job->post_status !== 'publish' && ! listings_user_can_edit_pending_submissions() ) ) {
			echo wpautop( __( 'Invalid listing', 'listings-jobs' ) );
			return;
		}

		$this->init_fields();

		foreach ( $this->fields as $group_key => $group_fields ) {
			foreach ( $group_fields as $key => $field ) {
				if ( ! isset( $this->fields[ $group_key ][ $key ]['value'] ) ) {
					if ( 'job_title' === $key ) {
						$this->fields[ $group_key ][ $key ]['value'] = $job->post_title;

					} elseif ( 'job_description' === $key ) {
						$this->fields[ $group_key ][ $key ]['value'] = $job->post_content;

					} elseif ( 'company_logo' === $key ) {
						$this->fields[ $group_key ][ $key ]['value'] = has_post_thumbnail( $job->ID ) ? get_post_thumbnail_id( $job->ID ) : get_post_meta( $job->ID, '_' . $key, true );

					} elseif ( ! empty( $field['taxonomy'] ) ) {
						$this->fields[ $group_key ][ $key ]['value'] = wp_get_object_terms( $job->ID, $field['taxonomy'], array( 'fields' => 'ids' ) );

					} else {
						$this->fields[ $group_key ][ $key ]['value'] = get_post_meta( $job->ID, '_' . $key, true );
					}
				}
			}
		}

		$this->fields = apply_filters( 'submit_job_form_fields_get_job_data', $this->fields, $job );

		wp_enqueue_script( 'listings-jobs-job-submission' );

		listings_get_template( 'job-submit.php', array(
			'form'               => $this->form_name,
			'job_id'             => $this->get_job_id(),
			'action'             => $this->get_action(),
			'job_fields'         => $this->get_fields( 'job' ),
			'company_fields'     => $this->get_fields( 'company' ),
			'step'               => $this->get_step(),
			'submit_button_text' => __( 'Save changes', 'listings-jobs' )
			) );
	}

	/**
	 * Submit Step is posted
	 */
	public function submit_handler() {
		if ( empty( $_POST['submit_job'] ) ) {
			return;
		}

		try {

			// Get posted values
			$values = $this->get_posted_fields();

			// Validate required
			if ( is_wp_error( ( $return = $this->validate_fields( $values ) ) ) ) {
				throw new \Exception( $return->get_error_message() );
			}

			// Update the job
			$this->save_job( $values['job']['job_title'], $values['job']['job_description'], '', $values, false );
			$this->update_job_data( $values );

			// Successful
			switch ( get_post_status( $this->job_id ) ) {
				case 'publish' :
					echo '<div class="listings-message">' . __( 'Your changes have been saved.', 'listings-jobs' ) . ' <a href="' . get_permalink( $this->job_id ) . '">' . __( 'View &rarr;', 'listings-jobs' ) . '</a>' . '</div>';
				break;
				default :
					echo '<div class="listings-message">' . __( 'Your changes have been saved.', 'listings-jobs' ) . '</div>';
				break;
			}

		} catch ( \Exception $e ) {
			echo '<div class="listings-error">' . $e->getMessage() . '</div>';
			return;
		}
	}
}
