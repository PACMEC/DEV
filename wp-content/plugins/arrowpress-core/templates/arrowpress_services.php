<?php
$output = $title = $description = $title_color = $desc_color = $link = $el_class = $layout_style ='';
extract(shortcode_atts(array(
	'layout' => 'layout1',
	'title' => '',
    'description' => '',
    'number_step' => '',
    'image' => '',
    'btn_text' => __('Find out more', 'arrowpress'),
    'btn_style' => 'btn_style_1',
    'title_color' => '',
    'desc_color' => '',
    'text_align' => 'center',
    'bg_color_content' => '',
    'link' => '#',
    'bgh_color_content' => '',
    'titleh_color' => '',
    'desch_color' => '',    
    'el_class' => ''
), $atts));
$href = vc_build_link($link);
$el_class = arrowpress_shortcode_extract_class($el_class);
$bgImage = wp_get_attachment_url($image);
$layout_class = '';
if($layout == 'layout2'){
    $layout_class = ' service_type2';
}elseif($layout == 'layout3'){
    $layout_class = ' service_type3';
}elseif($layout == 'layout4'){
    $layout_class = ' service_type4';
}elseif($layout == 'layout5'){
    $layout_class = ' service_type5';
}else{
	$layout_class = ' service_type1';
}
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

$color_1 = '';
$color_2 = '';
$bg_1 = '';
if(($title_color != '') || ($desc_color != '')){
	$color_1 .= 'style="color:'. esc_attr( $title_color ) . '"';
	$color_2 .= 'style="color:'. esc_attr( $desc_color ) . '"';
}
if(($bg_color_content != '')){
	$bg_1 .= 'style="background:'. esc_attr( $bg_color_content ) . '"';
}
$id =  'apr_service-'.wp_rand();
$output = '<div class="service-content' . $el_class . $layout_class . '"';
$output .= '>';
ob_start();
?>
<div id="<?php echo $id; ?>" class="service-box <?php if($text_align == 'center'){echo 'text-center';}?>
		<?php if($text_align == 'left'){echo 'text-left';}?>
		<?php if($text_align == 'right'){echo 'text-right';}?> " <?php echo $bg_1; ?>>
	<?php if($layout=="layout1"):?>
		<?php if($image != ''): ?>
		<div class="service-img">
    		<img src="<?php echo esc_url($bgImage);?>" alt="img-service"/>
    	</div>
		<?php endif; ?>
		<?php if($title != ''): ?>
			<div class="service-title">
				<h4 <?php echo $color_1; ?>><?php echo $title; ?></h4>
			</div>
		<?php endif; ?>
	<?php else: ?>
		<?php if($layout == "layout5"): ?>
				<?php if($number_step != ''): ?>
					<div class="service-number">
						<span><?php echo $number_step; ?></span>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		<?php if($image != ''): ?>
			<div class="service-img">
				<img src="<?php echo esc_url($bgImage);?>" alt="img-service"/>
			</div>
		<?php endif; ?>
		<div class="service-info">
			<?php if($bgh_color_content != '' || $titleh_color != '' || $desch_color!=''):?>
				<style type="text/css">
					@media (min-width: 768px){
						.service-content #<?php echo $id;?>.text-center .service-info:hover{
							background: <?php echo esc_attr($bgh_color_content);?>;
							border-color: <?php echo esc_attr($bgh_color_content);?>;
						}
						.service-content #<?php echo $id;?>.text-center .service-info:hover h4{
							color: <?php echo esc_attr($titleh_color);?> !important;
						}
						.service-content #<?php echo $id;?>.text-center .service-info:hover p{
							color: <?php echo esc_attr($desch_color);?> !important;
						}
					}
				</style>
			<?php endif;?>			
			<?php if($layout != "layout3" && $layout != "layout5"): ?>
				<?php if($title != ''): ?>
					<div class="service-title">
						<h4 <?php echo $color_1; ?>><?php echo $title; ?></h4>
					</div>
				<?php endif; ?>
			<?php endif; ?>
			<div class="service-desc">
				<?php if($layout == "layout3"): ?>
					<?php if($title != ''): ?>
						<h4 <?php echo $color_1; ?>><?php echo $title; ?></h4>
					<?php endif; ?>
				<?php endif; ?>
				<p <?php echo $color_2; ?>><?php echo $description; ?></p>
			</div>
			<?php if( $layout != "layout3" && $layout != "layout4" && $layout != "layout5"): ?>
				<?php if($btn_text != ''): ?>
					<div class="service-sign">
						<a href="<?php echo $href['url']; ?>" class="btn <?php echo $btn_class; ?>">
							<?php echo $btn_text; ?>
						</a>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php if($bg_color_content != ''):?>
		<style type="text/css" scoped>
			#<?php echo $id;?>.service-box .service-info:before{
				background-color: <?php echo esc_attr($bg_color_content);?>;
			}
		</style>
	<?php endif;?>	
</div>
<?php
$output .= ob_get_clean();
$output .= '</div>' . arrowpress_shortcode_end_block_comment('arrowpress_services') . "\n";

echo $output;


wp_reset_postdata();
