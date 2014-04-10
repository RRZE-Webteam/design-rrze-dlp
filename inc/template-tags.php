<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package RRZE-DLP
 * @since RRZE-DLP 2.0
 */

if ( ! function_exists( 'rrze-dlp_modified' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 *
 * @since RRZE-DLP 2.0
 */
function rrze_dlp_modified() {
    printf( __( '<br />Modified: <a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a><span class="byline"> by <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'rrze-dlp' ),
        esc_url( get_permalink() ),
        esc_attr( get_the_time() ),
        esc_attr( get_the_date( 'c' ) ),
        esc_html( get_the_modified_date(__('F j, Y', 'rrze-dlp') ) ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
        esc_attr( sprintf( __( 'View all posts by %s', 'rrze-dlp' ), get_the_author() ) ),
        esc_html( get_the_author() )
    );
}
endif;

/**
 * Returns true if a blog has more than 1 category
 *
 * @since RRZE-DLP 2.0
 */
function rrze_dlp_categorized_blog() {
    if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
        // Create an array of all the categories that are attached to posts
        $all_the_cool_cats = get_categories( array(
            'hide_empty' => 1,
        ) );

        // Count the number of categories that are attached to the posts
        $all_the_cool_cats = count( $all_the_cool_cats );

        set_transient( 'all_the_cool_cats', $all_the_cool_cats );
    }

    if ( '1' != $all_the_cool_cats ) {
        // This blog has more than 1 category so rrze_dlp_categorized_blog should return true
        return true;
    } else {
        // This blog has only 1 category so rrze_dlp_categorized_blog should return false
        return false;
    }
}

/**
 * Flush out the transients used in rrze_dlp_categorized_blog
 *
 * @since RRZE-DLP 2.0
 */
function rrze_dlp_category_transient_flusher() {
    // Like, beat it. Dig?
    delete_transient( 'all_the_cool_cats' );
}
add_action( 'edit_category', 'rrze_dlp_category_transient_flusher' );
add_action( 'save_post', 'rrze_dlp_category_transient_flusher' );