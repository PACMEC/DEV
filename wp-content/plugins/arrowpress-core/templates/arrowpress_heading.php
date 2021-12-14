<?php
$output = $item_delay = $image = $el_class = '';
extract(shortcode_atts(array(
	'heading_style' => 'style1',
    'big_heading_title' => '',
    'small_heading_title' => '',
    'content_desc' => '',
	'tag_heading' => '',
	'image' => '',
	'space_top_title' => '',
	'space_bottom_title' => '',
    'color_default' => '',
    'color_primary' => '',
    'color_secondary' => '',
    'color_small' => '',
    'color_desc' => '',
    'separator' => '',
    'line_w' => '',
	'radius_w' => '',
    'line2_h' => '',
    'line2_w' => '',
    'big_title_size' => '',
    'b_letter_space' => '',
    'sm_text_transform' => '',
    'big_text_transform' => '',
    'sm_letter_space' => '',
    'space_top_se' => '',
    'line_color' => '',
    'line_h' => '',
    'use_theme_fonts' => '',
    'google_fonts' => '',
    'big_google_fonts' => '',
    'small_title_size' => '',
    'big_use_theme_fonts' => '',
    'big_top_space' => '',
    'type_icon'	=> '',
    'ult_icon' => '',
    'icon_type'	=> '',
    'icon_linecons' => '',
    'icon_fontawesome' => '',
    'icon_pestrokefont' => '',
    'icon_openiconic' => '',
    'icon_typicons' => '',
    'icon_aprfont' => '',
    'icon_size' => '',
    'icon_entypo' => '',
    'item_delay' => '',
    'animation_type' => '',
    'animation_delay' => 500,
    'heading_align' => 'center',
    'image_i' => '',
    'icon_color' => '',
    'heading_pos' => 'left-pos',
    'heading_rotate' => '',
    'big_title_size_tab' => '',
    'big_title_size_tab_port' => '',
    'big_title_size_mob_land' => '',
    'big_title_size_mob' => '',
    'big_title_lh' => '',
    'big_title_lh_tab' => '',
    'big_title_lh_tab_port' => '',
    'big_title_lh_mob_land' => '',
    'big_title_lh_mob' => '', 
    'space_top_desc' => '',   
    'el_class' => '',
    'heading_bg_img' => '',
    'desc_size' => '',
    'desc_lh' => '',
    'desc_w' => '',
    'sm_title_size_tab' => '',
    'sm_title_size_tab_port' => '',
    'sm_title_size_mob_land' => '',
    'sm_title_size_mob' => '',    
    'hide_in_mobile_port' => '',
    'hide_in_tablet_port' => '',
    'hide_in_tablet_land' => '',
    'hide_in_mobile_land' => '',
    'sm_title_lh' => '',
    'desc_weight' => '',
    'css' => ''
), $atts));
$icon_class = '';
if($ult_icon!=''){
 $icon_class= $ult_icon;
}else{
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
}

