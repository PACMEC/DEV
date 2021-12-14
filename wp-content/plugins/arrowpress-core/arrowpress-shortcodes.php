<?php

/*
  Plugin Name: ArrowPress Core
  Plugin URI:
  Description: Core for ArrowPress Theme.
  Version: 1.0.0
  Author: ArrowPress
  Author URI:
  Text-domain: arrowpress-core
 */

// don't load directly
if (!defined('ABSPATH'))
    die('-1');

require_once dirname(__FILE__) . '/social-widget/functions.php'; 
define('ARROWPRESS_SHORTCODES_URL', plugin_dir_url(__FILE__));
define('ARROWPRESS_SHORTCODES_PATH', dirname(__FILE__) . '/shortcodes/');
define('ARROWPRESS_SHORTCODES_LIB', dirname(__FILE__) . '/lib/');
define('ARROWPRESS_SHORTCODES_TEMPLATES', dirname(__FILE__) . '/templates/');
define('ARROWPRESS_SHORTCODES_WOO_PATH', dirname(__FILE__) . '/woo_shortcodes/');
define('ARROWPRESS_SHORTCODES_WOO_TEMPLATES', dirname(__FILE__) . '/woo_templates/');
class ArrowPressShortcodesClass {

    private $shortcodes = array("arrowpress_instagram_feed","arrowpress_static_block", "arrowpress_container", "arrowpress_banner", "arrowpress_member", "arrowpress_slider_wrap", "arrowpress_testimonial","arrowpress_heading", "arrowpress_blog", "arrowpress_icon_box","arrowpress_services","arrowpress_mining_calculator","arrowpress_event_list","arrowpress_event_item","arrowpress_projects","arrowpress_events","arrowpress_time_circles");
	private $woo_shortcodes = array("arrowpress_product");
   function __construct() {

        // Load text domain
        add_action('plugins_loaded', array($this, 'loadTextDomain'));
        // Init plugins
        add_action('init', array($this, 'initPlugin'));

        $this->addShortcodes();
        add_filter('the_content', array($this, 'formatShortcodes'));
        add_filter('widget_text', array($this, 'formatShortcodes'));
        add_action('vc_base_register_front_css',  array($this,'arrowpress_iconpicker_base_register_css'));
        add_action('vc_base_register_admin_css', array($this,'arrowpress_iconpicker_base_register_css'));
        add_action('vc_backend_editor_enqueue_js_css', array($this,'arrowpress_iconpicker_editor_jscss'));
        add_action('vc_frontend_editor_enqueue_js_css', array($this,'arrowpress_iconpicker_editor_jscss'));
        add_action('wp_enqueue_scripts', array($this,'arrowpress_enqueue_script'));
        add_action('wp_enqueue_scripts', array($this,'arrowpress_iconpicker_editor_jscss'));
    }

    // Init plugins
    function initPlugin() {
        $this->addTinyMCEButtons();

        if ( file_exists( dirname( __FILE__ ) . '/lib/cmb2/init.php' ) ) {
            require_once dirname( __FILE__ ) . '/lib/cmb2/init.php';
        } elseif ( file_exists( dirname( __FILE__ ) . '/lib/CMB2/init.php' ) ) {
            require_once dirname( __FILE__ ) . '/lib/CMB2/init.php';
        }
    }

    // load plugin text domain
    function loadTextDomain() {
        load_plugin_textdomain('arrowpress-core', false, dirname(__FILE__) . '/languages/');
    }

    // Add buttons to tinyMCE
    function addTinyMCEButtons() {
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
            return;

        if (get_user_option('rich_editing') == 'true') {
            add_filter('mce_buttons', array(&$this, 'registerTinyMCEButtons'));
        }
    }

    function registerTinyMCEButtons($buttons) {
        array_push($buttons, "arrowpress_shortcodes_button");
        return $buttons;
    }

    // Add shortcodes
    function addShortcodes() {
        require_once(ARROWPRESS_SHORTCODES_LIB . 'functions.php');
        require_once(ARROWPRESS_SHORTCODES_LIB . 'apr-post-type.php');
        require_once(ARROWPRESS_SHORTCODES_LIB . 'categories_image.php');
        require_once(ARROWPRESS_SHORTCODES_LIB . 'gallery_like_count.php');
        foreach ($this->shortcodes as $shortcode) {
            require_once(ARROWPRESS_SHORTCODES_PATH . $shortcode . '.php');
        }
		//if (  function_exists('is_plugin_active') && is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            foreach ($this->woo_shortcodes as $woo_shortcode) {
                require_once(ARROWPRESS_SHORTCODES_WOO_PATH . $woo_shortcode . '.php');
            }
            //if (function_exists('is_plugin_active') && is_plugin_active( 'yith-woocommerce-brands-add-on/init.php' )) {
            //    require_once(BARBER_SHORTCODES_WOO_PATH . 'barber_brands' . '.php');
            //}
        //}
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }

    // Format shortcodes content
    function formatShortcodes($content) {
        $block = join("|", $this->shortcodes);
        // opening tag
        $content = preg_replace("/(<p>)?\[($block)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content);
        // closing tag
        $content = preg_replace("/(<p>)?\[\/($block)](<\/p>|<br \/>)/", "[/$2]", $content);

        return $content;
    }
    function arrowpress_iconpicker_base_register_css() {
        $arrowpress_core_suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min'; 
        wp_register_style('pestrokefont', ARROWPRESS_SHORTCODES_URL  . 'assets/css/pe-icon-7-stroke'.$arrowpress_core_suffix.'.css', false, '1.0', 'screen');
        wp_register_style('themifyfont', ARROWPRESS_SHORTCODES_URL  . 'assets/css/themify-icons'.$arrowpress_core_suffix.'.css', false, '1.0', 'screen');
        wp_register_style('arrowpressfont', ARROWPRESS_SHORTCODES_URL  . 'assets/css/cryptcio-icons'.$arrowpress_core_suffix.'.css', false, '1.0', 'screen');
    }

    function arrowpress_iconpicker_editor_jscss() {
        wp_enqueue_style('pestrokefont');
        wp_enqueue_style('themifyfont');
        wp_enqueue_style('arrowpressfont');
    }
    function arrowpress_enqueue_script() {   
        $handle = 'slick.min.js';
        $list = 'enqueued';   
        $arrowpress_core_suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min'; 
        wp_enqueue_script( 'custom-script', plugin_dir_url( __FILE__ ) . 'assets/js/functions'.$arrowpress_core_suffix.'.js',array ( 'jquery' ), 1.0, true);
        wp_enqueue_script( 'times-script', plugin_dir_url( __FILE__ ) . 'assets/js/TimeCircles.js',array ( 'jquery' ), 1.0, true);
        $post = get_post(get_the_ID());
        if(get_the_ID()!=''){
            $post_content = $post->post_content;
            // echo $post_content;
            if(stripos($post_content,'countdown_enable')){
                wp_enqueue_script('jquery.timecircle');
                wp_enqueue_script('jquery.countdown');           
            }
        }

        if (is_rtl()) {
            wp_enqueue_style('apr-core-style-rtl', plugin_dir_url( __FILE__ )  . 'assets/css/apr_core_rtl'.$arrowpress_core_suffix.'.css');
        }
        else{
            wp_enqueue_style('apr-core-style', plugin_dir_url( __FILE__ )  . 'assets/css/apr_core'.$arrowpress_core_suffix.'.css');
        }        
    }

}

// Finally initialize code
new ArrowPressShortcodesClass();
