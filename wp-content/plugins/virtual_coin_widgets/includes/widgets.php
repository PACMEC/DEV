<?php defined( 'VCW_INDEX' ) or die( '' );

if(!class_exists('VCW_Widget')) {

    abstract class VCW_Widget {

        protected $color            = 'white';
        protected $link_url         = null;
        protected $link_target      = '_self';
        protected $full_width       = false;
        protected $id               = 'bitcoin';
        protected $ids              = array();
        protected $info             = null;
        protected $currency         = 'USD';
        protected $currencies       = array();
        protected $show_logo        = true;

        protected $main_class    = '';

        public function setColor($color)
        {
            if(array_key_exists($color, VCW_Constants::$color_schemas)){
                $this->color = $color;
            }
        }

        public function getColor()
        {
            return $this->color;
        }

        public function setLinkURL($url)
        {
            if(is_string($url)){
                $this->link_url = $url;
            }
        }

        public function getLinkURL()
        {
            return $this->link_url;
        }

        public function setLinkTarget($target)
        {
            if($target === '_self' || $target === '_blank'){
                $this->link_target = $target;
            }
        }

        public function getLinkTarget()
        {
            return $this->link_target;
        }

        public function setFullWidth($enable)
        {
            $this->full_width = $enable === true || $enable === 'yes';
        }

        public function getFullWidth()
        {
            return $this->full_width;
        }

        public function setId($id)
        {
            if(is_string($id)) {
                $this->id = $id;
            }
        }

        public function getId()
        {
            return $this->id;
        }

        public function setCurrency($currency)
        {
            if(is_string($currency)) {
                $this->currency = $currency;
            }
        }

        public function getCurrency()
        {
            return $this->currency;
        }

        public function setCurrencies($currency1, $currency2, $currency3)
        {
            if(is_string($currency1)) {
                $this->currencies[0] = $currency1;
            }

            if(is_string($currency2)) {
                $this->currencies[1] = $currency2;
            }

            if(is_string($currency3)) {
                $this->currencies[2] = $currency3;
            }
        }

        public function getCurrencies()
        {
            return $this->currencies;
        }

        public function setShowLogo($enable)
        {
            $this->show_logo = $enable === true || $enable === 'yes';
        }

        public function classes()
        {
            $classes = "vcw vcw-$this->main_class vcw-$this->color";

            if($this->full_width){
                $classes .= ' vcw-full-width';
            }

            if($this->link_url) {
                $classes .= ' vcw-cursor';
            }

            return $classes;
        }

        protected function buildLink(&$elem)
        {
            if($this->link_url) {
                $elem->data_target   = $this->link_target;
                $elem->data_url      = $this->link_url;
            }
        }

        protected function changeIcon($type)
        {
            $w_or_b = $this->color === 'white' || $this->color === 'black';

            $change = $this->info["change_$type"];

            if(!is_numeric($change)){
                $classes = $w_or_b ? 'fa-question vcw-warn-color' : 'fa-question vcw-text';
            }
            else if($change > 0) {
                $classes = $w_or_b ? 'fa-caret-up vcw-up-color' : 'fa-caret-up vcw-text';
            }
            else if($change < 0) {
                $classes = $w_or_b ? 'fa-caret-down vcw-down-color' : 'fa-caret-down vcw-text';
            }
            else {
                $classes = $w_or_b ? 'fa-circle-o vcw-around-color' : 'fa-circle-o vcw-text';
            }

            return VCW_HTML::i("vcw fa $classes");
        }

        protected function quotation($show_unit = true, $currency = null)
        {
            if(!is_array($this->info)) {
                return '---';
            }

            $currency       = $currency ?: $this->currency;
            $rate           = VCW_Data::rate($currency);
            $lower_code     = strtolower($currency);
            $price_value    = empty($this->info['price'][$lower_code]) ? '?' : VCW_Helpers::price_format($this->info['price'][$lower_code]);
            $id             = $this->info['id'];

            return $show_unit ?
                "{$rate['unit']} <price-output data-currency=\"$currency\" data-id=\"$id\">$price_value</price-output>" :
                "<price-output data-currency=\"$currency\" data-id=\"$id\">$price_value</price-output>";
        }

        protected function pricesBlock()
        {
            $prices = array();

            foreach ($this->currencies as $n => $currency) {
                $prices[] = VCW_HTML::div('vcw-price', array(
                    VCW_HTML::div('vcw-currency vcw-header', $currency),
                    VCW_HTML::div('vcw-value vcw-text', $this->quotation(false, $currency)),
                ));
            }

            return VCW_HTML::div('vcw-prices', $prices);
        }

        protected function change($type, $percent_sign = true)
        {
            $change = $this->info["change_$type"];

            if(!is_numeric($change)) {
                return '---';
            }

            $change_str = VCW_Helpers::number_format(abs($change), 2);
            return $percent_sign ? "$change_str %" : $change_str;
        }

        protected function changesBlock()
        {
            $changes = array();

            foreach (array('1h','24h','7d') as $type) {

                $changes[] = VCW_HTML::div('vcw-change', array(
                    VCW_HTML::div('vcw-title vcw-header', VCW_Constants::$widgets_change_headers["change_$type"]),
                    VCW_HTML::div('vcw-value', array(
                        $this->changeIcon($type),
                        VCW_HTML::div('vcw-number vcw-text', $this->change($type, false))
                    ))
                ));
            }

            return VCW_HTML::div('vcw-changes', $changes);
        }

        protected function logoImage($src)
        {
            return VCW_HTML::img(array('class'=>'vcw-logo','src' => $src ?: '//:0'));
        }

        abstract public function build();

    }

    class VCW_PriceLabelWidget extends VCW_Widget {

        protected $main_class = 'price-label';

        public function build()
        {
            $this->info = VCW_Data::coin($this->id);
            if(!$this->info) return '';

            $elem = VCW_HTML::div($this->classes());

            $this->buildLink($elem);

            if($this->show_logo) {
                $elem->addChildren($this->logoImage($this->info['image_small']));
            }

            $elem->addChildren(array(
                VCW_HTML::div('vcw-name vcw-header', $this->info['name']),
                VCW_HTML::div('vcw-number vcw-text', $this->quotation()),
            ));

            return (string) $elem;
        }

    }

    class VCW_ChangeLabelWidget extends VCW_Widget {

        protected $main_class = 'change-label';

        public function build()
        {
            $this->info = VCW_Data::coin($this->id);
            if(!$this->info) return '';

            $elem = VCW_HTML::div($this->classes());

            $this->buildLink($elem);

            if($this->show_logo) {
                $elem->addChildren($this->logoImage($this->info['image_small']));
            }

            $elem->addChildren(array(
                VCW_HTML::div('vcw-name vcw-header', $this->info['name']),
                $this->changeIcon('24h'),
                VCW_HTML::div('vcw-change vcw-text', $this->change('24h'))
            ));

            return (string) $elem;
        }

    }

    class VCW_PriceBigLabelWidget extends VCW_Widget {

        protected $main_class = 'price-big-label';

        public function build()
        {
            $this->info = VCW_Data::coin($this->id);
            if(!$this->info) return '';

            $elem = VCW_HTML::div($this->classes());

            $this->buildLink($elem);

            if($this->show_logo) {
                $elem->addChildren($this->logoImage($this->info['image_small']));
            }

            $elem->addChildren(array(
                VCW_HTML::div('vcw-symbol vcw-header', $this->info['symbol']),
                $this->pricesBlock()
            ));

            return (string) $elem;
        }

    }

    class VCW_ChangeBigLabelWidget extends VCW_Widget {

        protected $main_class = 'change-big-label';

        public function build()
        {
            $this->info = VCW_Data::coin($this->id);
            if(!$this->info) return '';

            $elem = VCW_HTML::div($this->classes());

            $this->buildLink($elem);

            if($this->show_logo) {
                $elem->addChildren($this->logoImage($this->info['image_small']));
            }

            $elem->addChildren(array(
                VCW_HTML::div('vcw-symbol vcw-header', $this->info['symbol']),
                $this->changesBlock()
            ));

            return (string) $elem;
        }

    }

    class VCW_PriceCardWidget extends VCW_Widget {

        protected $main_class = 'price-card';

        public function build()
        {
            $this->info = VCW_Data::coin($this->id);
            if(!$this->info) return '';

            $elem = VCW_HTML::div($this->classes());

            $this->buildLink($elem);

            if($this->show_logo) {
                $elem->addChildren($this->logoImage($this->info['image_small']));
            }
            else {
                $elem->addChildren(VCW_HTML::div('vcw-symbol vcw-text', $this->info['symbol']));
            }

            $elem->addChildren(array(
                VCW_HTML::div('vcw-name vcw-header', $this->info['name']),
                $this->pricesBlock()
            ));

            return (string) $elem;
        }

    }

    class VCW_ChangeCardWidget extends VCW_Widget {

        protected $main_class = 'change-card';

        public function build()
        {
            $this->info = VCW_Data::coin($this->id);
            if(!$this->info) return '';

            $elem = VCW_HTML::div($this->classes());

            $this->buildLink($elem);

            if($this->show_logo) {
                $elem->addChildren($this->logoImage($this->info['image_small']));
            }
            else {
                $elem->addChildren(VCW_HTML::div('vcw-symbol vcw-text', $this->info['symbol']));
            }

            $elem->addChildren(VCW_HTML::div('vcw-right', array(
                VCW_HTML::div('vcw-name vcw-header', $this->info['name']),
                $this->changesBlock()
            )));

            return (string) $elem;
        }

    }

    class VCW_FullCardWidget extends VCW_Widget {

        protected $main_class = 'full-card';

        public function build()
        {
            $this->info = VCW_Data::coin($this->id);
            if(!$this->info) return '';

            $elem = VCW_HTML::div($this->classes());

            $this->buildLink($elem);

            if($this->show_logo) {
                $elem->addChildren($this->logoImage($this->info['image_small']));
            }
            else {
                $elem->addChildren(VCW_HTML::div('vcw-symbol vcw-text', $this->info['symbol']));
            }

            $elem->addChildren(array(
                VCW_HTML::div('vcw-name vcw-header', $this->info['name']),
                $this->changesBlock(),
                VCW_HTML::div('vcw-divider'),
                $this->pricesBlock()
            ));

            return (string) $elem;
        }

    }

    class VCW_ConverterWidget extends VCW_Widget {

        protected $main_class = 'converter';

        protected $symbols    = array();
        protected $initial    = 1;

        public function __construct()
        {
            $this->symbols[0] = 'BTC';
            $this->symbols[1] = 'USD';
        }

        public function setSymbols($symbol1, $symbol2)
        {
            $this->symbols[0] = $symbol1;
            $this->symbols[1] = $symbol2;
        }

        public function getSymbols()
        {
            return $this->symbols;
        }

        public function setInitial($initial)
        {
            $this->initial = floatval($initial);
        }

        public function getInitial()
        {
            return $this->initial;
        }

        protected function rateOptions($selected = null)
        {
            $priority   = VCW_Constants::$converter_widget_priority;
            $rates      = VCW_Data::allCurrenciesRates();
            $options_1  = VCW_HTML::optgroup();

            foreach ($priority as $code) {
                if(!array_key_exists($code, $rates)) continue;

                $rate = $rates[$code];
                $attrs = array('value' => $code);

                if($selected === $code) {
                    $attrs['selected'] = '';
                }

                $options_1->addChildren(VCW_HTML::option($attrs, $rate['name']));
            }

            $options_2  = VCW_HTML::optgroup();

            foreach ($rates as $code => $rate) {
                if(in_array($code, $priority)) continue;

                $attrs = array('value' => $code);

                if($selected === $code) {
                    $attrs['selected'] = '';
                }

                $options_2->addChildren(VCW_HTML::option($attrs, $rate['name']));
            }

            return array($options_1, $options_2);
        }

        public function build()
        {
            $elem = VCW_HTML::div($this->classes());

            $elem->addChildren(array(
                VCW_HTML::div('vcw-input', array(
                    VCW_HTML::div('vcw-input-child', VCW_HTML::select('vcw-currency-1', $this->rateOptions($this->symbols[0]))),
                    VCW_HTML::div('vcw-input-child', VCW_HTML::input(array('class' => 'vcw-value-1', 'value' => $this->initial)))
                )),
                VCW_HTML::div('vcw-input', array(
                    VCW_HTML::div('vcw-input-child', VCW_HTML::select('vcw-currency-2', $this->rateOptions($this->symbols[1]))),
                    VCW_HTML::div('vcw-input-child', VCW_HTML::input('vcw-value-2'))
                ))
            ));

            return (string) $elem;
        }

    }

    class VCW_TableWidget extends VCW_Widget {

        protected $main_class = 'table';

        protected $ids   = array();

        public function setIds($ids)
        {
            if(is_array($ids)){
                $this->ids = $ids;
            }
        }

        public function getIds()
        {
            return $this->ids;
        }

        public function build()
        {
            $elem = VCW_HTML::table($this->classes());
            $rows = array();

            foreach ($this->ids as $id) {
                $this->info = VCW_Data::coin($id);

                $rows[] = VCW_HTML::tr(null, array(
                    VCW_HTML::td('vcw-name vcw-text',    $this->info['name']),
                    VCW_HTML::td('vcw-rate vcw-text',    $this->quotation(false)),
                    VCW_HTML::td('vcw-change vcw-text',  array($this->changeIcon('1h'), ' ', $this->change('1h'))),
                    VCW_HTML::td('vcw-change vcw-text',  array($this->changeIcon('24h'), ' ', $this->change('24h'))),
                    VCW_HTML::td('vcw-change vcw-text',  array($this->changeIcon('7d'), ' ', $this->change('7d')))
                ));
            }

            $elem->addChildren(array(
                VCW_HTML::thead(null, VCW_HTML::tr(null, array(
                    VCW_HTML::th('vcw-name vcw-header',    VCW_Constants::$table_widget_cols['name']),
                    VCW_HTML::th('vcw-rate vcw-header',    $this->currency),
                    VCW_HTML::th('vcw-change vcw-header',  VCW_Constants::$table_widget_cols['change_1h']),
                    VCW_HTML::th('vcw-change vcw-header',  VCW_Constants::$table_widget_cols['change_24h']),
                    VCW_HTML::th('vcw-change vcw-header',  VCW_Constants::$table_widget_cols['change_7d'])
                ))),
                VCW_HTML::tbody(null, $rows)
            ));

            return (string) $elem;
        }

    }

    class VCW_SmallTableWidget extends VCW_Widget {

        protected $main_class = 'small-table';

        protected $ids    = array();

        public function setIds($ids)
        {
            if(is_array($ids)){
                $this->ids = $ids;
            }
        }

        public function getIds()
        {
            return $this->ids;
        }

        public function build()
        {
            $elem = VCW_HTML::table($this->classes());
            $rows = array();

            foreach ($this->ids as $id) {
                $this->info = VCW_Data::coin($id);

                $rows[] = VCW_HTML::tr(null, array(
                    VCW_HTML::td('vcw-symbol vcw-text',  $this->info['symbol']),
                    VCW_HTML::td('vcw-rate vcw-text',    $this->quotation(false)),
                    VCW_HTML::td('vcw-change vcw-text',  array($this->changeIcon('1h'), ' ', $this->change('1h', false))),
                    VCW_HTML::td('vcw-change vcw-text',  array($this->changeIcon('24h'), ' ', $this->change('24h', false))),
                    VCW_HTML::td('vcw-change vcw-text',  array($this->changeIcon('7d'), ' ', $this->change('7d', false)))
                ));
            }

            $elem->addChildren(array(
                VCW_HTML::thead(null, VCW_HTML::tr(null, array(
                    VCW_HTML::th('vcw-symbol vcw-header',  VCW_Constants::$small_table_widget_cols['symbol']),
                    VCW_HTML::th('vcw-rate vcw-header',    $this->currency),
                    VCW_HTML::th('vcw-change vcw-header',  VCW_Constants::$small_table_widget_cols['change_1h']),
                    VCW_HTML::th('vcw-change vcw-header',  VCW_Constants::$small_table_widget_cols['change_24h']),
                    VCW_HTML::th('vcw-change vcw-header',  VCW_Constants::$small_table_widget_cols['change_7d'])
                ))),
                VCW_HTML::tbody(null, $rows)
            ));

            return (string) $elem;
        }

    }

}