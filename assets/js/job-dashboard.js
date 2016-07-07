jQuery(document).ready(function($) {

	$('.job-dashboard-action-delete').click(function() {
		return confirm( listings_job_dashboard.i18n_confirm_delete );
	});

});