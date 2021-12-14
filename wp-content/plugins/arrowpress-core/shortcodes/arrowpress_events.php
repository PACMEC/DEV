<?php
// Arrowpress_events
add_shortcode('arrowpress_events', 'arrowpress_shortcode_events');
add_action('vc_build_admin_page', 'arrowpress_load_events_shortcode');
add_action('vc_after_init', 'arrowpress_load_events_shortcode');

function arrowpress_shortcode_events($atts, $content = null) {
    ob_start();
    if ($template = arrowpress_shortcode_template('arrowpress_events'))
        include $template;
    return ob_get_clean();
}

function arrowpress_load_events_shortcode() {
    $custom_class = arrowpress_vc_custom_class();
	$order_by_values = arrowpress_vc_woo_order_by();
	$order_way_values = arrowpress_vc_woo_order_way();
	$product_tax = array(
		esc_html__('Product Category','arrowpress-core') => 'events_cat',
		esc_html__('Product Tag','arrowpress-core') => 'events_loaction',
	);
    vc_map( array(
        'name' => "ArrowPress " . esc_html__('Events', 'arrowpress-core'),
        'base' => 'arrowpress_events',
        'category' => esc_html__('ArrowPress', 'arrowpress-core'),
        'icon' => 'arrowpress_vc_icon',
        'weight' => - 50,
        "params" => array(
			array(
                "type" => "textfield",
                "heading" => esc_html__("Posts Count", 'arrowpress-core'),
                "param_name" => "number",
                "value" => "6",
                "admin_label" => true
            ), 
			array(
                'type' => 'dropdown',
                'heading' => __( 'Order by', 'arrowpress-core' ),
                'param_name' => 'orderby',
                'value' => $order_by_values,
                'description' => sprintf( __( 'Select how to sort retrieved products. More at %s.', 'arrowpress-core' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' )
            ),
            array(
                'type' => 'dropdown',
                'heading' => __( 'Order way', 'arrowpress-core' ),
                'param_name' => 'order',
                'value' => $order_way_values,
                'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'arrowpress-core' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' )
            ),
			array(
                "type" => "checkbox",
                "heading" => esc_html__("Show view more", 'arrowpress-core'),
                "param_name" => "show_viewmore",
                'value' => array(esc_html__('Yes', 'arrowpress-core') => 'yes'),
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("View more text", 'arrowpress-core'),
                "param_name" => "viewmore_text",
                'dependency' => array(
                    'element' => 'show_viewmore',
                    'value' => array('yes'),
                ),                
            ), 
            array(
                "type" => "textfield",
                "heading" => esc_html__("View more link", 'arrowpress-core'),
                'description' => esc_html__("By default, the link will be blog archive page.", 'arrowpress-core'),
                "param_name" => "viewmore_link",
                'dependency' => array(
                    'element' => 'show_viewmore',
                    'value' => array('yes'),
                ),                
            ),  
			array(
                "type" => "dropdown",
                "heading" => esc_html__("Button Style", 'arrowpress-core'),
                "param_name" => "btn_style",
                'std' => '',
                'value' => array(
                    esc_html__('Button Default', 'arrowpress-core') => 'btn_style_1',
                    esc_html__('Button Primary', 'arrowpress-core') => 'btn_style_2',
                    esc_html__('Button Highlight', 'arrowpress-core') => 'btn_style_5',
                    esc_html__('Button White', 'arrowpress-core') => 'btn_style_6',
                    esc_html__('Button Black', 'arrowpress-core') => 'btn_style_3',
                    esc_html__('Button Circle', 'arrowpress-core') => 'btn_style_4',
                ),
				'dependency' => array(
                    'element' => 'show_viewmore',
                    'value' => array('yes'),
                ),  
            ),
            array(
                "type" => "vc_link",
                "heading" => esc_html__("Link", 'arrowpress-core'),
                "param_name" => "link",
            ),              
            $custom_class
        )
    ) );

    if (!class_exists('WPBakeryShortCode_Arrowpress_Events')) {
        class WPBakeryShortCode_Arrowpress_Events extends WPBakeryShortCode {
        }
    }
}