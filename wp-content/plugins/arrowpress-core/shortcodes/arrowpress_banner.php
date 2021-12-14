<?php

add_shortcode('arrowpress_banner', 'arrowpress_shortcode_banner');
add_action('vc_build_admin_page', 'arrowpress_load_banner_shortcode');
add_action('vc_after_init', 'arrowpress_load_banner_shortcode');

function arrowpress_shortcode_banner($atts, $content = null) {
    ob_start();
    if ($template = arrowpress_shortcode_template('arrowpress_banner'))
        include $template;
    return ob_get_clean();
}

function arrowpress_load_banner_shortcode() {
    $custom_class = arrowpress_vc_custom_class();
    $animation_type = arrowpress_animation_custom();

    vc_map( array(
        'name' => "ArrowPress " . esc_html__('Banner', 'arrowpress-core'),
        'base' => 'arrowpress_banner',
        'category' => esc_html__('ArrowPress', 'arrowpress-core'),
        'icon' => 'arrowpress_vc_icon',
        'weight' => - 50,
        "params" => array(
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Layout", 'arrowpress-core'),
                "param_name" => "layout",
                'std' => 'banner_style_1',
                'value' => array(
                    esc_html__('Banner type 1', 'arrowpress-core') => 'banner_style_1',
                    esc_html__('Banner type 2', 'arrowpress-core') => 'banner_style_2',
                ),
                "admin_label" => true,
            ),    
            array(
                'type' => 'attach_image',
                'heading' => esc_html__('Image', 'arrowpress-core'),
                'param_name' => 'image',
                'value' => '',
                'description' => esc_html__( 'Upload image.', 'arrowpress-core' ),
            ),
            array(
                "type" => "textarea",
                "heading" => esc_html__("Big Title", 'arrowpress-core'),
                "param_name" => "big_title",
                "admin_label" => true,
            ), 
            array(
                "type" => "dropdown",
                "heading" => esc_html__( "Text Align", 'arrowpress-core' ),
                "param_name" => "text_align",
                'std' => 'center',
                "value" => array(
                    esc_html__('Center', 'arrowpress-core') => 'center',
                    esc_html__('Left', 'arrowpress-core') => 'left',
                    esc_html__('Right', 'arrowpress-core') => 'right',
                    ),
                "description" => esc_html__( "Select heading align.", 'arrowpress-core' )
            ),
			array(
                "type" => "textfield",
                "heading" => esc_html__("Link", 'arrowpress-core'),
                "param_name" => "link_text",
                'value' => esc_html__( 'lnr lnr-arrow-right', 'arrowpress-core' ),
                'description' => wp_kses(__('Add icon class you want here. You can find a lot of icons in these links <a target="_blank" href="http://fontawesome.io/icons/">Awesome icon</a> or <a target="_blank" href="https://linearicons.com/">Linearicons </a>, <a target="_blank" href="http://themes-pixeden.com/font-demos/7-stroke/">Pe stroke icon7 </a>','arrowpress-core'),array(
                    'a' => array(
                        'href'=>array(),
                        'target' => array(),
                        ),
                )),
            ),
            array(
                "type" => "vc_link",
                "heading" => esc_html__("Link", 'arrowpress-core'),
                "param_name" => "link",              
            ),
        //Skin  
			array(
                'type' => 'textfield',
                'heading' => esc_html__('Input height box', 'arrowpress-core'),
                'param_name' => 'height_box',
                'group' => esc_html__( 'Skin','arrowpress-core' ),    
				"description" => esc_html__( "px", 'arrowpress-core' ),                   
            ),
			array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Background Color', 'arrowpress-core'),
                'param_name' => 'bg1_color',
                'group' => esc_html__( 'Skin','arrowpress-core' )                          
            ),
            array(
                'type' => 'checkbox',
                'heading' => esc_html__("Enable Default Overlay", 'arrowpress-core'),
                'param_name' => 'en_overlay',
                'std' => '',
                'value' => array( esc_html__( 'Yes', 'arrowpress-core' ) => 'yes' ),
                'group' => esc_html__( 'Skin','arrowpress-core' ),
            ),   
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Background Color on Hover', 'arrowpress-core'),
                'param_name' => 'bg_hover_color',
                'group' => esc_html__( 'Skin','arrowpress-core' ),             
            ),  	
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Title Color', 'arrowpress-core'),
                'param_name' => 'title_color',
                'group' => esc_html__( 'Skin','arrowpress-core' ),
            ),  
			array(
                'type' => 'textfield',
                'heading' => esc_html__('Title Font Size', 'arrowpress-core'),
                'param_name' => 'title_size',
                'group' => esc_html__( 'Skin','arrowpress-core' ),
				"description" => esc_html__( "px", 'arrowpress-core' ),
            ),  
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Small title and description Color', 'arrowpress-core'),
                'param_name' => 'sm_title_color',
                'group' => esc_html__( 'Skin','arrowpress-core' ),
            ),   
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Text color', 'arrowpress-core'),
                'param_name' => 'text_color',
                'group' => esc_html__( 'Skin','arrowpress-core' ),              
            ),                                  
            $custom_class,
            array(
                'type' => 'checkbox',
                'heading' => esc_html__("Enable Animation", 'arrowpress-core'),
                'param_name' => 'item_delay',
                'std' => '',
                'value' => array( esc_html__( 'Yes', 'arrowpress-core' ) => 'yes' ),
                'group' => 'Animation',
                'admin_label' => true,
            ),
            array(
                "type" => "dropdown",
                "heading" => esc_html__( "Animation Type", 'arrowpress-core' ),
                "param_name" => "animation_type",
                "value" => $animation_type,
                "description" => esc_html__( "Select Animation Style.", 'arrowpress-core' ),
                'dependency' => array(
                    'element' => 'item_delay',
                    'value' => 'yes',
                ),
                'group' => 'Animation'
            ),
            array(
                "type" => "textfield",
                "class" => "",
                "heading" => esc_html__("Animation Delay", 'arrowpress-core'),
                "description" => esc_html__( "Enter Animation Delay.", 'arrowpress-core' ),
                'dependency' => array(
                    'element' => 'item_delay',
                    'value' => 'yes',
                ),
                "param_name" => "animation_delay",
                "value" => 500,
                'group' => 'Animation'
            ),
            array(
                'type' => 'css_editor',
                'heading' => esc_html__( 'CSS box', 'arrowpress-core' ),
                'param_name' => 'css',
                'group' => esc_html__( 'Design Options', 'arrowpress-core' ),
            ),
        )
    ) );
    if (!class_exists('WPBakeryShortCode_ArrowPress_Banner')) {
        class WPBakeryShortCode_ArrowPress_Banner extends WPBakeryShortCode {
        }
    }
}