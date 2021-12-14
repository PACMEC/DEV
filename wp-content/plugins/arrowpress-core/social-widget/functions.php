<?php 
	if ( !defined('ABSPATH') )
	    die('-1');
	require_once dirname(__FILE__) . '/twitter/settings.php';
	require_once dirname(__FILE__) . '/twitter/api/Abraham/TwitterOAuth/TwitterOAuth.php';
	require_once dirname(__FILE__) . '/twitter/widget.php';    
	require_once dirname(__FILE__) . '/instagram/settings.php';
	require_once dirname(__FILE__) . '/facebook/arrowpress-likebox-facebook.php';    

	/**
	 * Adds arrowpress_instagram_feed widget.
	 */

	class arrowpress_social extends WP_Widget {
		/**
	     * Register widget with WordPress.
	     */
	    function __construct() {
	        parent::__construct(
	            'arrowpress_instagram_feed', // Base ID
	            __('Arrowpress Instagram Feed', 'arrowpress-core'), // Name
	            array( 'description' => __( 'Arrowpress Instagram Feed', 'arrowpress-core' ), ) // Args
	        );
	        add_shortcode('arrowpress_instagram_feed', array($this, 'arrowpress_shortcode_instagram'));
	    }
	    function loadJs() {
	        wp_enqueue_script('arrowpress_instagram', plugin_dir_url(__FILE__) . '/social-widget/instagram/js/instagramfeed.js', array(), false, false);
	    }
	    public function get_tweets($number_tweets) {
	        # Define constants
	        $options = get_option('arrowpress_latest_tweet');
	        $username = $options['username'];
	        $consumer_key = $options['consumer_key'];
	        $consumer_secret = $options['consumer_secret'];
	        $access_token = $options['access_token'];
	        $access_token_secret = $options['access_token_secret'];
	        if(empty($username) || empty($consumer_key) || empty($consumer_secret) 
	                || empty($access_token) || empty($access_token_secret)) {
	            return false;
	        }
	        # Create the connection
	        $twitter = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
	        # Migrate over to SSL/TLS
	        $twitter->ssl_verifypeer = false;
	        # Load the Tweets
	        try {
	            $tweets = $twitter->get('statuses/user_timeline', array('screen_name' => $username, 'exclude_replies' => 'true', 'include_rts' => 'false', 'count' => $number_tweets));
	            # Example output
	            //echo '<pre>';print_r($tweets);die();
	            if (!empty($tweets)) {
	                echo '<div class="latest-tweets"><ul>';
	                foreach($tweets as $_tweet) {
	                    $user = $_tweet->user;
	                    $handle = $user->screen_name;
	                    $id_str = $_tweet->id_str;
	                    $link = esc_html( 'http://twitter.com/'.$handle.'/status/'.$id_str);
	                    $date = DateTime::createFromFormat('D M d H:i:s O Y', $_tweet->created_at );
	                    $output ='<li>';
	                    $output .= '<div class="twitter-tweet"><i class="fa fa-twitter"></i><div class ="tweet-text">'. esc_attr($_tweet->text).'<p class="my-date">'.esc_attr($date->format('g:i A - j M Y')).'</p></div>';
	                    $output .= '</div></li>';
	                    echo $output;
	                }
	                echo '</ul></div>';
	            }
	        } catch (Exception $exc) {
	            echo esc_html__('Something wrong, please check the connection or the api config!');
	        }

	        return null;
	    }
	    /**
	     * Front-end display of widget.
	     *
	     * @see WP_Widget::widget()
	     *
	     * @param array $args     Widget arguments.
	     * @param array $instance Saved values from database.
	     */	    
	    public function widget( $args, $instance ) {
	        $options = get_option('arrowpress_instagram');
	        $access_token = $options['access_token'];
	        $user_id = $options['user_id'];
	        
	        extract( $args );
	        $tag = ( ! empty( $instance['tag'] ) ) ? strip_tags( $instance['tag'] ) : '';
	        $title = isset($instance['title'])?apply_filters( 'widget_title', $instance['title'] ):'';
	        $i=0;
	        echo $before_widget;
			if ( $title )
			echo $before_title . $title . $after_title;	        
	        ?>

	        <?php if ($access_token != '' && $user_id != ''): ?>
	            <?php
	            $url = 'https://api.instagram.com/v1/users/' . $user_id . '/media/recent/?access_token=' . $access_token;
	            $all_result = $this->process_url($url);

	            $decoded_results = json_decode($all_result, true);
	        ?>
	            <div class="instagram-container">
	                <?php if (count($decoded_results) & isset($decoded_results['data'])) : ?>
	                    <?php if(isset($instance['number'])):?>
	            
	                            <div class="instagram-gallery">
	                            <?php if($tag != ""):?>
	                              <?php foreach (array_slice($decoded_results['data'], 0) as $value): ?>
	                                <?php if( isset($value['tags'][0])):?>
	                                  <?php if (in_array($tag, $value['tags'])):?>
	                                  <?php  $i ++;?>
	                                    <?php if($i <= $instance['number']):?>
	                                      <div class="instagram-img" style="background-image: url(<?php echo $value['images']['standard_resolution']['url'] ?>)">
												<a title="<?php echo $value['caption']['text'] ?>" target="_blank" href="<?php echo $value['link'] ?>">
													<i class="fa fa-instagram"></i>
												</a>
											</div>
	                                    <?php endif;?>
	                                  <?php endif;?>
	                                <?php endif;?>
	                              <?php endforeach; ?>   
	                            <?php else:?>
	                              <?php foreach (array_slice($decoded_results['data'], 0, $instance['number']) as $value): ?>
		                                <div class="instagram-img" style="background-image: url(<?php echo $value['images']['standard_resolution']['url'] ?>)">
											<a title="<?php echo $value['caption']['text'] ?>" target="_blank" href="<?php echo $value['link'] ?>">
												<i class="fa fa-instagram"></i>
											</a>
									    </div>
	                                <?php endforeach; ?>
	                            <?php endif;?>                             
	                            </div>
	                    <?php else:?>
	                                <div class="instagram-gallery">
	                                  <?php foreach (array_slice($decoded_results['data'], 0, 8) as $value): ?>
	                                    <div class="instagram-img" style="background-image: url(<?php echo $value['images']['standard_resolution']['url'] ?>)">
											<a title="<?php echo $value['caption']['text'] ?>" target="_blank" href="<?php echo $value['link'] ?>">
												<i class="fa fa-instagram"></i>
											</a>
										</div>
	                                  <?php endforeach; ?>
	                                </div>
	                    <?php endif;?>
	                        
	                <?php else: ?>
	                    <p> <?php echo esc_html__("Access token is not valid.",'arrowpress-core');?></p>
	                <?php endif;?>
	            </div>
	        <?php endif; ?>
	        <?php
	        echo $after_widget;
	    }
	    /**
	     * Back-end widget form.
	     *
	     * @see WP_Widget::form()
	     *
	     * @param array $instance Previously saved values from database.
	     */
	    public function form( $instance ) {
	        $defaults = array( 
	            'title' => 'Instagram', 
	            'number' => 9,
	            'tag' =>"",
	            );
	        $instance = wp_parse_args( (array) $instance, $defaults );
	        ?>
	        <p>
	            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
	            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" type="text" name="<?php echo $this->get_field_name('title'); ?>'" value="<?php echo $instance['title']; ?>" />
	        </p>
	        <p>
	            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of photos to display:'); ?></label>
	            <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" type="text" name="<?php echo $this->get_field_name('number'); ?>" value="<?php echo $instance['number']; ?>" />
	        </p>
	        <p>
	            <label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Hashtag:'); ?></label>
	            <input class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" type="text" name="<?php echo $this->get_field_name('tag'); ?>'" value="<?php echo $instance['tag']; ?>" />
	        </p>
	       
	       
	        <?php 
	    }
	    /**
	     * Sanitize widget form values as they are saved.
	     *
	     * @see WP_Widget::update()
	     *
	     * @param array $new_instance Values just sent to be saved.
	     * @param array $old_instance Previously saved values from database.
	     *
	     * @return array Updated safe values to be saved.
	     */
	    public function update( $new_instance, $old_instance ) {
	        $instance = array();
	        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	        $instance['tag'] = ( ! empty( $new_instance['tag'] ) ) ? strip_tags( $new_instance['tag'] ) : '';
	        $instance['number'] = ( ! empty( $new_instance['number'] ) ) ? strip_tags( $new_instance['number'] ) : '';
	        return $instance;
	    }
	    function arrowpress_shortcode_instagram($atts, $content = null) {
	        $options = get_option('arrowpress_instagram');
	        $access_token = $options['access_token'];
	        $user_id = $options['user_id'];

	        $limit = 20;
	        $output = $title = $el_class = '';
	        $per_page = 9;
	        extract(shortcode_atts(array(
	            'layout' => 'layout1',
	            'title' => '',
	            'per_page' => '',
	            'items_desktop_large1' => 4,
				'items_desktop_large' => 4,
			    'items_desktop' => 3,
			    'items_tablets' => 2,
			    'items_mobile' => 1,	
				'color_default'=> '',
				'color_border'=> '',
				'color_bg'=> '',
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
			    'image' => '',	
			    'image_bg' => '',
			    'image_bg2' => '',	
			    'icon_color' => '',
			    'icon_size' => '',	
	            'el_class' => ''
	        ), $atts));
	        $content = wpb_js_remove_wpautop($content, true);
			$layout_class = '';
			if($layout == 'layout1'){
				$layout_class = ' instagram-type1';
			}else if($layout == 'layout3'){
				$layout_class = ' instagram-type3';
			}else if($layout == 'layout4'){
				$layout_class = ' instagram-type4';
			}else if($layout == 'layout5'){
				$layout_class = ' instagram-type5';
			}else if($layout == 'layout6'){
				$layout_class = ' instagram-type6';
			}else if($layout == 'layout7'){
				$layout_class = ' instagram-type7';
			}else{
				$layout_class = ' instagram-type2';
			}
			//=============Style Inline for title==================//
			$big_inline_style = ''; 
			$big_title_style_inline='';	

			$big_title_style[] ='';
			if($color_default !=''){
				$big_title_style[] .= 'color:'. esc_attr( $color_default ) . '';
			}   
			if($color_border!=''){
				$big_title_style[] .= 'border-color:'. esc_attr( $color_border ) . '';
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

			$bg_color = "";
			if($color_bg !=''){
				$bg_color = $color_bg;
			}

			// Get icon class for layout 7
			$icon_class = "";
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
			}elseif (!empty($icon_themifyfont)) {
			    $icon_class = $icon_themifyfont;
			}
			$bgImage = wp_get_attachment_url($image);

	        $el_class = arrowpress_shortcode_extract_class($el_class);
	        $output = '<div class="arrowpress-animation ' . $el_class . '"';
	        $output .= '>';
	        ob_start();
	        ?>
	        <?php echo $output; ?>
	        <?php if ($access_token != '' && $user_id != ''): ?>
	            <?php
	            $url = 'https://api.instagram.com/v1/users/' . $user_id . '/media/recent/?access_token=' . $access_token;
	            $link_url = 'https://instagram.com/' . $user_id;
	            $all_result = $this->process_url($url);

	            $decoded_results = json_decode($all_result, true);
	            ?>
	            <div <?php if($color_bg !=''): ?> style="background-color: <?php echo $bg_color; ?>" <?php endif; ?> class="instagram-container <?php echo $layout_class; ?>">
					<?php if($layout == "layout1"): ?>
						<div class="title-insta"><h2 <?php echo $big_title_style_inline;?>><span><?php echo esc_html('Follow us on ','arrowpress-core'); ?></span><?php echo esc_attr($title); ?></h2></div>
						<div class="instagram-grid instagram-slider">
							<?php if (count($decoded_results) && $decoded_results['data'] ) : ?>
								<?php foreach (array_slice($decoded_results['data'], 0, $per_page) as $value): ?>
									<div class="instagram-img" style="background-image: url(<?php echo $value['images']['standard_resolution']['url'] ?>)">
										<a title="<?php echo $value['caption']['text'] ?>" target="_blank" href="<?php echo $value['link'] ?>">
											<i class="fa fa-instagram"></i>
										</a>
									</div>
								<?php endforeach; ?>   
							<?php endif; ?>
						</div>
					<?php elseif($layout == "layout3"): ?>
						<div class="instagram-image">
						<div class="instagram_parkery">
							<?php 
								$i = 0;
								$pakery_class ='';
							?>
							<?php if (count($decoded_results) && $decoded_results['data'] ) : ?>
								<?php foreach (array_slice($decoded_results['data'], 0, $per_page) as $value): ?>
									<?php 
									$index_size1 = array('1','10','19','28','37','46','55','64','73','82','91','100');
									$index_size2=  array('2','3','6','8','11','12','15','17','20','21','24','26','29','30','33','35','38','39','42','44','47','48','51','53','16');
									$index_size3=  array('4','7','22','25','31','34','40','43','52');
			                        if(in_array($i, $index_size1) ){
			                            $pakery_class = 'image_size1';
			                        }elseif(in_array($i, $index_size3) ){
			                             $pakery_class = 'image_size3';
			                        }elseif(in_array($i, $index_size2) ){
			                             $pakery_class = 'image_size2';
			                        }else{
			                        	$pakery_class = 'image_size';
			                        }
									?>
									<div class="instagram-img <?php echo esc_attr($pakery_class);?>" style="background-image: url(<?php echo $value['images']['standard_resolution']['url'] ?>)">
										<a title="<?php echo $value['caption']['text'] ?>" target="_blank" href="<?php echo $value['link'] ?>">
											<i class="fa fa-instagram"></i>
										</a>
									</div>
									<?php $i++;?>
								<?php endforeach; ?>   
							<?php endif; ?>
						</div>	
						</div>
					<?php elseif($layout == "layout5"): ?>
						<?php 
							$i = 0;
							$pakery_class ='';
						?>
						<div class="instagram-grid instagram-slider-1">
							<?php if (count($decoded_results) && $decoded_results['data'] ) : ?>
								<?php foreach (array_slice($decoded_results['data'], 0, $per_page) as $value): ?>
									<?php 
										$index_size1 = array('1','3','6','8','11','13','16','18','21','23','26','28','31','33','36','38','41','43','46','48','51','53','56','58','61','63','66','68','71','73','76','78','81','83','86','88','91','93','96','98','101');
								        $index =4;
								        $index_size2 =array();
								        for ($a=0; $a<300; $a++){
								            $index_size2[] = $index;
								            $index = $index +5;
								        }
				                        if(in_array($i, $index_size1) ){
				                            $pakery_class = 'image_size1';
				                        }elseif(in_array($i, $index_size2) ){
				                            $pakery_class = 'image_size2';
				                        }else{
				                        	$pakery_class = '';
				                        }
									?>									
									<div class="instagram-img <?php echo esc_attr($pakery_class);?>" style="background-image: url(<?php echo $value['images']['standard_resolution']['url'] ?>)">
										<a title="<?php echo $value['caption']['text'] ?>" target="_blank" href="<?php echo $value['link'] ?>">
											<i class="fa fa-instagram"></i>
										</a>
									</div>
									<?php $i++;?>
								<?php endforeach; ?>   
							<?php endif; ?>
						</div>
						<?php if($content!=''):?>
							<div <?php echo $big_title_style_inline;?> class="title-insta"><?php echo $content;?></div>
						<?php endif;?>
						<div class="title-insta"></div>	
							<script type="text/javascript">
								jQuery(document).ready(function ($) {
									jQuery('.instagram-slider-1').slick({
									  centerMode: false,
									  dots: false,
									  arrows: false,
									  slidesToShow: <?php echo $items_desktop_large; ?>,
									  slidesToScroll: 1,
									  infinite: false,
									  responsive: [
										{
										  breakpoint: 1367,
										  settings: {
											slidesToShow: <?php echo $items_desktop_large1; ?>
										  }
										},
										{
										  breakpoint: 1200,
										  settings: {
											slidesToShow: <?php echo $items_desktop; ?>
										  }
										},
										{
										  breakpoint: 1025,
										  settings: {
											slidesToShow: <?php echo $items_tablets; ?>
										  }
										},
										{
										  breakpoint: 768,
										  settings: {
											slidesToShow: <?php echo $items_tablets; ?>
										  }
										},
										{
										  breakpoint: 480,
										  settings: {
											slidesToShow: <?php echo $items_mobile; ?>
										  }
										}
									  ]
									});									
								});
							</script>											
					<?php elseif($layout == "layout4"): ?>
						<?php if (count($decoded_results) && $decoded_results['data'] ) : ?>
							<div class="instagram-slider-3">
								<?php foreach (array_slice($decoded_results['data'], 0, $per_page) as $value): ?>
									<div class="instagram-img" style="background-image: url(<?php echo $value['images']['standard_resolution']['url'] ?>)">
										<a title="<?php echo $value['caption']['text'] ?>" target="_blank" href="<?php echo $value['link'] ?>">
										</a>
									</div>
								<?php endforeach; ?>                                
							</div>
						<?php endif; ?>
					<?php elseif($layout == "layout6"): ?>
						<div class="title-insta"><h5 <?php echo $big_title_style_inline;?> class="title-border"><?php echo esc_html__($title); ?></h5></div>
						<div class="instagram-grid">
							<?php if (count($decoded_results) && $decoded_results['data'] ) : ?>
								<?php foreach (array_slice($decoded_results['data'], 0, $per_page) as $value): ?>
									<div class="instagram-img" style="background-image: url(<?php echo $value['images']['standard_resolution']['url'] ?>)">
										<a title="<?php echo $value['caption']['text'] ?>" target="_blank" href="<?php echo $value['link'] ?>">
											<i class="fa fa-instagram"></i>
										</a>
									</div>
								<?php endforeach; ?>   
							<?php endif; ?>
						</div>
					<?php elseif($layout == "layout7"): ?>
						<?php 
							$icon_inline_style =''; 
							$iconbox_style_inline =''; 
							$icon_style_i[] =''; 
							if($icon_color!=''){
								$icon_style_i[] .= 'color:'. esc_attr( $icon_color ) . '';
							}
							if($icon_size!=''){
								$icon_style_i[] .= 'font-size:'. esc_attr( $icon_size ) . 'px';
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
						?>
						<div class="instagram_parkery">
							<?php if($type_icon=='image_icon'):?>
								<?php if($bgImage):?>
									<div class="ins_icon_box" >
										<img src="<?php echo esc_url($bgImage);?>" alt="img">
									</div>
								<?php endif;?>	
							<?php elseif($icon_class != ''):?>
								<div class="ins_icon_box" >
									<i class="<?php echo $icon_class; ?>" ></i>					
								</div>
							<?php endif;?>							
							<?php 
								$i = 1;
								$pakery_class ='';
							?>
							<?php if (count($decoded_results) && $decoded_results['data'] ) : ?>
								<?php foreach (array_slice($decoded_results['data'], 0, $per_page) as $value): ?>
									<?php 
									/** 
									* Style inline for default image background.
									* @param $image_bg  Background image url
									*/
									$i7_style = '';
									$i7_final_style ='';
									$i7_style_array[] ='';
									$bg_image_url = wp_get_attachment_url($image_bg);
									if(isset($image_bg) && $bg_image_url!=''){
										$i7_style_array[] .= 'background-image: url('. esc_url($bg_image_url) . ')';
									}
									if (is_array($i7_style_array) || is_object($i7_style_array)){ 
										foreach( $i7_style_array as $attribute ){ 
											if($attribute!=''){          
											    $i7_style .= $attribute.'; ';   
											}    
										}
									} 	
									if($i7_style !=''){
								    	$i7_final_style = 'style="'.$i7_style.'"';
								    }									

									/** 
									* Style inline for SECOND image background.
									* @param $image_bg  Background image url
									*/
									$i7_style2 = '';
									$i7_final_style2 ='';
									$i7_style_array2[] ='';

									if(isset($image_bg2) && wp_get_attachment_url($image_bg2)!=''){
										$i7_style_array2[] .= 'background-image: url('.  wp_get_attachment_url($image_bg2) . ')';
									}else if(isset($image_bg) && $bg_image_url!=''){
										$i7_style_array2[] .= 'background-image: url('. esc_url($bg_image_url) . ')';
									}

									if (is_array($i7_style_array2) || is_object($i7_style_array2)){ 
										foreach( $i7_style_array2 as $attribute ){ 
											if($attribute!=''){          
											    $i7_style2 .= $attribute.'; ';   
											}    
										}
									} 	
									if($i7_style2 !=''){
								    	$i7_final_style2 = 'style="'.$i7_style2.'"';
								    }	

									$index_size1 = array('1','5','8','12','15','19','22','26','29','33','36','40','43','47');
									$index_size2=  array('2','4','9','11','16','18','23','25','30','32','');
									$index_size3=  array('3','10','17','24','31','38','45','52');
									$index_size4=  array('6','13','20','27','34','41','48','55');
									$i7_final_style_inline = $i7_final_style;
			                        if(in_array($i, $index_size1) ){
			                            $pakery_class = 'image_size1';
			                            $i7_final_style_inline = $i7_final_style2;
			                        }elseif(in_array($i, $index_size3) ){
			                             $pakery_class = 'image_size3';
			                             $i7_final_style_inline = $i7_final_style2;
			                        }elseif(in_array($i, $index_size2) ){
			                             $pakery_class = 'image_size2';
			                        }elseif(in_array($i, $index_size4) ){
			                             $pakery_class = 'image_size4';
			                        }else{
			                        	$pakery_class = 'image_size';
			                        }
									?>
									<div class="instagram-img <?php echo esc_attr($pakery_class);?>" <?php echo $i7_final_style_inline;?>>
										<div class="ins-img-container">
											<div class="instagram-img-inner " style="background-image: url(<?php echo $value['images']['standard_resolution']['url'] ?>)">
												<a title="<?php echo $value['caption']['text'] ?>" target="_blank" href="<?php echo $value['link'] ?>">
													<i class="fa fa-instagram"></i>
												</a>
											</div>											
										</div>
									</div>
									<?php $i++;?>
								<?php endforeach; ?>   
							<?php endif; ?>
						</div>					
					<?php else: ?>
						<div class="title-insta"><h2 <?php echo $big_title_style_inline;?>><?php echo esc_attr($title); ?></h2></div>
						<?php if (count($decoded_results) && $decoded_results['data'] ) : ?>
						  <?php 
							/*echo '<pre>';
								print_r($decoded_results);
							echo '</pre>';*/
							?>
							<div class="instagram-slider">
								<?php foreach (array_slice($decoded_results['data'], 0, $per_page) as $value): ?>
									<div class="instagram-img" style="background-image: url(<?php echo $value['images']['standard_resolution']['url'] ?>)">
										<a title="<?php echo $value['caption']['text'] ?>" target="_blank" href="<?php echo $value['link'] ?>">
											<i class="fa fa-instagram"></i>
										</a>
									</div>
								<?php endforeach; ?>                                
							</div>
						<?php endif; ?>
					<?php endif; ?>
	            </div>
	        <?php else: ?>
	            <div class="row">
	                <?php echo __('Instagram Plugin error: Plugin not fully configured', 'arrowpress-core') ?>
	            </div>
	        <?php endif; ?>
	            
	        </div>
	        <?php
	        return ob_get_clean();
	    }
	    function process_url($url) {
	        $ch = curl_init();
	        curl_setopt_array($ch, array(
	            CURLOPT_URL => $url,
	            CURLOPT_RETURNTRANSFER => true,
	            CURLOPT_SSL_VERIFYPEER => false,
	            CURLOPT_SSL_VERIFYHOST => 2
	        ));

	        $result = curl_exec($ch);
	        curl_close($ch);
	        return $result;
	    }
	}
	add_action( 'widgets_init', function(){
     register_widget( 'arrowpress_social' );
	});
?>