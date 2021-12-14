<?php

add_shortcode('arrowpress_member', 'arrowpress_shortcode_member');
add_action('vc_build_admin_page', 'arrowpress_load_member_shortcode');
add_action('vc_after_init', 'arrowpress_load_member_shortcode');

function arrowpress_shortcode_member($atts, $content = null) {
    ob_start();
    if ($template = arrowpress_shortcode_template('arrowpress_member'))
        include $template;
    return ob_get_clean();
}

function arrowpress_load_member_shortcode() {
    $custom_class = arrowpress_vc_custom_class();
    $animation_type = arrowpress_animation_custom();

    vc_map( array(
        'name' => "ArrowPress" . esc_html__(' Member', 'arrowpress-core'),
        'base' => 'arrowpress_member',
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
            ), 
            array(
                'type' => 'attach_image',
                'heading' => esc_html__('Image', 'arrowpress-core'),
                'param_name' => 'image',
                'value' => '',
                'description' => esc_html__( 'Upload image.', 'arrowpress-core' ),
            ),              
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Background color for member info', 'arrowpress-core'),
                'param_name' => 'bg_color',
                'value' => '',
                'dependency' => array(
                    'element' => 'bg_type',
                    'value' => array('color'),
                ),      
                'group' => esc_html__( 'Skin','arrowpress-core' ),                    
            ), 
            // array(
            //     'type' => 'colorpicker',
            //     'heading' => esc_html__('Overlay Background color', 'arrowpress-core'),
            //     'param_name' => 'bg_overlay_color',
            //     'value' => '',
            //     'dependency' => array(
            //         'element' => 'bg_type',
            //         'value' => array('color'),
            //     ),      
            //     'group' => esc_html__( 'Skin','arrowpress-core' ),                    
            // ),            
			array(
                "type" => "textfield",
                "heading" => esc_html__("Last Name", 'arrowpress-core'),
                "param_name" => "last_name",
                "admin_label" => true,
            ), 
             array(
                "type" => "textarea",
                "heading" => esc_html__("Category", 'arrowpress-core'),
                "param_name" => "cat_member",
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout4'),
                ), 
            ),
             array(
                "type" => "textfield",
                "heading" => esc_html__("Link", 'arrowpress-core'),
                "param_name" => "link_text",
                'value' => esc_html__( 'lnr lnr-arrow-right', 'arrowpress-core' ),
                'description' => wp_kses(__('Add icon class you want here. You can find a lot of icons in these links <a target="_blank" href="http://fontawesome.io/icons/">Awesome icon</a> or <a target="_blank" href="https://linearicons.com/">Linearicons </a>, <a target="_blank" href="http://themes-pixeden.com/font-demos/7-stroke/">Pe stroke icon7 </a>','arrowpress-core'),array(
                    'a' => array(
                        'href'=>array(),
                        'target' => array(),
                        ),
                )),
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout4'),
                ), 
            ),
            array(
                "type" => "textfield",
                "heading" => esc_html__("Address", 'arrowpress-core'),
                "param_name" => "address",
                "admin_label" => true,
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout2'),
                ),
            ),  
             array(
                "type" => "textarea",
                "heading" => esc_html__("Article Title", 'arrowpress-core'),
                "param_name" => "article_title",
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout3'),
                ),   
            ),
            array(
                "type" => "textarea",
                "heading" => esc_html__("Job", 'arrowpress-core'),
                "param_name" => "job",
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1','layout5'),
                ), 
            ),
			array(
                "type" => "textarea",
                "heading" => esc_html__("Description", 'arrowpress-core'),
                "param_name" => "description",
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1'),
                ), 
            ), 
			array(
                'type' => 'textfield',
                'heading' => esc_html__('Label 1', 'arrowpress-core'),
                'param_name' => 'info_1',
                'group' => esc_html__( 'Info','arrowpress-core' ),     
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1'),
                ), 
            ), 
			array(
                'type' => 'textfield',
                'heading' => esc_html__('Values 1', 'arrowpress-core'),
                'param_name' => 'values_1',
                'group' => esc_html__( 'Info','arrowpress-core' ), 
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1'),
                ),
            ), 
			array(
                'type' => 'textfield',
                'heading' => esc_html__('Label 2', 'arrowpress-core'),
                'param_name' => 'info_2',
                'group' => esc_html__( 'Info','arrowpress-core' ),
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1'),
                ),
            ), 
			array(
                'type' => 'textfield',
                'heading' => esc_html__('Values 2', 'arrowpress-core'),
                'param_name' => 'values_2',
                'group' => esc_html__( 'Info','arrowpress-core' ),
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1'),
                ),
            ), 
			array(
                'type' => 'textfield',
                'heading' => esc_html__('Label 3', 'arrowpress-core'),
                'param_name' => 'info_3',
                'group' => esc_html__( 'Info','arrowpress-core' ), 
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1'),
                ),
            ), 
			array(
                'type' => 'textfield',
                'heading' => esc_html__('Values 3', 'arrowpress-core'),
                'param_name' => 'values_3',
                'group' => esc_html__( 'Info','arrowpress-core' ),
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1'),
                ),
            ), 
			array(
                'type' => 'textfield',
                'heading' => esc_html__('Label 4', 'arrowpress-core'),
                'param_name' => 'info_4',
                'group' => esc_html__( 'Info','arrowpress-core' ),
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1'),
                ),
            ), 
			array(
                'type' => 'textfield',
                'heading' => esc_html__('Values 4', 'arrowpress-core'),
                'param_name' => 'values_4',
                'group' => esc_html__( 'Info','arrowpress-core' ),  	
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1'),
                ),
            ), 
			array(
                'type' => 'textfield',
                'heading' => esc_html__('Units', 'arrowpress-core'),
                'param_name' => 'units',
                'group' => esc_html__( 'Info','arrowpress-core' ),
				'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1'),
                ),
            ), 
            array(
                'type' => 'checkbox',
                'heading' => esc_html__("Show Socials", "arrowpress-core"),
                'param_name' => 'show_socials',
                'std' => 'Yes',
                'value' => array(esc_html__('Yes', 'arrowpress-core') => 'yes'),
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout1'),
                ), 
            ),
             array(
                 "type" => "vc_link",
                "heading" => esc_html__("Facebook", 'arrowpress-core'),
                "param_name" => "facebook_link",
                'dependency' => array(
                    'element' => 'show_socials',
                    'value' => 'yes',
                ),
            ),
            array(
                 "type" => "vc_link",
                "heading" => esc_html__("Google", 'arrowpress-core'),
                "param_name" => "google_link",
                'dependency' => array(
                    'element' => 'show_socials',
                    'value' => 'yes',
                ),
            ),
             array(
                "type" => "vc_link",
                "heading" => esc_html__("Twitter", 'arrowpress-core'),
                "param_name" => "twitter_link",
                'dependency' => array(
                    'element' => 'show_socials',
                    'value' => 'yes',
                ),
            ),
            array(
                 "type" => "vc_link",
                "heading" => esc_html__("Instagram", 'arrowpress-core'),
                "param_name" => "instagram_link",
                'dependency' => array(
                    'element' => 'show_socials',
                    'value' => 'yes',
                ),
            ),
             array(
                 "type" => "vc_link",
                "heading" => esc_html__("Linkedin", 'arrowpress-core'),
                "param_name" => "linkedin_link",
                'dependency' => array(
                    'element' => 'show_socials',
                    'value' => 'yes',
                ),
            ),
			array(
                "type" => "textarea",
                "heading" => esc_html__("Description", 'arrowpress-core'),
                "param_name" => "desc",
                'dependency' => array(
                    'element' => 'layout',
                    'value' => array('layout2','layout3','layout4'),
                ),   
            ),
            array(
                "type" => "vc_link",
                "heading" => esc_html__("Link", 'arrowpress-core'),
                "param_name" => "link",
            ),   
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Name Color', 'arrowpress-core'),
                'param_name' => 'name_color',
                'group' => esc_html__( 'Skin','arrowpress-core' ),              
            ),  
			array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Name Hover Color', 'arrowpress-core'),
                'param_name' => 'name_hover_color',
                'group' => esc_html__( 'Skin','arrowpress-core' ),              
            ),
             array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Category member color', 'arrowpress-core'),
                'param_name' => 'cat_color',
                'group' => esc_html__( 'Skin','arrowpress-core' ),                
            ),  
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__('Job color', 'arrowpress-core'),
                'param_name' => 'job_color',
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
                'heading' => esc_html__('Tittle Color', 'arrowpress-core'),
                'param_name' => 'title_color',
                'group' => esc_html__( 'Skin','arrowpress-core' ),              
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

    if (!class_exists('WPBakeryShortCode_arrowpress_Member')) {
        class WPBakeryShortCode_arrowpress_Member extends WPBakeryShortCode {
        }
    }
}