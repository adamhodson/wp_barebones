<?php
/**
 * Single Industry template
 *
 * @package WordPress
 * @version 1.0
 */
get_header();
?>

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<div class="single-service">
	

	<div class="section light knowledge-is-power">
		<div class="container">
			<div class="row">
				text

			</div>
		</div>
	</div>
	
	
	<?php echo get_template_part('partials/newsletter') ?>
</div>
<?php endwhile; endif; ?>

<?php get_footer(); ?>
