<?php
/*
Plugin Name: Administrador de Airdrops
Plugin URI: #
Description: Administrador de Airdrops es un complemento que le permite agregar y administrar proyectos de Airdrop, Bounty Hunt e ICO. Se adapta mejor al sitio web de criptomonedas, foro o blog sobre criptomonedas.
Author: PACMEC
Author URI: #
Version: 1.0.0
Text Domain: airdrop
License: GPL2
License URI: #
*/
?>
<?php 
if ( ! defined ( 'WPINC' ) ) {
    die;
}

// define plugin constant
define( 'AIRDROP_DIR', plugin_dir_path( __FILE__ ) );
define( 'AIRDROP_TEMPLATE_DIR', AIRDROP_DIR . '/templates/' );

function airdrop_load_plugin_textdomain() {
    load_plugin_textdomain( 'airdrop', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'airdrop_load_plugin_textdomain' );

// Register Post Type: Airdrop
add_action( 'init', 'airdrop_register_posttype' );
function airdrop_register_posttype() {
    $labels = array(
        'name'                  => _x( 'Airdrops', 'Post Type General Name', 'airdrop' ),
        'singular_name'         => _x( 'Airdrop', 'Post Type Singular Name', 'airdrop' ),
        'menu_name'             => __( 'Airdrops', 'airdrop' ),
        'name_admin_bar'        => __( 'Airdrop', 'airdrop' ),
        'archives'              => __( 'Item Archives', 'airdrop' ),
        'attributes'            => __( 'Item Attributes', 'airdrop' ),
        'parent_item_colon'     => __( 'Parent Item:', 'airdrop' ),
        'all_items'             => __( 'All Items', 'airdrop' ),
        'add_new_item'          => __( 'Add New Item', 'airdrop' ),
        'add_new'               => __( 'Add New', 'airdrop' ),
        'new_item'              => __( 'New Airdrop', 'airdrop' ),
        'edit_item'             => __( 'Edit Airdrop', 'airdrop' ),
        'update_item'           => __( 'Update Airdrop', 'airdrop' ),
        'view_item'             => __( 'View Airdrop', 'airdrop' ),
        'view_items'            => __( 'View Airdrops', 'airdrop' ),
        'search_items'          => __( 'Search Airdrop', 'airdrop' ),
        'not_found'             => __( 'Not found', 'airdrop' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'airdrop' ),
        'featured_image'        => __( 'Featured Image', 'airdrop' ),
        'set_featured_image'    => __( 'Set featured image', 'airdrop' ),
        'remove_featured_image' => __( 'Remove featured image', 'airdrop' ),
        'use_featured_image'    => __( 'Use as featured image', 'airdrop' ),
        'insert_into_item'      => __( 'Insert into item', 'airdrop' ),
        'uploaded_to_this_item' => __( 'Uploaded to this item', 'airdrop' ),
        'items_list'            => __( 'Items list', 'airdrop' ),
        'items_list_navigation' => __( 'Items list navigation', 'airdrop' ),
        'filter_items_list'     => __( 'Filter items list', 'airdrop' ),
    );
    $args = array(
        'label'                 => __( 'Airdrop', 'airdrop' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'thumbnail', 'comments', 'revisions', 'custom-fields', 'page-attributes', 'post-formats' ),
        'taxonomies'            => array( 'post_tag' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_icon'             => 'dashicons-marker',
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'page',
    );
    register_post_type( 'airdrop', $args );
}

// rewrite permalink when plugin activated
register_activation_hook( __FILE__, 'airdrop_flush_rewrites' );
function airdrop_flush_rewrites() {
    airdrop_register_posttype();
    flush_rewrite_rules();
}

add_action( 'wp_enqueue_scripts', 'airdrop_enqueue_script', 9 );
function airdrop_enqueue_script() {
    wp_enqueue_style( 'airdrop-fontawesome-all', plugins_url( '/css/fontawesome-all.css', __FILE__ ), array(), '5.0.13' );
    wp_enqueue_style( 'airdrop-fontawesome', plugins_url( '/css/fontawesome.css', __FILE__ ), array(), '5.0.13' );
    wp_enqueue_style( 'bootstrap', plugins_url( '/css/bootstrap.css', __FILE__ ), array(), '4.1.1' );
    wp_enqueue_style( 'airdrop', plugins_url( '/css/airdrop.css', __FILE__), array(), '040618' );
}

add_action( 'admin_enqueue_scripts', 'airdrop_admin_scripts' );
function airdrop_admin_scripts(){
    global $wp_scripts;

    // Enqueue admin stylesheet
    wp_enqueue_style( 'airdrop-admin', plugins_url( '/css/admin.css', __FILE__ ), array(), false, 'all' );
    // Enqueue date picker UI from WP core:
    wp_enqueue_script( 'jquery-ui-datepicker' );
    // Enqueue the jQuery UI theme css file from google:
    $ui = $wp_scripts->query('jquery-ui-core');
    $protocol = is_ssl() ? 'https' : 'http';
    $url = "$protocol://ajax.googleapis.com/ajax/libs/jqueryui/{$ui->ver}/themes/smoothness/jquery-ui.min.css";
    wp_enqueue_style('airdrop-admin-ui-css', $url, false, null);

    // Include WP Color Picker
    wp_enqueue_style( 'wp-color-picker' );

    // Include airdrop custom jquery file
    wp_enqueue_script( 'airdrop-main', plugins_url( '/js/main.js', __FILE__ ), array( 'wp-color-picker' ), '030618', true );

}

include( 'admin/settings.php' );

// Register thumbnail size for Airdrop
add_action( 'plugins_loaded', 'airdrop_thumbnail_size' );
function airdrop_thumbnail_size() {
    add_image_size( 'airdrop-main-thumbnail', 600, 450, true );
}

// Register Airdrop custom sidebar
add_action( 'widgets_init', 'airdrop_register_sidebar' );
function airdrop_register_sidebar() {
    register_sidebar( array(
        'name'          => esc_html__( 'Airdrop Sidebar', 'airdrop' ),
        'id'            => 'sidebar-airdrop',
        'description'   => esc_html__( 'Add widgets here will show on Airdrop templates.', 'airdrop' ),
        'before_widget' => '<section id="%1$s" class="widget widget-airdrop %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}

// add Claim Tokens Button in singular template
add_filter( 'the_content', 'airdrop_single_content_sections' );
function airdrop_single_content_sections( $content ) {

    // check if current template is Airdrop singular
    if( ! is_singular( 'airdrop' ) ) {
        return $content;
    }
    
    // start output buffering so we can write HTML structure to the page
    $airdrop_website_value = get_post_meta( get_the_ID(), 'airdrop_website', true );
    $airdrop_enddate_value = get_post_meta( get_the_ID(), 'airdrop_enddate', true );
    $airdrop_estimated_value = get_post_meta( get_the_ID(), 'airdrop_estimatedvalue', true );
    $airdrop_requirement_value = get_post_meta( get_the_ID(), 'airdrop_requirement', true );
    
    ob_start(); ?>

    <section class="airdrop-single-section description">
        <h4></i><?php _e( 'Token Overview', 'airdrop' ); ?></h4>
        <?php 
        $airdrop_overview = get_the_content();
        echo wpautop( $airdrop_overview ); ?>
    </section>
    
    <?php if( 0 < strlen( $airdrop_requirement_value ) ) { ?>
    <section class="airdrop-single-section requirement">
        <h4><?php _e( 'Token Requirement', 'airdrop' ); ?></h4>
        <?php echo wp_kses_post( $airdrop_requirement_value ); ?>
    </section>
    <?php } ?>
    
    <?php
    if( isset( $airdrop_website_value ) && "" != $airdrop_website_value ) { ?>
       <?php 
        global $options;
        $airdrop_settings = get_option( 'airdrop-settings' );
        $airdrop_gettokentext = esc_html( $airdrop_settings['gettokentext'] ); 
        $airdrop_gettokenicon = esc_attr( $airdrop_settings['gettokenicon'] );
        $airdrop_gettokenbackground = $airdrop_settings['gettokenbackground'];
        ?>
    <section id="claimtoken" class="airdrop-single-section claimtoken">
        <a target="_blank" href="<?php echo esc_url( $airdrop_website_value ); ?>" class="airdrop-claim" style="background-color: <?php echo esc_attr( $airdrop_gettokenbackground ); ?>"><i class="fas fa-<?php echo $airdrop_gettokenicon; ?>"></i><?php echo $airdrop_gettokentext; ?></a>
        <?php
        if( isset( $airdrop_enddate_value ) && "" != $airdrop_enddate_value ) { ?>
        <p><?php _e( "End in: ", "airdrop" ); ?><?php echo $airdrop_enddate_value; ?></p>
        <?php } ?>
    </section>
    <?php
    }

    // get our output string and save to a variable
    $airdrop_sections = ob_get_clean();

    // return all strings after manipulated them
    return $airdrop_sections;
}

// register Airdrop metabox
add_action( 'add_meta_boxes_airdrop', 'airdrop_add_meta_boxes' );
function airdrop_add_meta_boxes( $post ) {
    add_meta_box( 
        'airdrop-detail',
        __( 'Airdrop Project Detail', 'airdrop' ),
        'airdrop_render_meta_box',
        'airdrop',
        'normal',
        'high'
    );
}

// render Airdrop metabox content
function airdrop_render_meta_box( $post ) {
    global $post; // get the current post data

    // make sure request come from WordPress
    wp_nonce_field( basename(__FILE__), 'airdrop_meta_box_nonce' ); ?>

    <table class="widefat">
        <tr>
            <td><label for="airdrop_symbol"><?php _e( "Airdrop Token Symbol", "airdrop" ); ?></label></td>
            <td><input name="airdrop_symbol" type="text" value="<?php echo esc_textarea( get_post_meta($post->ID, 'airdrop_symbol', true) ); ?>" placeholder="<?php esc_attr_e( 'Token Symbol', 'airdrop' ); ?>"></td>
        </tr>
        <tr>
            <td><label for="airdrop_website"><?php _e( "Airdrop Project Website", "airdrop" ); ?></label></td>
            <td><input name="airdrop_website" type="url" value="<?php echo esc_url( get_post_meta($post->ID, 'airdrop_website', true) ); ?>" placeholder="<?php esc_attr_e( 'https://airdrop.com/?href=your_aff_ID', 'airdrop' ); ?>"></td>
        </tr>
        <tr>
            <td><label for="airdrop_enddate"><?php _e( "Airdrop Expiry Date", "airdrop" ); ?></label></td>
            <td><input id="airdrop_enddate" type="text" name="airdrop_enddate" value="<?php echo get_post_meta( $post->ID, 'airdrop_enddate', true ); ?>"></td>
            
            <script type="text/javascript">
                jQuery( function() {
                    jQuery( "#airdrop_enddate" ).datepicker({
                        dateFormat: "d M, yy",
                    });
                } );
            </script>
        </tr>
        <tr>
            <td> <label for="airdrop_estimatedvalue"><?php _e( "Airdrop Estimated Value", "airdrop" ); ?></label></td>
            <td><input type="number" name="airdrop_estimatedvalue" min="0" value="<?php echo absint( get_post_meta($post->ID, "airdrop_estimatedvalue", true) ); ?>"></td>
        </tr>
        <tr>
            <td colspan="2"><label for="airdrop_requirement"><?php _e( "Airdrop Requirement", "airdrop" ); ?></label></td>
        </tr>
        <tr>
            <td colspan="2"><?php $airdrop_requirement_value = get_post_meta($post->ID, "airdrop_requirement", true); ?>
        <?php wp_editor( $airdrop_requirement_value, 'airdrop_requirement_textarea', array(
        'media_buttons' => false,
        'textarea_name' => 'airdrop_requirement',
        'textarea_rows' => 5,
        'wpautop'       => false,
           ) ); ?></td>
        </tr>
    </table>
      
<?php  
}

// Save meta boxes value
add_action( 'save_post_airdrop', 'airdrop_save_meta_boxes_data', 10, 3 );
function airdrop_save_meta_boxes_data( $post_id, $post, $update ) {

    // If the nonce field is not set or its value in not correct or has expired, the execution is interrupted
    if( !isset( $_POST['airdrop_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['airdrop_meta_box_nonce'], basename(__FILE__) ) ){
            return $post_id;
    }

    // check the user's permissions
    if( !current_user_can( 'edit_post', $post_id ) ) {
        return $post_id;
    }

    // return if autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
        return $post_id;
    }

    if( "airdrop" != $post->post_type ) return $post_id;

    $airdrop_symbol_value      = "";
    $airdrop_website_value     = "";
    $airdrop_enddate_value     = "";
    $airdrop_estimated_value   = "";
    $airdrop_requirement_value = "";

    // store custom fields values
    if( isset( $_POST['airdrop_symbol'] ) ) {
        $airdrop_symbol_value = $_POST['airdrop_symbol'];
    }
    update_post_meta( $post_id, 'airdrop_symbol', sanitize_text_field( $airdrop_symbol_value ) );

    if( isset( $_POST['airdrop_website'] ) ) {
        $airdrop_website_value = $_POST['airdrop_website'];
    }
    update_post_meta( $post_id, 'airdrop_website', sanitize_url( $airdrop_website_value ) );

    if( isset( $_POST['airdrop_enddate'] ) ) {
        $airdrop_enddate_value = $_POST['airdrop_enddate'];
    }
    update_post_meta( $post_id, 'airdrop_enddate', $airdrop_enddate_value );

    if( isset( $_POST['airdrop_estimatedvalue'] ) ) {
        $airdrop_estimated_value = $_POST['airdrop_estimatedvalue'];
    }
    update_post_meta( $post_id, 'airdrop_estimatedvalue', absint( $airdrop_estimated_value ) );

    if( isset( $_POST['airdrop_requirement'] ) && '' != $_POST['airdrop_requirement'] ) {
        $airdrop_requirement_value = $_POST['airdrop_requirement'];
    }
    update_post_meta( $post_id, 'airdrop_requirement', $airdrop_requirement_value );
}

// Override Single Airdrop template
add_filter('template_include', 'airdrop_load_template');

function airdrop_load_template( $template_path ) {
    if( 'airdrop' == get_post_type() ) { 
        
        if( is_single() ) {
            if( $theme_file = locate_template( array( 'single-airdrop.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = AIRDROP_TEMPLATE_DIR . 'single-airdrop.php';
            }
        }

        if( is_archive() ) {
            if( $theme_file = locate_template( array( 'archive-airdrop.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = AIRDROP_TEMPLATE_DIR . 'archive-airdrop.php';
            }
        }

    }
    return $template_path;

}


// add airdrop HTML wrapper before & after loop start
add_action( 'airdrop_wrapper', 'airdrop_wrapper_outter' );
function airdrop_wrapper() { 
    do_action( 'airdrop_wrapper', 10 );
}

function airdrop_wrapper_outter() { ?>
    <div class="container">
        <div class="row">
            <div id="airdrop-wrapper" class="airdrop-content-area col-lg-8 col-md-8 col-sm-8 col-12">
<?php
}

function airdrop_wrapper_close() {
    do_action( 'airdrop_wrapper_close', 10 );
}

add_action( 'airdrop_wrapper_close', 'airdrop_wrapper_outter_close' );
function airdrop_wrapper_outter_close() { ?>
                <div id="airdrop-secondary" class="widget-area col-lg-4 col-md-4 col-sm-4 col-12">
                    <?php dynamic_sidebar( 'sidebar-airdrop' ); ?>
                </div><!-- /#airdrop-secondary -->
        
            </div><!-- /#airdrop-wrapper -->
        
        </div><!-- /.row before #airdrop-wrapper -->
    </div><!-- /.container -->
<?php
}

/* Add shortcode to display in post/page template */
include 'admin/shortcode.php';

