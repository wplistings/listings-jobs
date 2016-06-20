<?php

if ( ! function_exists( 'listings_jobs_get_types' ) ) :
    /**
     * Get job listing types
     *
     * @access public
     * @return array
     */
    function listings_jobs_get_types( $fields = 'all' ) {
        return get_terms( "job_listing_type", array(
            'orderby'    => 'name',
            'order'      => 'ASC',
            'hide_empty' => false,
            'fields'     => $fields
        ) );
    }
endif;

if ( ! function_exists( 'listings_jobs_get_filtered_links' ) ) :
    /**
     * Shows links after filtering jobs
     */
    function listings_jobs_get_filtered_links( $args = array() ) {
        $job_categories = array();
        $types          = listings_jobs_get_types();

        // Convert to slugs
        if ( $args['search_categories'] ) {
            foreach ( $args['search_categories'] as $category ) {
                if ( is_numeric( $category ) ) {
                    $category_object = get_term_by( 'id', $category, 'job_listing_category' );
                    if ( ! is_wp_error( $category_object ) ) {
                        $job_categories[] = $category_object->slug;
                    }
                } else {
                    $job_categories[] = $category;
                }
            }
        }

        $links = apply_filters( 'listings_jobs_filters_showing_links', array(
            'reset' => array(
                'name' => __( 'Reset', 'listings-jobs' ),
                'url'  => '#'
            ),
            'rss_link' => array(
                'name' => __( 'RSS', 'listings-jobs' ),
                'url'  => listings_jobs_get_rss_link( apply_filters( 'listings_jobs_get_listings_custom_filter_rss_args', array(
                    'job_types'       => isset( $args['filter_job_types'] ) ? implode( ',', $args['filter_job_types'] ) : '',
                    'search_location' => $args['search_location'],
                    'job_categories'  => implode( ',', $job_categories ),
                    'search_keywords' => $args['search_keywords'],
                ) ) )
            )
        ), $args );

        if ( sizeof( $args['filter_job_types'] ) === sizeof( $types ) && ! $args['search_keywords'] && ! $args['search_location'] && ! $args['search_categories'] && ! apply_filters( 'listings_jobs_get_listings_custom_filter', false ) ) {
            unset( $links['reset'] );
        }

        $return = '';

        foreach ( $links as $key => $link ) {
            $return .= '<a href="' . esc_url( $link['url'] ) . '" class="' . esc_attr( $key ) . '">' . $link['name'] . '</a>';
        }

        return $return;
    }
endif;

if ( ! function_exists( 'listings_jobs_get_rss_link' ) ) :
    /**
     * Get the Job Listing RSS link
     *
     * @return string
     */
    function listings_jobs_get_rss_link( $args = array() ) {
        $rss_link = add_query_arg( urlencode_deep( array_merge( array( 'feed' => 'job_feed' ), $args ) ), home_url() );
        return $rss_link;
    }
endif;