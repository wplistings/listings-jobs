<?php

if ( ! function_exists( 'listings_jobs_get_listings' ) ) :
    /**
     * Queries job listings with certain criteria and returns them
     *
     * @access public
     * @return void
     */
    function listings_jobs_get_listings( $args = array() ) {
        global $wpdb, $job_manager_keyword;

        $args = wp_parse_args( $args, array(
            'search_location'   => '',
            'search_keywords'   => '',
            'search_categories' => array(),
            'job_types'         => array(),
            'offset'            => 0,
            'posts_per_page'    => 20,
            'orderby'           => 'date',
            'order'             => 'DESC',
            'featured'          => null,
            'filled'            => null,
            'fields'            => 'all'
        ) );

        $query_args = array(
            'post_type'              => 'job_listing',
            'post_status'            => 'publish',
            'ignore_sticky_posts'    => 1,
            'offset'                 => absint( $args['offset'] ),
            'posts_per_page'         => intval( $args['posts_per_page'] ),
            'orderby'                => $args['orderby'],
            'order'                  => $args['order'],
            'tax_query'              => array(),
            'meta_query'             => array(),
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
            'cache_results'          => false,
            'fields'                 => $args['fields']
        );

        // WPML workaround
        if ( ( strstr( $_SERVER['REQUEST_URI'], '/jm-ajax/' ) || ! empty( $_GET['jm-ajax'] ) ) && isset( $_POST['lang'] ) ) {
            do_action( 'wpml_switch_language', sanitize_text_field( $_POST['lang'] ) );
        }

        if ( $args['posts_per_page'] < 0 ) {
            $query_args['no_found_rows'] = true;
        }

        if ( ! empty( $args['search_location'] ) ) {
            $location_meta_keys = array( 'geolocation_formatted_address', '_job_location', 'geolocation_state_long' );
            $location_search    = array( 'relation' => 'OR' );
            foreach ( $location_meta_keys as $meta_key ) {
                $location_search[] = array(
                    'key'     => $meta_key,
                    'value'   => $args['search_location'],
                    'compare' => 'like'
                );
            }
            $query_args['meta_query'][] = $location_search;
        }

        if ( ! is_null( $args['featured'] ) ) {
            $query_args['meta_query'][] = array(
                'key'     => '_featured',
                'value'   => '1',
                'compare' => $args['featured'] ? '=' : '!='
            );
        }

        if ( ! is_null( $args['filled'] ) || 1 === absint( get_option( 'job_manager_hide_filled_positions' ) ) ) {
            $query_args['meta_query'][] = array(
                'key'     => '_filled',
                'value'   => '1',
                'compare' => $args['filled'] ? '=' : '!='
            );
        }

        if ( ! empty( $args['job_types'] ) ) {
            $query_args['tax_query'][] = array(
                'taxonomy' => 'job_listing_type',
                'field'    => 'slug',
                'terms'    => $args['job_types']
            );
        }

        if ( ! empty( $args['search_categories'] ) ) {
            $field    = is_numeric( $args['search_categories'][0] ) ? 'term_id' : 'slug';
            $operator = 'all' === get_option( 'job_manager_category_filter_type', 'all' ) && sizeof( $args['search_categories'] ) > 1 ? 'AND' : 'IN';
            $query_args['tax_query'][] = array(
                'taxonomy'         => 'job_listing_category',
                'field'            => $field,
                'terms'            => array_values( $args['search_categories'] ),
                'include_children' => $operator !== 'AND' ,
                'operator'         => $operator
            );
        }

        if ( 'featured' === $args['orderby'] ) {
            $query_args['orderby'] = array(
                'menu_order' => 'ASC',
                'date'       => 'DESC'
            );
        }

        $job_manager_keyword = sanitize_text_field( $args['search_keywords'] );

        if ( ! empty( $job_manager_keyword ) && strlen( $job_manager_keyword ) >= apply_filters( 'job_manager_get_listings_keyword_length_threshold', 2 ) ) {
            $query_args['_keyword'] = $job_manager_keyword; // Does nothing but needed for unique hash
            add_filter( 'posts_clauses', 'get_job_listings_keyword_search' );
        }

        $query_args = apply_filters( 'job_manager_get_listings', $query_args, $args );

        if ( empty( $query_args['meta_query'] ) ) {
            unset( $query_args['meta_query'] );
        }

        if ( empty( $query_args['tax_query'] ) ) {
            unset( $query_args['tax_query'] );
        }

        // Polylang LANG arg
        if ( function_exists( 'pll_current_language' ) ) {
            $query_args['lang'] = pll_current_language();
        }

        // Filter args
        $query_args = apply_filters( 'get_job_listings_query_args', $query_args, $args );

        // Generate hash
        $to_hash         = json_encode( $query_args ) . apply_filters( 'wpml_current_language', '' );
        $query_args_hash = 'jm_' . md5( $to_hash ) . \Listings\CacheHelper::get_transient_version( 'get_job_listings' );

        do_action( 'before_get_job_listings', $query_args, $args );

        if ( false === ( $result = get_transient( $query_args_hash ) ) ) {
            $result = new WP_Query( $query_args );
            set_transient( $query_args_hash, $result, DAY_IN_SECONDS * 30 );
        }

        do_action( 'after_get_job_listings', $query_args, $args );

        remove_filter( 'posts_clauses', 'get_job_listings_keyword_search' );

        return $result;
    }
endif;

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