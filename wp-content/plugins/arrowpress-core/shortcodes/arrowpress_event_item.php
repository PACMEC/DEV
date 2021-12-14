<?php

add_shortcode('arrowpress_event_item', 'arrowpress_shortcode_event_item');
add_action('vc_build_admin_page', 'arrowpress_load_event_item_shortcode');
add_action('vc_after_init', 'arrowpress_load_event_item_shortcode');

function arrowpress_shortcode_event_item($atts, $content = null) {
    ob_start();
    if ($template = arrowpress_shortcode_template('arrowpress_event_item'))
        include $template;
    return ob_get_clean();
}

function arrowpress_load_event_item_shortcode() {
    $custom_class = arrowpress_vc_custom_class();
    $animation_type = arrowpress_animation_custom();

    vc_map( array(
        'name' => "ArrowPress" . esc_html__(' Event Item', 'arrowpress-core'),
        'base' => 'arrowpress_event_item',
        'category' => esc_html__('ArrowPress', 'arrowpress-core'),
        'icon' => 'arrowpress_vc_icon',
        'weight' => - 50,
        'as_child' => array(
            'only' => 'arrowpress_event_list', // Only root
        ),        
        'show_settings_on_create' => true,
        "params" => array( 
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Layout Item", 'arrowpress-core'),
                "param_name" => "layout_item",
                'std' => 'timeline_vertical',
                'value' => array(
                    esc_html__('Timeline Horizontal', 'arrowpress-core') => 'item_horizontal',
                    esc_html__('Timeline Vertical', 'arrowpress-core') => 'item_vertical',
                ),
                "admin_label" => true,
            ),
			array(
                'type' => 'attach_images',
                'heading' => esc_html__('Image', 'arrowpress-core'),
                'param_name' => 'vertical_image',
                'value' => '',
                'description' => esc_html__( 'Upload image.', 'arrowpress-core' ),
                'dependency' => array(
                    'element' => 'layout_item',
                    'value' => array('item_vertical'),
                ),
            ),   
			array(
                "type" => "textfield",
                "heading" => esc_html__("Title", 'arrowpress-core'),
                "param_name" => "title",
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("Date", 'arrowpress-core'),
                "param_name" => "date",
            ),            
            // array(
            //     "type" => "textarea",
            //     "heading" => esc_html__("Description", 'arrowpress-core'),
            //     "param_name" => "desc",
            //     // 'dependency' => array(
            //     //     'element' => 'layout',
            //     //     'value' => array('layout2','layout3'),
            //     // ),
            // ),   
            array(
                "type" => "textfield",
                "heading" => esc_html__("Event Link", 'arrowpress-core'),
                "param_name" => "link",               
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

    if (!class_exists('WPBakeryShortCode_arrowpress_Event_Item')) {
        class WPBakeryShortCode_arrowpress_Event_Item extends WPBakeryShortCode {
        }
    }
}