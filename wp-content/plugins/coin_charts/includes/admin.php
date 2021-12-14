<?php

defined( 'CCHARTS_INDEX' ) or die( '' );

class CCharts_Admin {

    static public function load()
    {
        if(is_admin()){
            add_action( 'admin_menu', array(get_class(), 'addToMenu') );
            add_action( 'admin_enqueue_scripts', array(get_class(), 'enqueueAssets') );
            add_filter( 'plugin_action_links_' . plugin_basename(CCHARTS_INDEX), array(get_class(), 'actionLinks')  );
        }
    }

    static public function actionLinks($links)
    {
        $custom_links = array(
            '<a href="' . CCHARTS_ADMIN_PAGES_URL.'ccharts_setup' . '">NEED TO SETUP!</a>'
        );

        return array_merge( $links, $custom_links );
    }

    static public function addToMenu()
    {
        add_menu_page( 'Usage',
            'Coin Charts',
            'manage_options',
            'ccharts',
            array(get_class(), 'usagePage'),
            'dashicons-chart-area'
        );

        add_submenu_page( 'ccharts',
            'Coin Charts - Usage',
            'Usage',
            'manage_options',
            'ccharts',
            array(get_class(), 'usagePage')
        );

        add_submenu_page( 'ccharts',
            'Coin Charts - Update Status',
            'Update Status',
            'manage_options',
            'ccharts_status',
            array(get_class(), 'statusPage')
        );

        add_submenu_page( 'ccharts',
            'Coin Charts - Setup',
            'Setup',
            'manage_options',
            'ccharts_setup',
            array(get_class(), 'setupPage')
        );

        add_submenu_page( 'ccharts',
            'Coin Charts - Help',
            'Help',
            'manage_options',
            'ccharts_help',
            array(get_class(), 'helpPage')
        );
    }

    static public function enqueueAssets()
    {

        wp_enqueue_script('jquery', '', array(), false, true);
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0');
        wp_enqueue_style('ccharts-admin-css', CCHARTS_URL.'css/admin.css', array(), '1.0');
        wp_register_script('ccharts-admin-js', CCHARTS_URL.'js/admin.js', array('jquery'), '1.0', true);

        wp_localize_script('ccharts-admin-js', 'CChartsAdmin', array(
            'urls' => array(
                'ajax' => CCHARTS_AJAX_URL
            )
        ));
        wp_enqueue_script('ccharts-admin-js');
    }

    static protected function pageHeader($id,$item)
    {
        ?>
        <div id="<?php echo $id; ?>" class="wrap">
            <h1>Coin Charts</h1>
            <p><a href="">By RunCoders</a></p>


            <h2 class="nav-tab-wrapper">
                <a href="<?php echo CCHARTS_ADMIN_PAGES_URL.'ccharts' ?>" class="nav-tab <?php if($item == 0) echo 'nav-tab-active'; ?>">Usage</a>
                <a href="<?php echo CCHARTS_ADMIN_PAGES_URL.'ccharts_status' ?>" class="nav-tab <?php if($item == 1) echo 'nav-tab-active'; ?>">Update Status</a>
                <a href="<?php echo CCHARTS_ADMIN_PAGES_URL.'ccharts_setup' ?>" class="nav-tab <?php if($item == 2) echo 'nav-tab-active'; ?>">Setup</a>
                <a href="<?php echo CCHARTS_ADMIN_PAGES_URL.'ccharts_help' ?>" class="nav-tab <?php if($item == 3) echo 'nav-tab-active'; ?>">Help</a>
            </h2>



        <?php
    }

    static protected function pageFooter()
    {
        ?>
        </div>
        <?php
    }

    static public function usagePage()
    {
        self::pageHeader('cc-usage-panel',0);

        ?>
            <div class="card">
                <h3>Shortcode Usage</h3>
                <p>The shortcode is simple to use:</p>
                <h4>Example of Ethereum light themed chart:</h4>
                <pre>[coin-chart symbol="ETH" theme="light" window="7d"]</pre>
                <h4>Example of Bitcoin dark themed chart:</h4>
                <pre>[coin-chart symbol="BTC" theme="dark" window="7d"]</pre>
            </div>

            <div class="card">
                <h3>Theme</h3>
                <p>There are two theme available: <strong>Light</strong> and <strong>Dark</strong></p>
            </div>

            <div class="card">
                <h3>Symbol</h3>
                <p>Symbol should be a valid cryptocurrency code, at the moment the are available the following:</p>
                <ol>
                    <?php
                    foreach (CCharts_Constants::$currencies as $symbol => $info){
                        $name = $info['name'];
                        echo "<li><strong>$symbol</strong> ($name)</li>";
                    }
                    ?>
                </ol>
            </div>

            <div class="card">
                <h3>Window</h3>
                <p>You can select the default window time, the available intervals are:</p>
                <ul>
                    <li><strong>1d</strong> (1 day)</li>
                    <li><strong>7d</strong> (7 day)</li>
                    <li><strong>1m</strong> (1 month)</li>
                    <li><strong>3m</strong> (3 months)</li>
                    <li><strong>6m</strong> (6 months)</li>
                    <li><strong>all</strong> (All-time)</li>
                </ul>
            </div>
        <?php
        self::pageFooter();
    }

