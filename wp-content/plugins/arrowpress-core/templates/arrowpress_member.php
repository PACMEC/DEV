<?php
$output = $first_name = $last_name = $job  = $image = $layout = $style =$addres = $layout_style = $show_socials = $css =$link = $facebook_link = $twitter_link = $linkedin_link = $title_color = $text_color = $sm_title_color = $instagram_link = $google_link = $print_link = $text_link = $bg_type = $bg_color = $el_class = $disable_info_bg = '';
extract(shortcode_atts(array(
    'first_name' => '',
    'last_name' => '',
    'cat_member' => '',
	'job' => '',
	'description' => '',
	'desc' => '',
	'info_1' => '',
	'values_1' => '',
	'info_2' => '',
	'values_2' => '',
	'info_3' => '',
	'values_3' => '',
	'info_4' => '',
	'values_4' => '',
	'units' => '',
	'article_title' => '',
	'address' => '',
    'image' => '',
    'images' => '',
    'bg_color' => '',
    'bg_overlay_color'=> '',
    'bg_hover' => '',
    'bg_type' => '',
    'phone'=> '',
    'layout' => 'layout1',
    'el_class' => '',
    'css' => '',
    'style' => '',
    'link' => '#',   
    'link_text' => '' ,
    'job_color' => '',
    'title_color' => '',
    'name_color' => '',
    'cat_color' => '',
    'name_hover_color' => '',
    'show_socials' => '',
    'facebook_link' => '#',
    'twitter_link' => '#',
    'google_link' => '#',
    'instagram_link' => '',
    'linkedin_link' => '#',
), $atts));
$layout_class = '';
if($layout == 'layout1'){
    $layout_class = ' member-type1 ';
}elseif($layout == 'layout2'){
    $layout_class = ' member-type2 ';
}else if($layout == 'layout3'){
	$layout_class = ' member-type3 ';
}else if($layout == 'layout4'){
	$layout_class = ' member-type4 ';
}else if($layout == 'layout5'){
	$layout_class = ' member-type5 ';
}

$href = vc_build_link($link);
$facebook_href = vc_build_link($facebook_link);
$twitter_href = vc_build_link($twitter_link);
$google_href = vc_build_link($google_link);
$instagram_href = vc_build_link($instagram_link); 
$linkedin_href = vc_build_link($linkedin_link);
$bgImage = wp_get_attachment_url($image);
$href['url'] = $href['url'] !=''? $href['url'] : '#';
$el_class = arrowpress_shortcode_extract_class($el_class);
if($bgImage != ''){
  $style .= ' style="background-image: url('.$bgImage.')"';
};

/**
 * Style inline
 **/
	$name_style_inline = $cat_style_inline = $title_style_inline = $sm_style_i = $text_style_i = $bg_hover_style = $name_style_hover  ='';
	if($bg_hover != ''){
		$bg_hover_style = 'style ="color: '.esc_attr($bg_hover).'"';
	}
	if($name_color != ''){
		$name_style_inline = 'style ="color: '.esc_attr($name_color).'"';
	}
	if($cat_color != ''){
		$cat_style_inline = 'style ="color: '.esc_attr($cat_color).'"';
	}
	if($name_hover_color != ''){
		$name_style_hover = 'style ="color: '.esc_attr($name_hover_color).'"';
	}
	if($title_color != ''){
		$title_style_inline = 'style ="color: '.esc_attr($title_color).'"';
	}
	if($job_color != ''){
		$sm_style_i = 'style ="color: '.esc_attr($job_color).'"';
	}
	if($text_color != ''){
		$text_style_i = 'style ="color: '.esc_attr($text_color).'"';
	}
