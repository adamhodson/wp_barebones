<div class="section light industries-served">
		<div class="container">

			<div class="title center">
				<h2 class="section-title-l">
					<?php echo get_field('first_section_title'); ?>
				</h2>
			</div>
			
			<div class="med-contain">
				<div class="text center topmargin-3 bottompad-4">
					<p class='big'>
						<?php echo get_field('first_section_content'); ?>
					</p>
				</div>	
			</div>
			

			<div class="row topmargin-5 flex flex-wrap services-grid">
				
				<?php
					wp_reset_query(); 
					$industries = new WP_Query( array(
					    'post_type' => 'industries',
					    'posts_per_page' => 12
					  )
					);
					while ( $industries->have_posts() ) : $industries->the_post(); 
				?>
				<div class="col-12 col-6-m col-4-l fade-in-content opaque">
					<a href="<?php echo get_the_permalink(); ?>" class="service-card">
						<div class="image">
							<?php 						
								$service_img = get_image(get_field('grid_image'), 'small_square')['img'];							
							?>
							<img src="<?php echo $service_img; ?>" alt="<?php echo get_the_title(); ?>">
						</div>
						<div class="content">
							<h3><?php echo get_the_title(); ?></h3>
							<p class="bottommargin-3">
								<?php echo get_field('grid_description'); ?>
							</p>
		
							<div class="buttons center">
								<span class="btn primary">Learn More</span>	
							</div>							
							
						</div>
					</a>
				</div>
				<?php endwhile; wp_reset_query(); ?>

				
			</div>
		</div>
	</div>