    static public function statusPage()
    {
        self::pageHeader('cc-status-panel',1);
        ?>
            <h3>Update Status</h3>
            <p>Current status of data updates for each cryptocurrency.</p>
            <p>If data becomes corrupted, just press reset button and it will be downloaded again.</p>
            <br>
            <form method="post">
                <input type="hidden" name="reset_target" value="">
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                    <tr>
                        <td class="manage-column">Symbol</td>
                        <td class="manage-column">Name</td>
                        <td class="manage-column">Last Update</td>
                        <td class="manage-column">Reset</td>
                    </tr>
                    </thead>
                    <tbody id="cc-update-table-body">
                    </tbody>
                </table>
            </form>
            <br>
            <p>You can reset all symbols:</p>
            <div class="button button-primary button-large" onclick="ccResetPair('all_pairs')">Reset All Symbols</div>
        <?php
        self::pageFooter();

    }

    static public function setupPage()
    {
        self::pageHeader('cc-setup-panel',2)
        ?>
            <div style="max-width: 100%;" class="card">
                <h3>Setup Cron Job</h3>
                <p>Coin Charts need a cron job to keep data updating.</p>
                <p>You need to complete two steps for setup the correct cron job in Wordpress.</p>
                <ol>
                    <li>
                        <h3>Modify wp-config.php</h3>
                        <ul>
                            <li><p>Open the <strong>wp-config.php</strong> file found in the root of your Wordpress installation.</p></li>
                            <li>
                                <p>At the <strong>end of the file</strong> paste the following code:</p>
                                <pre><strong>define('DISABLE_WP_CRON', true);</strong></pre>
                            </li>
                            <li><p>Save the file.</p></li>
                        </ul>
                    </li>
                    <li>
                        <h3>New Cron Job</h3>
                        <p>You need to create a cron job command</p>
                        <strong>From cPanel</strong>
                        <ul>
                            <li><p>Go to <strong>Advanced > Cron Jobs</strong></p></li>
                            <li><p>Navigate to <strong>Add New Cron Job</strong> or equivalent.</p></li>
                            <li><p>Select all fields with *, for every minute timeout.</p></li>
                            <li>
                                <p>Enter the following command with <strong>your_wordpress.domain</strong> replaced:</p>
                                <pre><strong>wget -q -O - http://your_wordpress.domain/wp-cron.php?doing_wp_cron >/dev/null 2>&1</strong></pre>
                                <img style="width: 500px;border:1px solid #666;" src="<?php echo CCHARTS_URL.'images/addnew_cronjob.jpg'; ?>">
                            </li>
                            <li>
                                <p>After adding, should appear a new entry in <strong>Current Cron Jobs</strong> or equivalent:</p>
                                <img style="width: 800px;border:1px solid #666;" src="<?php echo CCHARTS_URL.'images/current_cronjob.jpg'; ?>">
                            </li>

                        </ul>
                        <br>
                        <strong>From Command Line (Linux)</strong>
                        <ul>
                            <li><p>Open your server on terminal.</p></li>
                            <li>
                                <p>Run the following command:</p>
                                <pre><strong>crontab -e</strong></pre>
                            </li>
                            <li>
                                <p>Write the following command with <strong>your_wordpress.domain</strong> replaced:</p>
                                <pre><strong>* * * * * wget -q -O - http://your_wordpress.domain/wp-cron.php?doing_wp_cron >/dev/null 2>&1</strong></pre>
                            </li>
                            <li>
                                <p>Save the crontab.</p>
                            </li>
                            <li>
                                <p>Check if is running with:</p>
                                <pre><strong>crontab -l</strong></pre>
                            </li>
                        </ul>
                        <br>
                        <br>
                        <br>
                        <br>

                    </li>
                </ol>
            </div>
        <?php
        self::pageFooter();
    }

    static public function helpPage()
    {
        self::pageHeader('cc-help-panel',3);
        ?>
        <div class="card">
            <h3>Help & Support</h3>
            <p>If you need help with the setup/usage of this plugin, please contact us:</p>
            <ul>
                <li><a href="mailto:runcoders@gmail.com">runcoders@gmail.com</a></li>
                <li><a href="https://codecanyon.net/user/runcoders">RunCoders</a></li>
            </ul>
        </div>

        <?php
        self::pageFooter();
    }

}

CCharts_Admin::load();