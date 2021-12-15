<?php

use Servientrega\WebService;

$this->init_settings();
global $woocommerce;
$wc_main_settings = [];

if(isset($_POST['servientrega_validate_credentials']) || isset($_POST['servientrega_genaral_save_changes_button']))
{
    if ( !isset( $_POST['shipping_servientrega_wc_ss_general'] )
        || !wp_verify_nonce( $_POST['shipping_servientrega_wc_ss_general'], 'shipping_servientrega_wc_ss_general' ))
        return;

    save_configuration();
}

function save_configuration()
{
    $test_mode = (isset($_POST['servientrega_production']) && $_POST['servientrega_production'] === 'yes') ? true : false;
    $debug = $_POST['servientrega_debug'] ?? 'no';
    $guide_free_shipping = (isset($_POST['servientrega_guide_free_shipping']) && $_POST['servientrega_guide_free_shipping'] === 'yes') ? true : false;
    $num_recaudo = (isset($_POST['servientrega_num_recaudo']) && $_POST['servientrega_num_recaudo'] === 'yes') ? true : false;

    $wc_main_settings = get_option('woocommerce_servientrega_shipping_settings');
    $wc_main_settings['production'] = $test_mode;
    $wc_main_settings['servientrega_debug'] = $debug;
    $wc_main_settings['servientrega_guide_free_shipping'] = $guide_free_shipping;
    $wc_main_settings['servientrega_num_recaudo'] = $num_recaudo;
    $wc_main_settings['servientrega_user'] = (isset($_POST['servientrega_user'])) ? sanitize_text_field($_POST['servientrega_user']) : 'testajagroup';
    $wc_main_settings['servientrega_password'] = (isset($_POST['servientrega_password'])) ? sanitize_text_field($_POST['servientrega_password']) : 'Colombia1';
    $wc_main_settings['servientrega_billing_code'] = (isset($_POST['servientrega_billing_code'])) ? sanitize_text_field($_POST['servientrega_billing_code']) : 'Cargue SMP';
    $wc_main_settings['servientrega_id_client'] = (isset($_POST['servientrega_id_client'])) ? sanitize_text_field($_POST['servientrega_id_client']) : '900917801';
    $wc_main_settings['servientrega_address_sender'] = (isset($_POST['servientrega_address_sender'])) ? sanitize_text_field($_POST['servientrega_address_sender']) : '';
    $wc_main_settings['servientrega_product_type'] = (isset($_POST['servientrega_product_type'])) ? sanitize_text_field($_POST['servientrega_product_type']) : '';
    $wc_main_settings['servientrega_print_type'] = (isset($_POST['servientrega_print_type'])) ? sanitize_text_field($_POST['servientrega_print_type']) : '';

    update_option('woocommerce_servientrega_shipping_settings', $wc_main_settings);

    servientrega_validate_credentials($wc_main_settings);
}

