<?php 
/*
 Template Name: Coming soon
 */
?>
<?php $cryptcio_settings = cryptcio_check_theme_options(); ?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
    <?php if (!empty($cryptcio_settings['favicon'])): ?>
        <link rel="shortcut icon" href="<?php echo esc_url(str_replace(array('http:', 'https:'), '', $cryptcio_settings['favicon']['url'])); ?>" type="image/x-icon" />
    <?php endif; ?>
    <?php wp_head(); ?>
</head>
<body class="home">
<div id="primary" class="site-content">
    <div id="content">
    	<div class="coming-soon-container has-overlay text-left">
			<header class="display-flex">
	            <div class="container">
	            	
			            <?php if(isset($cryptcio_settings['coming-logo']) && $cryptcio_settings['coming-logo']!='' && $cryptcio_settings['coming-logo']['url']!='' && isset($cryptcio_settings['coming-logo']['url'])):?>
			                <img src="<?php echo esc_url($cryptcio_settings['404-logo']['url']);?>" alt="" />                
			               
			            <?php elseif(isset($cryptcio_settings['logo']) && $cryptcio_settings['logo']!=''):?>
				            <img src="<?php echo esc_url($cryptcio_settings['logo']['url']);?>" alt="" />                      
			            <?php endif;?>   
			            <?php if(isset($cryptcio_settings['coming_select_menu']) && $cryptcio_settings['coming_select_menu']!='' && isset($cryptcio_settings['coming_menu_link']) && $cryptcio_settings['coming_menu_link']!='' ):?>
		                   <div class="header-container text-right">
		                   		<ul class="mega-menu">
		                   			<li>
		                   				<a href="<?php echo esc_attr($cryptcio_settings['coming_menu_link']);?>"><?php echo esc_attr($cryptcio_settings['coming_select_menu']);?></a>
		                   			</li>
		                   		</ul>  
							</div>  
						<?php endif;?>
				</div>  
			</header>     		
    		<div class="page-coming-soon">        		
				<?php 
				if(isset($cryptcio_settings['coming-soon-block']) && $cryptcio_settings['coming-soon-block']!=''){
				    echo do_shortcode('[arrowpress_static_block static="'.esc_html(get_the_title($cryptcio_settings['coming-soon-block'])).'"]');
				}else{?>	
					<div class="coming-title"><?php echo esc_html__('Coming Soon','arrowpress-core');?></div>		
				<?php }
				?>
			</div>
		</div>
    </div><!-- #content -->
</div><!-- #primary -->
</body>
<?php wp_footer(); ?>
</html>
