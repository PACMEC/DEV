<?php

// arrowpress_mining_calculator
add_shortcode('arrowpress_mining_calculator', 'arrowpress_shortcode_mining_calculator');
add_action('vc_build_admin_page', 'arrowpress_load_mining_calculator_shortcode');
add_action('vc_after_init', 'arrowpress_load_mining_calculator_shortcode');

function arrowpress_shortcode_mining_calculator($atts, $content = null) {
    ob_start();
    if ($template = arrowpress_shortcode_template('arrowpress_mining_calculator'))
        include $template;
    return ob_get_clean();
}

function arrowpress_load_mining_calculator_shortcode() {
    $custom_class = arrowpress_vc_custom_class();
    $coin_select = array(
        esc_html__('Bitcoin','arrowpress-core') => 'bitcoin',
        esc_html__('Ethereum','arrowpress-core') => 'ethereum',
        esc_html__('Litecoin','arrowpress-core') => 'litecoin',
        esc_html__('Dash','arrowpress-core') => 'dash',
        esc_html__('Monero','arrowpress-core') => 'monero',
        esc_html__('Bitcoin Gold','arrowpress-core') => 'bitcoingold',
        esc_html__('Ethereum Classic','arrowpress-core') => 'ethereumclassic',
        esc_html__('Siacoin','arrowpress-core') => 'siacoin',
        esc_html__('Bytecoin','arrowpress-core') => 'bytecoin',        
        esc_html__('Bitconnect Coin','arrowpress-core') => 'bitconnectcoin',
        esc_html__('Zcash','arrowpress-core') => 'zcash',
        esc_html__('Doge','arrowpress-core') => 'doge',
        esc_html__('Komodo','arrowpress-core') => 'komodo',
        esc_html__('Pascal','arrowpress-core') => 'pascal',
    );    
    vc_map( array(
        'name' => "ArrowPress " . esc_html__('Mining Calculator', 'arrowpress-core'),
        'base' => 'arrowpress_mining_calculator',
        'category' => esc_html__('ArrowPress', 'arrowpress-core'),
        'icon' => 'arrowpress_vc_icon',
        'weight' => - 50,
        "params" => array(  
            array(
                "type"        => "checkbox",
                "heading"     => esc_html__( 'Coin Select', 'arrowpress-core' ),
                "param_name"  => "coin_select",
                "value"       => $coin_select,
                'std' => array('bitcoin'),  
                'admin'=> true,            
            ),  
            array(
                "type"        => "dropdown",
                "heading"     => esc_html__( 'Select active coin calculator', 'arrowpress-core' ),
                "param_name"  => "coin_select_active",
                "value"       => $coin_select,
                'std' => array('bitcoin'),  
                'admin'=> true,            
            ),           
            array(
                'type' => 'colorpicker',
                'heading' => esc_html__( 'Button background color', 'arrowpress-core' ),
                'param_name' => 'button_bg',
            ),                                                       
            $custom_class
        )
    ) );

    if (!class_exists('WPBakeryShortCode_Arrowpress_Mining_Calculator')) {
        class WPBakeryShortCode_Arrowpress_Mining_Calculator extends WPBakeryShortCode {
        }
    }
}