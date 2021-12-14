<?php

add_shortcode('arrowpress_blog', 'arrowpress_shortcode_blog');
add_action('vc_build_admin_page', 'arrowpress_load_blog_shortcode');
add_action('vc_after_init', 'arrowpress_load_blog_shortcode');

function arrowpress_shortcode_blog($atts, $content = null) {
    ob_start();
    if ($template = arrowpress_shortcode_template('arrowpress_blog'))
        include $template;
    return ob_get_clean();
}

function arrowpress_load_blog_shortcode() {
    $custom_class = arrowpress_vc_custom_class();
    $order_by_values = arrowpress_vc_woo_order_by();
    $order_way_values = arrowpress_vc_woo_order_way();
    vc_map( array(
        'name' => "ArrowPress " . esc_html__('Blog', 'arrowpress-core'),
        'base' => 'arrowpress_blog',
        'category' => esc_html__('ArrowPress', 'arrowpress-core'),
        'icon' => 'arrowpress_vc_icon',
        'weight' => - 50,
        "params" => array(
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Layout", 'arrowpress-core'),
                "param_name" => "layout",
                'std' => 'grid_style_1',
                'value' => array(
                    esc_html__('Grid Style 1', 'arrowpress-core') => 'grid_style_1',
                    esc_html__('Grid Style 2', 'arrowpress-core') => 'grid_style_2',
                    esc_html__('Grid Style 3', 'arrowpress-core') => 'grid_style_3',
                    esc_html__('Grid Style 4', 'arrowpress-core') => 'list_style_1',
                    esc_html__('Grid Style 5', 'arrowpress-core') => 'grid_style_6',
                    esc_html__('Packery', 'arrowpress-core') => 'packery_style_2',
                    esc_html__('Packery style 1', 'arrowpress-core') => 'packery_style_3',
                    esc_html__('Small List', 'arrowpress-core') => 'small_list',
                ),
                "admin_label" => true,
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("Posts Count", 'arrowpress-core'),
                "param_name" => "number",
                "value" => "3",
                "admin_label" => true
            ), 
            
            array(
                "type" => "textfield",
                "heading" => esc_html__(" Number of words in post description to display", 'arrowpress-core'),
                "param_name" => "trim_length",
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('grid_style_1','grid_style_2','grid_style_3','grid_style_4','grid_style_5',
                        'packery_style_1','list_style_1','grid_style_6','list_style_2','packery_style_2','packery_style_3'),
                ),                
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__(" String to show after trimmed description", 'arrowpress-core'),
                "param_name" => "more",
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('grid_style_1','grid_style_2','grid_style_3','grid_style_4','grid_style_5',
                        'packery_style_1','list_style_1','grid_style_6','list_style_2','packery_style_2','packery_style_3'),
                ),                
            ),                         
			array(
                "type" => "checkbox",
                "heading" => esc_html__("Remove Sticky Post", 'arrowpress-core'),
                "param_name" => "sticky_post",
				'std' => '',
                'value' => array(
					esc_html__('Yes', 'arrowpress-core') => 'yes',
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
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('grid_style_1','grid_style_2','grid_style_3','grid_style_4',
						'packery_style_1','list_style_1','grid_style_6'),
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
                    'value' => array('grid_style_1','grid_style_2','grid_style_3','grid_style_4',
						'packery_style_1','list_style_1','grid_style_6'),
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
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('grid_style_1','grid_style_2','grid_style_3','grid_style_4',
					'packery_style_1','list_style_1','grid_style_6'),
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
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('grid_style_1','grid_style_2','grid_style_3','grid_style_4',
						'packery_style_1','list_style_1','grid_style_6'),
                ),
            ),
			array(
                "type" => "checkbox",
                "heading" => esc_html__("Show Space", 'arrowpress-core'),
                "param_name" => "show_spacer",
				'std' => 'yes',
                'value' => array(
					esc_html__('Yes', 'arrowpress-core') => 'yes',
				),
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('grid_style_1','grid_style_2','grid_style_3','grid_style_4','grid_style_5',
                        'packery_style_1','list_style_1','grid_style_6','list_style_2','packery_style_3'),
                ),                
            ),
			array(
                "type" => "checkbox",
                "heading" => esc_html__("Show load more", 'arrowpress-core'),
                "param_name" => "show_loadmore",
                'value' => array(esc_html__('Yes', 'arrowpress-core') => 'yes'),
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('grid_style_1','grid_style_2','grid_style_3','grid_style_4','grid_style_5',
                        'packery_style_1','list_style_1','grid_style_6','list_style_2','packery_style_2','packery_style_3'),
                ),                
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
                "type" => "number",
                "class" => "",
                "heading" => esc_html__("Button Top Space", 'arrowpress-core'),
                "param_name" => "space_top_btn",
                "value" => "",
                'description' => esc_html__( 'px', 'arrowpress-core' ),
                'group' => 'Typography'
            ),
            array(
                'type' => 'textfield',
                'heading' => esc_html__( 'Blog title settings', 'arrowpress-core' ),
                'param_name' => 'big_title_text',
                'group' => esc_html__('Typography','arrowpress-core'),
                'edit_field_class' => 'arrowpress_info_field',
            ),   
            array(
                'type' => 'number',
                'heading' => esc_html__( 'Blog title font size', 'arrowpress-core' ),
                'param_name' => 'big_title_size',
                'group' => esc_html__('Typography','arrowpress-core'),
                'description' => 'px',
            ),  
            array(
                'type' => 'number',
                'heading' => esc_html__( 'Blog title line height', 'arrowpress-core' ),
                'param_name' => 'big_title_lh',
                'group' => esc_html__('Typography','arrowpress-core'),
                'description' => 'px',
            ),                                  
            array(
                'type' => 'checkbox',
                'heading' => __( 'Use custom font family?', 'arrowpress-core' ),
                'param_name' => 'big_use_theme_fonts',
                'value' => array( __( 'Yes', 'arrowpress-core' ) => 'yes' ),
                'description' => __( 'Use custom font family.', 'arrowpress-core' ),
                'group' => esc_html__('Typography','arrowpress-core'),
            ),            
            array(
                'type' => 'google_fonts',
                'param_name' => 'big_google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => __( 'Select blog title font family.', 'arrowpress-core' ),
                        'font_style_description' => __( 'Select blog title font styling.', 'arrowpress-core' ),
                    ),
                ),
                'dependency' => array(
                    'element' => 'big_use_theme_fonts',
                    'value' => 'yes',
                ),
                'group' => esc_html__('Typography','arrowpress-core'),
            ),  
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Blog Title Color', 'arrowpress-core' ),
                'param_name' => 'color_default',
                'description' => esc_html__( 'Select color.', 'arrowpress-core' ),
                'group' => esc_html__('Typography','arrowpress-core'),
            ),  
            array(
                "type" => "number",
                "class" => "",
                "heading" => esc_html__("Blog title top space", 'arrowpress-core'),
                "param_name" => "blog_title_space_top",
                "value" => "",
                'description' => esc_html__( 'px', 'arrowpress-core' ),
                'group' => esc_html__('Typography','arrowpress-core'),
            ),             