$id =  'arp_member-'.wp_rand();
$id_lightbox =  'lightbox_'.wp_rand();
ob_start();
?>
 <?php 
    $output = '<div  class="' . esc_attr($el_class) . esc_attr($layout_class) . '" id="'.esc_html($id).'"';
        $output .= '>';
        ?>
        <?php if($layout == 'layout1') :?>
	     	<div class="item-member-content">
	        	<div class="member-img">
	            	<a data-fancybox data-animation-duration="700" data-src="#<?php echo esc_attr($id_lightbox); ?>" href="javascript:;"><img src="<?php echo esc_url($bgImage);?>" alt="img-member"/></a>
	            </div>
	            <div class="member-info">
	            	<div class="member-name ">
	            		<h4 <?php echo $name_style_inline;?>> <?php echo esc_html($last_name); ?></h4>
	            	</div>
					<div class="member-job" <?php echo $sm_style_i;?>>
	            		<p><?php echo wp_kses($job, cryptcio_allow_html()); ?></p>
	            	</div>
	            	<div class="member-socials">
		                <ul class="social-list">
				        	 <?php if (!empty($facebook_href['url'])): ?>
				                <li><a href="<?php echo esc_url($facebook_href['url']) ?>" title="<?php echo esc_attr($facebook_href['title']);?>" target="<?php echo $facebook_href['target']!='' ? esc_attr($facebook_href['target']): '_self';?>" class="soc_icon icon_facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
				            <?php endif; ?>
				             <?php if (!empty($twitter_href['url'])): ?>
				                <li><a href="<?php echo esc_url($twitter_href['url']) ?>" title="<?php echo esc_attr($twitter_href['title']);?>" target="<?php echo $twitter_href['target']!='' ? esc_attr($twitter_href['target']): '_self';?>" class="soc_icon icon_twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
				            <?php endif; ?>
				            <?php if (!empty($google_href['url'])): ?>
				                <li><a href="<?php echo esc_url($google_href['url']) ?>" title="<?php echo esc_attr($google_href['title']);?>" target="<?php echo $google_href['target']!='' ? esc_attr($google_href['target']): '_self';?>" class="soc_icon icon_google"><i class="fa fa-google-plus" aria-hidden="true"></i></a></li>
				            <?php endif; ?>
				            <?php if (!empty($instagram_href['url'])): ?>
				                <li><a href="<?php echo esc_url($instagram_href['url']) ?>" title="<?php echo esc_attr($instagram_href['title']);?>" target="<?php echo $instagram_href['target']!='' ? esc_attr($instagram_href['target']): '_self';?>" class="soc_icon icon_instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
				            <?php endif; ?> 
				            <?php if (!empty($linkedin_href['url'])): ?>
				                <li><a href="<?php echo esc_url($linkedin_href['url']) ?>" title="<?php echo esc_attr($linkedin_href['title']);?>" target="<?php echo $linkedin_href['target']!='' ? esc_attr($linkedin_href['target']): '_self';?>" class="soc_icon icon_linkedin"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
				            <?php endif; ?> 
		                </ul>
		            </div>
	            </div>
				<div id="<?php echo esc_attr($id_lightbox); ?>" class="member-type1 light-box animated-modal">
					<div class="member-lightbox">
						<div class="member-img">
							<img src="<?php echo esc_url($bgImage);?>" alt="img-member"/>
						</div>
						<div class="member-info">
							<div class="member-name ">
								<h4 <?php echo $name_style_inline;?>> <?php echo esc_html($last_name); ?></h4>
							</div>
							<div class="member-job" <?php echo $sm_style_i;?>>
								<p><?php echo wp_kses($job, cryptcio_allow_html()); ?></p>
							</div>
							<div class="member-socials">
								<ul class="social-list">
									 <?php if (!empty($facebook_href['url'])): ?>
										<li><a href="<?php echo esc_url($facebook_href['url']) ?>" title="<?php echo esc_attr($facebook_href['title']);?>" target="<?php echo $facebook_href['target']!='' ? esc_attr($facebook_href['target']): '_self';?>" class="soc_icon icon_facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
									<?php endif; ?>
									 <?php if (!empty($twitter_href['url'])): ?>
										<li><a href="<?php echo esc_url($twitter_href['url']) ?>" title="<?php echo esc_attr($twitter_href['title']);?>" target="<?php echo $twitter_href['target']!='' ? esc_attr($twitter_href['target']): '_self';?>" class="soc_icon icon_twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
									<?php endif; ?>
									<?php if (!empty($google_href['url'])): ?>
										<li><a href="<?php echo esc_url($google_href['url']) ?>" title="<?php echo esc_attr($google_href['title']);?>" target="<?php echo $google_href['target']!='' ? esc_attr($google_href['target']): '_self';?>" class="soc_icon icon_google"><i class="fa fa-google-plus" aria-hidden="true"></i></a></li>
									<?php endif; ?>
									<?php if (!empty($instagram_href['url'])): ?>
										<li><a href="<?php echo esc_url($instagram_href['url']) ?>" title="<?php echo esc_attr($instagram_href['title']);?>" target="<?php echo $instagram_href['target']!='' ? esc_attr($instagram_href['target']): '_self';?>" class="soc_icon icon_instagram"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
									<?php endif; ?> 
									<?php if (!empty($linkedin_href['url'])): ?>
										<li><a href="<?php echo esc_url($linkedin_href['url']) ?>" title="<?php echo esc_attr($linkedin_href['title']);?>" target="<?php echo $linkedin_href['target']!='' ? esc_attr($linkedin_href['target']): '_self';?>" class="soc_icon icon_linkedin"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
									<?php endif; ?> 
								</ul>
							</div>
							<div class="desc">
								<p><?php echo wp_kses($description, cryptcio_allow_html()); ?></p>
							</div>
							<div class="progress-line">
								<?php if($info_1 != '' && $values_1 != ''): ?>
									<h4><?php echo esc_html($info_1); ?></h4>
									<div class="progress">
										<div class="progress-bar" role="progressbar" aria-valuenow="<?php esc_attr($values_1); ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo esc_attr($values_1); ?>%">
											<span><?php echo esc_html($values_1); ?><?php echo esc_html($units); ?></span>
										</div>
									</div>
								<?php endif; ?> 
								<?php if($info_2 != '' && $values_2 != ''): ?>
									<h4><?php echo esc_html($info_2); ?></h4>
									<div class="progress">
										<div class="progress-bar" role="progressbar" aria-valuenow="<?php esc_attr($values_2); ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo esc_attr($values_2); ?>%">
											<span><?php echo esc_html($values_2); ?><?php echo esc_html($units); ?></span>
										</div>
									</div>
								<?php endif; ?> 
								<?php if($info_3 != '' && $values_3 != ''): ?>
									<h4><?php echo esc_html($info_3); ?></h4>
									<div class="progress">
										<div class="progress-bar" role="progressbar" aria-valuenow="<?php esc_attr($values_3); ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo esc_attr($values_3); ?>%">
											<span><?php echo esc_html($values_3); ?><?php echo esc_html($units); ?></span>
										</div>
									</div>
								<?php endif; ?> 
								<?php if($info_4 != '' && $values_4 != ''): ?>
									<h4><?php echo esc_html($info_4); ?></h4>
									<div class="progress">
										<div class="progress-bar" role="progressbar" aria-valuenow="<?php esc_attr($values_4); ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo esc_attr($values_4); ?>%">
											<span><?php echo esc_html($values_4); ?><?php echo esc_html($units); ?></span>
										</div>
									</div>
								<?php endif; ?> 
							</div> 
						</div>
					</div>
				</div>
				<?php if($bg_hover != '' || $name_hover_color != '' ):?>
					<style type="text/css">
						<?php if($bg_hover_style!=''):?>
						#<?php echo $id;?> .item-member-content:hover .member-info{
							background: <?php echo esc_attr($bg_hover_style);?>;
						}
						<?php endif;?>
						<?php if($name_style_hover!=''):?>
						#<?php echo $id;?> .item-member-content:hover .member-name h5{
							background: <?php echo esc_attr($name_style_hover);?>;
						}
						<?php endif;?>
					</style>
				<?php endif;?>	
	        </div>
	    <?php elseif($layout == 'layout2') :?>
	     	<div class="item-member-content">
		     	<?php if($desc != ''):?>
		     		<div class="member-desc" <?php echo $sm_style_i;?>>
		     			<p><?php echo wp_kses($desc, cryptcio_allow_html()); ?></p>
		            </div>
		        <?php endif; ?>
	            <div class="member-info">
	            	<div class="member-name info">
	            		<h4 <?php echo $name_style_inline;?>><?php echo esc_html($last_name); ?></h4>
	            		<div class="address"><?php echo esc_html($address); ?> </div>
	            	</div>
					<div class="member-img info">
	            	<img src="<?php echo esc_url($bgImage);?>" alt="img-member"/>
	            </div>
	            </div>
				<?php if($bg_hover != '' || $name_hover_color != ''):?>
					<style type="text/css">
						.item-member-content:hover .member-info{
							background: <?php echo esc_attr($bg_hover_style);?>;
						}
						.item-member-content:hover .member-name h5{
							background: <?php echo esc_attr($name_style_hover);?>;
						}
					</style>
				<?php endif;?>	
	        </div> 
	    <?php elseif($layout == 'layout3') :?>
	     	<div class="item-member-content">
	     		<?php if($image != ''):?>
		     		<div class="member-img info">
		            	<img src="<?php echo esc_url($bgImage);?>" alt="img-member"/>
		           	</div>
		        <?php endif; ?>
		        <?php if($article_title != ''):?>
		     		<div class="member-title" <?php echo $title_style_inline;?>>
		            	<p><?php echo esc_html($article_title); ?></p>
		            </div>
		        <?php endif; ?>
		     	<?php if($desc != ''):?>
		     		<div class="member-desc" <?php echo $sm_style_i;?>>
		     			<p>“<?php echo wp_kses($desc, cryptcio_allow_html()); ?>”</p>
		            </div>
		        <?php endif; ?>
		        <?php if($last_name != ''):?>
			        <div class="member-name ">
		        		<h4 <?php echo $name_style_inline;?>> <?php echo esc_html($last_name); ?></h4>
		        	</div>
		        <?php endif; ?>
	        </div>  
	   	 <?php elseif($layout == 'layout4') :?>
	     	<div class="item-member-content">
	     		<?php if($image != ''):?>
		     		<div class="member-img">
		            	<img src="<?php echo esc_url($bgImage);?>" alt="img-member"/>
		            	<?php if($link_text != '') :?>
							<div class="link-text">
								<a target="_blank" href="<?php echo esc_url($href['url']); ?>">
									<i class="<?php echo esc_html($link_text); ?>"></i>
								</a>
							</div>
						<?php endif; ?>
		           	</div>
		        <?php endif; ?>
		        <div class="member-info">
			         <?php if($last_name != ''):?>
				        <div class="member-name ">
			        		<h4 <?php echo $name_style_inline;?>> <a target="_blank" href="<?php echo esc_url($href['url']); ?>"> <?php echo esc_html($last_name); ?></a></h4>
			        	</div>
			        <?php endif; ?>
			       	 <?php if($cat_member != ''):?>
				        <div class="member-cat ">
			        		<h5 <?php echo $cat_style_inline;?>> <?php echo wp_kses($cat_member, cryptcio_allow_html()); ?></h5>
			        	</div>
			        <?php endif; ?>
			     	<?php if($desc != ''):?>
			     		<div class="member-desc" <?php echo $sm_style_i;?>>
			     			<p><?php echo wp_kses($desc, cryptcio_allow_html()); ?></p>
			            </div>
			        <?php endif; ?>
		       	</div>
	        </div> 
	    <?php elseif($layout == 'layout5') :?>
	     	<div class="item-member-content">
	     		<?php if($image != ''):?>
		     		<div class="member-img">
		            	<img src="<?php echo esc_url($bgImage);?>" alt="img-member"/>
		           	</div>
		        <?php endif; ?>
		        <div class="member-info">
			         <?php if($last_name != ''):?>
				        <div class="member-name ">
			        		<h4 <?php echo $name_style_inline;?>><?php echo esc_html($last_name); ?></h4>
			        	</div>
			        <?php endif; ?>
				    <?php if($job !="") :?>
			        	<div class="member-job" <?php echo $sm_style_i;?>>
	            		<p><?php echo wp_kses($job, cryptcio_allow_html()); ?></p>
		            	</div>
				    <?php endif; ?>
			    </div>
	        </div> 
	    <?php endif;?>      
<?php
$output .= ob_get_clean();
$output .= '</div>' . arrowpress_shortcode_end_block_comment('arrowpress_member') . "\n";

echo $output;


wp_reset_postdata();