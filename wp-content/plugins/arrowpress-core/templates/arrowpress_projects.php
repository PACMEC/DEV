<?php
	$output = $title = $title_color = $link = $el_class ='';
	extract(shortcode_atts(array(
		'layout' => 'layout1',
		'number' => 6,
		'category_parent' => 0,
		'exclude_cat' => '',
		'title' => '',
		'learn_more' => 'Lean more',
		'trim_length' => '',
		'more'=> '',
		'show_filter' => '',
		'show_all_filter' => 'yes',
		'btn_text' => __('View our cases', 'arrowpress'),
		'btn_style' => 'btn_style_2',
		'title_color' => '',
		'text_align' => 'center',
		'bg_color_content' => '',
		'viewmore_link' => '',
		'title_color' => '',    
		'el_class' => ''
	), $atts));
	$taxonomy_names = get_object_taxonomies( 'project' );
	if ( is_array( $taxonomy_names ) && count( $taxonomy_names ) > 0  && in_array( 'project_cat', $taxonomy_names ) ) {
		$exclude_cat_a = explode(',', $exclude_cat);
		$terms = get_terms( 'project_cat', array(
			'hierarchical'  => false,
			'hide_empty'        => true,
			'parent' => $category_parent, 
			'order' => '',
			'exclude'    => $exclude_cat_a
			) 
		);  
	}
	if($viewmore_link !=''){
		$viewmore_link = $viewmore_link;
	}else{
		$viewmore_link = get_post_type_archive_link('project');
	}
	$btn_class = '';
	if($btn_style == 'btn_style_2'){
		$btn_class = ' btn-primary';
	}elseif($btn_style == 'btn_style_3'){
		$btn_class = ' btn-black';
	}elseif($btn_style == 'btn_style_5'){
		$btn_class = ' btn-highlight';
	}elseif($btn_style == 'btn_style_6'){
		$btn_class = ' btn-white';
	}elseif($btn_style == 'btn_style_4'){
		$btn_class = ' btn-circle';
	}else{
		$btn_class = ' btn-default';
	}

	$color_1 = '';
	$bg_1 = '';
	if(($title_color != '')){
		$color_1 .= 'style="color:'. esc_attr( $title_color ) . '"';
	}
	if(($bg_color_content != '')){
		$bg_1 .= 'style="background:'. esc_attr( $bg_color_content ) . '"';
	}
	if ( get_query_var('paged') ) {
		$paged = get_query_var('paged');
	} elseif ( get_query_var('page') ) {
		$paged = get_query_var('page');
	} else {
		$paged = 1;
	}
	$current_page = get_query_var('paged') ? intval(get_query_var('paged')) : 1;
	$args = array(
		'post_type' => 'project',
		'post_status' => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page' => $number,
		'paged' => $paged,
		'orderby' => 'date',
	);
	query_posts($args);
	$blog = new WP_Query($args);
	global $wp_query;
	$i= 1;
	$el_class = arrowpress_shortcode_extract_class($el_class);
	$output = '<div class="project-container' . $el_class . '"';
	$output .= '>';
	ob_start();
