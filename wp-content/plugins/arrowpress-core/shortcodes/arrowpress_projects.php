<?php
// Arrowpress_projects
add_shortcode('arrowpress_projects', 'arrowpress_shortcode_projects');
add_action('vc_build_admin_page', 'arrowpress_load_projects_shortcode');
add_action('vc_after_init', 'arrowpress_load_projects_shortcode');

function arrowpress_shortcode_projects($atts, $content = null) {
    ob_start();
    if ($template = arrowpress_shortcode_template('arrowpress_projects'))
        include $template;
    return ob_get_clean();
}

function arrowpress_load_projects_shortcode() {
    $custom_class = arrowpress_vc_custom_class();
    vc_map( array(
        'name' => "ArrowPress " . esc_html__('Projects', 'arrowpress-core'),
        'base' => 'arrowpress_projects',
        'category' => esc_html__('ArrowPress', 'arrowpress-core'),
        'icon' => 'arrowpress_vc_icon',
        'weight' => - 50,
        "params" => array(
			array(
                "type" => "dropdown",
                "heading" => esc_html__( "Layout", 'arrowpress-core' ),
                "param_name" => "layout",
                'std' => 'layout1',
				"admin_label" => true,
                "value" => array(
                    esc_html__('Layout 1', 'arrowpress-core') => 'layout1',
                    esc_html__('Layout 2', 'arrowpress-core') => 'layout2',
                    ),
            ),
			array(
                "type" => "textfield",
                "heading" => esc_html__("Posts Count", 'arrowpress-core'),
                "param_name" => "number",
                "value" => "6",
                "admin_label" => true
            ), 
			array(
                "type" => "checkbox",
                "heading" => esc_html__("Show filter", 'arrowpress-core'),
                "param_name" => "show_filter",
                'std' => '',
                'value' => array(esc_html__('Yes', 'arrowpress-core') => 'yes')
            ),
			array(
                "type" => "checkbox",
                "heading" => esc_html__("Show button all filter", 'arrowpress-core'),
                "param_name" => "show_all_filter",
                'std' => 'yes',
                'value' => array(esc_html__('Yes', 'arrowpress-core') => 'yes'),
				"dependency" => array(
                    'element' => 'show_filter',
                    'value' => array('yes')
                ),
            ),
			array(
                "type" => "textfield",
                "heading" => esc_html__("ID Category Parent", "arrowpress-core"),
                "param_name" => "category_parent",
                "value" => 0,
                "admin_label" => true,
				"dependency" => array(
                    'element' => 'show_filter',
                    'value' => array('yes')
                ),
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("ID excluded categories", "arrowpress-core"),
                "param_name" => "exclude_cat",
				"dependency" => array(
                    'element' => 'show_filter',
                    'value' => array('yes')
                ),
            ),
			array(
                "type" => "textfield",
                "heading" => esc_html__("Text Learn more", 'arrowpress-core'),
                "param_name" => "learn_more",
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout2'),
                ),
            ),
			array(
                "type" => "textfield",
                "heading" => esc_html__(" Number of words in post description to display", 'arrowpress-core'),
                "param_name" => "trim_length",
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout2'),
                ),                
            ),
			array(
                "type" => "textfield",
                "heading" => esc_html__(" String to show after trimmed description", 'arrowpress-core'),
                "param_name" => "more",
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout2'),
                ),                
            ),   
            array(
                "type" => "textarea",
                "heading" => esc_html__("Title", 'arrowpress-core'),
                "param_name" => "title",
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1'),
                ),
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
                "description" => esc_html__( "Select heading align.", 'arrowpress-core' ),
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1'),
                ),
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("Button Text", 'arrowpress-core'),
                "param_name" => "btn_text",
                'value' => esc_html__( 'Find out more', 'arrowpress-core' ),
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1'),
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
                    'element' => 'layout',
                    'value' => array('layout1'),
                ),
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("View more link", 'arrowpress-core'),
                'description' => esc_html__("By default, the link will be blog archive page.", 'arrowpress-core'),
                "param_name" => "viewmore_link",               
            ), 
			array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Title Color', 'arrowpress-core' ),
                'param_name' => 'title_color',
                'description' => esc_html__( 'Select title color.', 'arrowpress-core' ),
                'group' => 'Color',
            ),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__( 'Background Color', 'arrowpress-core' ),
				'param_name' => 'bg_color_content',
				'description' => esc_html__( 'Select background color content.', 'arrowpress-core' ),
				'group' => 'Color',
			),                    
            $custom_class
        )
    ) );

    if (!class_exists('WPBakeryShortCode_Arrowpress_Projects')) {
        class WPBakeryShortCode_Arrowpress_Projects extends WPBakeryShortCode {
        }
    }
}