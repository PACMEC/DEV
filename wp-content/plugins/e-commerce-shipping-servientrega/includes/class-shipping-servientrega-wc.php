<?php

use Servientrega\WebService;

class Shipping_Servientrega_WC extends WC_Shipping_Method_Shipping_Servientrega_WC
{
    public $servientrega;

    public function __construct($instance_id = 0)
    {
        parent::__construct($instance_id);
        $this->servientrega = new WebService($this->user, $this->password, $this->billing_code, $this->id_client, get_bloginfo('name'));
    }

    public static function liquidation(array $params)
    {
        $instance = new self();
        $resp = new stdClass;

        try{
            $resp = $instance->servientrega->liquidation($params);
        }catch (\Exception $exception){
            shipping_servientrega_wc_ss()->log($params);
            shipping_servientrega_wc_ss()->log($exception->getMessage());
        }

        return $resp;
    }

    public static function generate_guide($order_id, $old_status, $new_status, WC_Order $order)
    {
        $sub_orders = get_children( array( 'post_parent' => $order_id, 'post_type' => 'shop_order' ) );

        if ( $sub_orders ) {
            foreach ($sub_orders as $sub) {
                $order = new WC_Order($sub->ID);
                self::exec_guide($order, $new_status);
            }
        }else{
            self::exec_guide($order, $new_status);
        }

        return apply_filters( 'servientrega_generate_guide', $order_id, $old_status, $new_status, $order );

    }

    public static function exec_guide(WC_Order $order, $new_status)
    {
        $wc_main_settings = get_option('woocommerce_servientrega_shipping_settings');
        $wc_main_settings['servientrega_license'];
        $guide_servientrega = get_post_meta($order->get_id(), 'guide_servientrega', true);
        $instance = new self();

        $order_id_origin = self::get_parent_id($order);
        $order_parent = new WC_Order($order_id_origin);

        if (!empty($instance->get_license()) &&
            ($order_parent->has_shipping_method($instance->id) ||
                $order_parent->get_shipping_total() == 0 &&
                $instance->guide_free_shipping) &&
            empty($guide_servientrega) &&
            $new_status === 'processing'){

            $guide = $instance->guide($order);

            if ($guide == new stdClass())
                return;

            if (!$guide->CargueMasivoExternoResult) return;

            $guide_number = $guide->envios->CargueMasivoExternoDTO->objEnvios->EnviosExterno->Num_Guia;

            if ( in_array(
                'woo-advanced-shipment-tracking/woocommerce-advanced-shipment-tracking.php',
                apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
                true
            ) ) {
                if (class_exists('WC_Advanced_Shipment_Tracking_Actions')){
                    $ast  = new WC_Advanced_Shipment_Tracking_Actions;
                    $args = array(
                        'tracking_provider'        => 'servientrega',
                        'tracking_number'          => $guide_number,
                        'date_shipped'             => date('Y-m-d')
                    );

                    $ast->add_tracking_item($order->get_id(), $args);
                }
            }
            $guide_url = sprintf( __( 'Servientrega Código de seguimiento <a target="_blank" href="%1$s">' . $guide_number .'</a>.' ), "https://www.servientrega.com/wps/portal/Colombia/transacciones-personas/rastreo-envios/detalle?id=$guide_number" );
            update_post_meta($order->get_id(), 'guide_servientrega', $guide_number);
            $order->add_order_note($guide_url);
        }
    }

