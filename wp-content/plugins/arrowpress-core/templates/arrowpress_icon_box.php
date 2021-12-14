<?php
$output = $image =  $type_icon = $text_align = $layout = $big_title = $link = $el_class = $icon_size= $title_color = $icon_color = $text_color = $overlay_bg= $color_bg= $top= $left = $en_overlay= '';
extract(shortcode_atts(array(
	'layout' => 'icon_box_1',
	'image' => '',
	'text_align' => 'left',
	'big_title' => '',
	'number' => '',
	'title_color' => '',
	'icon_color' => '',
	'icon_bg' => '',
	'icon_border_color' => '',
	'text_color' => '',
	'title_content' => '',
	'use_theme_fonts' => '',
    'google_fonts' => '',
    'big_google_fonts' => '',
    'big_use_theme_fonts' => '',
    'big_title_size' => '',
	'type_icon'	=> '',
	'icon_type' => '',
    'icon_aprfont' => '',
    'icon_linecons' => '',
    'icon_fontawesome' => '',
    'icon_openiconic' => '',
    'icon_typicons' => '',
    'icon_pestrokefont' => '',
    'icon_themifyfont' => '',
    'icon_entypo' => '',
    'link' => '#',
    'icon_size' => '',
    'icon_style' => '',
    'icon_width' => '',
    'icon_height' => '',
    'item_delay' => '',
    'ult_icon'=> '',
    'animation_type' => '',
    'title_margin_top' => '',
    'title_margin_bottom' => '',
    'box_bg_hover' => '',
    'text_color_hover' => '',
    'animation_delay' => 500,
    'el_class' => ''
                ), $atts));
$href = vc_build_link($link);
$icon_class = "";
if($ult_icon!='' && $ult_icon!='none'){
 $icon_class= $ult_icon;
}else if (!empty($icon_pestrokefont)) {
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
}elseif (!empty($icon_themifyfont)) {
    $icon_class = $icon_themifyfont;
}

$bgImage = wp_get_attachment_url($image);
$el_class = arrowpress_shortcode_extract_class($el_class);
$id =  'arp_icon_box-'.wp_rand();
$output = '<div class="icon_box-container ' . esc_html($el_class).'" id="'.esc_html($id).'"';
$output .= '>';

/**
 * Style inline
 **/
	$text_style_i = '';
	if($text_color != ''){
		$text_style_i = 'style ="color: '.esc_attr($text_color).'"';
	}
	
	if($icon_size != ''){
		$data_i_size ='style="font-size:'.$icon_size.'px;';
		if($left){
			$data_i_size .='left:'.$left.'px;';
		}
		if($top){
			$data_i_size .= 'margin-top:'.$top.'px;';
		}
		$data_i_size .='"';
	}else{
		$data_i_size ='style="';
		if($left){
			$data_i_size .='left:'.$left.'px;';
		}
		if($top){
			$data_i_size .= 'margin-top:'.$top.'px;';
		}
		$data_i_size .='"';
	}
	
	$icon_inline_style =''; 
	$iconbox_style_inline =''; 
	$icon_style_i[] =''; 
	if($icon_color!=''){
		$icon_style_i[] .= 'color:'. esc_attr( $icon_color ) . '';
	}
	if($icon_bg!=''){
		$icon_style_i[] .= 'background-color:'. esc_attr( $icon_bg ) . '';
	}
	if($icon_width!=''){
		$icon_style_i[] .= 'width:'. esc_attr( $icon_width ) . 'px';
	}
	if($icon_height!=''){
		$icon_style_i[] .= 'height:'. esc_attr( $icon_height ) . 'px';
	}
	if($icon_border_color!=''){
		$icon_style_i[] .= 'border-color:'. esc_attr( $icon_border_color ) . '';
	}
	if (count($icon_style_i) > 0 && (is_array($icon_style_i) || is_object($icon_style_i))){ 
		foreach( $icon_style_i as $attribute ){ 
 			if($attribute!=''){
				$icon_inline_style .= $attribute.'; ';  				
			}         		     
		}
	} 
    if($icon_inline_style !=''){
    	$iconbox_style_inline = 'style="'.$icon_inline_style.'"';
    } 
// ICON STYLE CLASS
	$icon_style_class ='';
	if($icon_style =='style2'){
		$icon_style_class ='icon_style2';
	}elseif($icon_style =='style3'){
		$icon_style_class ='icon_style3';
	}elseif($icon_style =='style4'){
		$icon_style_class ='icon_style4';
	}else{
		$icon_style_class ='icon_style1';
	}
