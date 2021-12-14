<?php

class ArrowpressInstagramFeedSettings {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    /**
     * Add options page
     */
    public function add_plugin_page() {
        // This page will be under "Settings"
        add_options_page(
                'Instagram Settings', 'Instagram Settings', 'manage_options', 'instagram-feed', array($this, 'create_admin_page')
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
        // Set class property
        $this->options = get_option('arrowpress_instagram');
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>          
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('arrowpress_instagram_group');
                do_settings_sections('instagram-feed');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init() {
        register_setting(
                'arrowpress_instagram_group', // Option group
                'arrowpress_instagram' // Option name
        );

        add_settings_section(
                'general_setting', // ID
                'General Settings', // Title
                array($this, 'print_section_info'), // Callback
                'instagram-feed' // Page
        );

        add_settings_field(
                'access_token', 'Access token', array($this, 'access_token_id_callback'), 'instagram-feed', 'general_setting'
        );
        
        add_settings_field(
                'type', 'User ID', array($this, 'type_callback'), 'instagram-feed', 'general_setting'
        );
    }

    /**
     * Print the Section text
     */
    public function print_section_info() {
        print 'Enter your settings below:';
        ?>
            <p><?php echo __('Get your Access Token ', 'arrowpress-core'); ?><a href="https://elfsight.com/service/get-instagram-access-token/" target="_blank"><?php echo __('here', 'arrowpress-core'); ?></a> <?php echo __('and enter the User ID, Access Token as ', 'arrowpress-core'); ?><a href="http://hn.arrowpress.net/plugins/get-token.png" target="_blank"><?php echo __('follows.', 'arrowpress-core'); ?></a></p>
        <?php
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function access_token_id_callback() {
        printf(
                '<input type="text" id="access_token" size="100" name="arrowpress_instagram[access_token]" value="%s" />', isset($this->options['access_token']) ? esc_attr($this->options['access_token']) : ''
        );
    }
    
    public function type_callback() {
        printf(
                '<input type="text" name="arrowpress_instagram[user_id]" value="%s"/><br>',
                isset($this->options['user_id']) ? esc_attr($this->options['user_id']) : ''
        );
    }

}

new ArrowpressInstagramFeedSettings();