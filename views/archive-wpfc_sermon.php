<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header(); ?>

		<div id="container">
			<div id="content" role="main">

			<h1 class="page-title">Sermons</h1>
			<form action="<?php bloginfo('url'); ?>" method="get">
			<div> Sort By Series: 
			<?php
				$taxonomies = array('wpfc_sermon_series');
				$args = array('orderby'=>'name','hide_empty'=>true);
				$select = wpfc_get_series_dropdown($taxonomies, $args);

				$select = preg_replace("#<select([^>]*)>#", "<select$1 onchange='return this.form.submit()'>", $select);
				echo $select;
			?>
			<noscript><div><input type="submit" value="Näytä" /></div></noscript>
			</div></form>
			<form action="<?php bloginfo('url'); ?>" method="get">
			<div> Sort By Preacher: 
			<?php
				$taxonomies = array('wpfc_preacher');
				$args = array('orderby'=>'name','hide_empty'=>true);
				$select = wpfc_get_preacher_dropdown($taxonomies, $args);

				$select = preg_replace("#<select([^>]*)>#", "<select$1 onchange='return this.form.submit()'>", $select);
				echo $select;
			?>
			<noscript><div><input type="submit" value="Näytä" /></div></noscript>
			</div></form>
			<?php
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