    public function guide(WC_Order $order)
    {
        $instance = new self();

        $nombre_destinatario = $order->get_shipping_first_name() ? $order->get_shipping_first_name() .
            " " . $order->get_shipping_last_name() : $order->get_billing_first_name() .
            " " . $order->get_billing_last_name();
        $direccion_destinatario = $order->get_shipping_address_1() ? $order->get_shipping_address_1() .
            " " . $order->get_shipping_address_2() : $order->get_billing_address_1() .
            " " . $order->get_billing_address_2();
        $state_code = $order->get_shipping_state() ? $order->get_shipping_state() : $order->get_billing_state();
        $country_code = $order->get_shipping_country() ? $order->get_shipping_country() :  $order->get_billing_country();
        $destination_state_name = self::name_destination($country_code, $state_code);
        $destination_state_name = self::short_name_location($destination_state_name);

        $items = $order->get_items();
        $item = end($items);
        $product_id = $item['product_id'];
        $seller = self::get_shop($product_id);

        $origin_state_name = isset($seller['address']['state']) ? self::name_destination($country_code, $seller['address']['state']) : '';
        $origin_state_name = self::short_name_location($origin_state_name);
        $origin_city = $seller['address']['city'] ?? '';
        $origin_city = self::normalize_string($origin_city);
        $origin_city = self::clean_city($origin_city);
        $origin_state_name = $origin_city === 'bogota' ? 'Cundinamarca' : $origin_state_name;

        $city = $order->get_shipping_city() ? $order->get_shipping_city() : $order->get_billing_city();
        $city = self::normalize_string($city);
        $city = self::clean_city($city);

        $city = self::city_differences_by_state($city, $destination_state_name);
        $origin_city = self::city_differences_by_state($origin_city, $origin_state_name);
        $destination_state_name = $city === 'bogota' ? 'Cundinamarca' : $destination_state_name;

        $data_products = self::dimensions_weight($items, true);
        $namesProducts = implode(" ",  $data_products['name_products']);

        $params = [
            'Num_Guia' => 0,
            'Num_Sobreporte' => 0,
            'Num_Piezas' => $this->get_quantity_product($items),
            'Des_TipoTrayecto' => 1, //nacional 2 internacional
            'Ide_Producto' => (int)$instance->servientrega_product_type, //mercancia premier
            'Ide_Destinatarios' => '00000000-0000-0000-0000-000000000000',
            'Ide_Manifiesto' => '00000000-0000-0000-0000-000000000000',
            'Des_FormaPago' => 2, // 2 Crédito 4 contra entrega
            'Des_MedioTransporte' => 1, // terrestre
            'Num_PesoTotal' => $data_products['weight'],
            'Num_ValorDeclaradoTotal' => $data_products['total_valorization'],
            'Num_VolumenTotal' => 0, // para que se calcule
            'Num_BolsaSeguridad' => 0, //solo para valores, de lo contrario 0
            'Num_Precinto' => 0,
            'Des_TipoDuracionTrayecto' => 1, //1 normal
            'Des_Telefono' => $order->get_billing_phone(),
            'Des_DepartamentoDestino' => $destination_state_name,
            'Des_Ciudad' => $city,
            'Des_Direccion' => $direccion_destinatario,
            'Nom_Contacto' => $nombre_destinatario,
            'Num_ValorLiquidado' => 0, //calculado por el sistem 0 para todos los casos
            'Des_DiceContener' => substr($namesProducts, 0, 50), // el contenido del envío
            'Des_TipoGuia' => 1,
            'Num_VlrSobreflete' => 0,
            'Num_VlrFlete' => 0,
            'Num_Descuento' => 0,
            'Num_PesoFacturado' => 0,
            'idePaisOrigen' => 1, // 1 Colombia
            'idePaisDestino' => 1, // 1 Colombia
            'Des_IdArchivoOrigen' => 0, // para tos los casos
            'Des_DepartamentoOrigen' => $origin_state_name,
            'Des_CiudadRemitente' => $origin_city,
            'Des_DireccionRemitente' => isset($seller['address']) ? "{$seller['address']['street_1']}  {$seller['address']['street_2']}" : '',
            'Num_TelefonoRemitente' => $seller['phone'] ?? '',
            'Est_CanalMayorista' => false,
            'Num_IdentiRemitente' => '',
            'Num_Alto' => $data_products['high'],
            'Num_Ancho' => $data_products['width'],
            'Num_Largo' => $data_products['length'],
            'Gen_Cajaporte' => 0,
            'Gen_Sobreporte' => 0,
            'Nom_UnidadEmpaque' => 'GENERICA',
            'Des_UnidadLongitud' => 'cm',
            'Des_UnidadPeso' => 'kg',
            'Num_ValorDeclaradoSobreTotal' => 0,
            'Num_Factura' => $order->get_id(),
            'Des_CorreoElectronico' => $order->get_billing_email(),
            'Num_Recaudo' => 0,
            'Est_EnviarCorreo' => false
        ];

        $order_id_origin = self::get_parent_id($order);

        if($instance->servientrega_product_type == 6){
            $params['objEnviosUnidadEmpaqueCargue'] = [
                'EnviosUnidadEmpaqueCargue' => $data_products['units']
            ];
        }

        if ($instance->num_recaudo){
            $params['Num_Recaudo'] = wc_format_decimal($order->get_total(), 0);
            $params['Tipo_Doc_Destinatario'] = 'CC';
            $params['Ide_Num_Identific_Dest'] = get_post_meta( $order_id_origin, '_billing_identificacion', true )  ? get_post_meta( $order_id_origin, '_billing_identificacion', true )  : get_post_meta( $order_id_origin, '_shipping_identificacion', true );
        }

        $resp = new stdClass;


        try{
            $resp = $this->servientrega->CargueMasivoExterno($params);
            if ($instance->debug === 'yes') shipping_servientrega_wc_ss()->log($resp);
        }catch (\Exception $exception){
            shipping_servientrega_wc_ss()->log($params);
            shipping_servientrega_wc_ss()->log($exception->getMessage());
        }

        return apply_filters( 'servientrega_guide', $resp, $order);
    }

