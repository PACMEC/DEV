<?php
$output  = $number = $layout = $cat = $slug_name  = $items_desktop_large = $items_desktop = $items_tablets = $items_mobile =  $row_number = $el_class = '';
extract(shortcode_atts(array(
	'number' => 3,
	'layout' => 'grid_style_1',
	'filter_align' => 'left',
	'image_type' => 1,
	'post_display_type' => '',
	'cat' => '',
	'sticky_post' => '',
	'show_filter' => 'yes',
	'show_spacer' => 'yes',
	'show_viewmore' => '',
	'show_loadmore' => '',
	'space_top_btn' => '',
	'slug_name' => '',
	'order' => 'desc',
	'orderby' => 'date',
	'items_desktop_large' => 3,
	'items_desktop' => 3,
	'items_tablets' => 2,
	'items_mobile' => 1,
	'style' => '',
	'filter_color' => '',
	'blog_info_bg' => '',
	'color_default'=> '',
	'big_title_size'=> '',
	'big_title_lh'=> '',
	'big_use_theme_fonts'=> '',
	'big_google_fonts'=> '',
	'desc_size' => '',
	'desc_lh' => '',
	'desc_use_theme_fonts' => '',
	'desc_google_fonts' => '',
	'desc_color' => '',
	'info_color' => '',	
	'viewmore_text' => esc_html__('Go to blog','arrowpress-core'),
	'viewmore_link' => '',
	'blog_info_padding' => '',
	'blog_title_space_top' => '',
	'el_class' => '',
	'trim_length' => '',
	'more'=> '',
	'css' => ''
), $atts));

$layout_class = '';
global $wp_query;
if($layout == 'grid_style_2'){
	$layout_class = 'blog-grid-2';
}elseif($layout == 'grid_style_3'){
	$layout_class = ' blog-grid-3';
}elseif($layout == 'grid_style_4'){
	$layout_class = ' blog-grid-4';
}elseif($layout == "grid_style_1"){
	$layout_class = 'blog-grid-1';
}elseif($layout == 'grid_style_6'){
	$layout_class = ' blog-grid-6 load-item grid-isotope';
}elseif($layout == 'packery_style_2'){
	$layout_class = 'blog-entries-wrap grid-isotope blog-packery blog-packery-2';
}elseif($layout == 'packery_style_3'){
	$layout_class = 'blog-entries-wrap grid-isotope blog-packery blog-packery-3';
}elseif($layout == 'small_list'){
	$layout_class == 'blog_small_list';
}else{
	$layout_class = 'blog-list-1';
}
$show_spacer_class = '';
if($show_spacer == 'yes' && $layout !='packery_style_2'){
	$show_spacer_class = ' show-space';
}else{
	$show_spacer_class = ' no-space';
}
if($viewmore_link !=''){
	$viewmore_link = $viewmore_link;
}else{
	$viewmore_link = get_post_type_archive_link('post');
}
$space_1 ='';
if($space_top_btn != ''){
	$space_1 .= 'style="margin-top:'. esc_attr( $space_top_btn ) . 'px"';
}
if ( get_query_var('paged') ) {
	$paged = get_query_var('paged');
} elseif ( get_query_var('page') ) {
	$paged = get_query_var('page');
} else {
	$paged = 1;
}
$current_page = get_query_var('paged') ? intval(get_query_var('paged')) : 1;
if($sticky_post){
	$sticky = 'post__not_in';
}else{
	$sticky = 'post_in';
}
if ($post_display_type == 'featured') {
	$args = array(
		'paged' => $paged,
		'post_type' => 'post',
		'post_status' => 'publish',
		'meta_key' => 'special_box_check',
		'order' => $order,
		'orderby' => $orderby,
		'posts_per_page' => $number,
		'category_name' => $slug_name
	);
}
else if ($post_display_type == 'most-viewed'){
	$args = array(
		'paged' => $paged,
		'post_type' => 'post',
		'post_status' => 'publish',
		'ignore_sticky_posts' => 1,
		'meta_key' => 'post_views_count', 
		'orderby' => 'meta_value_num', 
		'order' => $order,
		$sticky => get_option( 'sticky_posts' ),
		'posts_per_page' => $number,
		'category_name' => $slug_name
	);    
}else {
	$args = array(
		'paged' => $paged,
		'post_type' => 'post',
		'post_status' => 'publish',
		'ignore_sticky_posts' => 1,
		$sticky => get_option( 'sticky_posts' ),
		'order' => $order,
		'orderby' => $orderby,
		'posts_per_page' => $number,
		'category_name' => $slug_name
	);
}
$catArray = explode(',', $cat);
if ($cat){
	$args['tax_query'] = array(
		array(
			'taxonomy' => 'category',
			'field'    => 'term_id',
			'terms'    => $catArray,
		),
	);
}
$taxonomy_names = get_object_taxonomies( 'post' );
if ( is_array( $taxonomy_names ) && count( $taxonomy_names ) > 0  && in_array( 'category', $taxonomy_names ) ) {
	if($cat){
		$terms = get_terms( 'category', array(
			'parent' => $cat, 
			'hide_empty'        => true,
		) );
	}else{
		$terms = get_terms(array(
			'taxonomy' => 'category',
			'hide_empty' => true,
			'parent'  => 0, 
			'hierarchical' => false, 
		) );          
	}
}
query_posts($args);
global $wp_query;