function servientrega_validate_credentials($wc_main_settings){

    update_option('servientrega_validation_error', '');

    if(strpos($wc_main_settings['servientrega_billing_code'], 'SER') === false){
        update_option('servientrega_validation_error','<small style="color:red">Revise que este ingresando el Código de facturación correctamente</small>');
        $wc_main_settings = get_option('woocommerce_servientrega_shipping_settings');
        $wc_main_settings['servientrega_billing_code'] = '';
        update_option('woocommerce_servientrega_shipping_settings',$wc_main_settings);
        return false;
    }

    $wc_main_settings = get_option('woocommerce_servientrega_shipping_settings');
    $license = $wc_main_settings['servientrega_license'] ?? '';

    try{
        $servientrega = new WebService($wc_main_settings['servientrega_user'], $wc_main_settings['servientrega_password'], $wc_main_settings['servientrega_billing_code'], $wc_main_settings['servientrega_id_client'], get_bloginfo('name') );

        $unidades = [];

        $unidades[] = [
            'Num_Alto' => 1,
            'Num_Ancho' => 1,
            'Num_Largo' => 1,
            'Num_Cantidad' => 5,
            'Des_DiceContener' => 'mercancia',
            'Des_IdArchivoOrigen' => 1,
            'Nom_UnidadEmpaque' => 'GENERICA',
            'Num_Peso' => 3,
            'Des_UnidadLongitud' => 'cm',
            'Des_UnidadPeso' => 'kg',
            'Num_Volumen' => 3,
            'Num_ValorDeclarado' => 50000,
            'Num_Distribuidor' => 0,
            'Ide_UnidadEmpaque' => '00000000-0000-0000-0000-000000000000',
            'Ide_Envio' => '00000000-0000-0000-0000-000000000000',
            'Num_Consecutivo' => '0'
        ];

        $params = array(
            'Num_Guia' => 0,
            'Num_Sobreporte' => 0,
            'Num_Piezas' => 1,
            'Des_TipoTrayecto' => 1,
            'Ide_Producto' => (int)$wc_main_settings['servientrega_product_type'],
            'Ide_Destinatarios' => '00000000-0000-0000-0000-000000000000',
            'Ide_Manifiesto' => '00000000-0000-0000-0000-000000000000',
            'Des_FormaPago' => 2, //crédito
            'Des_MedioTransporte' => 1,
            'Num_PesoTotal' => $wc_main_settings['servientrega_product_type'] == 1 ? 2 : 3,
            'Num_ValorDeclaradoTotal' => 50000,
            'Num_VolumenTotal' => 0,
            'Num_BolsaSeguridad' => 0,
            'Num_Precinto' => 0,
            'Des_TipoDuracionTrayecto' => 1,
            'Des_Telefono' => 7700380,
            'Des_Ciudad' => 'Rionegro (ANT)',
            'Des_Direccion' => 'CALLE 5 # 66-44',
            'Nom_Contacto' => 'No despachar',
            'Des_VlrCampoPersonalizado1' => '',
            'Num_ValorLiquidado' => 0,
            'Des_DiceContener' => 'PAQUETE ESTANDAR',
            'Des_TipoGuia' => 1,
            'Num_VlrSobreflete' => 0,
            'Num_VlrFlete' => 0,
            'Num_Descuento' => 0,
            'idePaisOrigen' => 1,
            'idePaisDestino' => 1,
            'Des_IdArchivoOrigen' => 1,
            'Des_DireccionRemitente' => '',
            'Num_PesoFacturado' => 0,
            'Est_CanalMayorista' => false,
            'Num_IdentiRemitente' => '',
            'Num_TelefonoRemitente' => '',
            'Num_Alto' => 1,
            'Num_Ancho' => 1,
            'Num_Largo' => 1,
            'Des_DepartamentoDestino' => 'Antioquia',
            'Des_DepartamentoOrigen' => '',
            'Gen_Cajaporte' => 0,
            'Gen_Sobreporte' => 0,
            'Nom_UnidadEmpaque' => 'GENERICA',
            'Des_UnidadLongitud' => 'cm',
            'Des_UnidadPeso' => 'kg',
            'Num_ValorDeclaradoSobreTotal' => 0,
            'Num_Factura' => 'FACT-001',
            'Des_CorreoElectronico' => 'cortuclas@gmail.com',
            'Num_Recaudo' => 0,
            'Est_EnviarCorreo' => false,
            'Tipo_Doc_Destinatario' => 'CC',
            'Ide_Num_Identific_Dest' => '1099163824'
        );

        if($wc_main_settings['servientrega_product_type'] == 6){
            $params['objEnviosUnidadEmpaqueCargue'] = [
                'EnviosUnidadEmpaqueCargue' => $unidades
            ];
        }

        if ($wc_main_settings['servientrega_num_recaudo']){
            $params['Num_Recaudo'] = 50000;
            $params['Tipo_Doc_Destinatario'] = 'CC';
            $params['Ide_Num_Identific_Dest'] = '1094163892';
        }

        $servientrega->CargueMasivoExterno($params);

        $params = [
            'IdProducto'          => $wc_main_settings['servientrega_product_type'],
            'NumeroPiezas'        => 1,
            'Piezas'              =>
                [
                    [
                        'Peso'  => $wc_main_settings['servientrega_product_type'] == 2 ? 3 : 1,
                        'Largo' => 10,
                        'Ancho' => 5,
                        'Alto'  => 10
                    ]
                ],
            'ValorDeclarado'      => 50000,
            'IdDaneCiudadOrigen'  => $wc_main_settings['servientrega_address_sender'] . '000',
            'IdDaneCiudadDestino' => '11001000',
            'EnvioConCobro'       => $wc_main_settings['servientrega_num_recaudo'],
            'FormaPago'           => 2,
            'TiempoEntrega'       => 1,
            'MedioTransporte'     => 1,
            'NumRecaudo'          => 1
        ];

        $servientrega->liquidation($params);

    }catch (Exception $exception){
        $message_error = '<small style="color:red">' . $exception->getMessage() . '</small>';
        $logger = new WC_Logger();
        $logger->add('shipping-servientrega', $exception->getMessage());
        $logger->add('shipping-servientrega', "License: $license");
        if (strpos($exception->getMessage(), 'failed to load external entity') !== false)
            $message_error = '<small style="color:red">
                El proveedor de alojamiento esta bloqueando el puerto 8081, solicite que lo abran o bien resuelva actualizando a la 
            </small><a 
            target="_blank" href="https://shop.saulmoralespa.com/producto/plugin-shipping-servientrega-woocommerce/">Versión completa
            </a>';

       if($exception->getCode() !== 503) update_option('servientrega_validation_error', $message_error);
        return false;
    }

    return true;

}

