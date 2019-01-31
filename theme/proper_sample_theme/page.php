<?php
/**
 * Single page template
 *
 * @package WordPress
 * @version 1.0
 */
get_header();
?>

<?php if (have_posts()) : while (have_posts()) : the_post();?>

<div id="page-wrapper"> 
    <div class="container-fluid">
    	<div class="white-box m-t-30">
    		<?php the_content(); ?>
    	</div>        
    </div>
</div>

<?php endwhile; endif; ?>

<?php get_footer();