$items_desktop_large_no= 12/$items_desktop_large;
$items_desktop_no= 12/$items_desktop;
$items_tablets_no = 12/$items_tablets;
$items_mobile_no = 12/$items_mobile;

/**
 * Style inline
 **/
$filter_style_inline ='';
if($filter_color != ''){
	$filter_style_inline = 'style ="color: '.esc_attr($filter_color).'"';
}


$blog = new WP_Query($args);
//=============Style Inline for Blog title==================//
$big_inline_style = ''; 
$big_title_style_inline='';	
$big_text_font_data = arrowpress_getFontsData( $big_google_fonts );    
    // Build the inline style
$big_inline_style .= arrowpress_googleFontsStyles( $big_text_font_data );   

    // Enqueue the right font  
if ( ( isset( $big_use_theme_fonts ) || 'yes' === $big_use_theme_fonts ) ) { 
	arrowpress_enqueueGoogleFonts( $big_text_font_data );
}
$big_title_style[] ='';
if($color_default !=''){
	$big_title_style[] .= 'color:'. esc_attr( $color_default ) . '';
}   
if($big_title_size!=''){
	$big_title_style[] .= 'font-size:'. esc_attr( $big_title_size ) . 'px';
}
if($big_title_lh!=''){
	$big_title_style[] .= 'line-height:'. esc_attr( $big_title_lh ) . 'px';
}	
if($blog_title_space_top!=''){
	$big_title_style[] .= 'padding-top:'. esc_attr( $blog_title_space_top ) . 'px';
	$big_title_style[] .= 'display: block';
}	

if (count($big_title_style) > 0 && (is_array($big_title_style) || is_object($big_title_style))){ 
	foreach( $big_title_style as $attribute ){ 
		if($attribute!=''){
			$big_inline_style .= $attribute.'; ';  				
		}         		     
	}
} 
if($big_inline_style !=''){
	$big_title_style_inline = 'style="'.$big_inline_style.'"';
} 
//=============Style Inline for Blog descrition ==================//
$desc_inline_style = ''; 
$desc_title_style_inline='';	
$desc_text_font_data = arrowpress_getFontsData( $desc_google_fonts );    
    // Build the inline style
$desc_inline_style .= arrowpress_googleFontsStyles( $desc_text_font_data );   

    // Enqueue the right font  
if ( ( isset( $desc_use_theme_fonts ) || 'yes' === $desc_use_theme_fonts ) ) { 
	arrowpress_enqueueGoogleFonts( $desc_text_font_data );
}
$desc_title_style[] ='';
if($desc_color !=''){
	$desc_title_style[] .= 'color:'. esc_attr( $desc_color ) . '';
}   
if($desc_size!=''){
	$desc_title_style[] .= 'font-size:'. esc_attr( $desc_size ) . 'px';
}
if($desc_lh!=''){
	$desc_title_style[] .= 'line-height:'. esc_attr( $desc_lh ) . 'px';
}	

