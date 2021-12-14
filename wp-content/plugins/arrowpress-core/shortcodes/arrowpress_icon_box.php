<?php

add_shortcode('arrowpress_icon_box', 'arrowpress_shortcode_icon_box');
add_action('vc_build_admin_page', 'arrowpress_load_icon_box_shortcode');
add_action('vc_after_init', 'arrowpress_load_icon_box_shortcode');

function arrowpress_shortcode_icon_box($atts, $content = null) {
    ob_start();
    if ($template = arrowpress_shortcode_template('arrowpress_icon_box'))
        include $template;
    return ob_get_clean();
}

function arrowpress_load_icon_box_shortcode() {
    $custom_class = arrowpress_vc_custom_class();
    $animation_type = arrowpress_animation_custom();

    vc_map( array(
        'name' => "ArrowPress " . esc_html__('Icon Box', 'arrowpress-core'),
        'base' => 'arrowpress_icon_box',
        'category' => esc_html__('ArrowPress', 'arrowpress-core'),
        'icon' => 'arrowpress_vc_icon',
        'weight' => - 50,
        "params" => array(
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Layout", 'arrowpress-core'),
                "param_name" => "layout",
                'std' => 'icon_box_1',
                'value' => array(
                    esc_html__('Icon box 1', 'arrowpress-core') => 'icon_box_1',
                    esc_html__('Icon box 2', 'arrowpress-core') => 'icon_box_2',
                    esc_html__('Icon box 3', 'arrowpress-core') => 'icon_box_3',
                ),
            ),            
        //Icon group
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
                "type" => "dropdown",
                "heading" => esc_html__("Icon Style", 'arrowpress-core'),
                "param_name" => "icon_style",
                'std' => 'font_icon',
                'value' => array(
                    esc_html__('Default', 'arrowpress-core') => 'default',
                    esc_html__('Style 2', 'arrowpress-core') => 'style2',
                    esc_html__('Style 3', 'arrowpress-core') => 'style3',
                    esc_html__('Style 4', 'arrowpress-core') => 'style4',
                ),
                'dependency' => array(
                    'element' => 'type_icon',
                    'value' => array('font_icon'),
                ),         
                'group'    => esc_html__("Icon", 'arrowpress-core'),        
            ),                       
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Icon library', 'arrowpress-core'),
                'value' => array(
                    esc_html__('Font Awesome', 'arrowpress-core') => 'fontawesome',
                    esc_html__('Font CryptoCio', 'arrowpress-core') => 'arrowpressfont',
                    esc_html__('Open Iconic', 'arrowpress-core') => 'openiconic',
                    esc_html__('Typicons', 'arrowpress-core') => 'typicons',
                    esc_html__('Stroke Icons 7', 'arrowpress-core') => 'pestrokefont',
                    esc_html__('Themify Icons', 'arrowpress-core') => 'themifyfont',
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
                'param_name' => 'icon_themifyfont',
                'settings' => array(
                    'emptyIcon' => false, // default true, display an "EMPTY" icon?
                    'type' => 'themifyfont',
                    'iconsPerPage' => 4000, // default 100, how many icons per/page to display
                ),
                'dependency' => array(
                    'element' => 'icon_type',
                    'value' => 'themifyfont',
                ),
                'weight' => 9,
                'description' => esc_html__('Select icon from library.', 'arrowpress-core'),
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
                    'type' => 'arrowpressfont',
                    'iconsPerPage' => 4000, // default 100, how many icons per/page to display
                ),
                 'dependency' => array(
                    'element' => 'icon_type',
                    'value' => 'arrowpressfont',
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
                "type" => "icon_manager",
                "class" => "",
                "heading" => __("[Ultimate > Icon Manager] Select Icon ","ultimate_vc"),
                "param_name" => "ult_icon",
                "value" => "",
                "description" => __("Click and select icon of your choice. If you can't find the one that suits for your purpose, you can","ultimate_vc")." <a href='admin.php?page=bsf-font-icon-manager' target='_blank'>".__('add new here','ultimate_vc')."</a>.",
                'group'    => esc_html__("Icon", 'arrowpress-core'), 
                'dependency' => array(
                    'element' => 'type_icon',
                    'value' => 'font_icon',
                ),                 
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
            ),
            array(
                "type" => "number",
                "heading" => esc_html__("Icon font size", 'arrowpress-core'),
                "param_name" => "icon_size",
                'dependency' => array(
                    'element' => 'type_icon',
                    'value' => 'font_icon',
                ), 
                'group'    => esc_html__("Icon", 'arrowpress-core'),     
            ), 
            array(
                "type" => "textfield",
                "heading" => esc_html__("Big Title", 'arrowpress-core'),
                "param_name" => "big_title",
                "admin_label" => true,
            ), 
            array(
                "type" => "textarea_html",
                "heading" => esc_html__("Description", 'arrowpress-core'),
                "param_name" => "content",
            ), 
            array(
                "type" => "textfield",
                "heading" => esc_html__("Number", 'arrowpress-core'),
                "param_name" => "number",
                 'dependency' => array(
                    'element' => 'layout',
                    'value' => array('icon_box_1','icon_box_2','icon_box_3'),
                ), 
            ),   
            array(
                "type" => "dropdown",
                "heading" => esc_html__( "Text Align", 'arrowpress-core' ),
                "param_name" => "text_align",
                'std' => 'left',
                "value" => array(
                    esc_html__('Left', 'arrowpress-core') => 'left',
					esc_html__('Center', 'arrowpress-core') => 'center',
                    esc_html__('Right', 'arrowpress-core') => 'right',
                    ),
                "description" => esc_html__( "Select heading align.", 'arrowpress-core' )
            ),
            array(
                "type" => "vc_link",
                "heading" => esc_html__("Link", 'arrowpress-core'),
                "param_name" => "link",              
            ),
        //Skin    
			array(
                'type' => 'checkbox',
                'heading' => __( 'Use custom font family?', 'arrowpress-core' ),
                'param_name' => 'big_use_theme_fonts',
                'value' => array( __( 'Yes', 'arrowpress-core' ) => 'yes' ),
                'description' => __( 'Use custom font family.', 'arrowpress-core' ),
                'group' => esc_html__("Typography", 'arrowpress-core'), 

            ),            
            array(
                'type' => 'google_fonts',
                'param_name' => 'big_google_fonts',
                'value' => 'font_family:Abril%20Fatface%3Aregular|font_style:400%20regular%3A400%3Anormal',
                'settings' => array(
                    'fields' => array(
                        'font_family_description' => __( 'Select big heading font family.', 'arrowpress-core' ),
                        'font_style_description' => __( 'Select big heading font styling.', 'arrowpress-core' ),
                    ),
                ),
                'dependency' => array(
                    'element' => 'big_use_theme_fonts',
                    'value' => 'yes',
                ),
                'group' => esc_html__("Typography", 'arrowpress-core'), 
            ),     
			array(
                'type' => 'number',
                'heading' => esc_html__( 'Big Title font size', 'arrowpress-core' ),
                'param_name' => 'big_title_size',
                'edit_field_class' => 'shc_inline_group',
                'group' => esc_html__("Typography", 'arrowpress-core'), 
                'description' => 'px',
            ),
            array(
                'type' => 'number',
                'heading' => esc_html__('Title margin top', 'arrowpress-core'),
                'param_name' => 'title_margin_top',
                'group' => esc_html__("Typography", 'arrowpress-core'), 
                'description' => 'px',   
            ),     
            array(
                'type' => 'number',
                'heading' => esc_html__('Title margin bottom', 'arrowpress-core'),
                'param_name' => 'title_margin_bottom',
                'description' => 'px',
                'group' => esc_html__("Typography", 'arrowpress-core'),    
            ),              
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Title Color', 'arrowpress-core'),
                'param_name' => 'title_color',
                'group' => esc_html__( 'Skin','arrowpress-core' ),
            ),  
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Text color', 'arrowpress-core'),
                'param_name' => 'text_color',
                'group' => esc_html__( 'Skin','arrowpress-core' ),    
            ),   
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Icon color', 'arrowpress-core'),
                'param_name' => 'icon_color',
                'group' => esc_html__( 'Skin','arrowpress-core' ),
            ),
			array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Icon Background', 'arrowpress-core'),
                'param_name' => 'icon_bg',
                'group' => esc_html__( 'Skin','arrowpress-core' ),
            ),  
			array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Icon Border Color', 'arrowpress-core'),
                'param_name' => 'icon_border_color',
                'group' => esc_html__( 'Skin','arrowpress-core' ),
            ), 
            array(
                'type' => 'number',
                'heading' => esc_html__('Icon Box Width', 'arrowpress-core'),
                'param_name' => 'icon_width',
                'group' => esc_html__("Icon", 'arrowpress-core'), 
                'description' => 'px',   
            ),     
            array(
                'type' => 'number',
                'heading' => esc_html__('Icon Box Height', 'arrowpress-core'),
                'param_name' => 'icon_height',
                'description' => 'px',
                'group' => esc_html__("Icon", 'arrowpress-core'),    
            ), 
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('[Hover] Box Container Background', 'arrowpress-core'),
                'param_name' => 'box_bg_hover',
                'group' => esc_html__( 'Skin','arrowpress-core' ),
            ),   
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('[Hover] Text color', 'arrowpress-core'),
                'param_name' => 'text_color_hover',
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