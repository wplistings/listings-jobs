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

    return apply_filters('listings_jobs_the_job_type', $type, $post);
}

/**
 * listings_jobs_the_job_location function.
 * @param  boolean $map_link whether or not to link to google maps
 * @return [type]
 */
function listings_jobs_the_job_location( $map_link = true, $post = null ) {
    $location = listings_jobs_get_the_job_location( $post );

    if ( $location ) {
        if ( $map_link ) {
            // If linking to google maps, we don't want anything but text here
            echo apply_filters( 'listings_jobs_the_job_location_map_link', '<a class="google_map_link" href="' . esc_url( 'http://maps.google.com/maps?q=' . urlencode( strip_tags( $location ) ) . '&zoom=14&size=512x512&maptype=roadmap&sensor=false' ) . '" target="_blank">' . esc_html( strip_tags( $location ) ) . '</a>', $location, $post );
        } else {
            echo wp_kses_post( $location );
        }
    } else {
        echo wp_kses_post( apply_filters( 'listings_jobs_the_job_location_anywhere_text', __( 'Anywhere', 'listings-jobs' ) ) );
    }
}

/**
 * get_the_job_location function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function listings_jobs_get_the_job_location( $post = null ) {
    $post = get_post( $post );
    if ( $post->post_type !== 'job_listing' ) {
        return;
    }

    return apply_filters( 'the_job_location', $post->_job_location, $post );
}

/**
 * the_company_logo function.
 *
 * @access public
 * @param string $size (default: 'full')
 * @param mixed $default (default: null)
 * @return void
 */
function listings_jobs_the_company_logo( $size = 'thumbnail', $default = null, $post = null ) {
    $logo = listings_jobs_get_the_company_logo( $post, $size );

    if ( has_post_thumbnail( $post ) ) {
        echo '<img class="company_logo" src="' . esc_attr( $logo ) . '" alt="' . esc_attr( listings_jobs_get_the_company_name( $post ) ) . '" />';
    } elseif ( $default ) {
        echo '<img class="company_logo" src="' . esc_attr( $default ) . '" alt="' . esc_attr( listings_jobs_get_the_company_name( $post ) ) . '" />';
    } else {
        echo '<img class="company_logo" src="' . esc_attr( apply_filters( 'listings_jobs_default_company_logo', LISTINGS_JOBS_PLUGIN_URL . '/assets/images/company.png' ) ) . '" alt="' . esc_attr( listings_jobs_get_the_company_name( $post ) ) . '" />';
    }
}

/**
 * get_the_company_logo function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return string Image SRC
 */
function listings_jobs_get_the_company_logo( $post = null, $size = 'thumbnail' ) {
    $post = get_post( $post );

    if ( has_post_thumbnail( $post->ID ) ) {
        $src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $size );
        return $src ? $src[0] : '';
    }

    return '';
}

/**
 * Output the company video
 */
function listings_jobs_the_company_video( $post = null ) {
    $video    = listings_jobs_get_the_company_video( $post );
    $filetype = wp_check_filetype( $video );

    // FV Wordpress Flowplayer Support for advanced video formats
    if ( shortcode_exists( 'flowplayer' ) ) {
        $video_embed = '[flowplayer src="' . esc_attr( $video ) . '"]';
    } elseif ( ! empty( $filetype['ext'] ) ) {
        $video_embed = wp_video_shortcode( array( 'src' => $video ) );
    } else {
        $video_embed = wp_oembed_get( $video );
    }

    $video_embed = apply_filters( 'listings_jobs_the_company_video_embed', $video_embed, $post );

    if ( $video_embed ) {
        echo '<div class="company_video">' . $video_embed . '</div>';
    }
}

/**
 * Get the company video URL
 *
 * @param mixed $post (default: null)
 * @return string
 */
function listings_jobs_get_the_company_video( $post = null ) {
    $post = get_post( $post );
    if ( $post->post_type !== 'job_listing' ) {
        return;
    }
    return apply_filters( 'listings_jobs_the_company_video', $post->_company_video, $post );
}

/**
 * Display or retrieve the current company name with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function listings_jobs_the_company_name( $before = '', $after = '', $echo = true, $post = null ) {
    $company_name = listings_jobs_get_the_company_name( $post );

    if ( strlen( $company_name ) == 0 )
        return;

    $company_name = esc_attr( strip_tags( $company_name ) );
    $company_name = $before . $company_name . $after;

    if ( $echo )
        echo $company_name;
    else
        return $company_name;
}

/**
 * get_the_company_name function.
 *
 * @access public
 * @param int $post (default: null)
 * @return string
 */
function listings_jobs_get_the_company_name( $post = null ) {
    $post = get_post( $post );
    if ( $post->post_type !== 'job_listing' ) {
        return '';
    }

    return apply_filters( 'listings_jobs_the_company_name', $post->_company_name, $post );
}

/**
 * listings_jobs_get_the_company_website function.
 *
 * @access public
 * @param int $post (default: null)
 * @return void
 */
function listings_jobs_get_the_company_website( $post = null ) {
    $post = get_post( $post );

    if ( $post->post_type !== 'job_listing' )
        return;

    $website = $post->_company_website;

    if ( $website && ! strstr( $website, 'http:' ) && ! strstr( $website, 'https:' ) ) {
        $website = 'http://' . $website;
    }

    return apply_filters( 'listings_jobs_the_company_website', $website, $post );
}

/**
 * Display or retrieve the current company tagline with optional content.
 *
 * @access public
 * @param mixed $id (default: null)
 * @return void
 */
function listings_jobs_the_company_tagline( $before = '', $after = '', $echo = true, $post = null ) {
    $company_tagline = listings_jobs_get_the_company_tagline( $post );

    if ( strlen( $company_tagline ) == 0 )
        return;

    $company_tagline = esc_attr( strip_tags( $company_tagline ) );
    $company_tagline = $before . $company_tagline . $after;

    if ( $echo )
        echo $company_tagline;
    else
        return $company_tagline;
}

/**
 * get_the_company_tagline function.
 *
 * @access public
 * @param int $post (default: 0)
 * @return void
 */
function listings_jobs_get_the_company_tagline( $post = null ) {
    $post = get_post( $post );

    if ( $post->post_type !== 'job_listing' )
        return;

    return apply_filters( 'listings_jobs_the_company_tagline', $post->_company_tagline, $post );
}