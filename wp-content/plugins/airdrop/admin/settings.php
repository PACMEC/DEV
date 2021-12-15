<?php
/**
 * Create plugin setting page
 */
function airdrop_settings_init() {
    // register a new setting for "airdrop" page
    register_setting( 'airdrop', 'airdrop-settings', 'airdrop_settings_validate' );
 
    // register a new section in the "airdrop" page
     add_settings_section(
        'airdrop_section_basic',
        __( 'Basic Settings', 'airdrop' ),
        'airdrop_section_basic_cb',
        'airdrop'
     );
 
    // register a new field in the "airdrop_section_basic" section, inside the "airdrop" page
    add_settings_field(
        'airdrop-gettokentext', // as of WP 4.6 this value is used only internally
        // use $args' label_for to populate the id inside the callback
        __( 'Get token button text', 'airdrop' ),
        'airdrop_gettokentext_cb',
        'airdrop',
        'airdrop_section_basic',
        array(
            'label_for' => 'airdrop-gettokentext',
            'class' => 'airdrop_row',
            'airdrop_custom_data' => 'custom',
        )
    );

    add_settings_field( 
        'airdrop-gettokenicon', 
        __( 'Get token button icon', 'airdrop' ), 
        'airdrop_gettokenicon_cb', 
        'airdrop', 
        'airdrop_section_basic', 
        array(
            'label_for'     => 'airdrop-gettokenicon',
            'class'         => 'airdrop_row',
    ) );

    add_settings_field( 
        'airdrop-gettokenbackground', 
        __( 'Button background color', 'airdrop' ), 
        'airdrop_gettokenbackground_cb', 
        'airdrop', 
        'airdrop_section_basic', 
        array(
            'label_for'     => 'airdrop-gettokenbackground',
            'class'         => 'airdrop_row',
    ) );
}

/**
 * register airdrop_settings_init to the admin_init action hook
 */
add_action( 'admin_init', 'airdrop_settings_init' );

/**
 * custom option and settings:
 * callback functions
 */
 
// developers section cb
 
// section callbacks can accept an $args parameter, which is an array.
// $args have the following keys defined: title, id, callback.
// the values are defined at the add_settings_section() function.
function airdrop_section_basic_cb( $args ) {
    esc_html__( 'Basic config for WP Airdrop Manager', 'airdrop' ); 
}

// submittext field cb
 
// field callbacks can accept an $args parameter, which is an array.
// $args is defined at the add_settings_field() function.
// wordpress has magic interaction with the following keys: label_for, class.
// the "label_for" key value is used for the "for" attribute of the <label>.
// the "class" key value is used for the "class" attribute of the <tr> containing the field.
// you can add custom key value pairs to be used inside your callbacks.
function airdrop_gettokentext_cb( $args ) {

    // get the value of the setting we've registered with register_setting()
    $settings = get_option( 'airdrop-settings' );
    $gettokentext = esc_attr( $settings['gettokentext'] );
    
    echo "<input id='$args[label_for]' type='text' name='airdrop-settings[gettokentext]' value='$gettokentext' />";
}

function airdrop_gettokenicon_cb( $args ) {
    $settings = get_option( 'airdrop-settings' );
    $gettokenicon = esc_attr( $settings['gettokenicon'] );
   
    echo "<input id='$args[label_for]' type='text' name='airdrop-settings[gettokenicon]' value='$gettokenicon' placeholder='". esc_attr__( 'name of icon', 'airdrop' ) ."' />";
    echo "<p>List name of icon here: <a target='_blank' href='https://fontawesome.com/icons?d=gallery&s=solid&m=free'>https://fontawesome.com/icons/</a></p>";
}

function airdrop_gettokenbackground_cb( $args ) {
    $settings = get_option( 'airdrop-settings' );
    $gettokenbackground = esc_attr( $settings['gettokenbackground'] );
 
    echo "<input id='$args[label_for]' type='text' name='airdrop-settings[gettokenbackground]' value='$gettokenbackground' class='airdrop-color-picker' />";
}

/**
 * Add sub menu 'Airdrop Settings' to top level menu 'Airdrops'
 */
function airdrop_options_page() {
    // add top level menu page
    add_submenu_page(
        'edit.php?post_type=airdrop',
        esc_html__( 'WP Airdrop Manager Setting', 'airdrop' ),
        esc_html__( 'Airdrop Settings', 'airdrop' ),
        'manage_options',
        'airdrop',
        'airdrop_options_page_html'
    );
}

function airdrop_settings_validate( $input ) {
    // sanitize value user input
    $input['gettokentext']          = sanitize_text_field( $input['gettokentext'] );
    $input['gettokenicon']          = sanitize_text_field( $input['gettokenicon'] );
    $input['gettokenbackground']    = sanitize_hex_color( $input['gettokenbackground'] );

    return $input;
}

/**
 * register our airdrop_options_page to the admin_menu action hook
 */
add_action( 'admin_menu', 'airdrop_options_page' );

/**
 * callback functions
 */
function airdrop_options_page_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) { return; }

    // add error/update messages
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
    // add settings saved message with the class of "updated"
    add_settings_error( 'airdrop_messages', 'airdrop_message', __( 'Settings Saved', 'airdrop' ), 'updated' );
    }

    // show error/update messages
    settings_errors( 'airdrop_messages' );

?>
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <div class="airdrop-content-wrapper">
        <div id="primary-container" class="airdrop_content_cell">
            <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "airdrop"
            settings_fields( 'airdrop' );

            // output setting sections and their fields
            // (sections are registered for "airdrop", each field is registered to a specific section)
            do_settings_sections( 'airdrop' );

            // output save settings button
            submit_button();
            ?>
            </form>
        </div><!-- /#primary-container -->

        <div id="sidebar-container" class="airdrop_content_cell">
            <a target="_blank" href="https://goo.gl/GPkcSe">
                <img alt="Best Crypto WordPress Theme 2018" src="<?php echo plugins_url( '/images/box-bestcryptotheme.jpg', dirname(__FILE__) ); ?>">
            </a>

            <a target="_blank" href="https://goo.gl/rEB55U">
                <img alt="Best Crypto WordPress Plugin 2018" src="<?php echo plugins_url( '/images/box-bestcryptoplugin.jpg', dirname(__FILE__) ); ?>">
            </a>

            <a target="_blank" href="https://goo.gl/NWzNFG"><?php esc_html_e( 'Need help? Plugin document here', 'airdrop' ); ?></a>
        </div><!-- /#sidebar-container-->

    </div><!-- /.airdrop-content-wrapper -->
</div>
<?php
}