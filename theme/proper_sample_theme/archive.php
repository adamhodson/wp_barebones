<?php
/**
 * Default archive template
 *
 * @package WordPress
 * @version 1.0
 */

get_header();
?>

<section class="page blog search-results category-page">
	<div class="blog-container">
		<div class="main-content-container">
			<div class="breadcrumb">
				<div class="bread-container">
					<a href="<?php the_permalink() ?>/blog"><i class="fa fa-angle-left"></i>Back To Blog</a>
				</div>
			</div>
			<div class="blog-title-bar search-container">
				<div class="search-title">
					<h2><?php $catName = single_cat_title(); ?></h2>
				</div>
				<div class="blog-post-search">
					<form action="<?php bloginfo('siteurl'); ?>" id="searchform" method="get">
						<input type="hidden" name="post_type" value="post" />
						<input type="search" id="s" name="s" placeholder="Enter keywords" required />
						<input type="submit" value="Search">
					</form>
				</div>
				<div class="click-bait"></div>
			</div>
			<div class="posts-container">
				<?php
					wp_reset_query();
					$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
					$wp_query = new WP_Query(['post_type' => 'post', 'posts_per_page' => 9, 'paged' => $paged]);
					if (have_posts()) : while (have_posts()) : the_post();?>
						<a href="<?php the_permalink(); ?>" class="post">
							<div class="post-featured-image">
								<?php the_post_thumbnail(); ?>
							</div>
							<div class="post-info-container">
								<p class="post-date"><?php the_time('F j Y'); ?></p>
								<p class="post-txt"><?= the_title(); ?></p>
							</div>
						</a>
				<?php endwhile; endif;?>
				<div class="post-pagination-container">
					<?= paginate_links( array(
								'base'               => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
								'format'             => '',
								'current'            => max( 1, get_query_var('paged') ),
								'total'              => $wp_query->max_num_pages,
								'prev_text'          => '<i class="fa fa-angle-left"></i>',
								'next_text'          => '<i class="fa fa-angle-right"></i>',
								'type'               => 'list',
								'end_size'           => 3,
								'mid_size'           => 3
							)
						); wp_reset_postdata(); wp_reset_query();
					?>
				</div>
			</div>
		</div>
		<div class="sidebar-container">
			<?php global $wp_query;
				$term = get_queried_object();
				$children = get_terms($term->taxonomy, array(
					'parent'    => $term->term_id,
					'hide_empty' => false
				) );
				if($children):?>
				<div class="content-container-sidebar list-container">
					<span class="side-title-container">
						<i class="fa fa-minus"></i><h3>Sub-Categories</h3>
					</span>
					<ul class="category-list">
						<?php
							$category = get_category( get_query_var( 'cat' ) );
							$cat_id = $category->cat_ID;
							$args = array(
								'child_of' => $cat_id,
								'orderby' => 'name',
								'order' => 'ASC'
							);
							$categories = get_categories($args);
							foreach($categories as $category) {
								if($category->name !== 'Featured Main' && $category->name !== 'Featured Sub') {
									echo '<li class="category-single"><a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View all posts in %s" ), $category->name ) . '" ' . '>' . $category->name.'</a></li> ';
								}
							}
						?>
					</ul>
				</div>
			<?php endif; ?>
			<div class="content-container-sidebar list-container">
                <span class="side-title-container">
                    <i class="fa fa-minus"></i><h3>Other Categories</h3>
                </span>
				<ul class="category-list">
					<?php
					$args = array(
						'orderby' => 'name',
						'order' => 'ASC'
					);
					$categories = get_categories($args);
					foreach($categories as $category) {
						if($category->name !== 'Featured Main' && $category->name !== 'Featured Sub') {
							echo '<li class="category-single"><a href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View all posts in %s" ), $category->name ) . '" ' . '>' . $category->name.'</a></li> ';
						}
					}
					?>
				</ul>
			</div>
			<?php if(get_field('sidebar_image')): ?>
                <div class="content-container-sidebar media-container">
                    <div class="content-media">
                        <?php if(get_field('sidebar_image_link')): ?>
                                <a href="<?= get_field('sidebar_image_link'); ?>"><img src="<?= get_field('sidebar_image')?>" alt="cta-img"/></a>
                            <?php else: ?>
                                <img src="<?= get_field('sidebar_image'); ?>" alt="cta-img"/>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
			<div class="sidebar-logo">
				<a href="<?= esc_url( home_url( '/' ) ); ?>">
					<img src="<?= of_get_option('site_logo_alternate') ?>">
				</a>
			</div>
		</div>
	</div>
</section>

<?php get_footer();