$img_icon= wp_get_attachment_url($image_i);
$bgImage = wp_get_attachment_url($image);
$background_img = wp_get_attachment_url($heading_bg_img);
$color_1 = '';
$color_2 = '';
if(($color_default != '') || ($color_primary != '')){
	$color_1 .= 'style="color:'. esc_attr( $color_default ) . ';';
	if($big_title_size){
		$color_1 .= 'font-size:'. esc_attr( $big_title_size ) . 'px';
	}
	$color_1 .= '"';
	$color_2 .= 'style="color:'. esc_attr( $color_primary ) . '"';
}else{
	if($big_title_size){
		$color_1 .= 'style="font-size:'. esc_attr( $big_title_size ) . '"';
	}	
}
$space_1 ='';
$space_2 = '';
if(($space_top_title != '') || ($space_bottom_title != '') || ($b_letter_space !='')){
	if($space_top_title !=''){
		$space_1 .= 'style="padding-bottom: '. esc_attr( $space_top_title ) . 'px"';
	}
	if($space_bottom_title !='' || $b_letter_space !=''){
		if($space_bottom_title !=''){
			if($b_letter_space !=''){
				$space_2 .= 'style="padding-bottom: '. esc_attr( $space_bottom_title ) . 'px;';
				$space_2 .= 'letter-spacing:'.$b_letter_space.'px;"';
			}else{
				$space_2 .= 'style="padding-bottom: '. esc_attr( $space_bottom_title ) . 'px;"';
			}
		}
		if($b_letter_space !='' && $space_bottom_title ==''){
			$space_2 .= 'style="letter-spacing:'.$b_letter_space.'px;"';
		}
	}elseif($b_letter_space!=''){
		$space_2.= 'style="letter-spacing:'.$b_letter_space.'px"';
	}
}
$layout_heading='';
if($heading_style == 'style1'){
    $layout_heading = ' heading-1';
}else if($heading_style == 'style3'){
	$layout_heading = ' heading-3';
}else if($heading_style == 'style4'){
	$layout_heading = ' heading-4';
}else if($heading_style == 'style5'){
	$layout_heading = ' heading-5';
}else if($heading_style == 'style6'){
	$layout_heading = ' heading-6';
}else{
	$layout_heading = ' heading-2';
}
if($b_letter_space !=''){
	$layout_heading .= ' heading_letter_spacing ';
}
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), 'arrowpress_heading', $atts );
$el_class = arrowpress_shortcode_extract_class( $el_class );
if(isset($hide_in_mobile_port) && $hide_in_mobile_port=='yes'){
	$el_class .= ' hide_in_mobile';
}
if(isset($hide_in_mobile_land) && $hide_in_mobile_land=='yes'){
	$el_class .= ' hide_in_mobile_land';
}
if(isset($hide_in_tablet_land) && $hide_in_tablet_land=='yes'){
	$el_class .= ' hide_in_tablet_land';
}
if(isset($hide_in_tablet_port) && $hide_in_tablet_port=='yes'){
	$el_class .= ' hide_in_tablet_port';
}
$output = '<div class="arrowpress_container ' . $el_class . '"';
$output .= '>';
$se_style ='';
if($radius_w !='' || $line_w !='' || $line_h !='' || $line_color !='' || $space_top_se !=''){
	$se_style = 'style="';
	if(isset($line_w) && $line_w !=''){
		$se_style .= ' width:'.$line_w.'px;';
	}
	if(isset($radius_w) && $radius_w !=''){
		$se_style .= ' border-radius:'.$radius_w.'px;';
	}
	if(isset($line_h) && $line_h !=''){
		$se_style .= ' height:'.$line_h.'px;';
	}
	if(isset($line_color) && $line_color !=''){
		$se_style .= ' background:'.$line_color.';';
	}
	if(isset($space_top_se) && $space_top_se !=''){
		$se_style .= ' margin-top:'.$space_top_se.'px;';
	}
	$se_style .= '"';
}
$lines_style ='';
if($line2_w !='' || $line2_h !='' || $line_color !=''){
	$lines_style = 'style="';
	if(isset($line2_w) && $line2_w !=''){
		$lines_style .= ' width:'.$line2_w.'px;';
	}
	if(isset($line2_h) && $line2_h !=''){
		$lines_style .= ' height:'.$line2_h.'px;';
	}
	if(isset($line_color) && $line_color !=''){
		$lines_style .= ' background:'.$line_color.';';
	}
	$lines_style .= '"';
}
// ============Style inline for description text=============//
	$des_style = '';
	$des_final_style ='';
	$des_style_array[] ='';
