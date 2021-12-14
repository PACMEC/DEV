<?php

// arrowpress_product
add_shortcode('arrowpress_product', 'arrowpress_shortcode_product');
add_action('vc_build_admin_page', 'arrowpress_load_product_shortcode');
add_action('vc_after_init', 'arrowpress_load_product_shortcode');

function arrowpress_shortcode_product($atts, $content = null) {
    ob_start();
    if ($template = arrowpress_shortcode_woo_template('arrowpress_product'))
        include $template;
    return ob_get_clean();
}

function arrowpress_load_product_shortcode() {
    $custom_class = arrowpress_vc_custom_class();
    $order_by_values = arrowpress_vc_woo_order_by();
    $order_way_values = arrowpress_vc_woo_order_way();
    vc_map( array(
        'name' => "ArrowPress " . esc_html__('Product', 'arrowpress-core'),
        'base' => 'arrowpress_product',
        'category' => esc_html__('ArrowPress', 'arrowpress-core'),
        'icon' => 'arrowpress_vc_icon',
        'weight' => - 50,
        "params" => array( 
			array(
                'type' => 'dropdown',
                'heading' => __( 'Layout', 'arrowpress-core' ),
                'param_name' => 'layout',
                'std' => '',
				'value' => array(
                    esc_html__('Product Grid', 'arrowpress-core') => 'grid',
                    esc_html__('Product Grid Slide', 'arrowpress-core') => 'slide',
                    esc_html__('Product List', 'arrowpress-core') => 'list',
                    esc_html__('Product List Slide', 'arrowpress-core') => 'list-slider',
                    esc_html__('Product Packery', 'arrowpress-core') => 'packery',
                ),
                "admin_label" => true,
            ),
			array(
                'type' => 'dropdown',
                'heading' => __( 'Product Style', 'arrowpress-core' ),
                'param_name' => 'product_style',
                'std' => '',
				'value' => array(
                    esc_html__('Style 1', 'arrowpress-core') => 'style-1',
                    esc_html__('Style 2', 'arrowpress-core') => 'style-2',
                    esc_html__('Style 3', 'arrowpress-core') => 'style-3',
                ),
                "admin_label" => false,
				"dependency" => array(
                    'element' => 'layout',
                    'value' => array('grid')
                ),
            ),   
			array(
                'type' => 'dropdown',
                'heading' => __( 'Product type', 'arrowpress-core' ),
                'param_name' => 'shortcodes_layout',
                'std' => '',
				'value' => array(
                    esc_html__('Recent Products', 'arrowpress-core') => 'recent_products',
                    esc_html__('Featured Products', 'arrowpress-core') => 'featured_products',
                    esc_html__('Best-Selling Products', 'arrowpress-core') => 'best_selling_products',
                    esc_html__('Top Rated Products', 'arrowpress-core') => 'top_rated_products',
                    esc_html__('Sale Products', 'arrowpress-core') => 'sale_products',
                    esc_html__('Related Products', 'arrowpress-core') => 'related_products',
                ),
                "admin_label" => true,
            ),
            array(
                'type' => 'textfield',
                'heading' => __( 'Per page', 'arrowpress-core' ),
                'value' => 12,
                'param_name' => 'per_page',
                'description' => __( 'The "per_page" shortcode determines how many products to show on the page', 'arrowpress-core' ),
            ),
			array(
                "type" => "textfield",
                "heading" => esc_html__("Number rows product to slide", "arrowpress-shortcodes"),
                "param_name" => "items",
                "value" => 2,
                "admin_label" => false,
				"dependency" => array(
                    'element' => 'layout',
                    'value' => array('slide','list-slider','slide2')
                ),
            ),  
            array(
                'type' => 'dropdown',
                'heading' => __( 'Columns', 'arrowpress-core' ),
                'param_name' => 'columns',
                'std' => '4',
                'value' => arrowpress_sh_commons('products_columns'),
				'admin_label' => true,
            ),
			array(
                "type" => "textfield",
                "heading" => esc_html__("Slug Name", "arrowpress-shortcodes"),
                "param_name" => "slug_name",
                "value" => '',
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
                "heading" => esc_html__("Show spacing", 'arrowpress-core'),
                "param_name" => "show_spacing",
				'std' => 'yes',
                'value' => array(esc_html__('Yes', 'arrowpress-core') => 'yes'),       
            ),
			// Show filter
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
                "type" => "checkbox",
                "heading" => esc_html__("Show view more", 'arrowpress-core'),
                "param_name" => "view_more",
				'std' => 'yes',
                'value' => array(esc_html__('Yes', 'arrowpress-core') => 'yes'),       
            ),
            array(
                "type" => "checkbox",
                "heading" => esc_html__("Show Compare", 'arrowpress-core'),
                "param_name" => "show_compare",
                'std' => 'yes',
                'value' => array(esc_html__('Yes', 'arrowpress-core') => 'yes'),       
            ),  
            array(
                "type" => "checkbox",
                "heading" => esc_html__("Show Wishlist", 'arrowpress-core'),
                "param_name" => "show_wishlist",
                'std' => 'yes',
                'value' => array(esc_html__('Yes', 'arrowpress-core') => 'yes'),       
            ),       
            array(
                "type" => "checkbox",
                "heading" => esc_html__("Show Quick View", 'arrowpress-core'),
                "param_name" => "show_quickview",
                'std' => 'yes',
                'value' => array(esc_html__('Yes', 'arrowpress-core') => 'yes'),       
            ),    
            array(
                "type" => "checkbox",
                "heading" => esc_html__("Show Link", 'arrowpress-core'),
                "param_name" => "show_link",
                'std' => 'yes',
                'value' => array(esc_html__('Yes', 'arrowpress-core') => 'yes'),       
            ), 
            array(
                "type" => "checkbox",
                "heading" => esc_html__("Show Price", 'arrowpress-core'),
                "param_name" => "show_price",
                'std' => 'yes',
                'value' => array(esc_html__('Yes', 'arrowpress-core') => 'yes'),       
            ),                                               
			array(
                "type" => "dropdown",
                "heading" => esc_html__("Number Column on Desktop Large (> 1200px)", "arrowpress-shortcodes"),
                "param_name" => "items_desktop_large",
                'std' => 3,
                'value' => array(
                    esc_html__('4', 'arrowpress-core') => 4,
                    esc_html__('3', 'arrowpress-core') => 3,
                    esc_html__('2', 'arrowpress-core') => 2,
                    esc_html__('1', 'arrowpress-core') => 1,
                ),
				"dependency" => array(
                    'element' => 'layout',
                    'value' => array('slide','list-slider','slide2')
                ),
				'group' => esc_html__( 'Responsive Slide','arrowpress-core' ),
            ),
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Number Column on Desktop", "arrowpress-shortcodes"),
                "param_name" => "items_desktop",
                'std' => 2,
                'value' => array(
                    esc_html__('4', 'arrowpress-core') => 4,
                    esc_html__('3', 'arrowpress-core') => 3,
                    esc_html__('2', 'arrowpress-core') => 2,
                    esc_html__('1', 'arrowpress-core') => 1,
                ),
				"dependency" => array(
                    'element' => 'layout',
                    'value' => array('slide','list-slider','slide2')
                ),
				'group' => esc_html__( 'Responsive Slide','arrowpress-core' )
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
				"dependency" => array(
                    'element' => 'layout',
                    'value' => array('slide','list-slider','slide2')
                ),
				'group' => esc_html__( 'Responsive Slide','arrowpress-core' )
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
				"dependency" => array(
                    'element' => 'layout',
                    'value' => array('slide','list-slider','slide2')
                ),
				'group' => esc_html__( 'Responsive Slide','arrowpress-core' )
            ),
			array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Filter Color', 'arrowpress-core' ),
                'param_name' => 'filter_color',
                'group' => esc_html__('Skin','arrowpress-core'),
				"dependency" => array(
                    'element' => 'show_filter',
                    'value' => array('yes')
                ),
            ),   
            array(
                'type' => 'number',
                'heading' => esc_html__( 'Filter font size', 'arrowpress-core' ),
                'param_name' => 'filter_size',
                'group' => esc_html__('Skin','arrowpress-core'),
                'description' => 'px',
				"dependency" => array(
                    'element' => 'show_filter',
                    'value' => array('yes')
                ),
            ),      
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Filter Border Color', 'arrowpress-core' ),
                'param_name' => 'filter_border_color',
                'group' => esc_html__('Skin','arrowpress-core'),
				"dependency" => array(
                    'element' => 'show_filter',
                    'value' => array('yes')
                ),
            ),  
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Filter Border Styles", 'arrowpress-core'),
                "param_name" => "filter_border_style",
                'std' => 'solid',
                'value' => array(
                    esc_html__('Dotted', 'arrowpress-core') => 'dotted',
                    esc_html__('Dashed', 'arrowpress-core') => 'dashed',
                    esc_html__('Solid', 'arrowpress-core') => 'solid',
                    esc_html__('Double', 'arrowpress-core') => 'double',
                    esc_html__('Groove', 'arrowpress-core') => 'groove',
                    esc_html__('Ridge', 'arrowpress-core') => 'ridge',
                ),
                'group' => esc_html__('Skin','arrowpress-core'),
				"dependency" => array(
                    'element' => 'show_filter',
                    'value' => array('yes')
                ),
            ),  
            array(
                "type" => "vc_link",
                "heading" => esc_html__("Button Link", 'arrowpress-core'),
                "param_name" => "button_link",              
            ),            
            $custom_class
        )
    ) );

    if (!class_exists('WPBakeryShortCode_Arrowpress_Product')) {
        class WPBakeryShortCode_Arrowpress_Product extends WPBakeryShortCode {
        }
    }
}