if (count($desc_title_style) > 0 && (is_array($desc_title_style) || is_object($desc_title_style))){ 
	foreach( $desc_title_style as $attribute ){ 
		if($attribute!=''){
			$desc_inline_style .= $attribute.'; ';  				
		}         		     
	}
} 
if($desc_inline_style !=''){
	$desc_title_style_inline = 'style="'.$desc_inline_style.'"';
} 
// ============Style inline for blog info=============//
$info_style = '';
$info_final_style ='';
$info_style_array[] ='';
if($info_color!=''){
	$info_style_array[] .= 'color:'. esc_attr( $info_color ) . '';
}	
if (is_array($info_style_array) || is_object($info_style_array)){ 
	foreach( $info_style_array as $attribute ){ 
		if($attribute!=''){          
			$info_style .= $attribute.'; ';   
		}    
	}
} 	
if($info_style !=''){
	$info_final_style = 'style="'.$info_style.'"';
}
// ============Style inline for blog post info=============//
$bloginfo_style = '';
$bloginfo_final_style ='';
$bloginfo_style_array[] ='';
if($blog_info_bg!=''){
	$bloginfo_style_array[] .= 'background:'. esc_attr( $blog_info_bg ) . '';
}	
if($blog_info_padding!=''){
	$bloginfo_style_array[] .= 'padding-left:'. esc_attr( $blog_info_padding ) . 'px';
	$bloginfo_style_array[] .= 'padding-right:'. esc_attr( $blog_info_padding ) . 'px';
}
if (is_array($bloginfo_style_array) || is_object($bloginfo_style_array)){ 
	foreach( $bloginfo_style_array as $attribute ){ 
		if($attribute!=''){          
			$bloginfo_style .= $attribute.'; ';   
		}    
	}
} 	
if($bloginfo_style !=''){
	$bloginfo_final_style = 'style="'.$bloginfo_style.'"';
}
if ($blog->have_posts()) {
	$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), 'arrowpress_blog', $atts );
	$el_class = arrowpress_shortcode_extract_class($el_class);
	if($layout== 'small_list'){
		$el_class .= ' widget_post_blog ';
	}
	$output = '<div class="blog-container' .esc_html($el_class) . '"';        
	$output .= '>';
	$i = 1;
	ob_start();
	if($layout=='small_list'){
		$container_wrapper = 'ul';
	}else{
		$container_wrapper = 'div';
	}
	?>
	<<?php echo $container_wrapper; ?> class="row <?php echo esc_html($layout_class); ?> <?php echo esc_attr($show_spacer_class);?> ">
	<?php while ($blog->have_posts()) : $blog->the_post(); ?>
		<?php 
		$apr_post_term_arr = get_the_terms( get_the_ID(), 'category' );
		$apr_post_term_filters = '';
		$apr_post_term_names = '';

		if (is_array($apr_post_term_arr) || is_object($apr_post_term_arr)){
			foreach ( $apr_post_term_arr as $post_term ) {

				$apr_post_term_filters .= $post_term->slug . ' ';
				$apr_post_term_names .= $post_term->name . ', ';
				if($post_term->parent!=0){
					$parent_term = get_term( $post_term->parent,'category' );
					$apr_post_term_filters .= $parent_term->slug . ' ';

				}
			}
		}

		$apr_post_term_filters = trim( $apr_post_term_filters );
		$apr_post_term_names = substr( $apr_post_term_names, 0, -2 );
		$apr_author = get_the_author_link();					
		?>
		<?php if($layout == "grid_style_1" || $layout == "grid_style_2" || $layout == "grid_style_3" || $layout == "grid_style_6" ): ?>
			<div class="<?php echo esc_html($apr_post_term_filters); ?> col-lg-<?php echo esc_html($items_desktop_large_no);?> col-md-<?php echo esc_html($items_desktop_no) ?> col-sm-<?php echo esc_html($items_tablets_no) ?> col-xs-<?php echo esc_html($items_mobile_no) ?> grid-item">
				<div class="blog-content">
					<div class="blog-item">
						<?php if($layout=="grid_style_2"):?>
							<div class="blog-img">
								<?php
								$attachment_id = get_post_thumbnail_id();
								$image_grid = arrowpress_shortcode_get_attachment($attachment_id, 'cryptcio-blog-packery-32'); 
								?>
								<a href="<?php the_permalink(); ?>"><img width="<?php echo esc_attr($image_grid['width']) ?>" height="<?php echo esc_attr($image_grid['height']) ?>" src="<?php echo esc_url($image_grid['src']) ?>" alt="<?php echo esc_attr($image_grid['alt']) ?>" /></a>
							</div>
						<?php endif; ?>
						<?php if($layout=="grid_style_6"):?>
							<div class="blog-img">
								<?php
								$attachment_id = get_post_thumbnail_id();
								$image_grid = arrowpress_shortcode_get_attachment($attachment_id, 'cryptcio-blog-grid-6'); 
								?>
								<a href="<?php the_permalink(); ?>"><img width="<?php echo esc_attr($image_grid['width']) ?>" height="<?php echo esc_attr($image_grid['height']) ?>" src="<?php echo esc_url($image_grid['src']) ?>" alt="<?php echo esc_attr($image_grid['alt']) ?>" /></a>
							</div>
						<?php endif; ?>						
						<div class="blog-post-info <?php if (has_post_thumbnail() == '' && (get_post_format() != 'audio') && (get_post_format() != 'video')){ echo 'no-img';}?>" <?php echo $bloginfo_final_style;?>>
							<?php if ($layout == "grid_style_1"): ?>
								<div class="blog-info">
									<div class="info blog-date">
										<p class="date">
											<a <?php echo $info_final_style;?> href="<?php the_permalink(); ?>"> <?php echo get_the_date('F d, Y'); ?></a>
										</p>
									</div>
									<div class="info info-category">
										<?php echo get_the_term_list($blog->ID,'category', '', ', ' ); ?>
									</div>
								</div>
							<?php endif; ?>
							<?php if(get_the_title() != ''):?>
								<div class="blog-post-title">
									<?php if($layout=="grid_style_6"):?>
										<div class="info info-category">
											<?php echo get_the_term_list($blog->ID,'category', '', ', ' ); ?>
										</div>	
									<?php endif; ?>									
									<div class="post-name">
										<a <?php echo $big_title_style_inline;?> href="<?php the_permalink(); ?>"><?php the_title(); ?>       
										</a>                                     
									</div>					
								</div>
							<?php endif;?>

							<?php if ($layout == "grid_style_2"): ?>
								<div class="blog-info">
									<div class="info blog-date">
										<p class="date">
											<a <?php echo $info_final_style;?> href="<?php the_permalink(); ?>"> <?php echo get_the_date('d M Y'); ?></a>
										</p>
									</div>
									<div class="info author-name">
										<?php echo __(' by <a href="'.esc_url(get_edit_user_link( )).'" '.$info_final_style.'> <span>'.get_the_author().'</span></a>', 'arrowpress-core');?>
									</div>		

								</div>
							<?php endif;?>
							<?php if ($layout == "grid_style_6"): ?>
								<div class="blog-meta">
									<div class="info author-name">
										<?php echo __(' By: <a href="'.esc_url(get_edit_user_link( )).'" '.$info_final_style.'> <span>'.get_the_author().'</span></a>', 'arrowpress-core');?>
									</div>		
									<div class="info blog-date">
										<p class="date">
											<a <?php echo $info_final_style;?> href="<?php the_permalink(); ?>"><i class="fa fa-calendar"></i> <?php echo get_the_date('d, M'); ?></a>
										</p>
									</div>
									<div class="info info-comment"> 
										<i class="fa fa-comment"></i>
										<?php comments_popup_link(esc_html__('0', 'arrowpress-core'), esc_html__('1', 'arrowpress-core'), esc_html__('%', 'arrowpress-core')); ?>
									</div>
									<div class="info info-tag">
										<span><?php echo esc_html__('Tags:','arrowpress-core');?></span>
										<?php echo get_the_tag_list('',', ',''); ?>
									</div>
								</div>
							<?php endif; ?>							
							<?php if ($layout != "grid_style_2"): ?>
								<div class="blog_post_desc" <?php echo $desc_title_style_inline;?>>
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
						<?php endif; ?>
						<?php if ($layout == "grid_style_3" || $layout == "grid_style_1" ): ?>
							<div class="read-more">
								<a href="<?php the_permalink();?>"><i class="lnr lnr-arrow-right"></i></a>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php elseif($layout == 'packery_style_2'):?>
		<?php 
		$attachment_id = get_post_thumbnail_id();
		$apr_blog_grid = arrowpress_shortcode_get_attachment($attachment_id, 'cryptcio-blog-grid');
		$index_size1 = array('1','5','7','11','13','17','19','23','25','29','31','35','37','41','43','47','49');
		if(in_array($i, $index_size1)){
			$pakery_class = 'image_size1';
		}else{
			$pakery_class = 'image_size'; 
		} 							 
		?> 	
		<div class="<?php echo esc_html($apr_post_term_filters.' '.$pakery_class); ?> grid-item blog-content">
			<div class="blog-item">
				<div class="blog-img blog-media">
					<a href="<?php the_permalink(); ?>"><i class="fa fa-link" aria-hidden="true"></i><img width="<?php echo esc_attr($apr_blog_grid['width']) ?>" height="<?php echo esc_attr($apr_blog_grid['height']) ?>" src="<?php echo esc_url($apr_blog_grid['src']) ?>" alt="<?php echo esc_attr($apr_blog_grid['alt']) ?>" /></a>
				</div>
				<div class="blog-post-info box-shadow <?php if (has_post_thumbnail() == '' && (get_post_format() != 'audio') && (get_post_format() != 'video')){ echo 'no-img';}?>" <?php echo $bloginfo_final_style;?>>
					<?php 
					$cat_style = '';
					if(get_post_meta(get_the_ID(),'cat_bg',true)!=''){
						$cat_style = 'style="background:'.get_post_meta(get_the_ID(),'cat_bg',true).'"';
					}
					?>
					<div class="info info-category" <?php echo $cat_style;?>>
						<?php echo get_the_term_list($blog->ID,'category', '', ', ' ); ?>
					</div>									
					<?php if(get_the_title() != ''):?>
						<div class="blog-post-title">
							<div class="post-name">
								<a <?php echo $big_title_style_inline;?> href="<?php the_permalink(); ?>"><?php the_title(); ?>        
								</a>                                     
							</div>					
						</div>
					<?php endif;?>
					<div class="info blog-date">
						<p class="date">
							<a <?php echo $info_final_style;?> href="<?php the_permalink(); ?>"><?php echo get_the_date('d M Y'); ?></a>
						</p>
					</div>
					<div class="info author-name">
						<?php echo __(' by <a href="'.esc_url(get_edit_user_link( )).'" '.$info_final_style.'> <span>'.get_the_author().'</span></a>', 'arrowpress-core');?>
					</div>												
				</div>
			</div>
		</div>	
		<?php $i++; ?>
	<?php elseif($layout == 'packery_style_3'): ?>
		<?php 
		$attachment_id = get_post_thumbnail_id();
		$apr_blog_grid = arrowpress_shortcode_get_attachment($attachment_id, 'cryptcio-blog-packery-3');
		$apr_blog_grid_2 = arrowpress_shortcode_get_attachment($attachment_id, 'cryptcio-blog-packery-31');
		$apr_blog_grid_3 = arrowpress_shortcode_get_attachment($attachment_id, 'cryptcio-blog-packery-32');
		$index_size1 = array('1','8','9');
		$index_size2 = array('2','5','10');
		if(in_array($i, $index_size1)){
			$pakery_class = 'image_size1';
		}else if(in_array($i, $index_size2)){
			$pakery_class = 'image_size2';
		}else{
			$pakery_class = 'image_size'; 
		} 							 
		?> 	
		<div class="<?php echo esc_html($apr_post_term_filters.' '.$pakery_class); ?> grid-item blog-content">
			<div class="blog-item">
				<?php $gallery = get_post_meta(get_the_ID(), 'images_gallery', true); ?>
				<?php if ( get_post_format() == 'gallery') : ?>
					<div class="blog-gallery-zones blog-img"> 
						<?php
						foreach ($gallery as $key => $value) :
							$apr_blog_grid = wp_get_attachment_image_src($value, 'cryptcio-blog-packery-3');
							$apr_blog_grid_2 = wp_get_attachment_image_src($value, 'cryptcio-blog-packery-31');
							$apr_blog_grid_3 = wp_get_attachment_image_src($value, 'cryptcio-blog-packery-32');
							$alt = get_post_meta($value, '_wp_attachment_image_alt', true);
							echo '<div class="img-gallery">
							<div class="img">';
							if(in_array($i, $index_size1)){
								echo '<img src="' . esc_url($apr_blog_grid[0]) . '" alt="gallery-blog" class="gallery-img" />';
							}else if(in_array($i, $index_size2)){
								echo '<img src="' . esc_url($apr_blog_grid_2[0]) . '" alt="gallery-blog" class="gallery-img" />';
							}else{
								echo '<img src="' . esc_url($apr_blog_grid_3[0]) . '" alt="gallery-blog" class="gallery-img" />';
							}
							echo '</div>';
							echo '</div>';
						endforeach;
						?>
					</div>
				<?php else: ?>
					<?php if(in_array($i, $index_size1)): ?>
						<div class="blog-img blog-media">
							<a href="<?php the_permalink(); ?>"><img width="<?php echo esc_attr($apr_blog_grid['width']) ?>" height="<?php echo esc_attr($apr_blog_grid['height']) ?>" src="<?php echo esc_url($apr_blog_grid['src']) ?>" alt="<?php echo esc_attr($apr_blog_grid['alt']) ?>" /></a>
						</div>
					<?php elseif(in_array($i, $index_size2)):?>
						<div class="blog-img blog-media">
							<a href="<?php the_permalink(); ?>"><img width="<?php echo esc_attr($apr_blog_grid_2['width']) ?>" height="<?php echo esc_attr($apr_blog_grid_2['height']) ?>" src="<?php echo esc_url($apr_blog_grid_2['src']) ?>" alt="<?php echo esc_attr($apr_blog_grid_2['alt']) ?>" /></a>
						</div>
					<?php else: ?>
						<div class="blog-img blog-media">
							<a href="<?php the_permalink(); ?>"><img width="<?php echo esc_attr($apr_blog_grid_3['width']) ?>" height="<?php echo esc_attr($apr_blog_grid_3['height']) ?>" src="<?php echo esc_url($apr_blog_grid_3['src']) ?>" alt="<?php echo esc_attr($apr_blog_grid_3['alt']) ?>" /></a>
						</div>
					<?php endif;?>
				<?php endif;?>
				<div class="blog-post-info <?php if (has_post_thumbnail() == '' && (get_post_format() != 'audio') && (get_post_format() != 'video')){ echo 'no-img';}?>" <?php echo $bloginfo_final_style;?>>
					<?php 
					$cat_style = '';
					if(get_post_meta(get_the_ID(),'cat_bg',true)!=''){
						$cat_style = 'style="background:'.get_post_meta(get_the_ID(),'cat_bg',true).'"';
					}
					?>
					<div class="info info-category" <?php echo $cat_style;?>>
						<?php echo get_the_term_list($blog->ID,'category', '', ', ' ); ?>
					</div>									
					<?php if(get_the_title() != ''):?>
						<div class="blog-post-title">
							<div class="post-name">
								<a <?php echo $big_title_style_inline;?> href="<?php the_permalink(); ?>"><?php the_title(); ?>        
								</a>                                     
							</div>					
						</div>
					<?php endif;?>
					<div class="blog-meta">
						<div class="info author-name">
							<?php echo __(' By: <a href="'.esc_url(get_edit_user_link( )).'" '.$info_final_style.'> <span>'.get_the_author().'</span></a>', 'arrowpress-core');?>
						</div>		
						<div class="info blog-date">
							<p class="date">
								<a <?php echo $info_final_style;?> href="<?php the_permalink(); ?>"><i class="fa fa-calendar"></i> <?php echo get_the_date('d, M'); ?></a>
							</p>
						</div>
						<div class="info info-comment"> 
							<i class="fa fa-comment"></i>
							<?php comments_popup_link(esc_html__('0', 'arrowpress-core'), esc_html__('1', 'arrowpress-core'), esc_html__('%', 'arrowpress-core')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php $i++; ?>				
	<?php elseif($layout == 'small_list'): ?>
		<li class="blog-item">
			<?php if (has_post_thumbnail()): ?>
				<?php $blogImages = wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()) ); ?>
				<div class="blog-img">
					<a data-fancybox="post"  href="<?php echo esc_url($blogImages[0]);?>"><i class="fa fa-search" aria-hidden="true"></i><img width="98" height="98" alt="" src="<?php echo esc_url($blogImages[0]); ?>"></a>
				</div>
			<?php endif;?>			
			<div class="blog-post-info " <?php echo $bloginfo_final_style;?>>
				<div class="blog-time info">
					<p class="date"><a href="<?php the_permalink(); ?>"><?php echo get_the_date('d M, Y '); ?></a></p>
				</div>		
				<div class="post-name">
					<a <?php echo $big_title_style_inline;?> href="<?php the_permalink(); ?>"><?php the_title(); ?> </a>
				</div>			
			</div>
		</li>					
	<?php else: ?>
		<div class="<?php echo esc_html($apr_post_term_filters); ?> col-lg-<?php echo esc_html($items_desktop_large_no);?> col-md-<?php echo esc_html($items_desktop_no) ?> col-sm-<?php echo esc_html($items_tablets_no) ?> col-xs-<?php echo esc_html($items_mobile_no) ?>  list-item">
			<div class="blog-content">
				<div class="blog-item">
					<div class="blog-img">
						<?php
						$attachment_id = get_post_thumbnail_id();
						$image_list = arrowpress_shortcode_get_attachment($attachment_id, 'cryptcio-blog-list-shortcode'); 
						?>
						<a href="<?php the_permalink(); ?>"><img width="<?php echo esc_attr($image_list['width']) ?>" height="<?php echo esc_attr($image_list['height']) ?>" src="<?php echo esc_url($image_list['src']) ?>" alt="<?php echo esc_attr($image_list['alt']) ?>" /></a>
					</div>
					<div class="blog-post-info <?php if (has_post_thumbnail() == '' && (get_post_format() != 'audio') && (get_post_format() != 'video')){ echo 'no-img';}?>" <?php echo $bloginfo_final_style;?>>
						<?php if(get_the_title() != ''):?>
							<div class="blog-post-title">
								<div class="post-name">
									<a <?php echo $big_title_style_inline;?> href="<?php the_permalink(); ?>"><?php the_title(); ?>       
									</a>                                     
								</div>					
							</div>
						<?php endif;?>
						<div class="blog-info">
							<div class="info blog-date">
								<p class="date">
									<i class="fa fa-clock-o"></i> <a href="<?php the_permalink(); ?>"><?php echo date("d M Y"); ?></a>
								</p>
							</div>
							<div class="info info-comment"> 
								<i class="fa fa-comments"></i>
								<?php comments_popup_link(esc_html__('0 Comment', 'arrowpress-core'), esc_html__('1 Comment', 'arrowpress-core'), esc_html__('% Comments', 'arrowpress-core')); ?>
							</div>	
						</div>
						<div class="blog_post_desc" <?php echo $desc_title_style_inline;?>>
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
					<div class="read-more">
						<a href="<?php the_permalink();?>"><span class="lnr lnr-arrow-right"></span></a>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>
<?php endwhile; ?>
</<?php echo $container_wrapper; ?>>
<?php if($show_loadmore) :?>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">	
			<div class="load-more">
				<div class="load_more_button">
					<span data-paged="<?php echo esc_attr($current_page) ?>" data-totalpage="<?php echo esc_attr($wp_query->max_num_pages) ?>" rel="<?php echo esc_attr($wp_query->max_num_pages); ?>">
						<?php echo get_next_posts_link(__('Load more', 'arrowpress-core')); ?>
					</span>
				</div>
			</div>	
		</div>
	</div>
<?php endif;?> 
<?php if($show_viewmore) :?>
	<div class="btn-viewmore text-center">
		<a class="view_more btn btn-primary" href="<?php echo esc_url($viewmore_link); ?>"><?php echo esc_html($viewmore_text);?></a>
	</div>
<?php endif; ?>		 
<?php
$output .= ob_get_clean();

$output .= '</div>' . arrowpress_shortcode_end_block_comment( 'arrowpress_blog' ) . "\n";

echo $output;
wp_reset_query();
}
