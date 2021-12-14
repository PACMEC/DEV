<?php

$output = $orderby = $order = $items_desktop_large = $items_desktop = $items_tablets = $items_mobile = $el_class = '';
$per_page = 12;
$columns = 4;
extract( shortcode_atts( array(
    'layout' => 'grid',
    'product_style' => 'style-1',
    'shortcodes_layout' => 'recent_products',
    'slug_name' => '',
	'category_parent' => 0,
    'exclude_cat' => '',
    'items' => 2,
    'per_page' => 12,
    'columns' => 4,
    'orderby' => 'date',
    'order' => 'desc',
    'view_more' => 'yes',
    'show_spacing' => 'yes',
	'items_desktop_large' => 3,
    'items_desktop' => 3,
    'items_tablets' => 2,
    'items_mobile' => 1,
    'button_link' => '',
	'show_filter' => '',
	'show_all_filter' => 'yes',
    'show_compare' => 'yes',
    'show_wishlist' => 'yes',
    'show_quickview' => 'yes',
    'show_link' => 'yes',
    'show_price' => 'yes',
	'filter_color' => '',
    'filter_size' => '',
    'filter_border_color' => '',
    'filter_border_style' => '',
	'color_bg'=> '',
    'el_class' => ''
), $atts ) );

$taxonomy_names = get_object_taxonomies( 'product' );
if ( is_array( $taxonomy_names ) && count( $taxonomy_names ) > 0  && in_array( 'product_cat', $taxonomy_names ) ) {
	$exclude_cat_a = explode(',', $exclude_cat);
	$terms = get_terms( 'product_cat', array(
		'hierarchical'  => false,
		'hide_empty'        => true,
		'parent' => $category_parent, 
		'order' => '',
		'exclude'    => $exclude_cat_a
		) 
	);  
}

$shortcodes = '';
if($shortcodes_layout == 'recent_products'){
	$shortcodes = 'recent_products';
}elseif($shortcodes_layout == 'featured_products'){
	$shortcodes = 'featured_products';
}elseif($shortcodes_layout == 'best_selling_products'){
	$shortcodes = 'best_selling_products';
}elseif($shortcodes_layout == 'top_rated_products'){
	$shortcodes = 'top_rated_products';
}elseif($shortcodes_layout == 'related_products'){
	$shortcodes = 'related_products';
}else{
	$shortcodes = 'sale_products';
}
if($layout == 'slide'){
	$slide_class = ' product_slide';
}elseif($layout == 'list'){
	$slide_class = ' product_list style-1';
}elseif($layout == 'list-slider'){
	$slide_class = ' product_list product_list_slider';
}elseif($layout == 'packery'){
	$slide_class = ' product_grid product_packery';
}else{
	$slide_class = ' product_grid ';
}

$style_class = '';
$spacing_class = '';
if($show_spacing == 'yes'){
	$spacing_class = ' show-spacing ';
}else{
	$spacing_class = ' no-spacing ';
}
global $woocommerce_loop;

$woocommerce_loop['columns'] = $columns;
if($layout == 'packery'){
	$woocommerce_loop['layout'] = $layout;
	$woocommerce_loop['i'] = 1;
}
$woocommerce_loop['product_style'] = $product_style;
$woocommerce_loop['packery'] = $layout;
$woocommerce_loop['grid'] = $layout;
if($layout == 'list' || $layout == 'list-slider' || $layout == 'grid'){
	$woocommerce_loop['layout'] = $layout;
}
if($layout == 'grid'){
	if($product_style == 'style-1'){
		$style_class = ' style-1 ';
	}elseif($product_style=='style-3'){
		$style_class = ' style-3 product_packery';
	}else{
		$style_class = ' style-2 ';
	}
}
if(!$show_compare){
	$style_class .= ' apr_hide_compare ';
}
if(!$show_wishlist){
	$style_class .= ' apr_hide_wishlish ';
}
if(!$show_quickview){
	$style_class .= ' apr_hide_quickview ';
}
if(!$show_price){
	$style_class .= ' apr_hide_price ';
}
if($show_link){
	$style_class .= ' apr_show_link ';
}else{
	$style_class .= ' apr_hide_link ';
}

$bg_color = 'transparent';
if($color_bg !=''){
	$bg_color = $color_bg;
}
	