$general_settings = get_option('woocommerce_servientrega_shipping_settings');
$general_settings = empty($general_settings) ? array() : $general_settings;
$validation = get_option('wf_dhl_shipping_validation_data');

$address_sender = include dirname(__FILE__) . '/../cities.php';


$products_type = [
    1 => __('Documento unitario'),
    2 => __('Mercancia premier'),
    6 => __('Mercancia industrial')
];


$prints_type = [
    1 => __('Impresión guía bond'),
    2 => __('Impresión guía sticker')
];

$htmlGeneral = '<img style="float:right;" src="' . shipping_servientrega_wc_ss()->assets . trailingslashit('img') . 'servientrega.png' . '" width="80" height="80" />';

$htmlGeneral .= '
<table>
    <tr valign="top">
        <td style="width:25%;padding-top:40px;font-weight:bold;">
            <label for="servientrega_production">' .  __('Información cuenta de Servientrega') . '</label><span class="woocommerce-help-tip" data-tip="' . __('La información suministrada por Servientrega, relacionada con el acuerdo como Usuario, Contraseña, Código de Facturación, Forma de pago') . '"></span>
        </td>' . $this->get_option('woocommerce_servientrega_production') . '
        <td scope="row" class="titledesc" style="display:block;margin-bottom:20px;margin-top:3px;padding-top:40px;">
            <fieldset style="padding:3px;">';
                if(isset($general_settings['production']) && $general_settings['production'] === true)
                {
                    $htmlGeneral .= '<input class="input-text regular-input " type="radio" name="servientrega_production"  id="servientrega_production" ';
                    $disable = ($validation === 'done') ? 'disabled="true" ' : ' ';
                    $htmlGeneral .= $disable;  $htmlGeneral .= 'value="no">' . __('Pruebas');
                    $htmlGeneral .= '<input class="input-text regular-input " type="radio"  name="servientrega_production" checked="true" id="servientrega_production" ';
                    $disable = ($validation === 'done') ? 'disabled="true" ' : ' ';
                    $htmlGeneral .= $disable; $htmlGeneral .= 'value="yes">' . __('Producción');
                 }else {
                    $htmlGeneral .= '<input class="input-text regular-input" type="radio" name="servientrega_production" checked="true" id="servientrega_production" ';
                    $disable = ($validation === 'done') ? 'disabled="true" ' : ' ';
                    $htmlGeneral .= $disable;
                    $htmlGeneral .= 'value="no">' . __('Pruebas');
                    $htmlGeneral .= '<input class="input-text regular-input" type="radio" name="servientrega_production" id="servientrega_production" ';
                    $disable = ($validation === 'done') ? 'disabled="true" ' : ' ';
                    $htmlGeneral .= $disable;
                    $htmlGeneral .= 'value="yes">' . __('Producción') . '</br></fieldset>';
                }
            $htmlGeneral .= '<fieldset style="padding:3px;">
                <input class="input-text regular-input" required type="text" name="servientrega_user" id="servientrega_user" ';
                $disable = ($validation === 'done') ? 'disabled="true" ' : ' ';
                $htmlGeneral .= $disable; $htmlGeneral .= 'value="';
                $value = (isset($general_settings['servientrega_user'])) ? $general_settings['servientrega_user'] : 'testajagroup';
                $htmlGeneral .= "$value\" ";
                $htmlGeneral .= 'placeholder="testajagroup"> <label for="servientrega_user">' . __('Usuario') . '</label> <span class="woocommerce-help-tip" data-tip="' . __('El usario con el que ingresa al SISCLINET') . '"></span>
            </fieldset>';
            $htmlGeneral .= '<fieldset style="padding:3px;">
                <input class="input-text regular-input" required type="password" name="servientrega_password" id="servientrega_password" ';
            $disable =  ($validation === 'done') ? 'disabled="true" ' : ' ';
            $htmlGeneral .= $disable;
            $htmlGeneral .= 'value="';
            $value = (isset($general_settings['servientrega_password'])) ? $general_settings['servientrega_password'] : 'Colombia1';
            $htmlGeneral .= "$value\">"; $htmlGeneral .= '<label for="servientrega_password">' . __('Contraseña') .'</label> <span class="woocommerce-help-tip" data-tip="' . __('La contraseña con la que ingresa SISCLINET') . '"></span>
            </fieldset>';
            $htmlGeneral .= '<fieldset style="padding:3px;">
                <input class="input-text regular-input" required type="text" name="servientrega_billing_code" id="servientrega_billing_code "'; $disable =  ($validation === 'done') ? 'disabled="true "' : ' '; $htmlGeneral .= $disable;  $htmlGeneral .= 'value= "'; $value = (isset($general_settings['servientrega_billing_code'])) ? $general_settings['servientrega_billing_code'] : 'SER408'; $htmlGeneral .= "$value\">"; $htmlGeneral .= '<label for="servientrega_billing_code">' . __('Código de Facturación') . '</label> <span class="woocommerce-help-tip" data-tip="' . __('Código de Facturación lo encuentroa dentro del panel de SISCLINET') . '"></span>
            </fieldset>';
            $htmlGeneral .= '<fieldset style="padding:3px;">
                <input class="input-text regular-input" required type="number" name="servientrega_id_client" id="servientrega_id_client" '; $disable =  ($validation === 'done') ? 'disabled="true "' : ' '; $htmlGeneral .= $disable;  $htmlGeneral .= 'value= "'; $value = (isset($general_settings['servientrega_id_client'])) ? $general_settings['servientrega_id_client'] : '900917801'; $htmlGeneral .= "$value\">"; $htmlGeneral .= '<label for="servientrega_id_client">' . __('ID cliente o NIT') . '</label> <span class="woocommerce-help-tip" data-tip="' . __('Es el mismo número NIT, lo encuentra dentro del panel de SISCLINET') . '"></span>
            </fieldset>';
            $htmlGeneral .= get_option('servientrega_validation_error') . '<fieldset style="padding:3px;">
                <input type="submit" value="Validar Credenciales" class="button button-secondary" name="servientrega_validate_credentials">
            </fieldset>
        </td>
    </tr>
    <tr valign="top">
        <td style="width:25%;font-weight:bold;">
            <label for="servientrega_address_sender">' . __('Ciudad Remitente') . '</label><span class="woocommerce-help-tip" data-tip="' . __('La ciudad de Origen conforme el acuerdo con Servientrega') . '"></span>
        </td>
        <td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">';
        $htmlGeneral .= '<fieldset style="padding:3px;">
                        <select class="wc-enhanced-select" name="servientrega_address_sender" id="servientrega_address_sender" required>';
        $htmlGeneral .= '<option value="">Seleccione ciudad de origen</option>';
        foreach ($address_sender as $key => $address):
            $selected = isset($general_settings['servientrega_address_sender']) && $general_settings['servientrega_address_sender'] === (string)$key ? 'selected' : '';
            $htmlGeneral .= "<option value='{$key}' $selected>{$address}</option>";
        endforeach;
        $htmlGeneral .= '</select>
            </fieldset>
       </td>
    </tr>';
    $htmlGeneral .= '<tr valign="top">
        <td style="width:25%;font-weight:bold;">
            <label for="servientrega_product_type">' . __('Tipo de producto') . '</label><span class="woocommerce-help-tip" data-tip="' . __('El tipo de producto por defecto premier') . '"></span>
        </td>
        <td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
            <fieldset style="padding:3px;">
                <select class="wc-enhanced-select" name="servientrega_product_type" id="servientrega_product_type">';
                    foreach ($products_type as $key => $value):
                        $selected = isset($general_settings['servientrega_product_type']) && $general_settings['servientrega_product_type'] === (string)$key ? 'selected' : '';
                        $htmlGeneral .= "<option value='{$key}' $selected>{$value}</option>";
                    endforeach;
                    $htmlGeneral .= '</select>
            </fieldset>
        </td>
     </tr>';
    $htmlGeneral .= '<tr valign="top">
        <td style="width:25%;font-weight:bold;">
            <label for="servientrega_print_type">' . __('Formato de Impresión guía') . '</label><span class="woocommerce-help-tip" data-tip="' . __('El tipo de formato de impresión de la guía') . '"></span>
        </td>
        <td scope="row" class="titledesc" style="display: block;margin-bottom: 20px;margin-top: 3px;">
            <fieldset style="padding:3px;">
                <select class="wc-enhanced-select" name="servientrega_print_type" id="servientrega_print_type">';
                    foreach ($prints_type as $key => $value):
                        $selected = isset($general_settings['servientrega_print_type']) && $general_settings['servientrega_print_type'] === (string)$key ? 'selected' : '';
                        $htmlGeneral .= "<option value='{$key}' $selected>{$value}</option>";
                    endforeach;
                    $htmlGeneral .= '</select>
            </fieldset>
        </td>
     </tr>';
    $htmlGeneral .= '<tr valign="top">
     <td style="width:25%;font-weight:bold;">
                <label for="servientrega_num_recaudo">' . __('¿ La cuenta logística de recaudo ?') . '</label><span class="woocommerce-help-tip" data-tip="' . __('Sí, cuando la cuenta tiene habilitado logística de recaudo') . '"></span>
            </td>
            <td scope="row" class="titledesc" style="display:block;margin-bottom:20px;">
    <fieldset style="padding:3px;">';
    if(isset($general_settings['servientrega_num_recaudo']) && $general_settings['servientrega_num_recaudo'] === true)
    {
        $htmlGeneral .= '<input class="input-text regular-input " type="radio" name="servientrega_num_recaudo"  id="servientrega_num_recaudo" ';
        $disable = ($validation === 'done') ? 'disabled="true" ' : ' ';
        $htmlGeneral .= $disable;  $htmlGeneral .= 'value="no">' . __('No');
        $htmlGeneral .= '<input class="input-text regular-input " type="radio"  name="servientrega_num_recaudo" checked="true" id="servientrega_num_recaudo" ';
        $disable = ($validation === 'done') ? 'disabled="true" ' : ' ';
        $htmlGeneral .= $disable; $htmlGeneral .= 'value="yes">' . __('Sí');
    }else {
        $htmlGeneral .= '<input class="input-text regular-input" type="radio" name="servientrega_num_recaudo" checked="true" id="servientrega_num_recaudo" ';
        $disable = ($validation === 'done') ? 'disabled="true" ' : ' ';
        $htmlGeneral .= $disable;
        $htmlGeneral .= 'value="no">' . __('No');
        $htmlGeneral .= '<input class="input-text regular-input" type="radio" name="servientrega_num_recaudo" id="servientrega_num_recaudo" ';
        $disable = ($validation === 'done') ? 'disabled="true" ' : ' ';
        $htmlGeneral .= $disable;
        $htmlGeneral .= 'value="yes">' . __('Sí') . '</br></fieldset>';
    }
    $htmlGeneral .= '</fieldset>
           </td>
        </tr>';
     $htmlGeneral .= '<tr valign="top">
 <td style="width:25%;font-weight:bold;">
            <label for="servientrega_guide_free_shipping">' . __('Generar guías cuando el envío es gratuito') . '</label><span class="woocommerce-help-tip" data-tip="' . __('Permitir generar gúias cuando el costo del envío es gratuito') . '"></span>
        </td>
        <td scope="row" class="titledesc" style="display:block;margin-bottom:20px;">
