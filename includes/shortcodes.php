<?php
/** Modified from: Display Posts Shortcode
 * Plugin URI: http://www.billerickson.net/shortcode-to-display-posts/
 * Description: Display a listing of posts using the [display-posts] shortcode
 * Version: 1.7
 * Author: Bill Erickson
 * Author URI: http://www.billerickson.net
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package Display Posts
 * @version 1.7
 * @author Bill Erickson <bill@billerickson.net>
 * @copyright Copyright (c) 2011, Bill Erickson
 * @link http://www.billerickson.net/shortcode-to-display-posts/
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

 
// Create the shortcode
add_shortcode('sermons', 'wpfc_display_sermons_shortcode');
function wpfc_display_sermons_shortcode($atts) {

	// Pull in shortcode attributes and set defaults
	extract( shortcode_atts( array(
		'id' => false,
		'posts_per_page' => '10',
		'order' => 'DESC',
		'taxonomy' => false,
		'tax_term' => false,
		'tax_operator' => 'IN'
	), $atts ) );
	
	// Set up initial query for post
	$args = array(
		'post_type' => 'wpfc_sermon',
		'posts_per_page' => $posts_per_page,
		'order' => $order,
		'meta_key' => 'sermon_date',
        'meta_value' => date("m/d/Y"),
        'meta_compare' => '>=',
        'orderby' => 'meta_value',
	);
	
	// If Post IDs
	if( $id ) {
		$posts_in = explode( ',', $id );
		$args['post__in'] = $posts_in;
	}
	
	// If taxonomy attributes, create a taxonomy query
	if ( !empty( $taxonomy ) && !empty( $tax_term ) ) {
	
		// Term string to array
		$tax_term = explode( ', ', $tax_term );
		
		// Validate operator
		if( !in_array( $tax_operator, array( 'IN', 'NOT IN', 'AND' ) ) )
			$tax_operator = 'IN';
					
		$tax_args = array(
			'tax_query' => array(
				array(
					'taxonomy' => $taxonomy,
					'field' => 'slug',
					'terms' => $tax_term,
					'operator' => $tax_operator
				)
			)
		);
		$args = array_merge( $args, $tax_args );
	}
	
	$listing = new WP_Query( $args, $atts ) ;
	if ( !$listing->have_posts() )
		return;
	$inner = '';
	while ( $listing->have_posts() ): $listing->the_post(); global $post;
		
		$ugly_date = get_post_meta($post->ID, 'sermon_date', 'true');
		$displayDate = date('l, F j, Y', $ugly_date); ?>
		<div class="wpfc_date"><?php echo $displayDate; ?></div>
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2> 
		<div id="wpfc_sermon">		  
			<div class="wpfc_sermon-meta">
				<?php 
					if (get_post_meta($post->ID, 'bible_passage', true)) {
					echo get_post_meta($post->ID, 'bible_passage', true); ?> |								
				<?php } 
					echo the_terms( $post->ID, 'wpfc_preacher', '', ', ', ' ' ); 
					echo the_terms( $post->ID, 'wpfc_sermon_series', '<br />Series: ', ', ', '' ); 
				?>
			</div>
		</div> <br />
		<?php /* Display navigation to next/previous pages when applicable */ ?>
		<?php /*if (  $index_query->max_num_pages > 1 ) : ?>
				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'thirdstyle' ) ); ?></div>
					<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'thirdstyle' ) ); ?></div>
				</div><!-- #nav-below -->
		<?php endif; */?>
	<?php

		
	endwhile; wp_reset_query();
	
	$return = $inner;

	return $return;
}
?>