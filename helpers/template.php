<?php

/**
 * Return whether or not the position has been marked as filled
 *
 * @param  object $post
 * @return boolean
 */
function is_position_filled( $post = null ) {
    $post = get_post( $post );
    return $post->_filled ? true : false;
}

/**
 * Return whether or not the position has been featured
 *
 * @param  object $post
 * @return boolean
 */
function is_position_featured( $post = null ) {
    $post = get_post( $post );
    return $post->_featured ? true : false;
}

/**
 * Return whether or not applications are allowed
 *
 * @param  object $post
 * @return boolean
 */
function candidates_can_apply( $post = null ) {
    $post = get_post( $post );
    return apply_filters( 'job_manager_candidates_can_apply', ( ! is_position_filled() && ! in_array( $post->post_status, array( 'preview', 'expired' ) ) ), $post );
}