//=============Style Inline for BIG title==================//
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
	if($title_color!=''){
		$big_title_style[] .= 'color:'. esc_attr( $title_color ) . '';
	}
	if($big_title_size!=''){
		$big_title_style[] .= 'font-size:'. esc_attr( $big_title_size ) . 'px';
	}
	if($title_margin_top!=''){
		$big_title_style[] .= 'margin-top:'. esc_attr( $title_margin_top ) . 'px';
	}	
	if($title_margin_bottom!=''){
		$big_title_style[] .= 'margin-bottom:'. esc_attr( $title_margin_bottom ) . 'px';
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

// ==============Style for Box Container Hover ====================/
if(($box_bg_hover && $box_bg_hover!='') || ($text_color_hover && $text_color_hover!='')){?>
	<style type="text/css">
		<?php if($box_bg_hover && $box_bg_hover!=''):?>
			#<?php echo esc_html($id)?> .icon_box_content:hover {
			    background-color: <?php echo $box_bg_hover;?> !important;
			    border-color: <?php echo $box_bg_hover;?>  !important;
			}	
		<?php endif;?>
		<?php if($text_color_hover && $text_color_hover!=''):?>
			#<?php echo esc_html($id)?> .icon_box_content:hover .icon_box_title h3 , #<?php echo esc_html($id)?> .icon_box_content:hover .icon_box_desc p{
			    color: <?php echo $text_color_hover;?> !important;
			}
		<?php endif;?>		
			
	</style>
<?php }  
ob_start();

?> 
<?php if($layout == 'icon_box_2') :?>   
	<div class="icon_box_content type_2
		<?php if($text_align == 'center'){echo 'text-center';}?>
		<?php if($text_align == 'left'){echo 'text-left';}?>
		<?php if($text_align == 'right'){echo 'text-right';}?> 
		<?php if($item_delay == 'yes'){echo 'animated';} ?>" data-animation-delay="<?php echo $animation_delay; ?>" data-animation="<?php echo $animation_type; ?>">
		<?php if($type_icon=='image_icon'):?>
			<?php if($bgImage):?>
				<img src="<?php echo esc_url($bgImage);?>" alt="img">
			<?php endif;?>	
		<?php elseif($icon_class != ''):?>
			<div class="icon_box <?php echo esc_attr($icon_style_class);?>" <?php echo $iconbox_style_inline;?> >
				<i class="<?php echo $icon_class; ?>" <?php echo $data_i_size;?> ></i>					
			</div>
		<?php endif;?>	
		<div class="icon_box_title">
			<?php if($big_title != '') :?>
				<h3 <?php echo $big_title_style_inline;?>><?php echo esc_html($big_title); ?></h3>
			<?php endif; ?>
		</div>	
		<div class="icon_box_desc" <?php echo $text_style_i; ?>>
			<?php 
				echo wpb_js_remove_wpautop(do_shortcode($content), true);
			?>		
		</div>
	</div>
<?php else: ?>
	<div class="icon_box_content 
		<?php if($layout == 'icon_box_1'){echo "type_1";}?>
		<?php if($layout == 'icon_box_3'){echo "type_3";}?>
		<?php if($text_align == 'center'){echo 'text-center';}?>
		<?php if($text_align == 'left'){echo 'text-left';}?>
		<?php if($text_align == 'right'){echo 'text-right';}?> 
		<?php if($item_delay == 'yes'){echo 'animated';} ?>" data-animation-delay="<?php echo $animation_delay; ?>" data-animation="<?php echo $animation_type; ?>">
		<?php if($type_icon=='image_icon'):?>
			<?php if($bgImage):?>
				<img src="<?php echo esc_url($bgImage);?>" alt="img">
			<?php endif;?>	
		<?php elseif($icon_class != ''):?>
			<div class="icon_box <?php echo esc_attr($icon_style_class);?>" <?php echo $iconbox_style_inline;?> >
				<i class="<?php echo $icon_class; ?>" <?php echo $data_i_size;?> ></i>	
				<?php if($number != '') :?>		
					<span class="number"><?php echo esc_html($number); ?></span>
				<?php endif;?>		
			</div>
		<?php endif;?>	
		<div class="icon_box_content">
			<div class="icon_box_title">
				<?php if($big_title != '') :?>
					<h3 <?php echo $big_title_style_inline;?>><?php echo esc_html($big_title); ?></h3>
				<?php endif; ?>	
			</div>	
			<div <?php echo $text_style_i; ?> class="icon_box_desc">
				<?php 
					echo wpb_js_remove_wpautop(do_shortcode($content), true);
				?>		
			</div>
		</div>
	</div>
<?php endif; ?>
<?php
$output .= ob_get_clean();
$output .= '</div>' . arrowpress_shortcode_end_block_comment('arrowpress_icon_box') . "\n";

echo $output;


wp_reset_postdata();