if($space_top_desc !='' || $color_desc !='' || $desc_size !='' || $desc_lh !='' || $desc_size !='' || $desc_w !=''){

	if($space_top_desc !=''){
		$des_style_array[] .= 'padding-top:'. esc_attr( $space_top_desc ) . 'px';
	}
	if($color_desc!=''){
		$des_style_array[] .= 'color:'. esc_attr( $color_desc ) . '';
	}
	if($desc_size!=''){
		$des_style_array[] .= 'font-size:'. esc_attr( $desc_size ) . 'px';
	}	
	if($desc_lh!=''){
		$des_style_array[] .= 'line-height:'. esc_attr( $desc_lh ) . 'px';
	}
	if($desc_weight !=''){
		$des_style_array[] .= 'font-weight:'. esc_attr( $desc_size ) ;
	}	
	if($desc_w!=''){
		$des_style_array[] .= 'width:'. esc_attr( $desc_w ) . '%';
	}
	if (is_array($des_style_array) || is_object($des_style_array)){ 
		foreach( $des_style_array as $attribute ){ 
			if($attribute!=''){          
			    $des_style .= $attribute.'; ';   
			}    
		}
	} 	
	if($des_style !=''){
    	$des_final_style = 'style="'.$des_style.'"';
    }	
}
//=============Style Inline for small title==================//
	$sm_inline_style = ''; 
	$small_title_style_inline='';	
    $text_font_data = arrowpress_getFontsData( $google_fonts );    
    // Build the inline style
    $sm_inline_style .= arrowpress_googleFontsStyles( $text_font_data );   
                 
    // Enqueue the right font  
    if ( ( isset( $use_theme_fonts ) && 'yes' === $use_theme_fonts ) ) { 
        arrowpress_enqueueGoogleFonts( $text_font_data );
    }
	$small_title_style1[] ='';
	if($color_small !=''){
		$small_title_style1[] .= 'color:'. esc_attr( $color_small ) . '';
	}   
	if($small_title_size!=''){
		$small_title_style1[] .= 'font-size:'. esc_attr( $small_title_size ) . 'px';
	}
	if($sm_text_transform !='default' && $sm_text_transform !=''){
		$small_title_style1[] .= 'text-transform:'. esc_attr( $sm_text_transform ) . '';
	}
	if($sm_letter_space!=''){
		$small_title_style1[] .= 'letter-spacing:'. esc_attr( $sm_letter_space ) . 'px';
	}
	if($sm_title_lh!=''){
		$small_title_style1[] .= 'line-height:'. esc_attr( $sm_title_lh ) . 'px';
	}		
	if (is_array($small_title_style1) || is_object($small_title_style1)){ 
		foreach( $small_title_style1 as $attribute ){ 
			if($attribute!=''){          
			    $sm_inline_style .= $attribute.'; ';   
			}    
		}
	} 
    if($sm_inline_style !=''){
    	$small_title_style_inline = 'style="'.$sm_inline_style.'"';
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
	if($color_default !=''){
		$big_title_style[] .= 'color:'. esc_attr( $color_default ) . '';
	}   
	if($big_text_transform !='default' && $big_text_transform !=''){
		$big_title_style[] .= 'text-transform:'. esc_attr( $big_text_transform ) . '';
	}
	if($big_title_size!=''){
		$big_title_style[] .= 'font-size:'. esc_attr( $big_title_size ) . 'px';
	}
	if($big_title_lh!=''){
		$big_title_style[] .= 'line-height:'. esc_attr( $big_title_lh ) . 'px';
	}	
	if($big_top_space!=''){
		$big_title_style[] .= 'margin-top:'. esc_attr( $big_top_space ) . 'px';
	}
	if (count($big_title_style) > 0 && (is_array($big_title_style) || is_object($big_title_style))){ 
		foreach( $big_title_style as $attribute ){ 
 			if($attribute!=''){
				$big_inline_style .= $attribute.'; ';  				
			}         		     
		}
	} 
    if($big_inline_style !=''){
    	$big_title_style_inline = 'style=" '.$big_inline_style.'" ';
    }    
//=============Style Inline for Icon==================//
	$icon_inline_style = ''; 
	$icon_style_final='';	
	$icon_style[] ='';
	if($icon_size !=''){
		$icon_style[] .= 'font-size:'. esc_attr( $icon_size ) . 'px';
	}   
	if($icon_color!=''){
		$icon_style[] .= 'color:'. esc_attr( $icon_color ) . '';
	}
	if (count($icon_style) > 0 && (is_array($icon_style) || is_object($icon_style))){ 
		foreach( $icon_style as $attribute ){ 
 			if($attribute!=''){
				$icon_inline_style .= $attribute.'; ';  				
			}         		     
		}
	} 
    if($icon_inline_style !=''){
    	$icon_style_final = 'style=" '.$icon_inline_style.'" ';
    }  
//============Big title responsive==================//   
    $id =  'arrowpress_heading-'.wp_rand();
	$tag ='';
	if($tag_heading!=''){
		$tag = str_replace("tag-","",$tag_heading);
	}   
	$args = array();
	if($big_title_size_tab !='' || $big_title_lh_tab !='' || $big_title_size_tab_port !='' || $big_title_lh_tab_port !='' || $big_title_size_mob_land !='' || $big_title_lh_mob_land !='' || $big_title_size_mob !='' || $big_title_lh_mob !=''){
			// FIX: set old font size before implementing responsive param
		$big_title_size_tab = $big_title_size_tab !='' ? $big_title_size_tab : $big_title_size;
		$big_title_size_tab_port = $big_title_size_tab_port !='' ? $big_title_size_tab_port : $big_title_size;
		$big_title_size_mob_land = $big_title_size_mob_land !='' ? $big_title_size_mob_land : $big_title_size;
		$big_title_size_mob = $big_title_size_mob !='' ? $big_title_size_mob : $big_title_size_mob;
		// $big_title_lh_tab = $big_title_lh_tab !='' ? $big_title_lh_tab : $big_title_lh;
		// $big_title_lh_tab_port = $big_title_lh_tab_port !='' ? $big_title_lh_tab_port : $big_title_lh;
		// $big_title_lh_mob_land = $big_title_lh_mob_land !='' ? $big_title_lh_mob_land : $big_title_lh;
		// $big_title_lh_mob = $big_title_lh_mob !='' ? $big_title_lh_mob : $big_title_lh;    
	// responsive {main} heading styles
	
		$args = array(
			'target'		=>	$id,
			'csstarget' 	=>  $tag,
			'tablet' 	=> array(
				'font-size' 	=> $big_title_size_tab.'px',
				'line-height' 	=> $big_title_lh_tab.'px !important',
			),
			'tablet-port' 	=> array(
				'font-size' 	=> $big_title_size_tab_port.'px',
				'line-height' 	=> $big_title_lh_tab_port.'px !important',
			),	
			'mobile-land' 	=> array(
				'font-size' 	=> $big_title_size_mob_land.'px',
				'line-height' 	=> $big_title_lh_mob_land.'px !important',
			),	
			'mobile' 	=> array(
				'font-size' 	=> $big_title_size_mob.'px',
				'line-height' 	=> $big_title_lh_mob.'px !important',
			),				
		);
	}
	$main_heading_responsive = arrowpress_get_responsive_media_css($args); 
ob_start();
?>  
<div <?php echo $space_2.$main_heading_responsive; ?> class=" arrowpress-heading <?php echo esc_html($layout_heading).' '.esc_attr($id); ?> <?php if($item_delay == 'yes'){echo 'animated show-animate';} ?> <?php if($heading_align == 'center'){echo 'text-center';}?><?php if($heading_align == 'left'){echo 'text-left';}?><?php if($heading_align == 'right'){echo 'text-right';}?> <?php if ( $css_class ){echo $css_class;}?> <?php if($heading_pos == 'left-pos'){echo 'left-pos';}?><?php if($heading_pos == 'center-pos'){echo 'center-pos';}?><?php if($heading_rotate == 'yes'){echo ' disable_header_rotate';}?>" data-animation-delay="<?php echo $animation_delay; ?>" data-animation="<?php echo $animation_type; ?>">
	<?php if($image != ''):?>
		<?php if($bgImage && $heading_bg_img!=''):?>
			<img src="<?php echo esc_url($bgImage);?>" alt="img">
		<?php endif;?>	
	<?php endif;?>
	<?php if($background_img):?>
		<img class="bgheadingimg" src="<?php echo esc_url($background_img);?>" alt="img">
	<?php endif;?>	
	<?php if ( $heading_style != 'style3' ) :?>
		<?php if($small_heading_title != ''):?>
			<div <?php echo $space_1; ?> class="small-title">
				<p <?php echo $small_title_style_inline; ?>><?php echo $small_heading_title; ?></p>
			</div>
		<?php endif;?>
	<?php endif;?>
	<?php if ( $tag_heading != '' ) :?>
		<?php if($big_heading_title != ''):?>
			<?php
			switch ( $tag_heading ) {
				case 'tag-h1':
					?>
					<h1 class="title-heading" <?php echo $big_title_style_inline; ?>>
						<?php echo $big_heading_title;?>
					</h1>
					<?php    
					break;
				case 'tag-h2':
					?>
					<h2 class="title-heading" <?php echo $big_title_style_inline; ?>>
						<?php echo $big_heading_title;?>
					</h2>
					<?php 
					break;
				case 'tag-h3':
					?>
					<h3 class="title-heading" <?php echo $big_title_style_inline; ?>>
						<?php echo $big_heading_title;?>	
					</h3>
					<?php 
					break;
				case 'tag-h4':
					?>
					<h4 class="title-heading" <?php echo $big_title_style_inline; ?>>
						<?php echo $big_heading_title;?>
					</h4>
					<?php 
					break;
				case 'tag-h5':
					?>
					<h5 class="title-heading" <?php echo $big_title_style_inline; ?>>
						<?php echo $big_heading_title;?>
					</h5>
					<?php 
					break;
				case 'tag-h6':
					?>
					<h6 class="title-heading" <?php echo $big_title_style_inline; ?>>
						<?php echo $big_heading_title;?>
					</h6>
					<?php 
					break;
				default:
					?>
					<div <?php echo $big_title_style_inline; ?>>
						<?php echo $big_heading_title;?>
					</div>
					<?php 
					break;
			}
			endif;
			?>
	<?php endif;?>
	<?php if ( $heading_style == 'style3' ) :?>
		<?php if($small_heading_title != ''):?>
			<div <?php echo $space_1; ?> class="small-title">
				<p <?php echo $small_title_style_inline; ?>><?php echo $small_heading_title; ?></p>
			</div>
		<?php endif;?>
	<?php endif;?>
	<?php if($separator =='line'):?>
		<div class="line_separator" <?php echo $se_style;?>></div>
	<?php elseif($separator == 'icon'):?>
		<?php if($type_icon=='image_icon'):?>
			<?php if($img_icon!=''):?>
				<div class="img_separator" <?php echo $se_style;?>>
					<img src="<?php echo esc_url($img_icon);?>" alt="img">
				</div>
			<?php endif;?>	
		<?php else:?>
			<div class="icon_separator" <?php echo $se_style;?>><i class="<?php echo $icon_class;?> " <?php echo $icon_style_final;?>></i></div>
		<?php endif;?>	
	<?php elseif($separator =='line2'):?>
		<div class="two_lines_separator">
			<div class="line_separator" <?php echo $se_style;?>></div>
			<div class="second_line" <?php echo $lines_style;?>></div>
		</div>
	<?php endif;?>	
	<?php if($content_desc  != '') :?>
		<div  class="desc-title" <?php echo $des_final_style; ?>><?php echo $content_desc;?></div>
	<?php endif;?>		
</div>
<?php
$output .= ob_get_clean();
$output .= '</div>' . arrowpress_shortcode_end_block_comment( 'arrowpress_heading' ) . "\n";
echo $output;