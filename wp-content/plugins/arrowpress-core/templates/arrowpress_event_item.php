<?php
$output = $css = $el_class = '';
extract(shortcode_atts(array(
	'layout_item' => 'item_vertical',
	'vertical_image' => '',
	'title' => '',
	'date' => '',
	'link'=> '',
    'desc' => '',
    'el_class' => '',
    'css' => '',
), $atts));
$href = vc_build_link($link);
$href['url'] = $href['url'] !=''? $href['url'] : '#';
$bgImage = wp_get_attachment_url($vertical_image);
$el_class = arrowpress_shortcode_extract_class( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), 'arrowpress_event_item', $atts );
$output .= '<div class="event_item-container ' . esc_html($el_class) . esc_html($css_class ). '">';
ob_start();
$wrap_class = ' item_event_item ';

// //====================Style inline for desc =================//
// 	$desc_inline_style = ''; 
// 	$desc_inline_style_final='';	
//     $text_font_data = arrowpress_getFontsData( $google_fonts );    
//     // Build the inline style
//     $desc_inline_style .= arrowpress_googleFontsStyles( $text_font_data );   
                 
//     // Enqueue the right font  
//     if ( ( isset( $use_theme_fonts ) && 'yes' === $use_theme_fonts ) ) { 
//         arrowpress_enqueueGoogleFonts( $text_font_data );
//     }
// 	$desc_style_array[] ='';  
// 	if($desc_size!=''){
// 		$desc_style_array[] .= 'font-size:'. esc_attr( $desc_size ) . 'px';
// 	}
// 	if($desc_color!=''){
// 		$desc_style_array[] .= 'color:'. esc_attr( $desc_color ) . '';
// 	}	
// 	if($desc_lh!=''){
// 		$desc_style_array[] .= 'line-height:'. esc_attr( $desc_lh ) . 'px';
// 	}	
// 	if (is_array($desc_style_array) || is_object($desc_style_array)){ 
// 		foreach( $desc_style_array as $attribute ){ 
// 			if($attribute!=''){          
// 			    $desc_inline_style .= $attribute.'; ';   
// 			}    
// 		}
// 	} 
//     if($desc_inline_style !=''){
//     	$desc_inline_style_final = 'style="'.$desc_inline_style.'"';
//     }
?>    
<div class="<?php echo esc_attr($wrap_class); ?>"> 
	<?php if($layout_item == 'item_vertical'): ?>
		<div class="item_vertical"> 
			<div class="ev-date-out">
				<p><?php echo $date; ?></p>
			</div>
			<div class="item_vertical_content">
				<?php if($bgImage != ''): ?>
					<div class="item_img">
						<img src="<?php echo $bgImage; ?>" alt="" />
					</div>
				<?php endif; ?>
				<?php if($title != ''): ?>
					<div class="item_title">
						<a href="<?php echo esc_url($href['url']);?>"><?php echo $title;?></a>
					</div>
				<?php endif; ?>
				<?php if($desc != ''): ?>
					<div class="item_desc">
						<p><?php echo $desc;?></p>
					</div>
				<?php endif; ?>
			</div>					
		</div>
	<?php else: ?>
        <div class="caption_event_item"> 
            <div class="timeline-content-item">
                <span class="time-dot"></span>
                <div class="ev-date-out">
                    <p class="ev-date"><?php echo $date; ?></p>
                    <p><?php echo $title;?></p>
                </div>
                <div class="timeline-content-item-reveal">
                    <a href="<?php echo esc_url($href['url']);?>">
                        <h6><?php echo $title;?></h6>
                        <div class="ev-date"><?php echo $date; ?></div>
                    </a>
                </div>
            </div>                  
        </div>
	<?php endif; ?>
</div>
<?php
$output .= ob_get_clean();

$output .= '</div>';
echo $output;


wp_reset_postdata();

