<?php
$output = $image = $btn_text = $text_align = $layout = $small_title = $big_title = $link = $el_class = $bg_hover_color = $icon_size= $title_color = $icon_color = $sm_title_color = $text_color = $overlay_bg= $color_bg= $top= $left = $en_overlay= $desc = $bottom_title_color = '';
extract(shortcode_atts(array(
	'layout' => 'banner_style_1',
	'image' => '',
	'text_align' => 'center',
	'small_title' => '',
	'big_title' => '',
	'title_color' => '',
	'bg_hover_color' => '',
	'bottom_title_color' => '',
	'bg1_color' => '',
	'title_size' => '',
	'sm_title_color' => '',
	'text_color' => '',
	'overlay_bg' => '',
	'color_bg' => '',
	'height_box' => '',
	'link_text' => '',
    'link' => '#',
    'item_delay' => '',
    'en_overlay' => '',
	'en_filter' => '',
    'animation_type' => '',
    'animation_delay' => 500,
    'el_class' => '',
    'css' => '',
                ), $atts));
$href = vc_build_link($link);
$href['url'] = $href['url'] !=''? $href['url'] : '#';
$bgImage = wp_get_attachment_url($image);
$class_image ='';
if($image == ''){
	$class_image = ' no-image ';
}
$layout_class = '';
if($layout == 'banner_style_2'){
	$layout_class = ' banner-type2';
}else{
	$layout_class = ' banner-type1';
}

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), 'arrowpress_banner', $atts );
$el_class = arrowpress_shortcode_extract_class($el_class);
$id =  'arp_banner-'.wp_rand();
$output = '<div class="banner-container ' .$css_class. ' '.esc_html($el_class).'" id="'.esc_html($id).'"';
$output .= '>';

/**
 * Style inline
 **/
	$title_style_inline[] = '';
	$small_title_style_inline = $title_style = $bg_color_style = $bottom_title_style_inline = $sm_style_i = $text_style_i = $icon_style_i = $bg_style_4='';
	if($title_color != ''){
		$title_style_inline[] .= 'color:'. esc_attr( $title_color ) . '';
	}
	if($title_size != ''){
		$title_style_inline[] .= 'font-size:'. esc_attr( $title_size ) . 'px';
	}
	if($sm_title_color != ''){
		$sm_style_i = 'style ="color: '.esc_attr($sm_title_color).'"';
	}
	if($text_color != ''){
		$text_style_i = 'style ="color: '.esc_attr($text_color).'"';
	}
	if($bg1_color != ''){
		$bg_color_style = 'style ="background-color: '.esc_attr($bg1_color).'"';
	}
	if (is_array($title_style_inline) || is_object($title_style_inline)){ 
		foreach( $title_style_inline as $attribute ){ 
			if($attribute!=''){          
			    $title_style .= $attribute.'; ';   
			}    
		}
	} 
	if($title_style !=''){
    	$small_title_style_inline = 'style="'.$title_style.'"';
    }	
	
	$main_style_inline[] = '';
	$small_main_style_inline = $main_style = '';
	if($bg1_color != ''){
		$main_style_inline[] .= 'background-color:'. esc_attr( $bg1_color ) . '';
	}
	if($height_box != ''){
		$main_style_inline[] .= 'height:'. esc_attr( $height_box ) . 'px';
	}
	if (is_array($main_style_inline) || is_object($main_style_inline)){ 
		foreach( $main_style_inline as $attribute ){ 
			if($attribute!=''){          
			    $main_style .= $attribute.'; ';   
			}    
		}
	} 
	if($main_style !=''){
    	$small_main_style_inline = 'style="'.$main_style.'"';
    }
	$allowed_html = array(
		'a' => array(
			'href' => array(),
			'title' => array()
		),
		'br' => array(),
		'span' => array(),
		'strong' => array(),
	);
ob_start();

?> 
<?php if($layout == 'banner_style_2') :?>   
	<div class="banner-content <?php echo esc_html($layout_class); ?>
		<?php if($text_align == 'center'){echo 'text-center';}?>
		<?php if($text_align == 'left'){echo 'text-left';}?>
		<?php if($text_align == 'right'){echo 'text-right';}?> 
		<?php if($en_overlay == 'yes'){echo 'en_overlay';} ?>
		<?php if($item_delay == 'yes'){echo 'animated';} ?>" data-animation-delay="<?php echo $animation_delay; ?>" data-animation="<?php echo $animation_type; ?>" 
		style="background-image: url('<?php echo $bgImage; ?>');">
		<?php if($overlay_bg != ''):?>
			<style type="text/css" scoped>
				#<?php echo $id;?> .en_overlay:before{
					background-color: <?php echo esc_attr($overlay_bg);?>;
				}
			</style>
		<?php endif;?>	
		<div class="banner-mid">
			<div class="banner-title">	
				<?php if($big_title != '') :?>
					<a <?php echo $small_title_style_inline;?> href="<?php echo $href['url']; ?>">
						<?php echo wp_kses($big_title, $allowed_html); ?>
					</a>
				<?php endif; ?>
			</div>	
		</div>
	</div>
<?php else :?>   
	<div  <?php echo $small_main_style_inline;?> class="banner-content <?php echo esc_html($layout_class); ?>
		<?php if($text_align == 'center'){echo 'text-center';}?>
		<?php if($text_align == 'left'){echo 'text-left';}?>
		<?php if($text_align == 'right'){echo 'text-right';}?> 
		<?php if($en_filter != 'yes'){echo 'en_filterbw';} ?>
		<?php if($en_overlay == 'yes'){echo 'en_overlay';} ?>
		<?php if($item_delay == 'yes'){echo 'animated';} ?>" data-animation-delay="<?php echo $animation_delay; ?>" data-animation="<?php echo $animation_type; ?>" >
		<?php if($bg_hover_color != ''):?>
			<style type="text/css" scoped>
				#<?php echo $id;?> .en_overlay:hover:before{
					background: <?php echo esc_attr($bg_hover_color);?>;
				}
			</style>
		<?php endif;?>	
		<div class="banner-info" style="background-image: url('<?php echo $bgImage; ?>');">
			<div class="banner-mid">
				<div class="banner-title">
					<a class="fancybox-thumb btn-plus" data-fancybox-group="fancybox-thumb"  href="<?php echo esc_url($bgImage);?>" title="">	
					</a>		
					<?php if($big_title != '') :?>
						<h2 <?php echo $small_title_style_inline;?>><?php echo wp_kses($big_title, $allowed_html); ?></h2>
					<?php endif; ?>
					<?php if($small_title != '') :?>
						<h3 <?php echo $sm_style_i;?>><?php echo esc_html($small_title); ?></h3>
					<?php endif; ?>
				</div>	
				<?php if($link_text != '') :?>
					<div class="link-text">
						<a href="<?php echo esc_url($href['url']); ?>">
							<i class="<?php echo esc_html($link_text); ?>"></i>
						</a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	
<?php endif; ?>
<?php
$output .= ob_get_clean();
$output .= '</div>' . arrowpress_shortcode_end_block_comment('arrowpress_banner') . "\n";

echo $output;


wp_reset_postdata();