    public static  function name_destination($country, $state_destination)
    {
        $countries_obj = new WC_Countries();
        $country_states_array = $countries_obj->get_states();

        $name_state_destination = '';

        if(!isset($country_states_array[$country][$state_destination]))
            return $name_state_destination;

        $name_state_destination = $country_states_array[$country][$state_destination];
        $name_state_destination = self::normalize_string($name_state_destination);
        return $name_state_destination;
    }

    public static function short_name_location($name_location)
    {
        if ( 'valle del cauca' === $name_location )
            $name_location =  'valle';
        return $name_location;
    }

    public static function normalize_string($string)
    {
        $not_permitted = array ("á","é","í","ó","ú","Á","É","Í",
            "Ó","Ú","ñ");
        $permitted = array ("a","e","i","o","u","A","E","I","O",
            "U","n");
        $text = str_replace($not_permitted, $permitted, $string);
        $text = mb_strtolower($text);
        return $text;
    }

    public static function clean_city($city)
    {
        return $city == 'bogota d.c' ? 'bogota' : $city;
    }

    public static function clean_cities($cities)
    {
        foreach ($cities as $key => $value){
            $cities[$key] = self::normalize_string($value);
        }

        return $cities;
    }

    public static function format_dane_city_code($code)
    {
        return "{$code}000";
    }

    public function get_quantity_product($items)
    {
        $item_quantity = 0;

        foreach ($items as $item_id => $item_data)

            $item_quantity += $item_data->get_quantity();

        return $item_quantity;
    }

