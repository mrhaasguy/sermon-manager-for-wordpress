<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Ten
 * @since Twenty Ten 1.0
 */

get_header(); ?>

		<div id="container">
			<div id="content" role="main">

<?php while ( have_posts() ) : the_post(); ?>

	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php $ugly_date = get_post_meta($post->ID, 'sermon_date', 'true');
				$displayDate = date('l, F j, Y', $ugly_date);?>
			<div class="wpfc_date"><?php echo $displayDate; ?> (<?php echo get_post_meta($post->ID, 'service_type', true); ?>)</div>
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'twentyten' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>

			<div class="entry-content">
				<div id="wpfc_sermon">		  
					<p>	<?php 
							if (get_post_meta($post->ID, 'bible_passage', true)) {
								echo get_post_meta($post->ID, 'bible_passage', true); ?> |								
						<?php } 
							echo the_terms( $post->ID, 'wpfc_preacher', '', ', ', ' ' ); 
							echo the_terms( $post->ID, 'wpfc_sermon_series', '<br />Series: ', ', ', '' ); 
						?>
					</p>
					<?php if (get_post_meta($post->ID, 'sermon_video', true)) { ?>
								<div class="wpfc_sermon-video"><?php echo get_post_meta($post->ID, 'sermon_video', true); ?></div>								
							<?php } else { ?>
								<div id="wpfc_sermon-audio">
									<div id='mediaspace'>This text will be replaced</div>
									<script type='text/javascript'>
									jwplayer('mediaspace').setup({
									'flashplayer': '<?php echo ''.WPFC_SERMONS . '/js/player.swf'?>',
									'file': '<?php echo get_post_meta($post->ID, 'sermon_audio', true); ?>',
									'controlbar': 'bottom',
									'width': '400',
									'height': '24'
									});
									</script>
								</div>
							<?php } ?>
							<p><?php echo get_post_meta($post->ID, 'sermon_description', true); ?></p>
							<div id="wpfc-attachments">
								<?php
									$args = array(
										'post_type' => 'attachment',
										'numberposts' => -1,
										'post_status' => null,
										'post_parent' => $post->ID,
									);
									$attachments = get_posts($args);
									if ($attachments) {
										echo '<p><strong>Additional Files:</strong>';
										foreach ($attachments as $attachment) {
										echo '<br/><a target="_blank" href="'.wp_get_attachment_url($attachment->ID).'">';
										echo $attachment->post_title;
										echo '</a>';
									}
									echo '</p>';
									}
								?>
							</div>
					
				</div>


			</div><!-- .entry-content -->

			<div class="entry-utility">
					<span class="tag-links">
						<?php echo the_terms( $post->ID, 'wpfc_sermon_topics', '<br />Topics: ', ', ', ' ' ); ?>
					</span>
					<span class="meta-sep">|</span>
				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'twentyten' ), __( '1 Comment', 'twentyten' ), __( '% Comments', 'twentyten' ) ); ?></span>
				<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="meta-sep">|</span> <span class="edit-link">', '</span>' ); ?>
			</div><!-- .entry-utility -->
		</div><!-- #post-## -->

		<?php comments_template( '', true ); ?>


<?php endwhile; // End the loop. Whew. ?>

<?php /* Display navigation to next/previous pages when applicable */ ?>
<?php if (  $wp_query->max_num_pages > 1 ) : ?>
				<div id="nav-below" class="navigation">
					<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentyten' ) ); ?></div>
					<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentyten' ) ); ?></div>
				</div><!-- #nav-below -->
<?php endif; ?>

			
			
			</div><!-- #content -->
		</div><!-- #container -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