<fieldset style="padding:3px;">';
                if(isset($general_settings['servientrega_guide_free_shipping']) && $general_settings['servientrega_guide_free_shipping'] === true)
                {
                    $htmlGeneral .= '<input class="input-text regular-input " type="radio" name="servientrega_guide_free_shipping"  id="servientrega_guide_free_shipping" ';
                    $disable = ($validation === 'done') ? 'disabled="true" ' : ' ';
                    $htmlGeneral .= $disable;  $htmlGeneral .= 'value="no">' . __('No');
                    $htmlGeneral .= '<input class="input-text regular-input " type="radio"  name="servientrega_guide_free_shipping" checked="true" id="servientrega_guide_free_shipping" ';
                    $disable = ($validation === 'done') ? 'disabled="true" ' : ' ';
                    $htmlGeneral .= $disable; $htmlGeneral .= 'value="yes">' . __('Sí');
                 }else {
                    $htmlGeneral .= '<input class="input-text regular-input" type="radio" name="servientrega_guide_free_shipping" checked="true" id="servientrega_guide_free_shipping" ';
                    $disable = ($validation === 'done') ? 'disabled="true" ' : ' ';
                    $htmlGeneral .= $disable;
                    $htmlGeneral .= 'value="no">' . __('No');
                    $htmlGeneral .= '<input class="input-text regular-input" type="radio" name="servientrega_guide_free_shipping" id="servientrega_guide_free_shipping" ';
                    $disable = ($validation === 'done') ? 'disabled="true" ' : ' ';
                    $htmlGeneral .= $disable;
                    $htmlGeneral .= 'value="yes">' . __('Sí') . '</br></fieldset>';
                }
