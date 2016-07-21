<?php if ( $apply = listings_jobs_get_application_method() ) :
	wp_enqueue_script( 'listings-jobs-application' );
	?>
	<div class="job_application application">
		<?php do_action( 'listings_jobs_application_start', $apply ); ?>
		
		<input type="button" class="application_button button" value="<?php _e( 'Apply for job', 'listings-jobs' ); ?>" />
		
		<div class="application_details">
			<?php
				/**
				 * listings_jobs_application_details_email or listings_jobs_application_details_url hook
				 */
				do_action( 'listings_jobs_application_details_' . $apply->type, $apply );
			?>
		</div>
		<?php do_action( 'listings_jobs_application_end', $apply ); ?>
	</div>
<?php endif; ?>
