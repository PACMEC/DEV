<?php

// arrowpress_event_list
add_shortcode('arrowpress_event_list', 'arrowpress_shortcode_event_list');
add_action('vc_build_admin_page', 'arrowpress_load_event_list');
add_action('vc_after_init', 'arrowpress_load_event_list');

function arrowpress_shortcode_event_list($atts, $content = null) {
    ob_start();
    if ($template = arrowpress_shortcode_template('arrowpress_event_list'))
        include $template;
    return ob_get_clean();
}

function arrowpress_load_event_list() {
    $custom_class = arrowpress_vc_custom_class();
    $animation_type = arrowpress_animation_custom();
    vc_map( array(
        'name' => "ArrowPress " . esc_html__('Event List', 'arrowpress'),
        'base' => 'arrowpress_event_list',
        'category' => esc_html__('ArrowPress', 'arrowpress'),
        'icon' => 'arrowpress_vc_container',
        'is_container' => true,
        'js_view' => 'VcColumnView',
        'as_parent' => array(
            'only' => 'arrowpress_event_item',
        ),          
        'weight' => - 50,
        "params" => array(
			array(
                "type" => "dropdown",
                "heading" => esc_html__("Layout", 'arrowpress-core'),
                "param_name" => "layout",
                'std' => 'timeline_vertical',
                'value' => array(
                    esc_html__('Timeline Horizontal', 'arrowpress-core') => 'timeline_horizontal',
                    esc_html__('Timeline Vertical', 'arrowpress-core') => 'timeline_vertical',
                ),
                "admin_label" => true,
            ),
            array(
                'type' => 'attach_images',
                'heading' => esc_html__('Add TimeLine Background Image', 'arrowpress-core'),
                'param_name' => 'timeline_image',
                'value' => '',
                'description' => esc_html__( 'Upload image.', 'arrowpress-core' ),
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('timeline_horizontal'),
                ),
            ),    
            array(
                "type" => "textfield",
                "heading" => esc_html__("Number Column on Desktop Large (> 1200px)", 'arrowpress-core'),
                "param_name" => "items_desktop_large",
                'std' => 7,
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('timeline_horizontal'),
                ),                
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("Number Column on Desktop", 'arrowpress-core'),
                "param_name" => "items_desktop",
                'std' => 2,
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('timeline_horizontal'),
                ),                
            ),
            array(
                "type" => "dropdown",
                "heading" => __("Number Column on Tablets", "arrowpress"),
                "param_name" => "items_tablets",
                'std' => 2,
                'value' => array(
                    esc_html__('7', 'arrowpress-core') => 7,
                    esc_html__('6', 'arrowpress-core') => 6,
                    esc_html__('5', 'arrowpress-core') => 5,
                    esc_html__('4', 'arrowpress-core') => 4,
                    esc_html__('3', 'arrowpress-core') => 3,
                    esc_html__('2', 'arrowpress-core') => 2,
                    esc_html__('1', 'arrowpress-core') => 1,
                ),
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('timeline_horizontal'),
                ),                
            ),
            array(
                "type" => "dropdown",
                "heading" => __("Number Column on Mobile", "arrowpress"),
                "param_name" => "items_mobile",
                'std' => 1,
                'value' => array(
                    esc_html__('7', 'arrowpress-core') => 7,
                    esc_html__('6', 'arrowpress-core') => 6,
                    esc_html__('5', 'arrowpress-core') => 5,
                    esc_html__('4', 'arrowpress-core') => 4,
                    esc_html__('3', 'arrowpress-core') => 3,
                    esc_html__('2', 'arrowpress-core') => 2,
                    esc_html__('1', 'arrowpress-core') => 1,
                ),
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('timeline_horizontal'),
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

    if (!class_exists('WPBakeryShortCode_arrowpress_event_list')) {
        class WPBakeryShortCode_arrowpress_event_list extends WPBakeryShortCodesContainer {
        }
    }
}