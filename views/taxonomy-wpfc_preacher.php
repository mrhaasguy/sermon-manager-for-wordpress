<?php
/**
 * The template for displaying Speaker pages.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header(); 
$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );  
$termid = $term->term_id; 
?>

		<div id="container">
			<div id="content" role="main">

				<h1 class="page-title"><?php
					printf( __( 'Sermons by: %s', 'twentyten' ), '<span>' . single_cat_title( '', false ) . '</span>' );
				?></h1>
				<?php
					/* Preacher Image */
						$saved_data = get_tax_meta($termid,'wpfc_preacher_image',true);
						$attachment_id = $saved_data['id'];
						$image_attributes = wp_get_attachment_image_src( $attachment_id, 'medium' ); // returns an array
				?> 
				<?php if ($saved_data) { ?>
			    <img src="<?php echo $image_attributes[0]; ?>" width="<?php echo $image_attributes[1]; ?>" height="<?php echo $image_attributes[2]; ?>">
				<?php }
					$category_description = category_description();
					if ( ! empty( $category_description ) )
						echo '<div class="archive-meta">' . $category_description . '</div>';
				/* Run the loop for the category page to output the posts.
				 * If you want to overload this in a child theme then include a file
				 * called loop-category.php and that will be used instead.
				 */
				// Order sermons by date with the latest sermon first.
				global $wp_query;
				$args = array_merge( $wp_query->query, array( 
				'meta_key' => 'sermon_date',
                'meta_value' => date("m/d/Y"),
                'meta_compare' => '>=',
                'orderby' => 'meta_value',
                'order' => 'DESC',
                //'posts_per_page' => '3',
				) );
				query_posts( $args );
				?>		
				
				<?php while ( have_posts() ) : the_post(); //Here's the archive output?>
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<?php $ugly_date = get_post_meta($post->ID, 'sermon_date', 'true');
						$displayDate = date('l, F j, Y', $ugly_date);?>
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
					</div>
				</div>		
	<?php endwhile; // End the loop. Whew. ?>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if (  $wp_query->max_num_pages > 1 ) : ?>
				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'thirdstyle' ) ); ?></div>
					<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'thirdstyle' ) ); ?></div>
				</div><!-- #nav-below -->
<?php endif; ?>

			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