?>
	<?php if (have_posts()) : ?>
		<?php if (!empty($show_filter) && is_array( $terms ) && count( $terms ) > 0 ) : ?>
			<div class="fillter-project">
				<ul class="btn-filter-project">
					<?php if(!empty($show_all_filter)): ?>
						<li><a class="button-project active" data-filter="*"><?php echo esc_html__('All','arrowpress-core'); ?></a></li>
					<?php endif;?>
					<?php foreach ( $terms as $term ) : ?>
					<?php 
						$arrowpress_filter_active = get_term_meta($term->term_id,'arrowpress_core_checkbox');
						$arrowpress_cryptcio_icon = get_term_meta( $term->term_id, 'icon_text', true);
					?>
						<?php 
							$arrowpress_filter_active_class = '';
							$arrowpress_btn_active_class = '';
							if(!empty($arrowpress_filter_active)){
								$arrowpress_filter_active_class = "active";
								$arrowpress_btn_active_class = "btn-active";
							}
						?>
						<li>
							<a class="button-project <?php echo esc_attr($arrowpress_filter_active_class);?>" data-filter=".<?php echo esc_attr($term->slug); ?>">
								<span><?php echo esc_html($term->name); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>                                         
			</div>
		<?php endif;?>
		<div class="load-item project-entries-wrap grid-isotope clearfix">
			<?php while (have_posts()) : the_post(); ?>
				<?php if($layout == 'layout2'): ?>
					<?php 
					$apr_post_term_arr = get_the_terms( get_the_ID(), 'project_cat' );
					$apr_post_term_filters = '';
					$apr_post_term_names = '';

					if (is_array($apr_post_term_arr) || is_object($apr_post_term_arr)){
						foreach ( $apr_post_term_arr as $post_term ) {

							$apr_post_term_filters .= $post_term->slug . ' ';
							$apr_post_term_names .= $post_term->name . ', ';
							if($post_term->parent!=0){
								$parent_term = get_term( $post_term->parent,'project_cat' );
								$apr_post_term_filters .= $parent_term->slug . ' ';

							}
						}
					}

					$apr_post_term_filters = trim( $apr_post_term_filters );
					$apr_post_term_names = substr( $apr_post_term_names, 0, -2 );
					$apr_author = get_the_author_link();					
					?>
					<div class="grid-item project-2 <?php echo esc_html($apr_post_term_filters); ?>">
						<div class="project-content">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="project-image">
									<?php 
										$attachment_id = get_post_thumbnail_id();
										$cryptcio_project_grid = cryptcio_get_attachment($attachment_id, 'cryptcio-project-grid4'); 
									?>
									<a href="<?php the_permalink(); ?>"><img class="lazyignore" width="<?php echo esc_attr($cryptcio_project_grid['width']) ?>" height="<?php echo esc_attr($cryptcio_project_grid['height']) ?>" src="<?php echo esc_url($cryptcio_project_grid['src']) ?>" alt="<?php echo esc_html__('project','cryptcio') ?>" /></a>
								</div>  
							<?php endif;?>
							<div class="project-post">     
								<?php if(get_the_title() != ''):?>
									<div class="blog-post-title">
										<div class="post-name">
											<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>                                   
										</div>                  
									</div>
								<?php endif;?>
								<div class="blog_post_desc">
									<?php 
									if(isset($trim_length) && $trim_length!=''):
										if (get_post_meta(get_the_ID(),'highlight',true) != ""){
											echo wp_trim_words(get_post_meta(get_the_ID(),'highlight',true), $trim_length, $more );
										}else{
											echo wp_trim_words( get_the_content(), $trim_length, $more );
										}
										else:?>										
										<?php 
										if (get_post_meta(get_the_ID(),'highlight',true) != "") : ?>                            
										<?php echo get_post_meta(get_the_ID(),'highlight',true);?>
									<?php else:?>
										<?php
										echo '<div class="entry-content">';
										the_excerpt();
										wp_link_pages( array(
											'before'      => '<div class="page-links"><span class="page-links-title">' . esc_html__( 'Pages:', 'arrowpress-core' ) . '</span>',
											'after'       => '</div>',
											'link_before' => '<span>',
											'link_after'  => '</span>',
											'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'arrowpress-core' ) . ' </span>%',
											'separator'   => '<span class="screen-reader-text">, </span>',
										) );
										echo '</div>';
										?>
									<?php endif; ?>
								<?php endif; ?>
								</div>
								<div class="lean-more">
									<a href="<?php the_permalink(); ?>"><?php echo esc_html($learn_more); ?> <i class="fa fa-angle-right"></i></a>
								</div>
							</div>
						</div>
					</div>
				<?php else: ?>
					<?php
						$index_size1 = array('6','7','10');
						$index_size2 = array('3','9');
						if(in_array($i, $index_size1)){
							$pakery_class = 'image_size1';
						}elseif(in_array($i, $index_size2)){
							$pakery_class = 'image_size2';
						}else{
							$pakery_class = 'image_size';
						} 
					?>
					<?php if($i == 3): ?>
						<div class="grid-item title-project <?php echo esc_attr($pakery_class); ?>">
							<div class="title-content">
								<p><?php echo $title; ?></p>
								<div class="view-more">
									<a class="btn <?php echo esc_attr($btn_class);?>" href="<?php echo esc_url($viewmore_link); ?>"><?php echo esc_html($btn_text);?></a>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<div class="grid-item <?php echo esc_attr($pakery_class); ?>">
						<div class="project-content">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="project-img">
									<?php 
										$attachment_id = get_post_thumbnail_id();
										$cryptcio_project_grid = cryptcio_get_attachment($attachment_id, 'cryptcio-project-grid'); 
										$cryptcio_project_grid1 = cryptcio_get_attachment($attachment_id, 'cryptcio-project-grid2'); 
										$cryptcio_project_grid2 = cryptcio_get_attachment($attachment_id, 'cryptcio-project-grid3'); 
									?>
									<?php if(in_array($i, $index_size1) ): ?>
										<a href="<?php the_permalink(); ?>"><img class="lazyignore" width="<?php echo esc_attr($cryptcio_project_grid1['width']) ?>" height="<?php echo esc_attr($cryptcio_project_grid1['height']) ?>" src="<?php echo esc_url($cryptcio_project_grid1['src']) ?>" alt="<?php echo esc_html__('project','cryptcio') ?>" /></a>
									<?php elseif(in_array($i, $index_size2) ): ?>
										<a href="<?php the_permalink(); ?>"><img class="lazyignore" width="<?php echo esc_attr($cryptcio_project_grid2['width']) ?>" height="<?php echo esc_attr($cryptcio_project_grid2['height']) ?>" src="<?php echo esc_url($cryptcio_project_grid2['src']) ?>" alt="<?php echo esc_html__('project','cryptcio') ?>" /></a>
									<?php else: ?>
										<a href="<?php the_permalink(); ?>"><img class="lazyignore" width="<?php echo esc_attr($cryptcio_project_grid['width']) ?>" height="<?php echo esc_attr($cryptcio_project_grid['height']) ?>" src="<?php echo esc_url($cryptcio_project_grid['src']) ?>" alt="<?php echo esc_html__('project','cryptcio') ?>" /></a>
									<?php endif;?>
								</div>  
							<?php endif;?>
							<div class="project-post-info">     
								<div class="link-plus"><a href="<?php the_permalink(); ?>"><?php echo esc_html__('+','cryptcio') ?></a></div>
								<?php if(get_the_title() != ''):?>
									<div class="blog-post-title">
										<div class="post-name">
											<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>                                   
										</div>                  
									</div>
								<?php endif;?>
								<div class="info-category">
									<?php echo get_the_term_list($blog->ID,'project_cat', '', ', ' ); ?>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
				
			<?php $i++; endwhile; ?>
		</div> 
	<?php endif;?>
<?php
$output .= ob_get_clean();
$output .= '</div>' . arrowpress_shortcode_end_block_comment('arrowpress_projects') . "\n";

echo $output;


wp_reset_postdata();
