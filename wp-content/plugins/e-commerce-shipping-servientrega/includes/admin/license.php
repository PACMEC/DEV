<?php

$wc_main_settings = [];
$wc_main_settings = get_option('woocommerce_servientrega_shipping_settings');

if(isset($_POST['shipping_servientrega_wc_ss_license']))
{
    if (!wp_verify_nonce( $_POST['shipping_servientrega_wc_ss_license'], 'shipping_servientrega_wc_ss_license' ))
        return;

    $license = $_POST['servientrega_license'];
    Shipping_Servientrega_WC::upgrade_working_plugin($license);
    header("Refresh:0");
}

$htmlLicense = '<table>
    <tr valign="top">
         <td style="width:25%;padding-top:40px;font-weight:bold;">
            <label for="servientrega_license">' .  __('Licencia') . '</label><span class="woocommerce-help-tip" data-tip="' . __('La licencia que se adquirió para el uso del plugin completo') . '"></span>
         </td>
         <td scope="row" class="titledesc" style="display:block;margin-bottom:20px;margin-top:3px;padding-top:40px;">
            <fieldset style="padding:3px;">
                <input id="servientrega_license" name="servientrega_license" type="password"';
$htmlLicense .= 'value="';
$value = (isset($wc_main_settings['servientrega_license'])) ? $wc_main_settings['servientrega_license'] : '';
$htmlLicense .= "$value\">";
$htmlLicense .= '</fieldset>
         </td>
    </tr>
    <tr>
        <td colspan="2" style="text-align:center;">' .
    wp_nonce_field( "shipping_servientrega_wc_ss_license", "shipping_servientrega_wc_ss_license" ) . '
            <button type="submit" class="button button-primary">' . __('Guardar cambios') . '</button>
        </td>
    </tr>
    ';
return $htmlLicense;
