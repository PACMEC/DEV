<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 26/03/19
 * Time: 06:05 PM
 */

class WC_Shipping_Method_Shipping_Servientrega_WC extends WC_Shipping_Method
{

    public function __construct($instance_id = 0)
    {
        parent::__construct($instance_id);

        $this->id = 'shipping_servientrega_wc';
        $this->instance_id = absint( $instance_id );
        $this->method_title = __( 'Servientrega' );
        $this->method_description = __( 'Servientrega empresa transportadora de Colombia' );
        $this->title = __( 'Servientrega' );

        $wc_main_settings = get_option('woocommerce_servientrega_shipping_settings');

        $this->debug = $wc_main_settings['servientrega_debug'] ?? 'no';
        $this->isTest = isset($wc_main_settings['servientrega_production']) ? $wc_main_settings['servientrega_production'] : false;
        $this->guide_free_shipping = isset($wc_main_settings['servientrega_guide_free_shipping']) ? $wc_main_settings['servientrega_guide_free_shipping'] : false;
        $this->num_recaudo = isset($wc_main_settings['servientrega_num_recaudo']) ? $wc_main_settings['servientrega_num_recaudo'] : false;
        $this->user = isset($wc_main_settings['servientrega_user']) ? $wc_main_settings['servientrega_user'] : '';
        $this->password = isset($wc_main_settings['servientrega_password']) ? $wc_main_settings['servientrega_password'] : '';
        $this->billing_code = isset($wc_main_settings['servientrega_billing_code']) ? $wc_main_settings['servientrega_billing_code'] : '';
        $this->id_client = isset($wc_main_settings['servientrega_id_client']) ? $wc_main_settings['servientrega_id_client'] : '';
        $this->address_sender = isset($wc_main_settings['servientrega_address_sender']) ? $wc_main_settings['servientrega_address_sender'] : '';
        $this->servientrega_product_type = isset($wc_main_settings['servientrega_product_type']) ? $wc_main_settings['servientrega_product_type'] : 2;
        $this->servientrega_print_type = isset($wc_main_settings['servientrega_print_type']) ? $wc_main_settings['servientrega_print_type'] : 2;
        $this->rates_servientrega = isset($wc_main_settings['rate']) ? $wc_main_settings['rate'] : [];

        $this->supports = array(
            'settings',
            'shipping-zones'
        );

        $this->init();
    }

    public function is_available($package)
    {

        return parent::is_available($package) &&
            $this->user &&
            $this->password &&
            $this->billing_code &&
            $this->id_client &&
            $this->address_sender;
    }

    /**
     * Init the class settings
     */
    public function init()
    {
        // Load the settings API.
        $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings.
    }

    public function init_form_fields()
    {
        if(isset($_GET['page']) && $_GET['page'] === 'wc-settings')
            $this->form_fields = include(dirname(__FILE__) . '/admin/settings.php');
    }

    public function name_tabs()
    {
        $servientrega_shipping_tabs = [
          'general',
          'rates',
          'license'
        ];

        return apply_filters( 'servientrega_shipping_tabs', $servientrega_shipping_tabs );
    }

    public function add_tab_per_file($tab)
    {
       $tab = file_exists(__DIR__ . "/admin/$tab.php") ? __DIR__ . "/admin/$tab.php" : __DIR__ . "/includes/admin/$tab.php";

       return $tab;
    }

    public function generate_servientrega_tab_box_html()
    {
        include(dirname(__FILE__) . '/admin/tabs.php');
    }

    public function servientrega_shipping_page_tabs($current = 'general')
    {
        $wc_main_settings = get_option('woocommerce_servientrega_shipping_settings');
        $license = $wc_main_settings['servientrega_license'] ?? '';

        if(!empty($license)){
            $acivated_tab_html =  "<small style='color:green;font-size:xx-small;'>(Activada)</small>";

        }else{
            $acivated_tab_html =  "<small style='color:red;font-size:xx-small;'>(Activar)</small>";
        }

        $tabs = array(
            'general' => __("General"),
            'license' => __("Licencia ".$acivated_tab_html)
        );
        $html = '<h2 class="nav-tab-wrapper">';
        foreach ($tabs as $tab => $name) {
               $class = ($tab == $current) ? 'nav-tab-active' : '';
            $style = ($tab == $current) ? 'border-bottom: 1px solid transparent !important;' : '';
            $html .= '<a style="text-decoration:none !important;' . $style . '" class="nav-tab ' . $class .
                '" href="?page=wc-settings&tab=shipping&section=shipping_servientrega_wc&subtab=' . $tab . '">' . $name . '</a>';
        }
        $html .= '</h2>';
        return $html;
    }

