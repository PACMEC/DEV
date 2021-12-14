<?php
	$output = $title = $title_color = $link = $el_class ='';
	extract(shortcode_atts(array(
		'number' => 4,
		'orderby' => 'title',
		'order' => 'desc',
		'viewmore_link' => '',
		'viewmore_text' => __('View more events', 'arrowpress'),
		'btn_style' => 'btn_style_2',
		'show_viewmore' => '',
		'link' => '#',  
		'el_class' => ''
	), $atts));
	
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

	if ( get_query_var('paged') ) {
		$paged = get_query_var('paged');
	} elseif ( get_query_var('page') ) {
		$paged = get_query_var('page');
	} else {
		$paged = 1;
	}
	$current_page = get_query_var('paged') ? intval(get_query_var('paged')) : 1;
	$args = array(
		'post_type' => 'events',
		'post_status' => 'publish',
		'ignore_sticky_posts' => 1,
		'posts_per_page' => $number,
		'paged' => $paged,
		'order' => $order,
		'orderby' => $orderby,
	);
	query_posts($args);
	
	if($viewmore_link !=''){
		$viewmore_link = $viewmore_link;
	}else{
		$viewmore_link = get_post_type_archive_link('events');
	}
	global $wp_query;
	$el_class = arrowpress_shortcode_extract_class($el_class);
	$output = '<div class="events-container' . $el_class . '"';
	$output .= '>';
	ob_start();
?>
	<?php if (have_posts()) : ?>
		<div class="load-item events-entries-wrap clearfix">
			<?php get_template_part( 'templates/content', 'events-archive' ); ?>
		</div> 
		<?php if($show_viewmore) :?>
			<div class="viewmore-events text-center">
				<a class="btn <?php echo esc_attr($btn_class); ?>" href="<?php echo esc_url($viewmore_link); ?>"><?php echo esc_html($viewmore_text);?></a>
			</div>
		<?php endif; ?>		 
	<?php endif;?>
<?php
$output .= ob_get_clean();
$output .= '</div>' . arrowpress_shortcode_end_block_comment('arrowpress_events') . "\n";

echo $output;


wp_reset_postdata();