//Blog description
            array(
                'type' => 'textfield',
                'heading' => esc_html__( 'Blog description settings', 'arrowpress-core' ),
                'param_name' => 'desc_title_text',
                'group' => esc_html__('Typography','arrowpress-core'),
                'edit_field_class' => 'arrowpress_info_field',
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('grid_style_1','grid_style_2','grid_style_3','grid_style_4','grid_style_5',
                        'packery_style_1','list_style_1','grid_style_6','list_style_2','packery_style_2','packery_style_3'),
                ),                
            ),   
            array(
                'type' => 'number',
                'heading' => esc_html__( 'Blog description font size', 'arrowpress-core' ),
                'param_name' => 'desc_size',
                'group' => esc_html__('Typography','arrowpress-core'),
                'description' => 'px',
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('grid_style_1','grid_style_2','grid_style_3','grid_style_4','grid_style_5',
                        'packery_style_1','list_style_1','grid_style_6','list_style_2','packery_style_2','packery_style_3'),
                ),                
            ),  
            array(
                'type' => 'number',
                'heading' => esc_html__( 'Blog description line height', 'arrowpress-core' ),
                'param_name' => 'desc_lh',
                'group' => esc_html__('Typography','arrowpress-core'),
                'description' => 'px',
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('grid_style_1','grid_style_2','grid_style_3','grid_style_4','grid_style_5',
                        'packery_style_1','list_style_1','grid_style_6','list_style_2','packery_style_2','packery_style_3'),
                ),                
            ),                                  
            array(
                'type' => 'checkbox',
                'heading' => __( 'Use custom font family?', 'arrowpress-core' ),
                'param_name' => 'desc_use_theme_fonts',
                'value' => array( __( 'Yes', 'arrowpress-core' ) => 'yes' ),
                'description' => __( 'Use custom font family.', 'arrowpress-core' ),
                'group' => esc_html__('Typography','arrowpress-core'),

            ),            
            array(
                'type' => 'google_fonts',
                'param_name' => 'desc_google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => __( 'Select blog description font family.', 'arrowpress-core' ),
                        'font_style_description' => __( 'Select blog description font styling.', 'arrowpress-core' ),
                    ),
                ),
                'dependency' => array(
                    'element' => 'desc_use_theme_fonts',
                    'value' => 'yes',
                ),
                'group' => esc_html__('Typography','arrowpress-core'),
            ),  
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Blog description Color', 'arrowpress-core' ),
                'param_name' => 'desc_color',
                'description' => esc_html__( 'Select blog description color.', 'arrowpress-core' ),
                'group' => esc_html__('Typography','arrowpress-core'),
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('grid_style_1','grid_style_2','grid_style_3','grid_style_4','grid_style_5',
                        'packery_style_1','list_style_1','grid_style_6','list_style_2','packery_style_2','packery_style_3'),
                ),                
            ),
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Blog info Color', 'arrowpress-core' ),
                'param_name' => 'info_color',
                'description' => esc_html__( 'Select blog info color such as blog date.', 'arrowpress-core' ),
                'group' => esc_html__('Typography','arrowpress-core'),
            ),                                                
			// array(
   //              'type' => 'colorpicker',
   //              'heading' => esc_html__('Filter Color', 'arrowpress-core'),
   //              'param_name' => 'filter_color',
   //              'group' => esc_html__('Typography','arrowpress-core'),
   //          ),   
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Blog info background color', 'arrowpress-core'),
                'param_name' => 'blog_info_bg',
                'group' => esc_html__('Typography','arrowpress-core'),
            ),         
            array(
                "type" => "number",
                "class" => "",
                "heading" => esc_html__("Blog info side space", 'arrowpress-core'),
                "param_name" => "blog_info_padding",
                "value" => "",
                'admin_label' => true,
                'description' => esc_html__( 'px', 'arrowpress-core' ),
                'group' => esc_html__('Typography','arrowpress-core'),
            ),                        
            $custom_class,
            // post type
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Display", 'arrowpress-core'),
                "param_name" => "post_display_type",
                'std' => '',
                'value' => array(
                    esc_html__('Recent', 'arrowpress-core') => 'recent',
                    esc_html__('Featured', 'arrowpress-core') => 'featured',
                    esc_html__('Most Viewed', 'arrowpress-core') => 'most-viewed',
                ),
                "admin_label" => true,
                'group' => 'Data'
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__( 'Order by', 'arrowpress-core' ),
                'param_name' => 'orderby',
                'value' => $order_by_values,
                'description' => sprintf( esc_html__( 'Select how to sort retrieved products_category. More at %s.', 'arrowpress-core' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
                'group' => 'Data'
            ),
            array(
                'type' => 'dropdown',
                'heading' => esc_html__( 'Order way', 'arrowpress-core' ),
                'param_name' => 'order',
                'value' => $order_way_values,
                'description' => sprintf( esc_html__( 'Designates the ascending or descending order. More at %s.', 'arrowpress-core' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
                'group' => 'Data'
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("Category IDs", 'arrowpress-core'),
                "description" => esc_html__("Comma separated list of category ids", 'arrowpress-core'),
                "param_name" => "cat",
                "admin_label" => true,
                'group' => 'Data'
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("Slug Name Category", 'arrowpress-core'),
                "param_name" => "slug_name",
                "value" => "",
                "admin_label" => true,
                'group' => 'Data'
            ),
            array(
                'type' => 'css_editor',
                'heading' => esc_html__( 'CSS box', 'arrowpress-core' ),
                'param_name' => 'css',
                'group' => esc_html__( 'Design Options', 'arrowpress-core' ),
            ),
        )
    ) );

    if (!class_exists('WPBakeryShortCode_ArrowPress_Blog')) {
        class WPBakeryShortCode_ArrowPress_Blog extends WPBakeryShortCode {
        }
    }
}