    public function calculate_shipping($package = [])
    {
        $country = $package['destination']['country'];
        $state_destination = $package['destination']['state'];
        $city_destination  = $package['destination']['city'];
        $city_destination  = Shipping_Servientrega_WC::get_city($city_destination);

        if($country !== 'CO' || empty($state_destination))
            return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', false, $package, $this );

        $name_state_destination = Shipping_Servientrega_WC::name_destination($country, $state_destination);

        if (empty($name_state_destination))
            return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', false, $package, $this );

        $address_destine = "$city_destination - $name_state_destination";
        $address_destine = Shipping_Servientrega_WC::normalize_string($address_destine);

        if ($this->debug === 'yes')
            shipping_servientrega_wc_ss()->log("address_destine: $address_destine");

        $cities = include dirname(__FILE__) . '/cities.php';

        $destine = array_search($address_destine, Shipping_Servientrega_WC::clean_cities($cities));

        if(!$destine)
            return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', false, $package, $this );

        $destine = Shipping_Servientrega_WC::format_dane_city_code($destine);

        $items = $package['contents'];
        $item = end($items);
        $product = $item['data'];
        $seller = Shipping_Servientrega_WC::get_shop($product->get_id());

        if (isset($seller['address']['city']) && !empty($seller['address']['city'])){
            $city_origin = $seller['address']['city'];
            $city_origin  = Shipping_Servientrega_WC::get_city($city_origin);
            $state_origin = $seller['address']['state'];
            $name_state_origin = Shipping_Servientrega_WC::name_destination($country, $state_origin);
            $address_origin = "$city_origin - $name_state_origin";
            $address_origin = Shipping_Servientrega_WC::normalize_string($address_origin);
            $origin = array_search($address_origin, Shipping_Servientrega_WC::clean_cities($cities));
            if(!$origin)
                return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', false, $package, $this );
            $origin = Shipping_Servientrega_WC::format_dane_city_code($origin);
        }else{
            $origin = Shipping_Servientrega_WC::format_dane_city_code($this->address_sender);
        }

        $data_products = Shipping_Servientrega_WC::dimensions_weight($items);

        if (empty($data_products['name_products']))
            return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', false, $package, $this );

        $data = $this->calculate_cost($data_products, $origin, $destine);

        if (empty((array)$data))
            return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', false, $package, $this );

        $rate = [
            'id' => $this->id,
            'label' => $this->title,
            'cost' => $data->ValorTotal,
            'package' => $package,
        ];

        return $this->add_rate( $rate );

    }

    public function calculate_cost(array $data_products, $origin, $destine)
    {
        $id_product =  1;

        if ($data_products['weight'] == 3){
            $id_product = 2;
        }else if ($data_products['weight'] >= 4){
            $id_product = 6;
        }

        $params = [
            'IdProducto'          => $id_product,
            'NumeroPiezas'        => $id_product === 6 ? count($data_products['pieces']) : 1,
            'Piezas'              => $id_product === 6 ? $data_products['pieces'] :
                [
                    [
                        'Peso'  => $data_products['weight'],
                        'Largo' => ceil($data_products['length']),
                        'Ancho' => ceil($data_products['width']),
                        'Alto'  => ceil($data_products['high'])
                    ]
                ],
            'ValorDeclarado'      => $data_products['total_valorization'],
            'IdDaneCiudadOrigen'  => $origin,
            'IdDaneCiudadDestino' => $destine,
            'EnvioConCobro'       => $this->num_recaudo,
            'FormaPago'           => 2,
            'TiempoEntrega'       => 1,
            'MedioTransporte'     => 1,
            'NumRecaudo'          => 1
        ];

        if ($this->debug === 'yes')
            shipping_servientrega_wc_ss()->log($params);

        $response = Shipping_Servientrega_WC::liquidation($params);

        if ($this->debug === 'yes')
            shipping_servientrega_wc_ss()->log($response);

        return apply_filters( 'servientrega_shipping_calculate_cost', $response, $data_products, $origin, $destine );
    }
}