jQuery(document).ready(function($) {
	jQuery('body').on( 'click', '.listings-jobs-remove-uploaded-file', function() {
		jQuery(this).closest( '.listings-jobs-uploaded-file' ).remove();
		return false;
	});
});