<?php
$output = $el_class = $layout = '';
extract(shortcode_atts(array(
	'layout' => 'layout1',
	'items_desktop_large' => 3,
    'items_desktop' => 3,
    'items_tablets' => 2,
    'items_mobile' => 1,
	'show_dot' => '',
    'show_nav' => 'yes',
    'auto_play' => 'yes',
    'thumb_image' => '',
    'item_to_scroll' => '',
    'el_class' => '',
	/*'type_icon'	=> '',
	'icon_type' => '',
    'icon_aprfont' => '',
    'icon_linecons' => '',
    'icon_fontawesome' => '',
    'icon_openiconic' => '',
    'icon_typicons' => '',
    'icon_pestrokefont' => '',
    'icon_entypo' => '',*/
    'css' => ''
), $atts));
/*$icon_class = "";
if (!empty($icon_pestrokefont)) {
    $icon_class = $icon_pestrokefont;
} elseif (!empty($icon_fontawesome)) {
    $icon_class = $icon_fontawesome;
} elseif (!empty($icon_openiconic)) {
    $icon_class = $icon_openiconic;
} elseif (!empty($icon_typicons)) {
    $icon_class = $icon_typicons;
} elseif (!empty($icon_entypo)) {
    $icon_class = $icon_entypo;
} elseif (!empty($icon_linecons)) {
    $icon_class = $icon_linecons;
}elseif (!empty($icon_aprfont)) {
    $icon_class = $icon_aprfont;
}
*/
$slide_class = 'slider_wrap' . wp_rand();

$el_class = arrowpress_shortcode_extract_class($el_class);
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), 'arrowpress_slider_wrap', $atts );
ob_start();
?>
	<?php 
	$output = '<div class="slider-wrap-container ' . esc_html($el_class) . '"';
	$output .= '>';
	?>

		<div class="slick-default slider-for">
			<?php echo do_shortcode($content); ?>	
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {

				$('.slider-for').slick({
					<?php if(is_rtl()):?>
						rtl: true,
					<?php endif; ?>
					<?php if($show_dot == 'yes'): ?>
						dots: true,
					<?php else: ?>
						dots: false,
					<?php endif; ?>
					<?php if($show_nav == 'yes'): ?>
						arrows: true,
					<?php else: ?>
						arrows: false,
					<?php endif; ?>
					<?php if($auto_play == 'yes'): ?>
						autoplay: true,
					<?php else: ?>
						autoplay: false,
					<?php endif; ?>
					nextArrow: '<button class="btn-prev"><i class="ion-ios-arrow-right"></i></button>',
					prevArrow: '<button class="btn-next"><i class="ion-ios-arrow-left"></i></button>',
					// infinite: false,
					speed: 300,
					centerMode: true,
					centerPadding: '0px',
					slidesToShow: <?php echo $items_desktop_large; ?>,
					slidesToScroll: <?php echo $item_to_scroll!=''?$item_to_scroll:1; ?>,
					responsive: [
					{
					  breakpoint: 1200,
					  settings: {
						slidesToShow: <?php echo $items_desktop; ?>,
						slidesToScroll: 1,
					  }
					},
					{
					  breakpoint: 768,
					  settings: {
						slidesToShow: <?php echo $items_tablets; ?>,
						slidesToScroll: 1
					  }
					},
					{
					  breakpoint: 481,
					  settings: {
						slidesToShow: <?php echo $items_mobile; ?>,
						slidesToScroll: 1 
					  }
					}
				  ]
				});
			});
		</script>	
<?php
$output .= ob_get_clean();
$output .= '</div>' . arrowpress_shortcode_end_block_comment('arrowpress_slider_wrap') . "\n";

echo $output;


wp_reset_postdata();
