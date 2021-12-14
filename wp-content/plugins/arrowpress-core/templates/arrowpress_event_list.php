<?php
$output = $el_class = $layout = '';
extract(shortcode_atts(array(
	'layout' => 'timeline_vertical',
	'attach_images' => 'layout1',
	'items_desktop_large' => 7,
    'items_desktop' => 4,
    'items_tablets' => 2,
    'items_mobile' => 1,
	// 'show_dot' => '',
 //    'show_nav' => 'yes',
 //    'auto_play' => 'yes',
 //    'thumb_image' => '',
 //    'pagingInfo' => '',
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
$timeline_class = '';
if($layout == 'timeline_vertical'){
	$timeline_class = 'timeline-wrap-vertical ';
}else{
	$timeline_class = 'timeline-wrap-container ';
}
$image_no = 1;
$el_class = arrowpress_shortcode_extract_class($el_class);
$id = 'apr_event_list'.wp_rand();
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), 'arrowpress_event_list', $atts );
ob_start();
?>
	<?php 
	$output = '<div class="'. esc_html($timeline_class).'' . esc_html($el_class) . '"';
	$output .= '>';
	?>
	<?php if($layout == 'timeline_vertical'): ?>
		<div class="timeline-vertical clearfix">
			<?php echo do_shortcode($content); ?>	
		</div>
	<?php else: ?>
		<div class="timeline-wrapper clearfix" id="<?php echo $id;?>">
			<div class="timeline-content-day">
				<div class="timeline-line"></div>
				<div class="slider">
					<?php echo do_shortcode($content); ?>	
				</div>
			</div>
		</div>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {
				$('<?php echo '#'.$id.' .slider';?>').on('init', function(event, slick){

				});				
				$('<?php echo '#'.$id.' .slider';?>').slick({
					<?php if(is_rtl()):?>
						rtl: true,
					<?php endif; ?>
					nextArrow: '<button class="btn-prev"><i class="fa fa-angle-left"></i></button>',
					prevArrow: '<button class="btn-next"><i class="fa fa-angle-right"></i></button>',
					// infinite: false,
					arrows: true,
					speed: 300,
					infinite: false,
					// centerMode: true,
					centerPadding: '0px',
					slidesToShow: <?php echo $items_desktop_large; ?>,
					slidesToScroll: 1,
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
				  ],
				});	
				
				$('.timeline-wrapper').each(function(){
					var $currentSlide = $('<?php echo '#'.$id.' .slider';?>').slick('slickCurrentSlide');
					var $this = $(this);
					var $fillingLine = $this.find('.filling-line');
				    var slide_width = 0, slide_width2 = 0;
				    var disable = true,
				    	styles = {};	
				    var $left = 'left';
				    <?php if(is_rtl()):?>
				    	$left = 'right';
				    <?php endif; ?>					
						
					$(this).find('.event_item-container.active').addClass('current-view');
					   $(this).find('.event_item-container').click(function () {
					            var t = $(this).attr('data-slick-index');

					              	$this.find('.event_item-container').each(function () {
					                    if($(this).attr('data-slick-index') <= t){
					                        $(this).addClass('active');
					                    }else{
					                        $(this).removeClass('active');
					                    }
					                });
					    });	
					});
					$(this).find('.event_item-container.active').trigger('click');
					$(".timeline-wrapper .timeline-content-item > span").on("mouseenter mouseleave", function(e){
					  	$(".timeline-wrapper .event_item-container.current-view").removeClass("current-view");
					  	$(this).parent().parent().parent().parent().addClass("current-view");
					  	// $(this).parent().parent().parent().parent().trigger('click');
					});						
			
			});				
		</script>	
	<?php endif; ?>
<?php
$output .= ob_get_clean();
$output .= '</div>' . arrowpress_shortcode_end_block_comment('arrowpress_event_list') . "\n";

echo $output;


wp_reset_postdata();
