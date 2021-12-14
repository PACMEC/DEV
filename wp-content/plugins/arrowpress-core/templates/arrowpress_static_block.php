<?php
$output =  $el_class = $static ='';
extract(shortcode_atts(array(
    'static' =>'',
    'el_class' => ''
), $atts));

$posts = new WP_Query(array( 'name' => $static, 'post_type' => 'block' ));

    if ($posts->have_posts()) {
        $posts->the_post();

        global $post;
         wp_enqueue_script('wpb_composer_front_js');
        wp_enqueue_style('js_composer_front');

        $el_class = arrowpress_shortcode_extract_class( $el_class );

        if($el_class !=''){
            $output = '<div class="arrowpress-static-block' .$el_class . '"';
            $output .= '>';
        } 

        $post_content = $post->post_content;
        
        if (class_exists('Ultimate_VC_Addons')) {

            $uvc_addons = new Ultimate_VC_Addons();
            $uvc_addons->aio_front_scripts();
            $arrowpress_ult_assets = str_replace('js/', '', $uvc_addons->assets_js);
            $isAjax = false;
            $ultimate_ajax_theme = get_option('ultimate_ajax_theme');
            if($ultimate_ajax_theme == 'enable')
                $isAjax = true;
            $dependancy = array('jquery');
  
            // register js

            wp_register_script('ultimate-script', $arrowpress_ult_assets . 'min-js/ultimate.min.js', array('jquery'), ULTIMATE_VERSION, false);
            wp_register_script('ultimate-appear', $arrowpress_ult_assets . 'min-js/jquery.appear.min.js', array('jquery'), ULTIMATE_VERSION);
            wp_register_script('ultimate-custom', $arrowpress_ult_assets . 'min-js/custom.min.js', array('jquery'), ULTIMATE_VERSION);
            wp_register_script('ultimate-vc-params', $arrowpress_ult_assets . 'min-js/ultimate-params.min.js', array('jquery'), ULTIMATE_VERSION);

            // register css
            wp_register_style('ultimate-animate', $arrowpress_ult_assets . 'min-css/animate.min.css', array(), ULTIMATE_VERSION);
            wp_register_style('ultimate-style', $arrowpress_ult_assets . 'min-css/style.min.css', array(), ULTIMATE_VERSION);
            wp_register_style('ultimate-style-min', $arrowpress_ult_assets . 'min-css/ultimate.min.css', array(), ULTIMATE_VERSION);

            if(stripos($post_content, 'font_call:'))
            {
                preg_match_all('/font_call:(.*?)"/',$post_content, $display);
                enquque_ultimate_google_fonts_optimzed($display[1]);
            }

            $ultimate_js = get_option('ultimate_js');
            if($ultimate_js == 'enable' || $isAjax == true)
            {
                wp_enqueue_script('ultimate-script');

                if( stripos( $post_content, '[icon_timeline') ) {
                    wp_enqueue_script('masonry');
                }
                if( stripos( $post_content, '[ultimate_google_map') ) {
                    wp_enqueue_script('googleapis');
                }
                if($isAjax == true) { // if ajax site load all js
                    wp_enqueue_script('masonry');
                }
            }
            else if($ultimate_js == 'disable')
            {
                wp_enqueue_script('ultimate-vc-params');

                if(
                    stripos( $post_content, '[ultimate_spacer')
                    || stripos( $post_content, '[ult_buttons')
                    || stripos( $post_content, '[ultimate_icon_list')
                ) {
                    wp_enqueue_script('ultimate-custom');
                }
                if(
                    stripos( $post_content, '[just_icon')
                    || stripos( $post_content, '[ult_animation_block')
                    || stripos( $post_content, '[icon_counter')
                    || stripos( $post_content, '[ultimate_google_map')
                    || stripos( $post_content, '[icon_timeline')
                    || stripos( $post_content, '[bsf-info-box')
                    || stripos( $post_content, '[info_list')
                    || stripos( $post_content, '[ultimate_info_table')
                    || stripos( $post_content, '[interactive_banner_2')
                    || stripos( $post_content, '[interactive_banner')
                    || stripos( $post_content, '[ultimate_pricing')
                    || stripos( $post_content, '[ultimate_icons')
                ) {
                    wp_enqueue_script('ultimate-appear');
                    wp_enqueue_script('ultimate-custom');
                }
                if( stripos( $post_content, '[ultimate_heading') ) {
                    wp_enqueue_script("ultimate-headings-script");
                }
                if( stripos( $post_content, '[ultimate_carousel') ) {
                    wp_enqueue_script('ult-slick');
                    wp_enqueue_script('ultimate-appear');
                    wp_enqueue_script('ult-slick-custom');
                }
                if( stripos( $post_content, '[ult_countdown') ) {
                    wp_enqueue_script('jquery.timecircle');
                    wp_enqueue_script('jquery.countdown');
                }
                if( stripos( $post_content, '[icon_timeline') ) {
                    wp_enqueue_script('masonry');
                }
                if( stripos( $post_content, '[ultimate_info_banner') ) {
                    wp_enqueue_script('ultimate-appear');
                    wp_enqueue_script('utl-info-banner-script');
                }
                if( stripos( $post_content, '[ultimate_google_map') ) {
                    wp_enqueue_script('googleapis');
                }
                if( stripos( $post_content, '[swatch_container') ) {
                    wp_enqueue_script('modernizr-79639-js');
                    wp_enqueue_script('swatchbook-js');
                }
                if( stripos( $post_content, '[ult_ihover') ) {
                    wp_enqueue_script('ult_ihover_js');
                }
                if( stripos( $post_content, '[ult_hotspot') ) {
                    wp_enqueue_script('ult_hotspot_js');
                    wp_enqueue_script('ult_hotspot_tooltipster_js');
                }
                if( stripos( $post_content, '[bsf-info-box') ) {
                    wp_enqueue_script('info_box_js');
                }
                if( stripos( $post_content, '[icon_counter') ) {
                    wp_enqueue_script('flip_box_js');
                }
                if( stripos( $post_content, '[ultimate_ctation') ) {
                    wp_enqueue_script('utl-ctaction-script');
                }
                if( stripos( $post_content, '[stat_counter') ) {
                    wp_enqueue_script('ultimate-appear');
                    wp_enqueue_script('front-js');
                    wp_enqueue_script('ult-slick-custom');
                    array_push($dependancy,'front-js');
                }
                if( stripos( $post_content, '[ultimate_video_banner') ) {
                    wp_enqueue_script('ultimate-video-banner-script');
                }
                if( stripos( $post_content, '[ult_dualbutton') ) {
                    wp_enqueue_script('jquery.dualbtn');

                }
                if( stripos( $post_content, '[ult_createlink') ) {
                    wp_enqueue_script('jquery.ult_cllink');
                }
                if( stripos( $post_content, '[ultimate_img_separator') ) {
                    wp_enqueue_script('ultimate-appear');
                    wp_enqueue_script('ult-easy-separator-script');
                }
            }

            $ultimate_css = get_option('ultimate_css');

            if($ultimate_css == "enable"){
                wp_enqueue_style('ultimate-style-min');
            } else {
                wp_enqueue_style('ultimate-style');


                if( stripos( $post_content, '[ult_animation_block') ) {
                    wp_enqueue_style('ultimate-animate');
                }
                if( stripos( $post_content, '[icon_counter') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('ultimate-style');
                    wp_enqueue_style('aio-flip-style', $arrowpress_ult_assets . 'min-css/flip-box.min.css');
                }
                if( stripos( $post_content, '[ult_countdown') ) {
                    wp_enqueue_style('countdown_shortcode', $arrowpress_ult_assets . 'min-css/countdown.min.css');
                }
                if( stripos( $post_content, '[ultimate_icon_list') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('aio-tooltip', $arrowpress_ult_assets . 'min-css/tooltip.min.css');
                }
                if( stripos( $post_content, '[ultimate_fancytext') ) {
                    wp_enqueue_style('ultimate-fancytext-style');
                }
                if( stripos( $post_content, '[icon_counter') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('aio-flip-style', $arrowpress_ult_assets . 'min-css/flip-box.min.css');

                }
                if( stripos( $post_content, '[ultimate_ctation') ) {
                    wp_enqueue_style('utl-ctaction-style');
                }
                if( stripos( $post_content, '[ult_buttons') ) {
                    wp_enqueue_style( 'ult-btn', $arrowpress_ult_assets . 'min-css/btn-min.css' );
                }
                if( stripos( $post_content, '[ultimate_heading') ) {
                    wp_enqueue_style("ultimate-headings-style");
                }
                if( stripos( $post_content, '[ultimate_icons') || stripos( $post_content, '[single_icon')) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('aio-tooltip', $arrowpress_ult_assets . 'min-css/tooltip.min.css');
                }
                if( stripos( $post_content, '[ult_ihover') ) {
                    wp_enqueue_style( 'ult_ihover_css' );
                }
                if( stripos( $post_content, '[ult_hotspot') ) {
                    wp_enqueue_style( 'ult_hotspot_css' );
                    wp_enqueue_style( 'ult_hotspot_tooltipster_css' );
                }
                if( stripos( $post_content, '[bsf-info-box') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('info-box-style', $arrowpress_ult_assets . 'min-css/info-box.min.css');
                }
                if( stripos( $post_content, '[info_circle') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('info-circle', $arrowpress_ult_assets . 'min-css/info-circle.min.css');
                }
                if( stripos( $post_content, '[ultimate_info_banner') ) {
                    wp_enqueue_style('utl-info-banner-style');
                    wp_enqueue_style('ultimate-animate');
                }
                if( stripos( $post_content, '[icon_timeline') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('aio-timeline', $arrowpress_ult_assets . 'min-css/timeline.min.css');
                }
                if( stripos( $post_content, '[just_icon') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('aio-tooltip', $arrowpress_ult_assets . 'min-css/tooltip.min.css');
                }
                if( stripos( $post_content, '[interactive_banner_2') ) {
                    wp_enqueue_style('utl-ib2-style', $arrowpress_ult_assets . 'min-css/ib2-style.min.css');
                }
                if( stripos( $post_content, '[interactive_banner') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('aio-interactive-styles', $arrowpress_ult_assets . 'min-css/interactive-styles.min.css');
                }
                if( stripos( $post_content, '[info_list') ) {
                    wp_enqueue_style('ultimate-animate');
                }
                if( stripos( $post_content, '[ultimate_modal') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('ultimate-modal', $arrowpress_ult_assets . 'min-css/modal.min.css');
                }
                if( stripos( $post_content, '[ultimate_info_table') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style("ultimate-pricing", $arrowpress_ult_assets . 'min-css/pricing.min.css');
                }
                if( stripos( $post_content, '[ultimate_pricing') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style("ultimate-pricing", $arrowpress_ult_assets . 'min-css/pricing.min.css');
                }
                if( stripos( $post_content, '[swatch_container') ) {
                    wp_enqueue_style('swatchbook-css', $arrowpress_ult_assets . 'min-css/swatchbook.min.css');
                }
                if( stripos( $post_content, '[stat_counter') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('stats-counter-style', $arrowpress_ult_assets . 'min-css/stats-counter.min.css');
                }
                if( stripos( $post_content, '[ultimate_video_banner') ) {
                    wp_enqueue_style('ultimate-video-banner-style');
                }
                if( stripos( $post_content, '[ult_dualbutton') ) {
                    wp_enqueue_style('ult-dualbutton');
                }
                if( stripos( $post_content, '[ult_createlink') ) {
                    wp_enqueue_style('ult_cllink');
                }
                if( stripos( $post_content, '[ultimate_img_separator') ) {
                    wp_enqueue_style('ultimate-animate');
                    wp_enqueue_style('ult-easy-separator-style');
                }
            }

            wp_register_script('ultimate-appear', $arrowpress_ult_assets . 'min-js/jquery.appear.min.js', array('jquery'), ULTIMATE_VERSION, true);
            wp_register_script('ultimate-custom', $arrowpress_ult_assets . 'min-js/custom.min.js', $dependancy, ULTIMATE_VERSION, true);
            wp_register_script('ultimate-smooth-scroll', $arrowpress_ult_assets . 'js/SmoothScroll.js', array('jquery'), ULTIMATE_VERSION, true);

            $ultimate_smooth_scroll = get_option('ultimate_smooth_scroll');
            if($ultimate_smooth_scroll == "enable")
                wp_enqueue_script('ultimate-smooth-scroll');

            if(function_exists('vc_is_editor')){
                if(vc_is_editor()){
                    wp_enqueue_style('vc-fronteditor', $arrowpress_ult_assets . 'min-css/vc-fronteditor.min.css');
                }
            }
            $fonts = get_option('smile_fonts');
            if(is_array($fonts))
            {
                foreach($fonts as $font => $info)
                {
                    $style_url = $info['style'];
                    if(strpos($style_url, 'http://' ) !== false) {
                        wp_enqueue_style('bsf-'.$font, $info['style']);
                    } else {
                        $paths = wp_upload_dir();
                        $paths['fonts'] = 'smile_fonts';
                        $paths['fonturl'] = set_url_scheme(trailingslashit($paths['baseurl']).$paths['fonts']);
                        wp_enqueue_style('bsf-'.$font, trailingslashit($paths['fonturl']).$info['style']);
                    }
                }
            }

            if( stripos( $post_content, '[ultimate_google_map') ) {
                if (!wp_script_is('googleapis', 'done')) {
                    echo "<script type='text/javascript' src='https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&ver=".arrowpress_version."'></script>";
                    wp_dequeue_script( 'googleapis' );
                }
            }

        }

        $shortcodes_custom_css = get_post_meta( get_the_ID(), '_wpb_shortcodes_custom_css', true );
        if ( ! empty( $shortcodes_custom_css ) ) {
            $output .= '<style type="text/css" data-type="vc_shortcodes-custom-css">';
            $output .= $shortcodes_custom_css;
            $output .= '</style>';
        }
        ob_start();
        ?>
            <?php
                echo do_shortcode($post_content);      
            ?>
        <?php
        $output .= ob_get_clean();
        if($el_class !=''){
            $output .= '</div>';
        }
        $output .= arrowpress_shortcode_end_block_comment( 'arrowpress_static_block' ) . "\n";
        echo $output;
    }

wp_reset_postdata();