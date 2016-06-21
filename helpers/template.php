<?php

/**
 * Return whether or not the position has been marked as filled
 *
 * @param  object $post
 * @return boolean
 */
function listings_jobs_is_position_filled( $post = null ) {
    $post = get_post( $post );
    return $post->_filled ? true : false;
}

/**
 * Return whether or not the position has been featured
 *
 * @param  object $post
 * @return boolean
 */
function listings_jobs_is_position_featured( $post = null ) {
    $post = get_post( $post );
    return $post->_featured ? true : false;
}

/**
 * Return whether or not applications are allowed
 *
 * @param  object $post
 * @return boolean
 */
function listings_jobs_candidates_can_apply( $post = null ) {
    $post = get_post( $post );
    return apply_filters( 'listings_jobs_candidates_can_apply', ( ! listings_jobs_is_position_filled() && ! in_array( $post->post_status, array( 'preview', 'expired' ) ) ), $post );
}

/**
 * listings_jobs_the_job_permalink function.
 *
 * @access public
 * @return void
 */
function listings_jobs_the_job_permalink( $post = null ) {
    echo listings_jobs_get_the_job_permalink( $post );
}

/**
 * listings_jobs_get_the_job_permalink function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return string
 */
function listings_jobs_get_the_job_permalink( $post = null ) {
    $post = get_post( $post );
    $link = get_permalink( $post );

    return apply_filters( 'listings_jobs_the_job_permalink', $link, $post );
}

/**
 * listings_jobs_get_application_method function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return object
 */
function listings_jobs_get_application_method( $post = null ) {
    $post = get_post( $post );

    if ( $post && $post->post_type !== 'job_listing' ) {
        return;
    }

    $method = new stdClass();
    $apply  = $post->_application;

    if ( empty( $apply ) )
        return false;

    if ( strstr( $apply, '@' ) && is_email( $apply ) ) {
        $method->type      = 'email';
        $method->raw_email = $apply;
        $method->email     = antispambot( $apply );
        $method->subject   = apply_filters( 'listings_jobs_application_email_subject', sprintf( __( 'Application via "%s" listing on %s', 'listings-jobs' ), $post->post_title, home_url() ), $post );
    } else {
        if ( strpos( $apply, 'http' ) !== 0 )
            $apply = 'http://' . $apply;
        $method->type = 'url';
        $method->url  = $apply;
    }

    return apply_filters( 'listings_jobs_application_method', $method, $post );
}

/**
 * listings_jobs_the_job_type function.
 *
 * @access public
 * @return void
 */
function listings_jobs_the_job_type($post = null) {
    if ($job_type = listings_jobs_get_the_job_type($post)) {
        echo $job_type->name;
    }
}

/**
 * listings_jobs_get_the_job_type function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function listings_jobs_get_the_job_type($post = null) {
    $post = get_post($post);
    if ($post->post_type !== 'job_listing') {
        return;
    }

    $types = wp_get_post_terms($post->ID, 'job_listing_type');

    if ($types) {
        $type = current($types);
    } else {
        $type = false;
    }

    return apply_filters('the_job_type', $type, $post);
}