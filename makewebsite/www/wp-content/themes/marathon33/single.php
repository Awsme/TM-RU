<?php get_header(); ?>	

<section class="section section-lessons">
	<div class="container">
		<div class="row">
			<div class="col-1-4">
				<aside class="sidebar-nav">
					<nav>
						<ol class="sidebar-nav_menu">
							<?php foreach (getListPosts() as $index => $post): 
								$customPost = get_post_custom($post->ID);
								$statusLesson = getInformationLesson($customPost); 
								$class = "";
								$url = get_permalink(get_the_ID());
								if($statusLesson["state_description"] === "feature"){
									$class = "locked ";
									$url = "";
								} ?>
								<li class="<?php echo  $class?>sidebar-nav_item">
									<a href="<?php echo $url ?>">Урок № <?php echo ($index + 1) ?></a>
								</li>
							<?php endforeach ?>	
						</ol>
					</nav>
				</aside>
			</div>

			<div class="col-2-4">
				<?php if ( have_posts() ): ?>
					<?php while (have_posts()) : the_post(); ?>
						<div class="lesson single"><?php the_content(); ?></div>		
				 	<?php endwhile; ?>
				<?php endif; ?> 

			</div>
		</div>
	</div><!-- .container-->
</section>



<?php get_footer(); ?>