<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6cd39ad071973d12e23974cf675cdc17
{
    public static $prefixLengthsPsr4 = array (
        'O' => 
        array (
            'OomphInc\\ComposerInstallersExtender\\' => 36,
        ),
        'M' => 
        array (
            'MaxMind\\WebService\\' => 19,
            'MaxMind\\Exception\\' => 18,
            'MaxMind\\Db\\' => 11,
        ),
        'G' => 
        array (
            'GeoIp2\\' => 7,
        ),
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
            'Composer\\CaBundle\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'OomphInc\\ComposerInstallersExtender\\' => 
        array (
            0 => __DIR__ . '/..' . '/oomphinc/composer-installers-extender/src',
        ),
        'MaxMind\\WebService\\' => 
        array (
            0 => __DIR__ . '/..' . '/maxmind/web-service-common/src/WebService',
        ),
        'MaxMind\\Exception\\' => 
        array (
            0 => __DIR__ . '/..' . '/maxmind/web-service-common/src/Exception',
        ),
        'MaxMind\\Db\\' => 
        array (
            0 => __DIR__ . '/..' . '/maxmind-db/reader/src/MaxMind/Db',
        ),
        'GeoIp2\\' => 
        array (
            0 => __DIR__ . '/..' . '/geoip2/geoip2/src',
        ),
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
        'Composer\\CaBundle\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/ca-bundle/src',
        ),
    );

    public static $classMap = array (
        'CT_Ultimate_CCPA_Service_Interface' => __DIR__ . '/../..' . '/includes/service/service-ccpa-interface.php',
        'CT_Ultimate_GDPR_Controller_Abstract' => __DIR__ . '/../..' . '/includes/controller/controller-abstract.php',
        'CT_Ultimate_GDPR_Controller_Admin' => __DIR__ . '/../..' . '/includes/controller/controller-admin.php',
        'CT_Ultimate_GDPR_Controller_Breach' => __DIR__ . '/../..' . '/includes/controller/controller-breach.php',
        'CT_Ultimate_GDPR_Controller_Cookie' => __DIR__ . '/../..' . '/includes/controller/controller-cookie.php',
        'CT_Ultimate_GDPR_Controller_Data_Access' => __DIR__ . '/../..' . '/includes/controller/controller-data-access.php',
        'CT_Ultimate_GDPR_Controller_Forgotten' => __DIR__ . '/../..' . '/includes/controller/controller-forgotten.php',
        'CT_Ultimate_GDPR_Controller_Interface' => __DIR__ . '/../..' . '/includes/controller/controller-interface.php',
        'CT_Ultimate_GDPR_Controller_Plugins' => __DIR__ . '/../..' . '/includes/controller/controller-plugins.php',
        'CT_Ultimate_GDPR_Controller_Policy' => __DIR__ . '/../..' . '/includes/controller/controller-policy.php',
        'CT_Ultimate_GDPR_Controller_Pseudonymization' => __DIR__ . '/../..' . '/includes/controller/controller-pseudonymization.php',
        'CT_Ultimate_GDPR_Controller_Rectification' => __DIR__ . '/../..' . '/includes/controller/controller-rectification.php',
        'CT_Ultimate_GDPR_Controller_Services' => __DIR__ . '/../..' . '/includes/controller/controller-services.php',
        'CT_Ultimate_GDPR_Controller_Terms' => __DIR__ . '/../..' . '/includes/controller/controller-terms.php',
        'CT_Ultimate_GDPR_Controller_Unsubscribe' => __DIR__ . '/../..' . '/includes/controller/controller-unsubscribe.php',
        'CT_Ultimate_GDPR_Model_Dummy' => __DIR__ . '/../..' . '/includes/model/model-phpmailer-dummy.php',
        'CT_Ultimate_GDPR_Model_Front_View' => __DIR__ . '/../..' . '/includes/model/model-front-view.php',
        'CT_Ultimate_GDPR_Model_Group' => __DIR__ . '/../..' . '/includes/model/model-group.php',
        'CT_Ultimate_GDPR_Model_Logger' => __DIR__ . '/../..' . '/includes/model/model-logger.php',
        'CT_Ultimate_GDPR_Model_Placeholders' => __DIR__ . '/../..' . '/includes/model/model-placeholders.php',
        'CT_Ultimate_GDPR_Model_Redirect' => __DIR__ . '/../..' . '/includes/model/model-redirect.php',
        'CT_Ultimate_GDPR_Model_Services' => __DIR__ . '/../..' . '/includes/model/model-services.php',
        'CT_Ultimate_GDPR_Model_User' => __DIR__ . '/../..' . '/includes/model/model-user.php',
        'CT_Ultimate_GDPR_Service_ARForms' => __DIR__ . '/../..' . '/includes/service/service-arforms.php',
        'CT_Ultimate_GDPR_Service_Abstract' => __DIR__ . '/../..' . '/includes/service/service-abstract.php',
        'CT_Ultimate_GDPR_Service_Addthis' => __DIR__ . '/../..' . '/includes/service/service-addthis.php',
        'CT_Ultimate_GDPR_Service_Buddypress' => __DIR__ . '/../..' . '/includes/service/service-buddypress.php',
        'CT_Ultimate_GDPR_Service_CF7DB' => __DIR__ . '/../..' . '/includes/service/service-cf7db.php',
        'CT_Ultimate_GDPR_Service_CT_Waitlist' => __DIR__ . '/../..' . '/includes/service/service-ct-waitlist.php',
        'CT_Ultimate_GDPR_Service_Caldera_Forms' => __DIR__ . '/../..' . '/includes/service/service-caldera-forms.php',
        'CT_Ultimate_GDPR_Service_Contact_Form_7' => __DIR__ . '/../..' . '/includes/service/service-contact-form-7.php',
        'CT_Ultimate_GDPR_Service_Custom_Facebook_Feed' => __DIR__ . '/../..' . '/includes/service/service-custom-facebook-feed.php',
        'CT_Ultimate_GDPR_Service_Eform' => __DIR__ . '/../..' . '/includes/service/service-eform.php',
        'CT_Ultimate_GDPR_Service_Events_Manager' => __DIR__ . '/../..' . '/includes/service/service-events-manager.php',
        'CT_Ultimate_GDPR_Service_Facebook_Pixel' => __DIR__ . '/../..' . '/includes/service/service-facebook-pixel.php',
        'CT_Ultimate_GDPR_Service_Flamingo' => __DIR__ . '/../..' . '/includes/service/service-flamingo.php',
        'CT_Ultimate_GDPR_Service_Formcraft' => __DIR__ . '/../..' . '/includes/service/service-formcraft.php',
        'CT_Ultimate_GDPR_Service_Formcraft_Form_Builder' => __DIR__ . '/../..' . '/includes/service/service-formcraft-form-builder.php',
        'CT_Ultimate_GDPR_Service_Formidable_Forms' => __DIR__ . '/../..' . '/includes/service/service-formidable-forms.php',
        'CT_Ultimate_GDPR_Service_GA_Google_Analytics' => __DIR__ . '/../..' . '/includes/service/service-ga-google-analytics.php',
        'CT_Ultimate_GDPR_Service_Google_Adsense' => __DIR__ . '/../..' . '/includes/service/service-google-adsense.php',
        'CT_Ultimate_GDPR_Service_Google_Analytics' => __DIR__ . '/../..' . '/includes/service/service-google-analytics.php',
        'CT_Ultimate_GDPR_Service_Google_Analytics_Dashboard_For_WP' => __DIR__ . '/../..' . '/includes/service/service-google-analytics-dashboard-for-wp.php',
        'CT_Ultimate_GDPR_Service_Google_Analytics_For_Wordpress' => __DIR__ . '/../..' . '/includes/service/service-google-analytics-for-wordpress.php',
        'CT_Ultimate_GDPR_Service_Gravity_Forms' => __DIR__ . '/../..' . '/includes/service/service-gravity-forms.php',
        'CT_Ultimate_GDPR_Service_Hotjar' => __DIR__ . '/../..' . '/includes/service/service-hotjar.php',
        'CT_Ultimate_GDPR_Service_Interface' => __DIR__ . '/../..' . '/includes/service/service-interface.php',
        'CT_Ultimate_GDPR_Service_Klaviyo' => __DIR__ . '/../..' . '/includes/service/service-klaviyo.php',
        'CT_Ultimate_GDPR_Service_Mailchimp' => __DIR__ . '/../..' . '/includes/service/service-mailchimp.php',
        'CT_Ultimate_GDPR_Service_Mailerlite' => __DIR__ . '/../..' . '/includes/service/service-mailerlite.php',
        'CT_Ultimate_GDPR_Service_Mailpoet' => __DIR__ . '/../..' . '/includes/service/service-mailpoet.php',
        'CT_Ultimate_GDPR_Service_Mailster' => __DIR__ . '/../..' . '/includes/service/service-mailster.php',
        'CT_Ultimate_GDPR_Service_Metorik_Helper' => __DIR__ . '/../..' . '/includes/service/service-metorik-helper.php',
        'CT_Ultimate_GDPR_Service_Newsletter' => __DIR__ . '/../..' . '/includes/service/service-newsletter.php',
        'CT_Ultimate_GDPR_Service_Ninja_Forms' => __DIR__ . '/../..' . '/includes/service/service-ninja-forms.php',
        'CT_Ultimate_GDPR_Service_Order_Delivery_Date_For_Woocommerce' => __DIR__ . '/../..' . '/includes/service/service-order-delivery-date-for-woocommerce.php',
        'CT_Ultimate_GDPR_Service_Polylang' => __DIR__ . '/../..' . '/includes/service/service-polylang.php',
        'CT_Ultimate_GDPR_Service_Quform' => __DIR__ . '/../..' . '/includes/service/service-quform.php',
        'CT_Ultimate_GDPR_Service_Ultimate_Member' => __DIR__ . '/../..' . '/includes/service/service-ultimate-member.php',
        'CT_Ultimate_GDPR_Service_WPForms_Lite' => __DIR__ . '/../..' . '/includes/service/service-wpforms-lite.php',
        'CT_Ultimate_GDPR_Service_WP_Comments' => __DIR__ . '/../..' . '/includes/service/service-wp-comments.php',
        'CT_Ultimate_GDPR_Service_WP_Foro' => __DIR__ . '/../..' . '/includes/service/service-wp-foro.php',
        'CT_Ultimate_GDPR_Service_WP_Mail_Bank' => __DIR__ . '/../..' . '/includes/service/service-wp-mail-bank.php',
        'CT_Ultimate_GDPR_Service_WP_Posts' => __DIR__ . '/../..' . '/includes/service/service-wp-posts.php',
        'CT_Ultimate_GDPR_Service_WP_Simple_Paypal_Shopping_Cart' => __DIR__ . '/../..' . '/includes/service/service-wp-simple-paypal-shopping-cart.php',
        'CT_Ultimate_GDPR_Service_WP_User' => __DIR__ . '/../..' . '/includes/service/service-wp-user.php',
        'CT_Ultimate_GDPR_Service_Woocommerce' => __DIR__ . '/../..' . '/includes/service/service-woocommerce.php',
        'CT_Ultimate_GDPR_Service_Wordfence' => __DIR__ . '/../..' . '/includes/service/service-wordfence.php',
        'CT_Ultimate_GDPR_Service_Wp_Job_Manager' => __DIR__ . '/../..' . '/includes/service/service-wp-job-manager.php',
        'CT_Ultimate_GDPR_Service_Yith_Woocommerce_Wishlist' => __DIR__ . '/../..' . '/includes/service/service-yith-woocommerce-wishlist.php',
        'CT_Ultimate_GDPR_Service_Youtube' => __DIR__ . '/../..' . '/includes/service/service-youtube.php',
        'CT_Ultimate_GDPR_Service_bbPress' => __DIR__ . '/../..' . '/includes/service/service-bbpress.php',
        'CT_Ultimate_GDPR_Shortcode_Cookie_Popup' => __DIR__ . '/../..' . '/includes/shortcode/shortcode-cookie-popup.php',
        'CT_Ultimate_GDPR_Shortcode_Myaccount' => __DIR__ . '/../..' . '/includes/shortcode/shortcode-myaccount.php',
        'CT_Ultimate_GDPR_Shortcode_Policy_Accept' => __DIR__ . '/../..' . '/includes/shortcode/shortcode-policy-accept.php',
        'CT_Ultimate_GDPR_Shortcode_Privacy_Center' => __DIR__ . '/../..' . '/includes/shortcode/shortcode-privacy-center.php',
        'CT_Ultimate_GDPR_Shortcode_Privacy_Policy' => __DIR__ . '/../..' . '/includes/shortcode/shortcode-privacy-policy.php',
        'CT_Ultimate_GDPR_Shortcode_Protection' => __DIR__ . '/../..' . '/includes/shortcode/shortcode-protection.php',
        'CT_Ultimate_GDPR_Shortcode_Terms_Accept' => __DIR__ . '/../..' . '/includes/shortcode/shortcode-terms-accept.php',
        'CT_Ultimate_GDPR_Update_Legacy_Options' => __DIR__ . '/../..' . '/includes/update/update-legacy-options.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit6cd39ad071973d12e23974cf675cdc17::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6cd39ad071973d12e23974cf675cdc17::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6cd39ad071973d12e23974cf675cdc17::$classMap;

        }, null, ClassLoader::class);
    }
}