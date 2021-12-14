<?php

// arrowpress_testimonial
add_shortcode('arrowpress_testimonial', 'arrowpress_shortcode_testimonial');
add_action('vc_build_admin_page', 'arrowpress_load_testimonial_shortcode');
add_action('vc_after_init', 'arrowpress_load_testimonial_shortcode');

function arrowpress_shortcode_testimonial($atts, $content = null) {
    ob_start();
    if ($template = arrowpress_shortcode_template('arrowpress_testimonial'))
        include $template;
    return ob_get_clean();
}

function arrowpress_load_testimonial_shortcode() {
    $animation_type = arrowpress_vc_animation_type();
    $custom_class = arrowpress_vc_custom_class();

    vc_map( array(
        'name' => "ArrowPress" . esc_html__(' Testimonial', 'arrowpress-core'),
        'base' => 'arrowpress_testimonial',
        'category' => esc_html__('ArrowPress', 'arrowpress-core'),
        'icon' => 'arrowpress_vc_icon',
        'weight' => - 50,
        "params" => array(
             array(
                "type" => "dropdown",
                "heading" => esc_html__("Layout", 'arrowpress-core'),
                "param_name" => "layout",
                'std' => 'layout1',
                'value' => array(
                    esc_html__('Layout 1', 'arrowpress-core') => 'layout1',
                    esc_html__('Layout 2', 'arrowpress-core') => 'layout2',
                    esc_html__('Layout 3', 'arrowpress-core') => 'layout3',
                    esc_html__('Layout 4', 'arrowpress-core') => 'layout4',
                    esc_html__('Layout 5', 'arrowpress-core') => 'layout5',
                ),
                "admin_label" => true,
            ),
             array(
                "type" => "dropdown",
                "heading" => esc_html__("Style", 'arrowpress-core'),
                "param_name" => "style",
                'std' => 'style1',
                'value' => array(
                    esc_html__('Layout Default', 'arrowpress-core') => 'style1',
                    esc_html__('Layout Style 2', 'arrowpress-core') => 'style2',
                ),
                'dependency' => array(
                    'element' => 'layout',
                    'value' => 'layout1',
                ),                
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
                "heading" => esc_html__("Description", "arrowpress"),
                "param_name" => "description",
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("Name", 'arrowpress-core'),
                "param_name" => "name_author",
                "admin_label" => true,
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("Job", 'arrowpress-core'),
                "param_name" => "job_author",
                "admin_label" => true,
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1','layout2','layout3','layout4'),
                ),
            ),

            array(
                'type' => 'attach_image',
                'heading' => esc_html__('Image', 'arrowpress-core'),
                'param_name' => 'image_signature',
                'value' => '',
                'description' => esc_html__( 'Upload image signature.', 'arrowpress-core' ),
                 'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1','layout2','layout3','layout4'),
                ),
            ), 
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Ratings", 'arrowpress-core'),
                "param_name" => "ratings",
                'std' => 'ratings',
                'value' => array(
                    esc_html__('Hide stars', 'arrowpress-core') => 'hide_star',
                    esc_html__('5 stars', 'arrowpress-core') => 'five_star',
                    esc_html__('4 stars', 'arrowpress-core') => 'four_star',
                    esc_html__('3 stars', 'arrowpress-core') => 'three_star',
                    esc_html__('2 stars', 'arrowpress-core') => 'two_star',
                    esc_html__('1 stars', 'arrowpress-core') => 'one_star',                    
                ),
                'default' => 'hide_star',
                "description" => esc_html__( "Select ratings", 'arrowpress-core' ),
                'dependency' => array(
                    'element' => 'layout',
                    'value' => 'layout4',
                ), 
            ),                               
            array(
                "type" => "dropdown",
                "heading" => esc_html__( "Testimonial Align", 'arrowpress-core' ),
                "param_name" => "testimonial_align",
                "value" => array(
                    esc_html__('Center', 'arrowpress-core') => 'center',
                    esc_html__('Left', 'arrowpress-core') => 'left',
                    esc_html__('Right', 'arrowpress-core') => 'right',
                    ),
                "description" => esc_html__( "Select testiomonial align.", 'arrowpress-core' )
            ),
            array(
                'type' => 'number',
                'heading' => esc_html__( 'Description font size', 'arrowpress-core' ),
                'param_name' => 'desc_size',
                'group' => 'Typography',
                'description' => 'px',
            ), 
            array(
                'type' => 'number',
                'heading' => esc_html__( 'Description line height', 'arrowpress-core' ),
                'param_name' => 'desc_lh',
                'group' => 'Typography',
                'description' => 'px',
            ),                       
            array(
                'type' => 'checkbox',
                'heading' => __( 'Use custom font family?', 'arrowpress-core' ),
                'param_name' => 'use_theme_fonts',
                'value' => array( __( 'Yes', 'arrowpress-core' ) => 'yes' ),
                'description' => __( 'Use custom font family.', 'arrowpress-core' ),
                'group' => esc_html__('Typography','arrowpress-core'),
            ),             
            array(
                'type' => 'google_fonts',
                'param_name' => 'google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => __( 'Select small heading font family.', 'arrowpress-core' ),
                        'font_style_description' => __( 'Select small heading font styling.', 'arrowpress-core' ),
                    ),
                ),
                'dependency' => array(
                    'element' => 'use_theme_fonts',
                    'value' => 'yes',
                ),
                'group' => esc_html__('Typography','arrowpress-core'),
            ),            
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Description Color', 'arrowpress-core'),
                'param_name' => 'desc_color',
                'admin_label' => true,
                'group' => esc_html__( 'Skin','arrowpress-core' ),
            ),
             array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Job Color', 'arrowpress-core'),
                'param_name' => 'job_color',
                'admin_label' => true,
                'group' => esc_html__( 'Skin','arrowpress-core' ),
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Name Color', 'arrowpress-core'),
                'param_name' => 'name_color',
                'admin_label' => true,
                'group' => esc_html__( 'Skin','arrowpress-core' ),
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Text Background Color', 'arrowpress-core'),
                'param_name' => 'bg_color',
                'admin_label' => true,
                'group' => esc_html__( 'Skin','arrowpress-core' ),
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout5'),
                ),                
            ),               
            $custom_class,
            array(
                'type' => 'css_editor',
                'heading' => esc_html__( 'Css','arrowpress-core' ),
                'param_name' => 'css',
                'group' => esc_html__( 'Design Option','arrowpress-core' ),
            )
        )
    ) );

    if (!class_exists('WPBakeryShortCode_ArrowPress_Testimonial')) {
        class WPBakeryShortCode_ArrowPress_Testimonial extends WPBakeryShortCode {
        }
    }
}