$htmlGeneral .= '</fieldset>
       </td>
    </tr>';
$htmlGeneral .= '<tr valign="top">
     <td style="width:25%;font-weight:bold;">
                <label for="servientrega_debug">' . __('Depurador') . '</label><span class="woocommerce-help-tip" data-tip="' . __('Activar esta opción para mostrar información de depuración') . '"></span>
            </td>
            <td scope="row" class="titledesc" style="display:block;margin-bottom:20px;">
    <fieldset style="padding:3px;">';
if(isset($general_settings['servientrega_debug']) && $general_settings['servientrega_debug'] === 'yes')
{
    $htmlGeneral .= '<input class="input-text regular-input " type="radio" name="servientrega_debug"  id="servientrega_debug" ';
    $disable = ($validation === 'done') ? 'disabled="true" ' : ' ';
    $htmlGeneral .= $disable;  $htmlGeneral .= 'value="no">' . __('No');
    $htmlGeneral .= '<input class="input-text regular-input " type="radio"  name="servientrega_debug" checked="true" id="servientrega_debug" ';
    $disable = ($validation === 'done') ? 'disabled="true" ' : ' ';
    $htmlGeneral .= $disable; $htmlGeneral .= 'value="yes">' . __('Sí');
}else {
    $htmlGeneral .= '<input class="input-text regular-input" type="radio" name="servientrega_debug" checked="true" id="servientrega_debug" ';
    $disable = ($validation === 'done') ? 'disabled="true" ' : ' ';
    $htmlGeneral .= $disable;
    $htmlGeneral .= 'value="no">' . __('No');
    $htmlGeneral .= '<input class="input-text regular-input" type="radio" name="servientrega_debug" id="servientrega_debug" ';
    $disable = ($validation === 'done') ? 'disabled="true" ' : ' ';
    $htmlGeneral .= $disable;
    $htmlGeneral .= 'value="yes">' . __('Sí') . '</br></fieldset>';
}
$htmlGeneral .= '</fieldset>
           </td>
        </tr>';
    $htmlGeneral .= '<tr>
        <td colspan="2" style="text-align:center;">' .
        wp_nonce_field( "shipping_servientrega_wc_ss_general", "shipping_servientrega_wc_ss_general" ) . '
            <button type="submit" class="button button-primary" name="servientrega_genaral_save_changes_button">' . __('Guardar cambios') . '</button>
        </td>
    </tr>';
return $htmlGeneral;