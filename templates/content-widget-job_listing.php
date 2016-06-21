<li <?php listings_jobs_job_listing_class(); ?>>
	<a href="<?php listings_jobs_the_job_permalink(); ?>">
		<div class="position">
			<h3><?php the_title(); ?></h3>
		</div>
		<ul class="meta">
			<li class="location"><?php listings_jobs_the_job_location( false ); ?></li>
			<li class="company"><?php listings_jobs_the_company_name(); ?></li>
			<li class="job-type <?php echo listings_jobs_the_job_type() ? sanitize_title( listings_jobs_the_job_type()->slug ) : ''; ?>"><?php listings_jobs_the_job_type(); ?></li>
		</ul>
	</a>
</li>