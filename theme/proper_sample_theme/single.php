<?php
/**
 * Single post template
 *
 * @package WordPress
 * @version 1.0
 */
get_header();
?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
 
	<div class="single-article">

	
		<div class="section light">
			<div class="container">

				<div class="col-12">

					<div class="text-contain">
						<?php echo the_content(); ?>	
					</div>
			
				</div>	

			</div>
		</div>


	</div>

<?php endwhile; endif; ?>

<?php get_footer();