$el_class = arrowpress_shortcode_extract_class( $el_class );
$id =  'apr_product-'.wp_rand();
$output = '<div style="background-color: '. $bg_color .'" id="'.esc_html($id).'" class="arrowpress-products ' .$shortcodes  . $slide_class . $style_class . $spacing_class . $el_class . '"';
$output .= '>';

if($button_link!=''){
	$btn_href = vc_build_link($button_link);
	$button_link = $btn_href['url'];
}else{
	$button_link = get_post_type_archive_link('product');
}
ob_start();
// ============ Style inline for filter text =============//
$filter_style = '';
$filter_final_style ='';
$filter_style_array[] ='';
if($filter_border_style !=''){
	$filter_style_array[] .= 'border-style:'. esc_attr( $filter_border_color ) . '';
}
if($filter_color!=''){
	$filter_style_array[] .= 'color:'. esc_attr( $filter_color ) . '';
}
if($filter_size!=''){
	$filter_style_array[] .= 'font-size:'. esc_attr( $filter_size ) . 'px';
}   
if($filter_border_color!=''){
	$filter_style_array[] .= 'border-color:'. esc_attr( $filter_border_color ) . '';
}   
if (is_array($filter_style_array) || is_object($filter_style_array)){ 
	foreach( $filter_style_array as $attribute ){ 
		if($attribute!=''){          
			$filter_style .= $attribute.'; ';   
		}    
	}
}   
if($filter_style !=''){
	$filter_final_style = 'style="'.$filter_style.'"';
}  
?>
	<?php if (!empty($show_filter) && is_array( $terms ) && count( $terms ) > 0 ) : ?>
		<div class="tabs-fillter">
			<ul class="nav nav-tabs btn-filter">
				<?php if(!empty($show_all_filter)): ?>
					<li><a <?php echo $filter_final_style;?> class="button active" data-filter="*"><?php echo esc_html__('All','arrowpress-core'); ?></a></li>
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
						<a <?php echo $filter_final_style;?> class="button <?php echo esc_attr($arrowpress_filter_active_class);?>" data-filter=".<?php echo esc_attr($term->slug); ?>">
							<i class="<?php echo $arrowpress_cryptcio_icon; ?>"></i>
							<span><?php echo esc_html($term->name); ?></span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>                                         
		</div>
	<?php endif;?>
	<?php
		echo do_shortcode('['.$shortcodes.' category="'.$slug_name.'" per_page="'.$per_page.'" columns="'.$columns.'" orderby="'.$orderby.'" order="'.$order.'"]');
	?>	
	<?php if($view_more) :?>
		<div class="btn-viewmore text-center">
			<a class="btn btn-primary" href="<?php echo esc_url($button_link); ?>"><?php echo esc_html__('View our store', 'arrowpress-core').'';?></a>
		</div>
	<?php endif;?>
	<?php if($layout == 'slide' || $layout == 'list-slider') : ?>
		<script type="text/javascript">
			jQuery(function ($) {
				$(document).ready(function(){
				  $('#<?php echo esc_js($id); ?> .product_types').slick({
					slidesToScroll: 1,	
					<?php if(is_rtl()):?>
						rtl: true,
					<?php endif; ?>
					infinite: true,
					rows: <?php echo $items; ?>,
					nextArrow: '<button class="btn-next slick-next"><i class="fa fa-angle-right"></i></button>',
	  				prevArrow: '<button class="btn-prev slick-prev"><i class="fa fa-angle-left"></i></button>',
					slidesToShow: <?php echo esc_js($items_desktop_large);?>,
					dots:false,
					arrows:true,
					responsive: [
						{
						  breakpoint: 1200,
						  settings: {
							slidesToShow: <?php echo esc_js($items_desktop);?>,
						  }
						},
						{
						  breakpoint: 992,
						  settings: {
							slidesToShow: <?php echo esc_js($items_tablets);?>,
						  }
						},
						{
						  breakpoint: 501,
						  settings: {
							slidesToShow: <?php echo esc_js($items_mobile);?>,
						  }
						},
					]
				  });
				});
			});
		</script>
	<?php endif; ?>
<?php
$output .= ob_get_clean();
$output .= '</div>' . arrowpress_shortcode_end_block_comment('arrowpress_product') . "\n";
echo $output;
wp_reset_postdata();