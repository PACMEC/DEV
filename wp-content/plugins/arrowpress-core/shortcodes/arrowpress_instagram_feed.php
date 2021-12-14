<?php

// arrowpress instagram feed
add_shortcode('arrowpress_instagram_feed', 'arrowpress_shortcode_instagram_feed');
add_action('vc_build_admin_page', 'arrowpress_load_instagram_feed_shortcode');
add_action('vc_after_init', 'arrowpress_load_instagram_feed_shortcode');

function arrowpress_shortcode_instagram_feed($atts, $content = null) {
    ob_start();
    if ($template = arrowpress_shortcode_template('arrowpress_instagram_feed'))
        include $template;
    return ob_get_clean();
}

function arrowpress_load_instagram_feed_shortcode() {
    $custom_class = arrowpress_vc_custom_class();
    vc_map( array(
        'name' => "Arrowpress " . esc_html__('Instagram Feed', 'arrowpress-core'),
        'base' => 'arrowpress_instagram_feed',
        'category' => esc_html__('ArrowPress', 'arrowpress-core'),
        'icon' => 'arrowpress_vc_icon',
        'weight' => - 50,
        "params" => array(
			array(
                "type" => "dropdown",
                "heading" => esc_html__("Layout", 'arrowpress-core'),
                "param_name" => "layout",
                'std' => 'layout1',
				"admin_label" => true,
                'value' => array(
                    esc_html__('Instagram Slider 1', 'arrowpress-core') => 'layout1',
                    esc_html__('Instagram Slider 2', 'arrowpress-core') => 'layout2',
					esc_html__('Instagram Slider 3', 'arrowpress-core') => 'layout4',
                    esc_html__('Instagram Slider 4', 'arrowpress-core') => 'layout5',
                    esc_html__('Instagram Masonry', 'arrowpress-core') => 'layout3',
                    esc_html__('Instagram Grid', 'arrowpress-core') => 'layout6',
                    esc_html__('Instagram Packery', 'arrowpress-core') => 'layout7',
                ),
            ), 
			array(
                "type" => "textarea_html",
                "heading" => esc_html__("Title", 'arrowpress-core'),
                "param_name" => "content",
				'dependency' => array(
                    'element' => 'layout',
					"admin_label" => true,
                    'value' => array('layout1','layout2','layout3','layout5'),
                ), 
            ),
			array(
                "type" => "textfield",
                "heading" => esc_html__("Title", 'arrowpress-core'),
                "param_name" => "title",
				"admin_label" => true,
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout6'),
                ), 
            ),
            array(
                "type" => "number",
                "heading" => esc_html__("Per page", 'arrowpress-core'),
                "param_name" => "per_page",
                'default' => '9',
                'description' => esc_html__('This field  determines how many blogs to show on the page', 'arrowpress-core')
            ),
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Number Column on Desktop Large (> 1365px)", 'arrowpress-core'),
                "param_name" => "items_desktop_large1",
                'std' => 4,
                'value' => array(
                    esc_html__('6', 'arrowpress-core') => 6,
                    esc_html__('5', 'arrowpress-core') => 5,
                    esc_html__('4', 'arrowpress-core') => 4,
                    esc_html__('3', 'arrowpress-core') => 3,
                    esc_html__('2', 'arrowpress-core') => 2,
                    esc_html__('1', 'arrowpress-core') => 1,
                ),
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1','layout2','layout4','layout5'),
                ),                 
            ),            
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Number Column on Desktop Large (> 1200px)", 'arrowpress-core'),
                "param_name" => "items_desktop_large",
                'std' => 4,
                'value' => array(
                    esc_html__('6', 'arrowpress-core') => 6,
                    esc_html__('5', 'arrowpress-core') => 5,
                    esc_html__('4', 'arrowpress-core') => 4,
                    esc_html__('3', 'arrowpress-core') => 3,
                    esc_html__('2', 'arrowpress-core') => 2,
                    esc_html__('1', 'arrowpress-core') => 1,
                ),
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1','layout2','layout4','layout5'),
                ),                 
            ),
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Number Column on Desktop", 'arrowpress-core'),
                "param_name" => "items_desktop",
                'std' => 2,
                'value' => array(
                    esc_html__('4', 'arrowpress-core') => 4,
                    esc_html__('3', 'arrowpress-core') => 3,
                    esc_html__('2', 'arrowpress-core') => 2,
                    esc_html__('1', 'arrowpress-core') => 1,
                ),
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1','layout2','layout4','layout5'),
                ),                 
            ),
            array(
                "type" => "dropdown",
                "heading" => __("Number Column on Tablets", 'arrowpress-core'),
                "param_name" => "items_tablets",
                'std' => 2,
                'value' => array(
                    esc_html__('4', 'arrowpress-core') => 4,
                    esc_html__('3', 'arrowpress-core') => 3,
                    esc_html__('2', 'arrowpress-core') => 2,
                    esc_html__('1', 'arrowpress-core') => 1,
                ),
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1','layout2','layout4','layout5'),
                ),                  
            ),
            array(
                "type" => "dropdown",
                "heading" => __("Number Column on Mobile", 'arrowpress-core'),
                "param_name" => "items_mobile",
                'std' => 1,
                'value' => array(
                    esc_html__('4', 'arrowpress-core') => 4,
                    esc_html__('3', 'arrowpress-core') => 3,
                    esc_html__('2', 'arrowpress-core') => 2,
                    esc_html__('1', 'arrowpress-core') => 1,
                ),
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1','layout2','layout4','layout5'),
                ),                  
            ),
			array(
                "type" => "checkbox",
                "heading" => esc_html__("Auto Play", 'arrowpress-core'),
                "param_name" => "auto_play",
                'std' => 'yes',
                'value' => array(esc_html__('Yes', 'arrowpress-core') => 'yes'),
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1','layout2','layout4','layout5'),
                ),  
            ),  
			array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Title Color', 'arrowpress-core' ),
                'param_name' => 'color_default',
                'description' => esc_html__( 'Select color.', 'arrowpress-core' ),
                'group' => esc_html__('Skin','arrowpress-core'),
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1','layout2','layout3','layout5','layout6'),
                ), 
            ), 
			array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Border Title Color', 'arrowpress-core' ),
                'param_name' => 'color_border',
                'description' => esc_html__( 'Select color.', 'arrowpress-core' ),
                'group' => esc_html__('Skin','arrowpress-core'),
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1','layout2','layout3','layout5','layout6'),
                ), 
            ), 
			array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Background Color', 'arrowpress-core' ),
                'param_name' => 'color_bg',
                'description' => esc_html__( 'Select color.', 'arrowpress-core' ),
                'group' => esc_html__('Skin','arrowpress-core'),
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1','layout2','layout3','layout5','layout6'),
                ), 
            ),
            // Icon for layout 7
        //Icon group
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Icon type", 'arrowpress-core'),
                "param_name" => "type_icon",
                'std' => 'font_icon',
                'value' => array(
                    esc_html__('Image Icon', 'arrowpress-core') => 'image_icon',
                    esc_html__('Icon library', 'arrowpress-core') => 'font_icon',
                ),
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout7',),
                ),         
                'group'    => esc_html__("Icon", 'arrowpress-core'),        
            ),                        
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Icon library', 'arrowpress-core'),
                'value' => array(
                    esc_html__('Font Awesome', 'arrowpress-core') => 'fontawesome',
                    // esc_html__('Font arrowpress', 'arrowpress-core') => 'aprfont',
                    esc_html__('Open Iconic', 'arrowpress-core') => 'openiconic',
                    esc_html__('Typicons', 'arrowpress-core') => 'typicons',
                    esc_html__('Stroke Icons 7', 'arrowpress-core') => 'pestrokefont',
                    esc_html__('Themify Icons', 'arrowpress-core') => 'themifyfont',
                    esc_html__('Entypo', 'arrowpress-core') => 'entypo',
                    esc_html__('Linecons', 'arrowpress-core') => 'linecons',
                ),
                'dependency' => array(
                    'element' => 'type_icon',
                    'value' => 'font_icon',
                ),                
                'param_name' => 'icon_type',
                'description' => esc_html__('Select icon library.', 'arrowpress-core'),
                'group'    => esc_html__("Icon", 'arrowpress-core'),       
            ),
            array(
                'type' => 'iconpicker',
                'heading' => esc_html__('Icon', 'arrowpress-core'),
                'param_name' => 'icon_themifyfont',
                'settings' => array(
                    'emptyIcon' => false, // default true, display an "EMPTY" icon?
                    'type' => 'themifyfont',
                    'iconsPerPage' => 4000, // default 100, how many icons per/page to display
                ),
                'dependency' => array(
                    'element' => 'icon_type',
                    'value' => 'themifyfont',
                ),
                'weight' => 9,
                'description' => esc_html__('Select icon from library.', 'arrowpress-core'),
                'group'    => esc_html__("Icon", 'arrowpress-core'),       
            ),            
            array(
                'type' => 'iconpicker',
                'heading' => esc_html__('Icon', 'arrowpress-core'),
                'param_name' => 'icon_pestrokefont',
                'settings' => array(
                    'emptyIcon' => false, // default true, display an "EMPTY" icon?
                    'type' => 'pestrokefont',
                    'iconsPerPage' => 4000, // default 100, how many icons per/page to display
                ),
                'dependency' => array(
                    'element' => 'icon_type',
                    'value' => 'pestrokefont',
                ),
                'weight' => 9,
                'description' => esc_html__('Select icon from library.', 'arrowpress-core'),
                'group'    => esc_html__("Icon", 'arrowpress-core'),       
            ),
            array(
                'type' => 'iconpicker',
                'heading' => esc_html__( 'Icon', 'arrowpress-core' ),
                'param_name' => 'icon_aprfont',
                'settings' => array(
                    'emptyIcon' => false, // default true, display an "EMPTY" icon?
                    'type' => 'aprfont',
                    'iconsPerPage' => 4000, // default 100, how many icons per/page to display
                ),
                 'dependency' => array(
                    'element' => 'icon_type',
                    'value' => 'aprfont',
                ),                
                'description' => esc_html__( 'Select icon from library.', 'arrowpress-core' ),
                'group'    => esc_html__("Icon", 'arrowpress-core'),       
            ),

            array(
                'type' => 'iconpicker',
                'heading' => esc_html__('Icon', 'arrowpress-core'),
                'param_name' => 'icon_fontawesome',
                'settings' => array(
                    'emptyIcon' => false, // default true, display an "EMPTY" icon?
                    'iconsPerPage' => 4000, // default 100, how many icons per/page to display
                ),
                 'dependency' => array(
                    'element' => 'icon_type',
                    'value' => 'fontawesome',
                ), 
                'description' => esc_html__('Select icon from library.', 'arrowpress-core'),
                'group'    => esc_html__("Icon", 'arrowpress-core'),        
            ),
            array(
                'type' => 'iconpicker',
                'heading' => esc_html__('Icon', 'arrowpress-core'),
                'param_name' => 'icon_openiconic',
                'settings' => array(
                    'emptyIcon' => false, // default true, display an "EMPTY" icon?
                    'type' => 'openiconic',
                    'iconsPerPage' => 4000, // default 100, how many icons per/page to display
                ),
                 'dependency' => array(
                    'element' => 'icon_type',
                    'value' => 'openiconic',
                ), 
                'description' => esc_html__('Select icon from library.', 'arrowpress-core'),
                'group'    => esc_html__("Icon", 'arrowpress-core'),        
            ),
            array(
                'type' => 'iconpicker',
                'heading' => esc_html__('Icon', 'arrowpress-core'),
                'param_name' => 'icon_typicons',
                'settings' => array(
                    'emptyIcon' => false, // default true, display an "EMPTY" icon?
                    'type' => 'typicons',
                    'iconsPerPage' => 4000, // default 100, how many icons per/page to display
                ),
                 'dependency' => array(
                    'element' => 'icon_type',
                    'value' => 'typicons',
                ), 
                'description' => esc_html__('Select icon from library.', 'arrowpress-core'),
                'group'    => esc_html__("Icon", 'arrowpress-core'),        
            ),
            array(
                'type' => 'iconpicker',
                'heading' => esc_html__('Icon', 'arrowpress-core'),
                'param_name' => 'icon_entypo',
                'settings' => array(
                    'emptyIcon' => false, // default true, display an "EMPTY" icon?
                    'type' => 'entypo',
                    'iconsPerPage' => 4000, // default 100, how many icons per/page to display
                ),
                'dependency' => array(
                    'element' => 'icon_type',
                    'value' => 'entypo',
                ), 
                'description' => esc_html__('Select icon from library.', 'arrowpress-core'),
                'group'    => esc_html__("Icon", 'arrowpress-core'),        
            ),
            array(
                'type' => 'iconpicker',
                'heading' => esc_html__('Icon', 'arrowpress-core'),
                'param_name' => 'icon_linecons',
                'settings' => array(
                    'emptyIcon' => false, // default true, display an "EMPTY" icon?
                    'type' => 'linecons',
                    'iconsPerPage' => 4000, // default 100, how many icons per/page to display
                ),
                 'dependency' => array(
                    'element' => 'icon_type',
                    'value' => 'linecons',
                ), 
                'description' => esc_html__('Select icon from library.', 'arrowpress-core'),
                'group'    => esc_html__("Icon", 'arrowpress-core'),        
            ),            
            array(
                'type' => 'attach_image',
                'heading' => esc_html__('Image logo', 'arrowpress-core'),
                'param_name' => 'image',
                'value' => '',
                'description' => esc_html__( 'Upload image logo.', 'arrowpress-core' ),
                "dependency" => array(
                    'element' => 'type_icon',
                    'value' => array('image_icon')
                ),
                'group'    => esc_html__("Icon", 'arrowpress-core'),        
            ),  
            array(
                "type" => "number",
                "heading" => esc_html__("Icon font size", 'arrowpress-core'),
                "param_name" => "icon_size",
                'dependency' => array(
                    'element' => 'type_icon',
                    'value' => 'font_icon',
                ), 
                'group'    => esc_html__("Icon", 'arrowpress-core'),     
            ),    
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Icon color', 'arrowpress-core'),
                'param_name' => 'icon_color',
                'group'    => esc_html__("Icon", 'arrowpress-core')
            ),                           
            array(
                'type' => 'attach_image',
                'heading' => esc_html__('Image Background', 'arrowpress-core'),
                'param_name' => 'image_bg',
                'value' => '',
                'description' => esc_html__( 'Upload image background.', 'arrowpress-core' ),
                "dependency" => array(
                    'element' => 'layout',
                    'value' => array('layout7')
                ),        
            ),  
            array(
                'type' => 'attach_image',
                'heading' => esc_html__('Second image background', 'arrowpress-core'),
                'param_name' => 'image_bg2',
                'value' => '',
                'description' => esc_html__( 'Upload image background.', 'arrowpress-core' ),
                "dependency" => array(
                    'element' => 'layout',
                    'value' => array('layout7')
                ),        
            ),                      
            $custom_class
        )
    ) );

    if (!class_exists('WPBakeryShortCode_Arrowpress_Instagram_Feed')) {
        class WPBakeryShortCode_Arrowpress_Instagram_Feed extends WPBakeryShortCode {
        }
    }
}