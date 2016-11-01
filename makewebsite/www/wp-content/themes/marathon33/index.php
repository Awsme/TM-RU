<?php get_header(); ?>
<section class="section section-lessons">
	<div class="container">
		<div class="row">
			<div class="col-1">
				<ol class="lessons">
					<?php $index = 0;
					$query = 'post_type=post&orderby=id&order=asc&post_status=publish';
					$queryObject = new WP_Query($query);
					if ($queryObject->have_posts()): 
						while ( $queryObject->have_posts()): $queryObject->the_post(); ?>
							<?php $customPost = get_post_custom(get_the_ID()); ?>
							<?php $statusLesson = getInformationLesson($customPost); ?>

							<li class="lesson <?php echo $statusLesson["class_wrapper"] ?>">
								<h2 class="lesson_title">
									<!-- <span><?php echo getNumber($index + 1); ?></span> -->
									<?php echo get_the_title(); ?>
								</h2>

								<div class="lesson_content">
									<p>
										<?php echo $customPost["wpcf-excerpt-text"][0] ?>
									</p>
									<!-- <div class="descr"><?php echo $customPost["wpcf-excerpt-text"][0] ?></div> -->
									<span class="lesson_task_subtitle">Задание:</span>
									<ul class="lesson_task">
										<!-- <li></li> -->
										<?php echo $customPost["wpcf-task-lesson"][0]; ?>
									</ul>
									
									<?php  if($statusLesson["state_description"] === "past"):?>
										<a href="<?php echo get_permalink(get_the_ID()) ?>" class="btn btn_lesson">подробнее</a>
										
									<?php endif ?>
									<?php  if($statusLesson["state_description"] === "now"):?>
										<a href="<?php echo get_permalink(get_the_ID()) ?>" class="btn btn_lesson">подробнее</a>
										
											<span data-time="<?php echo $statusLesson["time_dif"] ?>" data-hour="<?php echo $statusLesson["time_completion_hour"] ?>" data-minut="<?php echo $statusLesson["time_completion_minut"] ?>">
												<?php echo $statusLesson["time_completion"] ?>
											</span>
										</div>
									<?php endif ?>
									<?php  if($statusLesson["state_description"] === "feature"):?>
										</span> 
									<?php endif ?>						
									
								</div><!-- .lesson_content-->
							</li><!-- .lesson-->

							<?php $index++; ?>
						<?php endwhile; ?> 
					<?php endif;  ?>
				</ol><!-- .lessons-->
			</div><!-- .col-1-->
		</div><!-- .row-->
	</div><!-- .container-->
</section>
<?php get_footer(); ?>

