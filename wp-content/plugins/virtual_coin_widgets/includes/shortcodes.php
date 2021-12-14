<?php

defined( 'VCW_INDEX' ) or die( '' );

if(!class_exists('VCW_Shortcodes')) {

    class VCW_Shortcodes
    {

        static public function init()
        {
            add_filter('widget_text','do_shortcode');

            $class = get_class();

            add_shortcode( 'vcw-price-label',       array($class, 'priceLabel'));
            add_shortcode( 'vcw-change-label',      array($class, 'changeLabel'));
            add_shortcode( 'vcw-price-big-label',   array($class, 'priceBigLabel'));
            add_shortcode( 'vcw-change-big-label',  array($class, 'changeBigLabel'));
            add_shortcode( 'vcw-price-card',        array($class, 'priceCard'));
            add_shortcode( 'vcw-change-card',       array($class, 'changeCard'));
            add_shortcode( 'vcw-full-card',         array($class, 'fullCard'));
            add_shortcode( 'vcw-table',             array($class, 'table'));
            add_shortcode( 'vcw-small-table',       array($class, 'smallTable'));
            add_shortcode( 'vcw-converter',         array($class, 'converter'));

            add_action( 'wp_enqueue_scripts',       array($class, 'enqueueAssets') );
        }

        static public function enqueueAssets()
        {
            // CSS Files
            wp_enqueue_style('font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0');
            wp_enqueue_style('vcw-style', VCW_URL.'assets/css/vcw.css', array('font-awesome'), VCW_VERSION);

            // JS Files

            wp_enqueue_script('jquery', '', array(), false, true);
            wp_enqueue_script('money-js','https://cdnjs.cloudflare.com/ajax/libs/money.js/0.2.0/money.min.js', array(), '0.2.0', true);
            wp_enqueue_script('vcw-script', VCW_URL.'assets/js/vcw.min.js', array('jquery','money-js'), VCW_VERSION, true);
            wp_add_inline_script('vcw-script', 'var VirtualCoinWidgets = {ajax_url: "'.VCW_AJAX_URL.'"};','before');
        }

        static public function priceLabel($attrs)
        {
            extract(shortcode_atts(array(
                'id'        => 'bitcoin',
                'color'     => 'white',
                'currency'  => 'USD',
                'target'    => '_blank',
                'url'       => null,
                'fullwidth' => 'no',
                'show_logo' => 'yes'
            ), $attrs ));

            $widget = new VCW_PriceLabelWidget();

            $widget->setId($id);
            $widget->setColor($color);
            $widget->setCurrency($currency);
            $widget->setLinkTarget($target);
            $widget->setLinkURL($url);
            $widget->setFullWidth($fullwidth);
            $widget->setShowLogo($show_logo);

            return $widget->build();
        }

        static public function changeLabel($attrs)
        {
            extract(shortcode_atts(array(
                'id'        => 'bitcoin',
                'color'     => 'white',
                'target'    => '_blank',
                'url'       => null,
                'fullwidth' => 'no',
                'show_logo' => 'yes'
            ), $attrs ));

            $widget = new VCW_ChangeLabelWidget();

            $widget->setId($id);
            $widget->setColor($color);
            $widget->setLinkTarget($target);
            $widget->setLinkURL($url);
            $widget->setFullWidth($fullwidth);
            $widget->setShowLogo($show_logo);

            return $widget->build();
        }

        static public function priceBigLabel($attrs)
        {

            extract(shortcode_atts( array(
                'id'        => 'bitcoin',
                'color'     => 'white',
                'currency1' => 'USD',
                'currency2' => 'EUR',
                'currency3' => 'GBP',
                'target'    => '_blank',
                'url'       => null,
                'fullwidth' => 'no',
                'show_logo' => 'yes'
            ), $attrs ));

            $widget = new VCW_PriceBigLabelWidget();

            $widget->setId($id);
            $widget->setColor($color);
            $widget->setLinkTarget($target);
            $widget->setLinkURL($url);
            $widget->setFullWidth($fullwidth);
            $widget->setCurrencies($currency1, $currency2, $currency3);
            $widget->setShowLogo($show_logo);

            return $widget->build();
        }

        static public function changeBigLabel($attrs)
        {
            extract(shortcode_atts(array(
                'id'        => 'bitcoin',
                'color'     => 'white',
                'target'    => '_blank',
                'url'       => null,
                'fullwidth' => 'no',
                'show_logo' => 'yes'
            ), $attrs ));

            $widget = new VCW_ChangeBigLabelWidget();

            $widget->setId($id);
            $widget->setColor($color);
            $widget->setLinkTarget($target);
            $widget->setLinkURL($url);
            $widget->setFullWidth($fullwidth);
            $widget->setShowLogo($show_logo);

            return $widget->build();
        }

        static public function priceCard($attrs)
        {
            extract(shortcode_atts( array(
                'id'        => 'bitcoin',
                'color'     => 'white',
                'currency1' => 'USD',
                'currency2' => 'EUR',
                'currency3' => 'GBP',
                'target'    => '_blank',
                'url'       => null,
                'fullwidth' => 'no',
                'show_logo' => 'yes'
            ), $attrs ));

            $widget = new VCW_PriceCardWidget();

            $widget->setId($id);
            $widget->setColor($color);
            $widget->setLinkTarget($target);
            $widget->setLinkURL($url);
            $widget->setFullWidth($fullwidth);
            $widget->setCurrencies($currency1, $currency2, $currency3);
            $widget->setShowLogo($show_logo);

            return $widget->build();
        }

        static public function changeCard($attrs)
        {
            extract(shortcode_atts(array(
                'id'        => 'bitcoin',
                'color'     => 'white',
                'target'    => '_blank',
                'url'       => null,
                'fullwidth' => 'no',
                'show_logo' => 'yes'
            ), $attrs ));

            $widget = new VCW_ChangeCardWidget();

            $widget->setId($id);
            $widget->setColor($color);
            $widget->setLinkTarget($target);
            $widget->setLinkURL($url);
            $widget->setFullWidth($fullwidth);
            $widget->setShowLogo($show_logo);

            return $widget->build();
        }

        static public function fullCard($attrs)
        {
            extract(shortcode_atts( array(
                'id'            => 'bitcoin',
                'color'         => 'white',
                'currency1'     => 'USD',
                'currency2'     => 'EUR',
                'currency3'     => 'GBP',
                'target'        => '_blank',
                'url'           => null,
                'fullwidth'     => 'no',
                'show_logo'     => 'yes'
            ), $attrs ));

            $widget = new VCW_FullCardWidget();

            $widget->setId($id);
            $widget->setColor($color);
            $widget->setLinkTarget($target);
            $widget->setLinkURL($url);
            $widget->setFullWidth($fullwidth);
            $widget->setCurrencies($currency1, $currency2, $currency3);
            $widget->setShowLogo($show_logo);

            return $widget->build();
        }

        static public function converter($attrs)
        {

            extract(shortcode_atts( array(
                'symbol1'   => 'BTC',
                'symbol2'   => 'USD',
                'color'     => 'white',
                'initial'   => '1',
                'fullwidth' => 'no'
            ), $attrs ));

            $widget = new VCW_ConverterWidget();

            $widget->setSymbols($symbol1, $symbol2);
            $widget->setColor($color);
            $widget->setFullWidth($fullwidth);
            $widget->setInitial($initial);

            return $widget->build();
        }

        static public function table($attrs)
        {
            extract(shortcode_atts( array(
                'ids'       => null,
                'color'     => 'white',
                'currency'  => 'USD',
                'count'     => 25,
                'fullwidth' => 'no'
            ), $attrs ));

            if(!$ids) {
                $ids_array = VCW_Data::topCoinsIds($count);
            }
            else {
                $ids_array = explode(',', $ids);
            }

            $widget = new VCW_TableWidget();

            $widget->setIds($ids_array);
            $widget->setColor($color);
            $widget->setCurrency($currency);
            $widget->setFullWidth($fullwidth);

            return $widget->build();
        }

        static public function smallTable($attrs)
        {
            extract(shortcode_atts( array(
                'ids'       => null,
                'color'     => 'white',
                'currency'  => 'USD',
                'count'     => 10,
                'fullwidth' => 'no'
            ), $attrs ));

            if(!$ids) {
                $ids_array = VCW_Data::topCoinsIds($count);
            }
            else {
                $ids_array = explode(',', $ids);
            }

            $widget = new VCW_SmallTableWidget();

            $widget->setIds($ids_array);
            $widget->setCurrency($currency);
            $widget->setColor($color);
            $widget->setFullWidth($fullwidth);

            return $widget->build();
        }

    }

    VCW_Shortcodes::init();

}




