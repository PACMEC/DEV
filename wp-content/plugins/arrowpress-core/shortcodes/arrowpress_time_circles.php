<?php
// Arrowpress_time_circles
add_shortcode('arrowpress_time_circles', 'arrowpress_shortcode_time_circles');
add_action('vc_build_admin_page', 'arrowpress_load_time_circles_shortcode');
add_action('vc_after_init', 'arrowpress_load_time_circles_shortcode');

function arrowpress_shortcode_time_circles($atts, $content = null) {
    ob_start();
    if ($template = arrowpress_shortcode_template('arrowpress_time_circles'))
        include $template;
    return ob_get_clean();
}

function arrowpress_load_time_circles_shortcode() {
    $custom_class = arrowpress_vc_custom_class();
    vc_map( array(
        'name' => "ArrowPress " . esc_html__('Time Cirlces', 'arrowpress-core'),
        'base' => 'arrowpress_time_circles',
        'category' => esc_html__('ArrowPress', 'arrowpress-core'),
        'icon' => 'arrowpress_vc_icon',
        'weight' => - 50,
        "params" => array(
			array(
                "type" => "textfield",
                "heading" => esc_html__("Target Time For Countdown", 'arrowpress-core'),
                "param_name" => "times",
                "value" => "2019-01-01",
                "admin_label" => true
            ), 
			array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Timer Digit Background Color', 'arrowpress-core' ),
                'param_name' => 'timer_bgcolor',
				'value' => "#fff",
                'description' => esc_html__( 'Select background color.', 'arrowpress-core' ),
            ),
			array(
                "type" => "textfield",
                "heading" => esc_html__("Timer Digit Border Width", 'arrowpress-core'),
                "param_name" => "times_border",
                "value" => "0.1",
            ), 
			array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Line Circle Background Color', 'arrowpress-core' ),
                'param_name' => 'line_bgcolor',
				'value' => "#3a90f4",
                'description' => esc_html__( 'Select background color.', 'arrowpress-core' ),
            ),
			array(
                "type" => "textfield",
                "heading" => esc_html__("Line Circle Border Width", 'arrowpress-core'),
                "param_name" => "line_border",
                "value" => "0.03",
            ),    
			array(
                "type" => "textfield",
                "heading" => esc_html__("Days", 'arrowpress-core'),
                "param_name" => "days",
                "value" => "days",
				'group' => 'Strings Translation',
            ), 
			array(
                "type" => "textfield",
                "heading" => esc_html__("Hours", 'arrowpress-core'),
                "param_name" => "hours",
                "value" => "hours",
				'group' => 'Strings Translation',
            ), 
			array(
                "type" => "textfield",
                "heading" => esc_html__("Minutes", 'arrowpress-core'),
                "param_name" => "mins",
                "value" => "mins",
				'group' => 'Strings Translation',
            ), 
			array(
                "type" => "textfield",
                "heading" => esc_html__("Seconds", 'arrowpress-core'),
                "param_name" => "secs",
                "value" => "secs",
				'group' => 'Strings Translation',
            ), 
			array(
                "type" => "textfield",
                "heading" => esc_html__("Text font size", 'arrowpress-core'),
                "param_name" => "text_size",
                "value" => "0.07",
				'group' => 'Typography',
            ), 
			array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Text Color', 'arrowpress-core' ),
                'param_name' => 'text_bgcolor',
				'value' => "#fff",
                'description' => esc_html__( 'Select color.', 'arrowpress-core' ),
				'group' => 'Typography',
            ),
			array(
                "type" => "textfield",
                "heading" => esc_html__("Number font size", 'arrowpress-core'),
                "param_name" => "number_size",
                "value" => "0.28",
				'group' => 'Typography',
            ), 
			array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Number Color', 'arrowpress-core' ),
                'param_name' => 'number_bgcolor',
				'value' => "#fff",
                'description' => esc_html__( 'Select color.', 'arrowpress-core' ),
				'group' => 'Typography',
            ),
            $custom_class
        )
    ) );

    if (!class_exists('WPBakeryShortCode_Arrowpress_Time_Circles')) {
        class WPBakeryShortCode_Arrowpress_Time_Circles extends WPBakeryShortCode {
        }
    }
}