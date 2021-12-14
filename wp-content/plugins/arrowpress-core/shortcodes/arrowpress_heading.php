<?php

// Arrowpress Heading
add_shortcode('arrowpress_heading', 'arrowpress_shortcode_heading');
add_action('vc_build_admin_page', 'arrowpress_load_heading_shortcode');
add_action('vc_after_init', 'arrowpress_load_heading_shortcode');

function arrowpress_shortcode_heading($atts, $content = null) {
    ob_start();
    if ($template = arrowpress_shortcode_template('arrowpress_heading'))
        include $template;
    return ob_get_clean();
}

function arrowpress_load_heading_shortcode() {
    $custom_class = arrowpress_vc_custom_class();
    vc_map( array(
        'name' => "ArrowPress " . esc_html__('Heading', 'arrowpress-core'),
        'base' => 'arrowpress_heading',
        'category' => esc_html__('ArrowPress', 'arrowpress-core'),
        'icon' => 'arrowpress_vc_icon',
        'weight' => - 50,
        "params" => array(
            array(
                "type" => "dropdown",
                "heading" => esc_html__( "Headding style", 'arrowpress-core' ),
                "param_name" => "heading_style",
                "value" => array(
                    esc_html__('Heading Style 1', 'arrowpress-core') => 'style1',
                    esc_html__('Heading Style 2', 'arrowpress-core') => 'style2',
                    esc_html__('Heading Style 3', 'arrowpress-core') => 'style3',
                    // esc_html__('Heading Style 4 (vertical heading)', 'arrowpress-core') => 'style4',
                    // esc_html__('Heading Style 5', 'arrowpress-core') => 'style5',
                    // esc_html__('Heading Style 6', 'arrowpress-core') => 'style6',
                    ),
                "description" => esc_html__( "Select heading style.", 'arrowpress-core' )
            ),
			array(
                "type" => "textfield",
                "class" => "",
                "heading" => esc_html__("Small Heading Title", 'arrowpress-core'),
                "param_name" => "small_heading_title",
                "value" => "",
                'admin_label' => true,
				"dependency" => array(
                    'element' => 'heading_style',
                    'value' => array('style1','style2','style3','style4')
                ) 
            ),
            array(
                "type" => "textarea",
                "class" => "",
                "heading" => esc_html__("Big Heading Title", 'arrowpress-core'),
                "param_name" => "big_heading_title",
                "value" => "",
                'admin_label' => true,
            ),
			array(
                "type" => "textarea",
                "heading" => esc_html__("Description Title", 'arrowpress-core'),
                "param_name" => "content_desc",
                "admin_label" => true,
                "dependency" => array(
                    'element' => 'heading_style',
                    'value' => array('style2','style3')
                ) 
            ),
            array(
                "type" => "dropdown",
                "heading" => esc_html__( "Tag heading", 'arrowpress-core' ),
                "param_name" => "tag_heading",
                "value" => array(
                    esc_html__('Select tag heading', 'arrowpress-core') => '',
                    esc_html__('H1', 'arrowpress-core') => 'tag-h1',
                    esc_html__('H2', 'arrowpress-core') => 'tag-h2',
                    esc_html__('H3', 'arrowpress-core') => 'tag-h3',
                    esc_html__('H4', 'arrowpress-core') => 'tag-h4',
                    esc_html__('H5', 'arrowpress-core') => 'tag-h5',
                    esc_html__('H6', 'arrowpress-core') => 'tag-h6',
                    ),
                "description" => esc_html__( "Select tag heading.", 'arrowpress-core' )
            ),  
            array(
                "type" => "dropdown",
                "heading" => esc_html__( "Headding Align", 'arrowpress-core' ),
                "param_name" => "heading_align",
                "value" => array(
                    esc_html__('Center', 'arrowpress-core') => 'center',
                    esc_html__('Left', 'arrowpress-core') => 'left',
                    esc_html__('Right', 'arrowpress-core') => 'right',
                    ),
                "description" => esc_html__( "Select heading align.", 'arrowpress-core' ),
                 "dependency" => array(
                    'element' => 'heading_style',
                    'value' => array('style2','style1','style3','style5','style6')
                )                
            ),
            array(
                "type" => "dropdown",
                "heading" => esc_html__( "Headding Position", 'arrowpress-core' ),
                "param_name" => "heading_pos",
                "value" => array(
                    esc_html__('Left', 'arrowpress-core') => 'left-pos',
                    esc_html__('Right', 'arrowpress-core') => 'right-pos',
                    esc_html__('Center', 'arrowpress-core') => 'center-pos',
                    ),
                'default' => 'left-pos',
                "description" => esc_html__( "Select heading position.", 'arrowpress-core' ),
                 "dependency" => array(
                    'element' => 'heading_style',
                    'value' => array('style4')
                )                
            ),  
            array(
                'type' => 'checkbox',
                'heading' => __( 'Disable heading vertical rotation', 'arrowpress-core' ),
                'param_name' => 'heading_rotate',
                'value' => array( __( 'Yes', 'arrowpress-core' ) => 'yes' ),
                'description' => __( 'Disable heading vertical rotation', 'arrowpress-core' ),
                 "dependency" => array(
                    'element' => 'heading_style',
                    'value' => array('style4')
                )                 
            ),                     
            array(
                "type" => "dropdown",
                "heading" => esc_html__( "Separator", 'arrowpress-core' ),
                "param_name" => "separator",
                "value" => array(
                    esc_html__('No separator', 'arrowpress-core') => '',
                    esc_html__('Line', 'arrowpress-core') => 'line',
                    esc_html__('Icon', 'arrowpress-core') => 'icon',
                    esc_html__('2 Lines', 'arrowpress-core') => 'line2',
                    ),
                "description" => esc_html__( "Select Separator.", 'arrowpress-core' )
            ),  
            array(
                "type" => "number",
                "class" => "",
                "heading" => esc_html__("Line Width", 'arrowpress-core'),
                "param_name" => "line_w",
                'dependency' => array(
                    'element' => 'separator',
                    'value' => array('line','line2'),
                ),                 
                'description' => esc_html__( 'px', 'arrowpress-core' ),
            ), 
            array(
                "type" => "number",
                "class" => "",
                "heading" => esc_html__("Line Height", 'arrowpress-core'),
                "param_name" => "line_h",
                'dependency' => array(
                    'element' => 'separator',
                    'value' =>  array('line','line2'),
                ),                 
                'description' => esc_html__( 'px', 'arrowpress-core' ),
            ),
            array(
                "type" => "number",
                "heading" => esc_html__("Second Line Width", 'arrowpress-core'),
                "param_name" => "line2_w",
                'dependency' => array(
                    'element' => 'separator',
                    'value' => 'line2',
                ),                 
                'description' => esc_html__( 'px', 'arrowpress-core' ),
            ), 
            array(
                "type" => "number",
                "class" => "",
                "heading" => esc_html__("Second Line Height", 'arrowpress-core'),
                "param_name" => "line2_h",
                'dependency' => array(
                    'element' => 'separator',
                    'value' => 'line2',
                ),                 
                'description' => esc_html__( 'px', 'arrowpress-core' ),
            ),      
			array(
                "type" => "number",
                "class" => "",
                "heading" => esc_html__("Border Radius Width", 'arrowpress-core'),
                "param_name" => "radius_w",
                'dependency' => array(
                    'element' => 'separator',
                    'value' => array('line','line2'),
                ),                 
                'description' => esc_html__( 'px', 'arrowpress-core' ),
            ), 
            array(
                'type' => 'attach_image',
                'heading' => esc_html__('Image', 'arrowpress-core'),
                'param_name' => 'image',
                'value' => '',
                'description' => esc_html__( 'Upload an image to display above title.', 'arrowpress-core' ),     
            ),
            array(
                'type' => 'attach_image',
                'heading' => esc_html__('Background image', 'arrowpress-core'),
                'param_name' => 'heading_bg_img',
                'description' => esc_html__( 'Upload an image for heading background image', 'arrowpress-core' ),     
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
                'dependency' => array(
                    'element' => 'separator',
                    'value' =>  array('icon'),
                ), 
                'group'    => esc_html__("Icon", 'arrowpress-core'),        
            ),   
            array(
                'type' => 'attach_image',
                'heading' => esc_html__('Image icon', 'arrowpress-core'),
                'param_name' => 'image_i',
                'value' => '',
                'dependency' => array(
                    'element' => 'type_icon',
                    'value' => 'image_icon',
                ),                
                'description' => esc_html__( 'Upload an image icon.', 'arrowpress-core' ),
                 'group'    => esc_html__("Icon", 'arrowpress-core'),           
            ), 
            array(
                'type' => 'dropdown',
                'heading' => esc_html__('Icon library', 'arrowpress-core'),        
                'param_name' => 'icon_type',
                'value' => array(
                    esc_html__('Font Awesome', 'arrowpress-core') => 'fontawesome',
                    // esc_html__('Font Bonfire', 'arrowpress-core') => 'aprfont',
                    esc_html__('Open Iconic', 'arrowpress-core') => 'openiconic',
                    esc_html__('Stroke Icons 7', 'arrowpress-core') => 'pestrokefont',
                    esc_html__('Typicons', 'arrowpress-core') => 'typicons',
                    esc_html__('Entypo', 'arrowpress-core') => 'entypo',
                    esc_html__('Linecons', 'arrowpress-core') => 'linecons',
                ),
                'dependency' => array(
                    'element' => 'type_icon',
                    'value' => 'font_icon',
                ),    
                'description' => esc_html__('Select icon library.', 'arrowpress-core'),
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
                    'type' => 'aprfont',
                    'iconsPerPage' => 4000, // default 100, how many icons per/page to display
                ),
                 'dependency' => array(
                    'element' => 'icon_type',
                    'value' => 'aprfont',
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
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Icon Color', 'arrowpress-core' ),
                'param_name' => 'icon_color',
                'description' => esc_html__( 'Icon color.', 'arrowpress-core' ),
                'group' => esc_html__("Icon", 'arrowpress-core'),
                'dependency' => array(
                    'element' => 'type_icon',
                    'value' => 'font_icon',
                ),                    
            ),                
            array(
                'type' => 'textfield',
                'heading' => esc_html__( 'Big Title settings', 'arrowpress-core' ),
                'param_name' => 'big_title_text',
                'group' => 'Typography',
                'edit_field_class' => 'arrowpress_info_field',
            ),                                
            array(
                'type' => 'number',
                'heading' => esc_html__( 'Big Title font size', 'arrowpress-core' ),
                'param_name' => 'big_title_size',
                'edit_field_class' => 'shc_inline_group',
                'group' => 'Typography',
                'description' => 'px',
            ), 
            array(
                'type' => 'number',
                'heading' => esc_html__( '[Tablet] Big Title font size', 'arrowpress-core' ),
                'param_name' => 'big_title_size_tab',
                'edit_field_class' => 'shc_inline_group',
                'group' => 'Typography',
                'description' => 'px',
            ),  
            array(
                'type' => 'number',
                'heading' => esc_html__( '[Tablet Portrait] Big Title font size', 'arrowpress-core' ),
                'param_name' => 'big_title_size_tab_port',
                'edit_field_class' => 'shc_inline_group',
                'group' => 'Typography',
                'description' => 'px',
            ),   
            array(
                'type' => 'number',
                'heading' => esc_html__( '[Mobile landscape] Big Title font size', 'arrowpress-core' ),
                'param_name' => 'big_title_size_mob_land',
                'edit_field_class' => 'shc_inline_group',
                'group' => 'Typography',
                'description' => 'px',
            ), 
            array(
                'type' => 'number',
                'heading' => esc_html__( '[Mobile] Big Title font size', 'arrowpress-core' ),
                'param_name' => 'big_title_size_mob',
                'edit_field_class' => 'shc_inline_group',
                'group' => 'Typography',
                'description' => 'px',
            ),                                             
            array(
                'type' => 'checkbox',
                'heading' => __( 'Use custom font family?', 'arrowpress-core' ),
                'param_name' => 'big_use_theme_fonts',
                'value' => array( __( 'Yes', 'arrowpress-core' ) => 'yes' ),
                'description' => __( 'Use custom font family.', 'arrowpress-core' ),
                'group' => 'Typography',

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
                'group' => 'Typography'
            ),  
			array(
                "type" => "dropdown",
                "heading" => esc_html__( "Big title text transform", 'arrowpress-core' ),
                "param_name" => "big_text_transform",
                "value" => array(
                    esc_html__('Default', 'arrowpress-core') => 'default',
                    esc_html__('None', 'arrowpress-core') => 'none',
                    esc_html__('Uppercase', 'arrowpress-core') => 'uppercase',
                    esc_html__('Capitalize', 'arrowpress-core') => 'capitalize',
                    ),
                'group' => 'Typography',
            ), 
            array(
                "type" => "number",
                "class" => "",
                "heading" => esc_html__("Big title letter spacing", 'arrowpress-core'),
                "param_name" => "b_letter_space",
                "value" => "",
                'description' => esc_html__( 'px', 'arrowpress-core' ),
                'group' => 'Typography'
            ), 
            array(
                'type' => 'number',
                'heading' => esc_html__( 'Big Title line height', 'arrowpress-core' ),
                'param_name' => 'big_title_lh',
                'edit_field_class' => 'shc_inline_group',
                'group' => 'Typography',
                'description' => 'px',
            ), 
            array(
                'type' => 'number',
                'heading' => esc_html__( '[Tablet] Big Title line height', 'arrowpress-core' ),
                'param_name' => 'big_title_lh_tab',
                'edit_field_class' => 'shc_inline_group',
                'group' => 'Typography',
                'description' => 'px',
            ),  
            array(
                'type' => 'number',
                'heading' => esc_html__( '[Tablet Portrait] Big Title line height', 'arrowpress-core' ),
                'param_name' => 'big_title_lh_tab_port',
                'edit_field_class' => 'shc_inline_group',
                'group' => 'Typography',
                'description' => 'px',
            ),   
            array(
                'type' => 'number',
                'heading' => esc_html__( '[Mobile landscape] Big Title line height', 'arrowpress-core' ),
                'param_name' => 'big_title_lh_mob_land',
                'edit_field_class' => 'shc_inline_group',
                'group' => 'Typography',
                'description' => 'px',
            ), 
            array(
                'type' => 'number',
                'heading' => esc_html__( '[Mobile] Big Title line height', 'arrowpress-core' ),
                'param_name' => 'big_title_lh_mob',
                'edit_field_class' => 'shc_inline_group',
                'group' => 'Typography',
                'description' => 'px',
            ),       
            array(
                'type' => 'textfield',
                'heading' => esc_html__( 'Small Title settings', 'arrowpress-core' ),
                'param_name' => 'sm_title_text',
                'group' => 'Typography',
                'edit_field_class' => 'arrowpress_info_field',
            ),                                                                    
            array(
                'type' => 'number',
                'heading' => esc_html__( 'Small Title font size', 'arrowpress-core' ),
                'param_name' => 'small_title_size',
                'group' => 'Typography',
                'description' => 'px',
            ),
            // array(
            //     'type' => 'number',
            //     'heading' => esc_html__( '[Tablet] Small Title font size', 'arrowpress-core' ),
            //     'param_name' => 'sm_title_size_tab',
            //     'edit_field_class' => 'shc_inline_group',
            //     'group' => 'Typography',
            //     'description' => 'px',
            // ),  
            // array(
            //     'type' => 'number',
            //     'heading' => esc_html__( '[Tablet Portrait] Small Title font size', 'arrowpress-core' ),
            //     'param_name' => 'sm_title_size_tab_port',
            //     'edit_field_class' => 'shc_inline_group',
            //     'group' => 'Typography',
            //     'description' => 'px',
            // ),   
            // array(
            //     'type' => 'number',
            //     'heading' => esc_html__( '[Mobile landscape] Small Title font size', 'arrowpress-core' ),
            //     'param_name' => 'sm_title_size_mob_land',
            //     'edit_field_class' => 'shc_inline_group',
            //     'group' => 'Typography',
            //     'description' => 'px',
            // ), 
            // array(
            //     'type' => 'number',
            //     'heading' => esc_html__( '[Mobile] Small Title font size', 'arrowpress-core' ),
            //     'param_name' => 'sm_title_size_mob',
            //     'edit_field_class' => 'shc_inline_group',
            //     'group' => 'Typography',
            //     'description' => 'px',
            // ),              
            array(
                'type' => 'checkbox',
                'heading' => __( 'Use custom font family?', 'arrowpress-core' ),
                'param_name' => 'use_theme_fonts',
                'value' => array( __( 'Yes', 'arrowpress-core' ) => 'yes' ),
                'description' => __( 'Use custom font family.', 'arrowpress-core' ),
                'group' => 'Typography',
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
                'group' => 'Typography'
            ),
            array(
                "type" => "dropdown",
                "heading" => esc_html__( "Small title text transform", 'arrowpress-core' ),
                "param_name" => "sm_text_transform",
                "value" => array(
                    esc_html__('Default', 'arrowpress-core') => 'default',
                    esc_html__('None', 'arrowpress-core') => 'none',
                    esc_html__('Uppercase', 'arrowpress-core') => 'uppercase',
                    esc_html__('Capitalize', 'arrowpress-core') => 'capitalize',
                    ),
                'group' => 'Typography',
            ), 
            array(
                'type' => 'number',
                'heading' => esc_html__( 'Small Title line height', 'arrowpress-core' ),
                'param_name' => 'sm_title_lh',
                'group' => 'Typography',
                'description' => 'px',
            ),              
            array(
                "type" => "number",
                "class" => "",
                "heading" => esc_html__("Small title letter spacing", 'arrowpress-core'),
                "param_name" => "sm_letter_space",
                "value" => "",
                'description' => esc_html__( 'px', 'arrowpress-core' ),
                'group' => 'Typography'
            ),                                                 
            array(
                'type' => 'textfield',
                'heading' => esc_html__( 'Description settings', 'arrowpress-core' ),
                'param_name' => 'description_text',
                'group' => 'Typography',
                'edit_field_class' => 'arrowpress_info_field',
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
                'heading' => esc_html__( 'Description font weight', 'arrowpress-core' ),
                'param_name' => 'desc_weight',
                'group' => 'Typography',
            ),             
            array(
                'type' => 'number',
                'heading' => esc_html__( 'Description line height', 'arrowpress-core' ),
                'param_name' => 'desc_lh',
                'group' => 'Typography',
                'description' => 'px',
            ), 
			array(
                'type' => 'number',
                'heading' => esc_html__( 'Description container width', 'arrowpress-core' ),
                'param_name' => 'desc_w',
                'group' => 'Typography',
                'description' => '%',
            ), 

            //GROUP STYLE
            array(
                'type' => 'textfield',
                'heading' => esc_html__( 'Color options', 'arrowpress-core' ),
                'param_name' => 'color_options_text',
                'group' => esc_html__('Styles','arrowpress-core'),
                'edit_field_class' => 'arrowpress_info_field',
            ),            
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Big Title Color', 'arrowpress-core' ),
                'param_name' => 'color_default',
                'description' => esc_html__( 'Select heading color.', 'arrowpress-core' ),
                'group' => esc_html__('Styles','arrowpress-core'),
                'description' => esc_html__( 'px', 'arrowpress-core' ),
            ),              
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Small Title Color', 'arrowpress-core' ),
                'param_name' => 'color_small',
                'description' => esc_html__( 'Select heading color.', 'arrowpress-core' ),
                'group' => esc_html__('Styles','arrowpress-core'),
            ), 
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Description Color', 'arrowpress-core' ),
                'param_name' => 'color_desc',
                'description' => esc_html__( 'Select description color.', 'arrowpress-core' ),
                'group' => esc_html__('Styles','arrowpress-core'),
            ),  
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Line separator Color', 'arrowpress-core' ),
                'param_name' => 'line_color',
                'group' => esc_html__('Styles','arrowpress-core'),
            ),   
            array(
                'type' => 'textfield',
                'heading' => esc_html__( 'Space options', 'arrowpress-core' ),
                'param_name' => 'space_options_text',
                'group' => esc_html__('Styles','arrowpress-core'),
                'edit_field_class' => 'arrowpress_info_field',
            ),                                            
            array(
                "type" => "number",
                "class" => "",
                "heading" => esc_html__("Heading bottom space", 'arrowpress-core'),
                "param_name" => "space_bottom_title",
                "value" => "",
                'admin_label' => true,
                'description' => esc_html__( 'px', 'arrowpress-core' ),
                'group' => esc_html__('Styles','arrowpress-core'),
            ),
            array(
                "type" => "number",
                "class" => "",
                "heading" => esc_html__("Big title top space", 'arrowpress-core'),
                "param_name" => "big_top_space",
                "value" => "",
                'admin_label' => true,
                'description' => esc_html__( 'px', 'arrowpress-core' ),
                'group' => esc_html__('Styles','arrowpress-core'),
            ),             
            array(
                "type" => "number",
                "class" => "",
                "heading" => esc_html__("Small title bottom space ", "'arrowpress-core'"),
                "param_name" => "space_top_title",
                "value" => "",
                'admin_label' => true,
                'description' => esc_html__( 'px', 'arrowpress-core' ),
                'group' => esc_html__('Styles','arrowpress-core'),
            ),            
            array(
                "type" => "number",
                "class" => "",
                "heading" => esc_html__("Description top space ", "'arrowpress-core'"),
                "param_name" => "space_top_desc",
                "value" => "",
                'admin_label' => true,
                'description' => esc_html__( 'px', 'arrowpress-core' ),
                'group' => esc_html__('Styles','arrowpress-core'),
            ),                                                                                              
            array(
                "type" => "number",
                "class" => "",
                "heading" => esc_html__("Space Top of line separator", 'arrowpress-core'),
                "param_name" => "space_top_se",
                "value" => "",
                'admin_label' => true,
                'description' => esc_html__( 'px', 'arrowpress-core' ),
                'group' => esc_html__('Styles','arrowpress-core'),
            ), 
            array(
                'type' => 'checkbox',
                'heading' => __( 'Hide in tablet landscape', 'arrowpress-core' ),
                'param_name' => 'hide_in_tablet_land',
                'value' => array( __( 'Yes', 'arrowpress-core' ) => 'yes' ),
                'group' => esc_html__('Styles','arrowpress-core'),
            ),              
            array(
                'type' => 'checkbox',
                'heading' => __( 'Hide in tablet portrait', 'arrowpress-core' ),
                'param_name' => 'hide_in_tablet_port',
                'value' => array( __( 'Yes', 'arrowpress-core' ) => 'yes' ),
                'group' => esc_html__('Styles','arrowpress-core'),
            ),             
            array(
                'type' => 'checkbox',
                'heading' => __( 'Hide in mobile landscape', 'arrowpress-core' ),
                'param_name' => 'hide_in_mobile_land',
                'value' => array( __( 'Yes', 'arrowpress-core' ) => 'yes' ),
                'group' => esc_html__('Styles','arrowpress-core'),
            ),             
            array(
                'type' => 'checkbox',
                'heading' => __( 'Hide in mobile portrait', 'arrowpress-core' ),
                'param_name' => 'hide_in_mobile_port',
                'value' => array( __( 'Yes', 'arrowpress-core' ) => 'yes' ),
                'group' => esc_html__('Styles','arrowpress-core'),
            ),            
            //Other group                                                        
			array(
                'type' => 'checkbox',
                'heading' => esc_html__("Item delay", 'arrowpress-core'),
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
                "value" => array(
					esc_html__('bounce', 'arrowpress-core') => 'bounce',
                    esc_html__('flash', 'arrowpress-core') => 'flash',
                    esc_html__('pulse', 'arrowpress-core') => 'pulse',
                    esc_html__('rubberBand', 'arrowpress-core') => 'rubberBand',
                    esc_html__('shake', 'arrowpress-core') => 'shake',
                    esc_html__('swing', 'arrowpress-core') => 'swing',
                    esc_html__('tada', 'arrowpress-core') => 'swing',
                    esc_html__('wobble', 'arrowpress-core') => 'wobble',
                    esc_html__('jello', 'arrowpress-core') => 'jello',
                    esc_html__('bounceIn', 'arrowpress-core') => 'bounceIn',
                    esc_html__('bounceInDown', 'arrowpress-core') => 'bounceInDown',
                    esc_html__('bounceInLeft', 'arrowpress-core') => 'bounceInLeft',
                    esc_html__('bounceInRight', 'arrowpress-core') => 'bounceInRight',
                    esc_html__('bounceInUp', 'arrowpress-core') => 'bounceInUp',
                    esc_html__('bounceOut', 'arrowpress-core') => 'bounceOut',
                    esc_html__('bounceOutDown', 'arrowpress-core') => 'bounceOutDown',
                    esc_html__('bounceOutLeft', 'arrowpress-core') => 'bounceOutLeft',
                    esc_html__('bounceOutRight', 'arrowpress-core') => 'bounceOutRight',
                    esc_html__('bounceOutUp', 'arrowpress-core') => 'bounceOutUp',
					esc_html__('fadeIn', 'arrowpress-core') => 'fadeIn',
                    esc_html__('fadeInDown', 'arrowpress-core') => 'fadeInDown',
                    esc_html__('fadeInDownBig', 'arrowpress-core') => 'fadeInDownBig',
                    esc_html__('fadeInLeft', 'arrowpress-core') => 'fadeInLeft',
                    esc_html__('fadeInLeftBig', 'arrowpress-core') => 'fadeInLeftBig',
                    esc_html__('fadeInRight', 'arrowpress-core') => 'fadeInRight',
                    esc_html__('fadeInUp', 'arrowpress-core') => 'fadeInUp',
                    esc_html__('fadeInUpBig', 'arrowpress-core') => 'fadeInUpBig',
                    esc_html__('fadeOut', 'arrowpress-core') => 'fadeOut',
                    esc_html__('fadeOutDown', 'arrowpress-core') => 'fadeOutDown',
                    esc_html__('fadeOutDownBig', 'arrowpress-core') => 'fadeOutDownBig',
                    esc_html__('fadeOutLeft', 'arrowpress-core') => 'fadeOutLeft',
                    esc_html__('fadeOutLeftBig', 'arrowpress-core') => 'fadeOutLeftBig',
                    esc_html__('fadeOutRight', 'arrowpress-core') => 'fadeOutRight',
                    esc_html__('fadeOutUp', 'arrowpress-core') => 'fadeOutUp',
                    esc_html__('fadeOutUpBig', 'arrowpress-core') => 'fadeOutUpBig',
                    esc_html__('flip', 'arrowpress-core') => 'flip',
                    esc_html__('flipInX', 'arrowpress-core') => 'flipInX',
                    esc_html__('flipInY', 'arrowpress-core') => 'flipInY',
                    esc_html__('flipOutX', 'arrowpress-core') => 'flipOutX',
                    esc_html__('flipOutY', 'arrowpress-core') => 'flipOutY',
                    esc_html__('lightSpeedIn', 'arrowpress-core') => 'lightSpeedIn',
                    esc_html__('lightSpeedOut', 'arrowpress-core') => 'lightSpeedOut',
                    esc_html__('rotateIn', 'arrowpress-core') => 'rotateIn',
                    esc_html__('rotateInDownLeft', 'arrowpress-core') => 'rotateInDownLeft',
                    esc_html__('rotateInDownRight', 'arrowpress-core') => 'rotateInDownRight',
                    esc_html__('rotateInUpLeft', 'arrowpress-core') => 'rotateInUpLeft',
                    esc_html__('rotateInUpRight', 'arrowpress-core') => 'rotateInUpRight',
                    esc_html__('rotateOut', 'arrowpress-core') => 'rotateOut',
                    esc_html__('rotateOutDownLeft', 'arrowpress-core') => 'rotateOutDownLeft',
                    esc_html__('rotateOutDownRight', 'arrowpress-core') => 'rotateOutDownRight',
                    esc_html__('rotateOutUpLeft', 'arrowpress-core') => 'rotateOutUpLeft',
                    esc_html__('rotateOutUpRight', 'arrowpress-core') => 'rotateOutUpRight',
                    esc_html__('slideInUp', 'arrowpress-core') => 'slideInUp',
                    esc_html__('slideInDown', 'arrowpress-core') => 'slideInDown',
                    esc_html__('slideInLeft', 'arrowpress-core') => 'slideInLeft',
                    esc_html__('slideInRight', 'arrowpress-core') => 'slideInRight',
                    esc_html__('slideOutUp', 'arrowpress-core') => 'slideOutUp',
                    esc_html__('zoomIn', 'arrowpress-core') => 'zoomIn',
                    esc_html__('zoomInRight', 'arrowpress-core') => 'zoomInRight',
                    esc_html__('zoomInUp', 'arrowpress-core') => 'zoomInUp',
                    esc_html__('zoomOut', 'arrowpress-core') => 'zoomOut',
                    esc_html__('zoomOutDown', 'arrowpress-core') => 'zoomOutDown',
                    esc_html__('zoomOutLeft', 'arrowpress-core') => 'zoomOutLeft',
                    esc_html__('zoomOutRight', 'arrowpress-core') => 'zoomOutRight',
                    esc_html__('zoomOutUp', 'arrowpress-core') => 'zoomOutUp',
                    esc_html__('hinge', 'arrowpress-core') => 'hinge',
                    esc_html__('rollIn', 'arrowpress-core') => 'rollIn',
                    esc_html__('rollOut', 'arrowpress-core') => 'rollOut',
                    ),
                "description" => esc_html__( "Select Animation Style.", 'arrowpress-core' ),
				'group' => 'Animation'
            ),
			array(
                "type" => "textfield",
                "class" => "",
                "heading" => esc_html__("Animation Delay", 'arrowpress-core'),
				"description" => esc_html__( "Enter Animation Delay.", 'arrowpress-core' ),
                "param_name" => "animation_delay",
                "value" => 500,
				'group' => 'Animation'
            ),
            // vc_map_add_css_animation(),
            $custom_class,
            array(
                'type' => 'css_editor',
                'heading' => esc_html__( 'CSS box', 'arrowpress-core' ),
                'param_name' => 'css',
                'group' => esc_html__( 'Design Options', 'arrowpress-core' ),
            ),
        )
    ) );
    if (!class_exists('WPBakeryShortCode_arrowpress_Heading')) {
        class WPBakeryShortCode_arrowpress_Heading extends WPBakeryShortCode {
        }
    }
}