    public static function dimensions_weight($items, $guide = false)
    {
        $data['total_valorization'] = 0;
        $data['high'] = 0;
        $data['length'] = 0;
        $data['width'] = 0;
        $data['weight'] = 0;
        $data['name_products'] = [];
        $data['units'] = [];
        $data['pieces'] = [];
        $total_min_shipping = 6000;

        foreach ( $items as $item => $values ) {
            $_product_id = $guide ? $values['product_id'] : $values['data']->get_id();
            $_product = wc_get_product( $_product_id );

            if ( $values['variation_id'] > 0 && in_array( $values['variation_id'], $_product->get_children() ) )
                $_product = wc_get_product( $values['variation_id'] );

            if ( !$_product->get_weight() || !$_product->get_length()
                || !$_product->get_width() || !$_product->get_height() )
                break;

            $custom_price_product = get_post_meta($_product->get_id(), '_shipping_custom_price_product_smp', true);
            $total_price = $custom_price_product > 0 ? wc_format_decimal($custom_price_product, 0) : wc_format_decimal($_product->get_price(), 0);

            $quantity = $values['quantity'];
            $data['total_valorization'] += $total_price * $quantity;
            $data['name_products'][] = $_product->get_name();
            $data['units'][] = [
                'Num_Alto' => $_product->get_height(),
                'Num_Ancho' => $_product->get_width(),
                'Num_Largo' => $_product->get_length(),
                'Num_Cantidad' => $quantity,
                'Des_DiceContener' => substr($_product->get_name(), 0, 50),
                'Des_IdArchivoOrigen' => 0,
                'Nom_UnidadEmpaque' => 'GENERICA',
                'Num_Peso' => $_product->get_weight(),
                'Des_UnidadLongitud' => 'cm',
                'Des_UnidadPeso' => 'kg',
                'Num_Volumen' => $_product->get_weight(),
                'Num_ValorDeclarado' => wc_format_decimal($_product->get_price(), 0),
                'Num_Distribuidor' => 0,
                'Ide_UnidadEmpaque' => '00000000-0000-0000-0000-000000000000',
                'Ide_Envio' => '00000000-0000-0000-0000-000000000000',
                'Num_Consecutivo' => '0'
            ];

            $data['high'] += $_product->get_height() * $quantity;
            $data['length'] = $_product->get_length() > $data['length'] ? $_product->get_length() : $data['length'];
            $data['weight'] =+ $data['weight'] + ($_product->get_weight() * $quantity);
            $data['width'] =  $_product->get_width() > $data['width'] ? $_product->get_width() : $data['width'];

            if ($guide) break;

            for ($i = 1; $i <= $quantity; $i++){
                $data['pieces'][] = [
                    'Peso'  => $_product->get_weight(),
                    'Largo' => ceil($_product->get_length()),
                    'Ancho' => ceil($_product->get_width()),
                    'Alto'  => ceil($_product->get_height())
                ];
            }
        }

        $instance = new self();

        $data['weight'] = ceil($data['weight']);
        if ($instance->servientrega_product_type === '2' && $data['weight'] < 3)
            $data['weight'] = 3;

        $data['total_valorization'] = $data['total_valorization'] < $total_min_shipping ? $total_min_shipping : $data['total_valorization'];

        return apply_filters( 'servientrega_dimensions_weight', $data, $items, $guide );
    }

    public static function get_shop($product_id)
    {
        $id = get_post_field( 'post_author', $product_id );
        $store = function_exists('dokan_get_store_info') && dokan_get_store_info($id) ? dokan_get_store_info($id) : null;

        return apply_filters('shipping_servientrega_get_shop', $store, $product_id);
    }

    public static function get_parent_id(WC_Order $order)
    {
        return $order->get_parent_id() > 0 ? $order->get_parent_id() : $order->get_id();
    }

    public static function generate_stickers($guide_number)
    {
        $instance = new self();
        $sticker = array();

        $params = [
            'num_Guia' => $guide_number,
            'num_GuiaFinal' => $guide_number,
            'sFormatoImpresionGuia' => (int)$instance->servientrega_print_type,
            'Id_ArchivoCargar' => '0',
            'interno' => false
        ];

        try{
            $sticker = $instance->servientrega->GenerarGuiaSticker($params);
        }catch (\Exception $exception){
            shipping_servientrega_wc_ss()->log($params);
            shipping_servientrega_wc_ss()->log($exception->getMessage());
        }

        return $sticker;
    }

    public static function get_city($city)
    {
        $city = self::normalize_string($city);
        $city = self::clean_city($city);

        return $city;
    }

