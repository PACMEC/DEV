<?php
if (!defined('ABSPATH')) {
	exit;
}
?>
<div class="wf-tab-content" data-id="<?php echo $target_id;?>">
<p><?php _e('Configure the general settings required for the invoice.','print-invoices-packing-slip-labels-for-woocommerce');?></p>
<table class="wf-form-table">
	<?php
    Wf_Woocommerce_Packing_List_Admin::generate_form_field(array(
        array(
            'type'=>"radio",
            'label'=>__("Enable invoice",'print-invoices-packing-slip-labels-for-woocommerce'),
            'option_name'=>"woocommerce_wf_enable_invoice",
            'radio_fields'=>array(
                'Yes'=>__('Yes','print-invoices-packing-slip-labels-for-woocommerce'),
                'No'=>__('No','print-invoices-packing-slip-labels-for-woocommerce')
            ),
        ),
        array(
            'type'=>"radio",
            'label'=>__("Use order date as invoice date",'print-invoices-packing-slip-labels-for-woocommerce'),
            'option_name'=>"woocommerce_wf_orderdate_as_invoicedate",
            'help_text'=>__("If you choose 'No' then the invoice date will be the date on which it is generated.",'print-invoices-packing-slip-labels-for-woocommerce'),
            'radio_fields'=>array(
                'Yes'=>__('Yes','print-invoices-packing-slip-labels-for-woocommerce'),
                'No'=>__('No','print-invoices-packing-slip-labels-for-woocommerce')
            ),
        ),
        array(
            'type'=>'order_st_multiselect',
            'label'=>__("Generate invoice for order statuses",'print-invoices-packing-slip-labels-for-woocommerce'),
            'option_name'=>"woocommerce_wf_generate_for_orderstatus",
            'help_text'=>__("Order statuses for which an invoice should be generated.",'print-invoices-packing-slip-labels-for-woocommerce'),
            'order_statuses'=>$order_statuses,
            'field_vl'=>array_flip($order_statuses),
            'attr'=>'',
        ),
        array(
            'type'=>"radio",
            'label'=>__("Attach invoice PDF in order email",'print-invoices-packing-slip-labels-for-woocommerce'),
            'option_name'=>"woocommerce_wf_add_invoice_in_mail",
            'radio_fields'=>array(
                'Yes'=>__('Yes','print-invoices-packing-slip-labels-for-woocommerce'),
                'No'=>__('No','print-invoices-packing-slip-labels-for-woocommerce')
            ),
            'help_text'=>__('PDF version of invoice will be attached with the order email based on the above statuses','print-invoices-packing-slip-labels-for-woocommerce'),
        ),
    ), $this->module_id);
    ?>
    <?php 
    $order_meta_doc_url = 'https://www.webtoffee.com/adding-additional-fields-pdf-invoices-woocommerce/#add-order-meta';
    
    Wf_Woocommerce_Packing_List_Admin::generate_form_field(array(
        array(
            'type'=>"radio",
            'label'=>__("Enable print invoice option for customers",'print-invoices-packing-slip-labels-for-woocommerce'),
            'option_name'=>"woocommerce_wf_packinglist_frontend_info",
            'radio_fields'=>array(
                'Yes'=>__('Yes','print-invoices-packing-slip-labels-for-woocommerce'),
                'No'=>__('No','print-invoices-packing-slip-labels-for-woocommerce')
            ),
            'help_text'=>__("Displays print button in the order email, order listing page and in the order summary.",'print-invoices-packing-slip-labels-for-woocommerce'),
            'form_toggler'=>array(
                'type'=>'parent',
                'target'=>'wf_enable_print_button',
            )
        ),
        array(
            'type'=>'order_st_multiselect',
            'label'=>__("Show print button only for statuses",'print-invoices-packing-slip-labels-for-woocommerce'),
            'option_name'=>"woocommerce_wf_attach_invoice",
            'order_statuses'=>$order_statuses,
            'field_vl'=>$wf_generate_invoice_for,
            'form_toggler'=>array(
                'type'=>'child',
                'id'=>'wf_enable_print_button',
                'val'=>'Yes',
            )
        ),
        array(
            'type'=>"additional_fields",
            'label'=>__("Order meta fields", 'print-invoices-packing-slip-labels-for-woocommerce'),
            'option_name'=>'wf_'.$this->module_base.'_contactno_email',
            'module_base'=>$this->module_base,
            'help_text'=>__('Select/add the additional order information in the invoice','print-invoices-packing-slip-labels-for-woocommerce'),
        ),
        array(
            'type'=>"uploader",
            'label'=>__("Custom logo for invoice",'print-invoices-packing-slip-labels-for-woocommerce'),
            'option_name'=>"woocommerce_wf_packinglist_logo",
            'help_text'=>__('If left blank, defaulted to logo from General settings.Ensure to select company logo from ‘Invoice > Customize > Company Logo’ to reflect on the invoice. Recommended size is 150x50px.','print-invoices-packing-slip-labels-for-woocommerce'),
        ),
    ), $this->module_id);

    Wf_Woocommerce_Packing_List_Admin::generate_form_advanced_fields(array(
        array(
            'type'=>'field_group_head', //field type
            'head'=>__('Advanced options','print-invoices-packing-slip-labels-for-woocommerce'),
            'group_id'=>'advanced_field', //field group id
            'show_on_default'=>0,
        ),
        array(
            'type'=>"radio",
            'label'=>__("Generate invoice for old orders",'print-invoices-packing-slip-labels-for-woocommerce'),
            'option_name'=>"wf_woocommerce_invoice_prev_install_orders",
            'help_text'=>__("Choose ‘No’ to not generate invoice for orders which are created before the installation of the plugin.",'print-invoices-packing-slip-labels-for-woocommerce'),
            'radio_fields'=>array(
                'Yes'=>__('Yes','print-invoices-packing-slip-labels-for-woocommerce'),
                'No'=>__('No','print-invoices-packing-slip-labels-for-woocommerce')
            ),
            'field_group'=>'advanced_field',
        ),
        array(
            'type'=>"radio",
            'label'=>__("Generate invoice for free orders",'print-invoices-packing-slip-labels-for-woocommerce'),
            'option_name'=>"wf_woocommerce_invoice_free_orders",
            'help_text'=>__("Choose ‘No’ to not generate invoice for free orders.",'print-invoices-packing-slip-labels-for-woocommerce'),
            'radio_fields'=>array(
                'Yes'=>__('Yes','print-invoices-packing-slip-labels-for-woocommerce'),
                'No'=>__('No','print-invoices-packing-slip-labels-for-woocommerce')
            ),
            'field_group'=>'advanced_field',
        ),
        array(
            'type'=>"radio",
            'label'=>__("Display free line items in the invoice",'print-invoices-packing-slip-labels-for-woocommerce'),
            'option_name'=>"wf_woocommerce_invoice_free_line_items",
            'help_text'=>__("Choose ‘No’ to not display free line items in the invoice.",'print-invoices-packing-slip-labels-for-woocommerce'),
            'radio_fields'=>array(
                'Yes'=>__('Yes','print-invoices-packing-slip-labels-for-woocommerce'),
                'No'=>__('No','print-invoices-packing-slip-labels-for-woocommerce')
            ),
            'field_group'=>'advanced_field',
        ),
        array(
            'type' => "pdf_name_select",
            'label' => __("PDF name format", 'print-invoices-packing-slip-labels-for-woocommerce'),
            'option_name' => 'woocommerce_wf_custom_pdf_name',
            'help_text'=>__("Select a name format for PDF invoice that includes invoice/order number.",'print-invoices-packing-slip-labels-for-woocommerce'),
            'field_group'=>'advanced_field',
        ),
        array(
            'type' => "pdf_name_prefix",
            'label' => __("Custom PDF name prefix", 'print-invoices-packing-slip-labels-for-woocommerce'),
            'option_name' => 'woocommerce_wf_custom_pdf_name_prefix',
            'pdf_name_prefix_label' => 'yes',
            'help_text'=>__("Input a custom prefix for ‘PDF name format’ that will appear at the beginning of the name. Defaulted to ‘Invoice_’.",'print-invoices-packing-slip-labels-for-woocommerce'),
            'field_group'=>'advanced_field',
        ),
    ), $this->module_id);
    ?>
</table>
<?php 
include plugin_dir_path( WF_PKLIST_PLUGIN_FILENAME )."admin/views/admin-settings-save-button.php";
?>
</div>