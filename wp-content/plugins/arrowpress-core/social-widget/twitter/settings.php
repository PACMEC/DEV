<?php

class ArrowpressLatestTweetSettings {

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
                'Arrowpress Latest Tweets', 'Arrowpress Latest Tweets', 'manage_options', 'latest-tweet', array($this, 'create_admin_page')
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
        // Set class property
        $this->options = get_option('arrowpress_latest_tweet');
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>          
            <form method="post" action="options.php">
                <?php
                // This prints out all hidden setting fields
                settings_fields('arrowpress_latest_tweet_group');
                do_settings_sections('latest-tweet');
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
                'arrowpress_latest_tweet_group', // Option group
                'arrowpress_latest_tweet' // Option name
        );

        add_settings_section(
                'general_setting', // ID
                'Twitter Api Settings', // Title
                array($this, 'print_section_info'), // Callback
                'latest-tweet' // Page
        );

        add_settings_field(
                'username', 'Twitter Username', array($this, 'username_callback'), 'latest-tweet', 'general_setting'
        );
        
        add_settings_field(
                'consumer_key', 'Consumer Key', array($this, 'consumer_key_callback'), 'latest-tweet', 'general_setting'
        );
        
        add_settings_field(
                'consumer_secret', 'Consumer Secret', array($this, 'consumer_secret_callback'), 'latest-tweet', 'general_setting'
        );
        
        add_settings_field(
                'access_token', 'Access Token', array($this, 'access_token_callback'), 'latest-tweet', 'general_setting'
        );
        
        add_settings_field(
                'access_token_secret', 'Access Token Secret', array($this, 'access_token_secret_callback'), 'latest-tweet', 'general_setting'
        );
    }

    /**
     * Print the Section text
     */
    public function print_section_info() {
        print 'Enter your settings below:';
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function username_callback() {
        printf(
                '<input type="text" id="username" size="50" name="arrowpress_latest_tweet[username]" value="%s" />', isset($this->options['username']) ? esc_attr($this->options['username']) : ''
        );
    }
    
    public function consumer_key_callback() {
        printf(
                '<input type="text" size="100" name="arrowpress_latest_tweet[consumer_key]" value="%s"/><br>',
                isset($this->options['consumer_key']) ? esc_attr($this->options['consumer_key']) : ''
        );
    }
    
    public function consumer_secret_callback() {
        printf(
                '<input type="text" size="100" name="arrowpress_latest_tweet[consumer_secret]" value="%s"/><br>',
                isset($this->options['consumer_secret']) ? esc_attr($this->options['consumer_secret']) : ''
        );
    }
    
    public function access_token_callback() {
        printf(
                '<input type="text" size="100" name="arrowpress_latest_tweet[access_token]" value="%s"/><br>',
                isset($this->options['access_token']) ? esc_attr($this->options['access_token']) : ''
        );
    }
    
    public function access_token_secret_callback() {
        printf(
                '<input type="text" size="100" name="arrowpress_latest_tweet[access_token_secret]" value="%s"/><br>',
                isset($this->options['access_token_secret']) ? esc_attr($this->options['access_token_secret']) : ''
        );
    }

}

new ArrowpressLatestTweetSettings();