    public static function city_differences_by_state($city, $destination_state_name)
    {
        if ($city === 'rionegro' && $destination_state_name === 'antioquia')
            $city = "$city (ANT)";
        if ($city === 'argelia' && $destination_state_name === 'antioquia')
            $city = "$city (ANT)";
        if ($city === 'armenia' && $destination_state_name === 'antioquia')
            $city = "$city (ANT)";
        if ($city === 'argelia' && $destination_state_name === 'cauca')
            $city = "$city (CAU)";
        if ($city === 'argelia' && $destination_state_name === 'valle')
            $city = "$city (VAC)";
        if ($city === 'granada' && $destination_state_name === 'meta')
            $city = "$city (MET)";
        if ($city === 'granada' && $destination_state_name === 'antioquia')
            $city = "$city (ANT)";
        if ($city === 'granada' && $destination_state_name === 'cundinamarca')
            $city = "$city (CUN)";
        if ($city === 'zaragoza' && $destination_state_name === 'antioquia')
            $city = "$city (ANT)";
        if ($city === 'zaragoza' && $destination_state_name === 'valle')
            $city = "$city (VAC)";
        if ($city === 'venecia' && $destination_state_name === 'antioquia')
            $city = "$city (ANT)";
        if ($city === 'venecia' && $destination_state_name === 'cundinamarca')
            $city = "$city (CUN)";
        if ($city === 'valparaiso' && $destination_state_name === 'antioquia')
            $city = "$city (ANT)";
        if ($city === 'valparaiso' && $destination_state_name === 'caqueta')
            $city = "$city (CAQ)";
        if ($city === 'sucre' && $destination_state_name === 'cauca')
            $city = "$city (CAU)";
        if ($city === 'sucre' && $destination_state_name === 'santander')
            $city = "$city (SAN)";
        if ($city === 'santa ana'   && $destination_state_name === 'bolivar')
            $city = "$city (BOL)";
        if ($city === 'santa ana' && $destination_state_name === 'magdalena')
            $city = "$city (MAG)";
        if ($city === 'san joaquin' && $destination_state_name === 'cundinamarca')
            $city = "$city (CUN)";
        if ($city === 'san joaquin' && $destination_state_name === 'santander')
            $city = "$city (SAN)";

        return $city;
    }

    public function get_license()
    {
        $wc_main_settings = get_option('woocommerce_servientrega_shipping_settings');
        return $wc_main_settings['servientrega_license'] ?? '';
    }

    public static function upgrade_working_plugin($license = null)
    {
        $wc_main_settings = get_option('woocommerce_servientrega_shipping_settings');
        $wc_main_settings['servientrega_license'] = $license ?? $wc_main_settings['servientrega_license'];

        if (empty($wc_main_settings['servientrega_license'])) return;

        $secret_key = '5c88321cdb0dc9.43606608';

        $api_params = array(
            'slm_action' => 'slm_check',
            'secret_key' => $secret_key,
            'license_key' => $wc_main_settings['servientrega_license']
        );

        $siteGet = 'https://shop.saulmoralespa.com';

        $response = wp_remote_get(
            add_query_arg($api_params, $siteGet),
            array('timeout' => 60,
                'sslverify' => true
            )
        );

        if (is_wp_error($response)){
            shipping_servientrega_wc_ss()->log($response->get_error_message());
            return;
        }

        $data = json_decode(wp_remote_retrieve_body($response));

        if (isset($data->result) && $data->result === 'error') return;

        //max_allowed_domains

        //registered_domains  array() registered_domain

        if ($data->status === 'expired'){
            $wc_main_settings['servientrega_license'] = '';
        }elseif ($data->result === 'success' && $data->status === 'pending'){
            $api_params = array(
                'slm_action' => 'slm_activate',
                'secret_key' => $secret_key,
                'license_key' => $wc_main_settings['servientrega_license'],
                'registered_domain' => get_bloginfo( 'url' ),
                'item_reference' => '',
            );

            $query = esc_url_raw(add_query_arg($api_params, $siteGet));
            $response = wp_remote_get($query,
                array('timeout' => 60,
                    'sslverify' => true
                )
            );

            if (is_wp_error($response)){
                shipping_servientrega_wc_ss()->log( $response->get_error_message() );
                return;
            }

            $data = json_decode(wp_remote_retrieve_body($response));

            if (isset($data->result) && $data->result === 'error') return;

        }

        update_option('woocommerce_servientrega_shipping_settings',$wc_main_settings);
    }

}