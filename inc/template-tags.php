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
    printf( __( '<br />Modified: <time class="entry-date" datetime="%3$s" pubdate>%4$s</time><span class="byline"> by <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'rrze-dlp' ),
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