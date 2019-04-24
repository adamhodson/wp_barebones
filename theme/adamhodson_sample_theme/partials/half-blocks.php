<?php
	$i = 0;
	//$split_blocks = have_rows('split_blocks');	
	if( have_rows('split_blocks') ): while( have_rows('split_blocks') ): the_row(); 
?>
<div class="half-blocks <?php if($i % 2 == 0): ?>left-block<?php else: ?>right-block<?php endif; ?>">
	<div class="content">
		<div class="contents">
			<h2><?php echo get_sub_field('block_title'); ?></h2>
			<p>
				<?php echo get_sub_field('block_text'); ?>
			</p>
			<a href="<?php echo get_sub_field('block_link')['url']; ?>" class="learn-more"><?php echo get_sub_field('block_link_text'); ?></a>
		</div>

	</div>
	<div class="image">
		<?php 
			$block_img = get_image(get_sub_field('block_image'), 'big_square')['img'];
		?>
		<img src="<?php echo $block_img; ?>" alt="<?php echo get_sub_field('block_title'); ?>">
	</div>
</div>

<?php $i++; endwhile; endif; ?>