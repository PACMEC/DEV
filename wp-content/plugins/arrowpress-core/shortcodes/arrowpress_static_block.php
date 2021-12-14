<?php

// arrowpress_static_block
add_shortcode('arrowpress_static_block', 'arrowpress_shortcode_static_block');
add_action('vc_build_admin_page', 'arrowpress_load_static_block_shortcode');
add_action('vc_after_init', 'arrowpress_load_static_block_shortcode');
function arrowpress_shortcode_static_block($atts, $content = null) {
    ob_start();
    if ($template = arrowpress_shortcode_template('arrowpress_static_block'))
        include $template;
    return ob_get_clean();
}

function arrowpress_load_static_block_shortcode() {
    $custom_class = arrowpress_vc_custom_class();
    $block_options = array();
    $block_options[0] = esc_html__('Choose a block to display', 'arrowpress-core');
    $args = array(
        'numberposts'       => -1,
        'post_type'         => 'block',
        'post_status'       => 'publish',
    );
    $posts = get_posts($args);
    foreach( $posts as $_post ){
        $block_options[$_post->post_title] = $_post->post_title;
    }
    vc_map( array(
        'name' => "ArrowPress " . esc_html__('Static Block', 'arrowpress-core'),
        'base' => 'arrowpress_static_block',
        'category' => esc_html__('ArrowPress', 'arrowpress-core'),
        'icon' => 'arrowpress_vc_icon',
        'weight' => - 50,
        "params" => array(
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Static Block", 'arrowpress-core'),
                "param_name" => "static",
                'value' =>  $block_options,
                "admin_label" => true
            ),
            $custom_class
        )
    ));

    if (!class_exists('WPBakeryShortCode_ArrowPress_Static_Block')) {
        class WPBakeryShortCode_ArrowPress_Static_Block extends WPBakeryShortCode {
        }
    }
}


