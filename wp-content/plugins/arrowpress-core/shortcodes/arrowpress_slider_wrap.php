<?php

// arrowpress_slider_wrap
add_shortcode('arrowpress_slider_wrap', 'arrowpress_shortcode_slider_wrap');
add_action('vc_build_admin_page', 'arrowpress_load_slider_wrap');
add_action('vc_after_init', 'arrowpress_load_slider_wrap');

function arrowpress_shortcode_slider_wrap($atts, $content = null) {
    ob_start();
    if ($template = arrowpress_shortcode_template('arrowpress_slider_wrap'))
        include $template;
    return ob_get_clean();
}

function arrowpress_load_slider_wrap() {
    $custom_class = arrowpress_vc_custom_class();
    $animation_type = arrowpress_animation_custom();
    vc_map( array(
        'name' => "ArrowPress " . esc_html__('Slider Wrap', 'arrowpress'),
        'base' => 'arrowpress_slider_wrap',
        'category' => esc_html__('ArrowPress', 'arrowpress'),
        'icon' => 'arrowpress_vc_container',
        'is_container' => true,
        'js_view' => 'VcColumnView',
        'weight' => - 50,
        "params" => array(
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Layout", 'arrowpress-core'),
                "param_name" => "layout",
                'std' => 'slide',
                'value' => array(
                    esc_html__('Slick Default', 'arrowpress-core') => 'layout1',
                ),
            ), 
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Slide to scroll", 'arrowpress-core'),
                "param_name" => "item_to_scroll",
                'std' => 1,
                'value' => array(
                    esc_html__('4', 'arrowpress-core') => 4,
                    esc_html__('3', 'arrowpress-core') => 3,
                    esc_html__('2', 'arrowpress-core') => 2,
                    esc_html__('1', 'arrowpress-core') => 1,
                ),
            ),            
			array(
                "type" => "dropdown",
                "heading" => esc_html__("Number Column on Desktop Large (> 1200px)", 'arrowpress-core'),
                "param_name" => "items_desktop_large",
                'std' => 3,
                'value' => array(
                    esc_html__('4', 'arrowpress-core') => 4,
                    esc_html__('3', 'arrowpress-core') => 3,
                    esc_html__('2', 'arrowpress-core') => 2,
                    esc_html__('1', 'arrowpress-core') => 1,
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
            ),
            array(
                "type" => "dropdown",
                "heading" => __("Number Column on Tablets", "arrowpress"),
                "param_name" => "items_tablets",
                'std' => 2,
                'value' => array(
                    esc_html__('4', 'arrowpress-core') => 4,
                    esc_html__('3', 'arrowpress-core') => 3,
                    esc_html__('2', 'arrowpress-core') => 2,
                    esc_html__('1', 'arrowpress-core') => 1,
                ),
            ),
            array(
                "type" => "dropdown",
                "heading" => __("Number Column on Mobile", "arrowpress"),
                "param_name" => "items_mobile",
                'std' => 1,
                'value' => array(
                    esc_html__('4', 'arrowpress-core') => 4,
                    esc_html__('3', 'arrowpress-core') => 3,
                    esc_html__('2', 'arrowpress-core') => 2,
                    esc_html__('1', 'arrowpress-core') => 1,
                ),
            ),
			array(
                "type" => "checkbox",
                "heading" => esc_html__("Auto Play", 'arrowpress-core'),
                "param_name" => "auto_play",
                'std' => 'yes',
                'value' => array(esc_html__('Yes', 'arrowpress-core') => 'yes')
            ), 
            array(
                "type" => "checkbox",
                "heading" => esc_html__("Dots Navigation", 'arrowpress-core'),
                "param_name" => "show_dot",
                'std' => '',
                'value' => array(esc_html__('Yes', 'arrowpress-core') => 'yes')
            ), 
            // array(
            //     'type' => 'attach_images',
            //     'heading' => esc_html__('Add thumbnail images', 'arrowpress-core'),
            //     'param_name' => 'thumb_image',
            //     'value' => '',
            //     'description' => esc_html__( 'Upload image.', 'arrowpress-core' ),
            //     'dependency' => array(
            //         'element' => 'layout',
            //         'value' => array('layout1','layout2'),
            //     ), 
            // ),            
            array(
                "type" => "checkbox",
                "heading" => esc_html__("Navigation Arrows", 'arrowpress-core'),
                "param_name" => "show_nav",
                'std' => 'yes',
                'value' => array(esc_html__('Yes', 'arrowpress-core') => 'yes')
            ),      
            
			/*Icon group
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Icon type", 'arrowpress-core'),
                "param_name" => "type_icon",
                'std' => 'font_icon',
                'value' => array(
                    esc_html__('Image Icon', 'arrowpress-core') => 'image_icon',
                    esc_html__('Icon library', 'arrowpress-core') => 'font_icon',
                ),       
                'group'    => esc_html__("Icon", 'arrowpress-core'),        
            ),            
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Icon library', 'arrowpress-core'),
                'value' => array(
                    esc_html__('Font Awesome', 'arrowpress-core') => 'fontawesome',
                    esc_html__('Font arrowpress', 'arrowpress-core') => 'aprfont',
                    esc_html__('Open Iconic', 'arrowpress-core') => 'openiconic',
                    esc_html__('Typicons', 'arrowpress-core') => 'typicons',
                    esc_html__('Stroke Icons 7', 'arrowpress-core') => 'pestrokefont',
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
            ),*/
            $custom_class,
            array(
                'type' => 'css_editor',
                'heading' => esc_html__( 'Css','arrowpress-core' ),
                'param_name' => 'css',
                'group' => esc_html__( 'Design Option','arrowpress-core' ),
            )            
        )
    ) );

    if (!class_exists('WPBakeryShortCode_arrowpress_slider_wrap')) {
        class WPBakeryShortCode_arrowpress_slider_wrap extends WPBakeryShortCodesContainer {
        }
    }
}