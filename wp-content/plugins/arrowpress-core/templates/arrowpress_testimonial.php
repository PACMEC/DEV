<?php
$output = $css = $el_class = '';
extract(shortcode_atts(array(
	'layout' => 'layout1',
	'style' => 'style1',
    'name_author' => '',
    'job_author' => '',
    'description' => '',
    'desc_size' => '',
    'sign_img' => '',
    'job' => '',
    'image' => '',
    'image_signature' => '',
    'testimonial_align' => 'center',
    'name_color' => '',
    'desc_color' => '',
    'job_color' => '',
    'el_class' => '',
    'ratings' => '',
    'use_theme_fonts' => '',
    'google_fonts' => '',
    'desc_lh' => '',
    'bg_color' => '',
    'css' => '',
), $atts));
$bgImage = wp_get_attachment_url($image);
$signImage2 = wp_get_attachment_url($image_signature);
$el_class = arrowpress_shortcode_extract_class( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css, ' ' ), 'arrowpress_testimonial', $atts );
$output .= '<div class="testimonial-container ' . esc_html($el_class) . esc_html($css_class ). '">';
ob_start();
$wrap_class ='';
if($layout=='layout2'){
	$wrap_class .= ' item_testimonial2 ';
}elseif($layout=='layout3'){
	$wrap_class .= ' item_testimonial3 ';
}elseif($layout=='layout5'){
	$wrap_class .= ' item_testimonial5 ';
}elseif($layout=='layout4'){
	$wrap_class .= ' item_testimonial4 ';
	if($ratings=='hide_star'){
		$wrap_class .= ' no_display_star ';
	}
}else{
	$wrap_class .= ' item_testimonial ';
} 
$wrap_class .= ' text-'.$testimonial_align;
//====================Style inline for desc =================//
	$desc_inline_style = ''; 
	$desc_inline_style_final='';	
    $text_font_data = arrowpress_getFontsData( $google_fonts );    
    // Build the inline style
    $desc_inline_style .= arrowpress_googleFontsStyles( $text_font_data );   
                 
    // Enqueue the right font  
    if ( ( isset( $use_theme_fonts ) && 'yes' === $use_theme_fonts ) ) { 
        arrowpress_enqueueGoogleFonts( $text_font_data );
    }
	$desc_style_array[] ='';  
	if($desc_size!=''){
		$desc_style_array[] .= 'font-size:'. esc_attr( $desc_size ) . 'px';
	}
	if($desc_color!=''){
		$desc_style_array[] .= 'color:'. esc_attr( $desc_color ) . '';
	}	
	if($desc_lh!=''){
		$desc_style_array[] .= 'line-height:'. esc_attr( $desc_lh ) . 'px';
	}	
	if (is_array($desc_style_array) || is_object($desc_style_array)){ 
		foreach( $desc_style_array as $attribute ){ 
			if($attribute!=''){          
			    $desc_inline_style .= $attribute.'; ';   
			}    
		}
	} 
    if($desc_inline_style !=''){
    	$desc_inline_style_final = 'style="'.$desc_inline_style.'"';
    }
?>    
<div class="<?php echo esc_attr($wrap_class); ?>"> 
	<?php if($layout == 'layout1' || $layout == 'layout3' || $layout == 'layout4') :?>  
		<div class="caption_testimonial <?php if($style == 'style2'){echo 'style2';}?>"> 
			<?php if($bgImage!=''):?>
			<figure>
				<img class="img-tes" src="<?php echo $bgImage;?>" alt=""/>
			</figure>
			<?php endif;?>
			<p class="item-desc" <?php echo $desc_inline_style_final;?>
			><?php echo $description;?></p>
			<?php if($signImage2!=''):?>
			<figure>
				<img  class="img-signature" src="<?php echo $signImage2;?>" alt=""/>
			</figure>
			<?php endif;?>
			<?php if($layout == 'layout4') :?>
				<?php if($ratings!='hide_star'):?>
					<p class="ratings 
						<?php if($ratings == 'five_star'){echo 'five_star';} 
							if($ratings == 'four_star'){echo 'four_star';} 
							if($ratings == 'three_star'){echo 'three_star';} 
							if($ratings == 'two_star'){echo 'two_star';} 
							if($ratings == 'one_star'){echo 'one_star';} 
						 ?>">
					</p>				
				<?php endif;?>
			<?php endif;?>
			<div class="tes_info">
				<h6 class="tes_name" <?php if($name_color != ''):?>
		            style="color: <?php echo $name_color;?>"
		        <?php endif;?>
		    	><?php echo $name_author; ?></h6> 	
		    	<p class="tes_job"
				<?php if($job_color != ''):?>
		                style="color: <?php echo $job_color;?>"
		            <?php endif;?>
				><?php echo $job_author;?></p>		
			</div>	
		</div>
	<?php elseif($layout == 'layout2') :?>  
		<div class="caption_testimonial" <?php if($bg_color != ''):?>
		                style="background-color: <?php echo $bg_color;?>"
		            <?php endif;?>> 
			<?php if($bgImage!=''):?>
			<figure>
				<img class="img-tes" src="<?php echo $bgImage;?>" alt=""/>
			</figure>
			<?php endif;?>
			<p class="item-desc" <?php echo $desc_inline_style_final;?>
			>"<?php echo $description;?>"</p>
			<?php if($signImage2!=''):?>
			<figure>
				<img  class="img-signature" src="<?php echo $signImage2;?>" alt=""/>
			</figure>
			<?php endif;?>
			<div class="tes_info">
				<h6 class="tes_name" <?php if($name_color != ''):?>
		            style="color: <?php echo $name_color;?>"
		        <?php endif;?>
		    	><?php echo $name_author; ?></h6> 
		    	<?php if($job_author!=''):?>	
		    	<p class="tes_job"
				<?php if($job_color != ''):?>
		                style="color: <?php echo $job_color;?>"
		            <?php endif;?>
				><?php echo $job_author;?></p>	
				<?php endif;?>	
			</div>	
		</div>
	<?php elseif($layout == 'layout5') :?>  
		<?php if($bgImage!=''):?>
		<figure>
			<img class="img-tes" src="<?php echo $bgImage;?>" alt=""/>
		</figure>
		<?php endif;?>
		<div class="caption_testimonial" 
			<?php if($bg_color != ''):?>
		        style="background-color: <?php echo $bg_color;?>"
		     <?php endif;?> > 		
			<p class="item-desc" <?php echo $desc_inline_style_final;?>
			>"<?php echo $description;?>"</p>
			<div class="tes_info">
				<h6 class="tes_name" <?php if($name_color != ''):?>
		            style="color: <?php echo $name_color;?>"
		        <?php endif;?>
		    	><?php echo $name_author; ?></h6> 
			</div>				
		</div>
	<?php else:?>
		<div class="caption_testimonial"> 
			<p class="item-desc" <?php echo $desc_inline_style_final;?>
			><?php echo $description;?></p>
			<?php if($bgImage!=''):?>
			<figure>
				<img src="<?php echo $bgImage;?>" alt=""/>
			</figure>
			<?php endif;?>
			<div class="tes_name">
				<h4 <?php if($name_color != ''):?>
		            style="color: <?php echo $name_color;?>"
		        <?php endif;?>
		    	><?php echo $name_author; ?></h4> 			
			</div>					
		</div>
	<?php endif;?>
</div>
<?php
$output .= ob_get_clean();

$output .= '</div>' . arrowpress_shortcode_end_block_comment( 'arrowpress_testimonial' ) . "\n";
echo $output;


wp_reset_postdata();

