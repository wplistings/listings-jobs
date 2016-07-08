jQuery(document).ready(function($) {
    // Datepicker
    $('input#_job_expires').datepicker({
        altFormat: 'yy-mm-dd',
        dateFormat: listings_jobs_admin.date_format,
        minDate: 0
    });

    $('input#_job_expires').each(function () {
        if ($(this).val()) {
            var date = new Date($(this).val());
            $(this).datepicker("setDate", date);
        }
    });
});