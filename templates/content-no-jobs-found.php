<?php if ( defined( 'DOING_AJAX' ) ) : ?>
	<li class="no_job_listings_found"><?php _e( 'There are no listings matching your search.', 'listings-jobs' ); ?></li>
<?php else : ?>
	<p class="no_job_listings_found"><?php _e( 'There are currently no vacancies.', 'listings-jobs' ); ?></p>
<?php endif; ?>