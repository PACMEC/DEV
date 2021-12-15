<!-- DC ready -->
<style type="text/css">
body, html{margin:0px; padding:0px; font-family:"Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif;}
.clearfix::after{ display:block; clear:both; content:""; }

.wfte_invoice-main{ color:#202020; font-size:12px; font-weight:400; box-sizing:border-box; width:100%; padding:30px 0px; background:#fff; height:auto; }
.wfte_invoice-main *{ box-sizing:border-box;}


.wfte_row{ width:100%; display:block; }
.wfte_col-1{ width:100%; display:block;}
.wfte_col-2{ width:50%; display:block;}
.wfte_col-3{ width:33%; display:block;}
.wfte_col-4{ width:25%; display:block;}
.wfte_col-6{ width:30%; display:block;}
.wfte_col-7{ width:69%; display:block;}

.wfte_padding_left_right{ padding:0px 30px; }

.wfte_company_logo_img_box{ margin-bottom:10px; }
.wfte_company_logo_img{ width:150px; max-width:100%; }
.wfte_doc_title{ color:#23a8f9; font-size:30px; font-weight:bold; height:auto; width:auto; float:left; line-height:22px;}
.wfte_company_name{ font-size:24px; font-weight:bold; }
.wfte_company_logo_extra_details{ font-size:12px; margin-top:3px;}
.wfte_barcode{ margin-top:5px;}
.wfte_invoice_data div span:last-child, .wfte_extra_fields span:last-child{ font-weight:bold; }
.wfte_invoice_data{ line-height:16px; font-size:12px; }
.wfte_invoice_data_invoice_number{font-size: 18px !important;}
.wfte_invoice_number{ color:#000; font-size:18px; font-weight:normal; height:auto;}

.wfte_shipping_address{ width:95%;}
.wfte_billing_address{ width:95%; }
.wfte_address-field-header{ font-weight:bold; font-size:12px; color:#000; padding:3px; padding-left:0px;}
.wfte_addrss_field_main{ padding-top:15px;}

.wfte_product_table{ width:100%; border-collapse:collapse; margin:0px; }
.wfte_payment_summary_table_body .wfte_right_column{ text-align:left; }
.wfte_payment_summary_table{ margin-bottom:10px; }
.wfte_product_table_head_bg{ }
.wfte_table_head_color{ color:#2e2e2e; }

.wfte_product_table_head{}
.wfte_product_table_head th{ height:36px; padding:0px 5px; font-size:.75rem; text-align:start; line-height:10px; border-bottom:solid 1px #7b7b7b; border-top:solid 1px #7b7b7b; font-family:"Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif; text-transform:uppercase;}
.wfte_product_table_body td, .wfte_payment_summary_table_body td{ font-size:12px; line-height:10px;}
.wfte_product_table_body td{ padding:10px 5px; border-bottom:solid 1px #dddee0; text-align:start;}
.wfte_product_table .wfte_right_column{ width:15%;}
.wfte_payment_summary_table .wfte_left_column{ width:60%; }
.wfte_product_table_body .product_td b{ font-weight:normal; }
.wfte_product_table_head_product{width: 30%;}
.wfte_payment_summary_table_body td{ padding:7px 5px; border:none;}

.wfte_product_table_payment_total td{ font-size:13px; color:#000; height:28px; border-bottom:solid 1px #cccccc; border-top:solid 1px #cccccc;}
.wfte_product_table_payment_total td:nth-child(3){ font-weight:bold; }

/* for mPdf */
.wfte_invoice_data{ border:solid 0px #fff; }
.wfte_invoice_data td, .wfte_extra_fields td{ font-size:12px; padding:0px; font-family:"Helvetica Neue", Roboto, Arial, "Droid Sans", sans-serif; line-height:14px;}
.wfte_invoice_data tr td:nth-child(2), .wfte_extra_fields tr td:nth-child(2){ font-weight:bold; }

.wfte_signature{ width:100%; height:auto; min-height:60px; padding:5px 0px;}
.wfte_signature_label{ font-size:12px; }
.wfte_image_signature{ max-width:100%; }
.wfte_return_policy{width:100%; height:auto; border-bottom:solid 1px #dfd5d5; padding:5px 0px; margin-top:5px; }
.wfte_footer{width:100%; height:auto; padding:5px 0px; margin-top:5px;}

.wfte_received_seal{ position:absolute; z-index:10; margin-top:80px; margin-left:0px; width:130px; border-radius:5px; font-size:22px; height:40px; line-height:28px; border:solid 5px #00ccc5; color:#00ccc5; font-weight:900; text-align:center; transform:rotate(-45deg); opacity:.5; }

.float_left{ float:left; }
.float_right{ float:right; }

.wfte_product_table_category_row td{ padding:10px 5px;}
</style>
<div class="wfte_rtl_main wfte_invoice-main">
    <div class="wfte_row wfte_padding_left_right clearfix" style="margin-bottom:0px;">
        <div class="wfte_col-1 float_right wfte_text_right">
            <div class="wfte_doc_title">__[INVOICE]__</div>
        </div> 
    </div>
    <div class="clearfix"></div>  
    <div class="wfte_row wfte_padding_left_right clearfix" style="margin-top: 15px;">
        <div class="wfte_col-1 float_left wfte_text_left">
        </div>
    </div> 
    <div class="wfte_row wfte_padding_left_right clearfix" style="margin-bottom:0px;">                             
        <div class="wfte_col-7 float_left wfte_text_left">
            <div class="wfte_company_logo">
                <div class="wfte_company_logo_img_box">
                    <img src="[wfte_company_logo_url]" class="wfte_company_logo_img">
                </div>
                <div class="wfte_company_name wfte_hidden"> [wfte_company_name]</div>
                <div class="wfte_company_logo_extra_details">__[]__</div>
            </div>
        </div>
        <div class="wfte_col-6 float_right wfte_text_right">
            <div class="wfte_invoice_data">
                <div class="wfte_invoice_number"> <span class="wfte_invoice_number_label" style="font-size: 18px;">__[INVOICE]__#</span> <span class="wfte_invoice_number_val" style="font-size: 18px;">[wfte_invoice_number] </span></div>
            </div>
        </div>                    
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row wfte_padding_left_right float_left clearfix">
        <div class="wfte_col-3 float_left">
            <div class="wfte_from_address">
                <div class="wfte_from_address_val">
                    [wfte_from_address]
                </div>
            </div>
        </div>
        <div class="wfte_col-3 float_left"></div>
        <div class="wfte_col-3 float_right wfte_text_right">
            <div class="wfte_invoice_data">
                <div class="wfte_invoice_date" data-invoice_date-format="d/M/Y">
                    <span class="wfte_invoice_date_label">__[Invoice Date:]__ </span> 
                    <span class="wfte_invoice_date_val">[wfte_invoice_date]</span>
                </div>
                <div class="wfte_order_number">
                    <span class="wfte_order_number_label">__[Order No.:]__ </span> 
                    <span class="wfte_order_number_val">[wfte_order_number]</span>
                </div>
                <div class="wfte_order_date" data-order_date-format="m/d/Y">
                    <span class="wfte_order_date_label">__[Order Date:]__ </span> 
                    <span class="wfte_order_date_val">[wfte_order_date]</span>
                </div>
                <div class="wfte_order_item_meta">[wfte_order_item_meta]</div>
                <div class="wfte_extra_field_main">[wfte_extra_fields]</div>
            </div> 
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row wfte_padding_left_right clearfix" style="margin-top: 15px;">
        <div class="wfte_col-2 float_left wfte_text_left">
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row wfte_padding_left_right clearfix">
        <div class="wfte_col-2 float_left wfte_text_left">
            <div class="wfte_billing_address">
                <div class="wfte_address-field-header wfte_billing_address_label">__[Billing Address:]__</div>
                <div class="wfte_billing_address_val">
                    [wfte_billing_address]
                </div>
            </div>
            <div class="wfte_invoice_data">
                <div class="wfte_email">
                    <span class="wfte_email_label">__[Email:]__</span>
                    <span class="wfte_email_val" style="font-weight:normal;">[wfte_email]</span>
                </div>
                <div class="wfte_tel">
                    <span class="wfte_tel_label">__[Phone:]__ </span>
                    <span class="wfte_tel_val" style="font-weight:normal;">[wfte_tel]</span>
                </div>
            </div>
        </div>
        <div class="wfte_col-2 float_left wfte_text_left">
            <div class="wfte_shipping_address">
                <div class="wfte_address-field-header wfte_shipping_address_label">__[Shipping Address:]__</div>
                <div class="wfte_shipping_address_val">
                    [wfte_shipping_address]
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row clearfix wfte_padding_left_right">
        <div class="wfte_col-2 float_left"></div>
        <div class="wfte_col-2 float_right">
            <div class="wfte_received_seal"><span class="wfte_received_seal_text">__[RECEIVED]__</span>[wfte_received_seal_extra_text]</div>
        </div>
    </div>
    <div class="wfte_row wfte_padding_left_right clearfix" style="margin-top: 30px;">
        <div class="wfte_col-2 float_left wfte_text_left">
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row clearfix wfte_padding_left_right">
        <div class="wfte_col-1">
            [wfte_product_table_start]
            <table class="wfte_product_table wfte_side_padding_table">
                <thead class="wfte_product_table_head wfte_table_head_color wfte_product_table_head_bg">
                    <tr>
                        <th class="wfte_product_table_head_image wfte_product_table_head_bg wfte_table_head_color" col-type="image">__[Image]__</th>
                        <th class="wfte_product_table_head_sku wfte_product_table_head_bg wfte_table_head_color" col-type="sku">__[SKU]__</th>
                        <th class="wfte_product_table_head_product wfte_product_table_head_bg wfte_table_head_color" col-type="product">__[Product]__</th>
                        <th class="wfte_product_table_head_quantity wfte_product_table_head_bg wfte_table_head_color" col-type="quantity">__[Quantity]__</th>
                        <th class="wfte_product_table_head_price wfte_product_table_head_bg wfte_table_head_color" col-type="price">__[Price]__</th>
                        <th class="wfte_product_table_head_total_price wfte_product_table_head_bg wfte_table_head_color" col-type="total_price">__[Total Price]__</th>
                        <th class="wfte_product_table_head_tax wfte_product_table_head_bg wfte_table_head_color" col-type="-tax">__[Total Tax]__</th>
                    </tr>
                </thead>
                <tbody class="wfte_product_table_body wfte_table_body_color">
                </tbody>
            </table>
            [wfte_product_table_end]
            <table class="wfte_payment_summary_table wfte_product_table wfte_side_padding_table">
                <tbody class="wfte_payment_summary_table_body wfte_table_body_color">
                    <tr class="wfte_payment_summary_table_row wfte_product_table_subtotal">
                        <td colspan="2" class="wfte_product_table_subtotal_label wfte_text_right">__[Subtotal]__</td>
                        <td class="wfte_right_column wfte_text_left">[wfte_product_table_subtotal]</td>
                    </tr>
                    <tr class="wfte_payment_summary_table_row wfte_product_table_shipping">
                        <td colspan="2" class="wfte_product_table_shipping_label wfte_text_right">__[Shipping]__</td>
                        <td class="wfte_right_column wfte_text_left">[wfte_product_table_shipping]</td>
                    </tr>
                    <tr class="wfte_payment_summary_table_row wfte_product_table_cart_discount">
                        <td colspan="2" class="wfte_product_table_cart_discount_label wfte_text_right">__[Cart Discount]__</td>
                        <td class="wfte_right_column wfte_text_left">[wfte_product_table_cart_discount]</td>
                    </tr>
                    <tr class="wfte_payment_summary_table_row wfte_product_table_order_discount">
                        <td colspan="2" class="wfte_product_table_order_discount_label wfte_text_right">__[Order Discount]__</td>
                        <td class="wfte_right_column wfte_text_left">[wfte_product_table_order_discount]</td>
                    </tr>
                    <tr data-row-type="wfte_tax_items" class="wfte_payment_summary_table_row wfte_product_table_tax_item">
                        <td colspan="2" class="wfte_product_table_tax_item_label wfte_text_right">[wfte_product_table_tax_item_label]</td>
                        <td class="wfte_right_column wfte_text_left">[wfte_product_table_tax_item]</td>
                    </tr>
                    <tr class="wfte_payment_summary_table_row wfte_product_table_fee">
                        <td colspan="2" class="wfte_product_table_fee_label wfte_text_right">__[Fee]__</td>
                        <td class="wfte_right_column wfte_text_left">[wfte_product_table_fee]</td>
                    </tr>
                    <tr class="wfte_payment_summary_table_row wfte_product_table_payment_total">
                        <td class="wfte_left_column"></td>
                        <td class="wfte_product_table_payment_total_label wfte_text_right">__[Total]__</td>
                        <td class="wfte_product_table_payment_total_val wfte_right_column wfte_text_left">[wfte_product_table_payment_total]</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row wfte_padding_left_right clearfix">
        <div class="wfte_col-3 float_left">
            <div class="wfte_invoice_data">
                <div class="wfte_product_table_payment_method">
                    <span class="wfte_product_table_payment_method_label" style="font-weight:normal;">__[Payment method:]__ </span>
                    <span style="font-weight:normal;">[wfte_product_table_payment_method]</span>
                </div>
            </div>
        </div>
        <div class="wfte_col-3 float_left">

        </div>
        <div class="wfte_col-3 float_right">
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row wfte_padding_left_right clearfix">
        <div class="wfte_col-1">
            <div class="wfte_barcode float_right wfte_text_right">
                <img src="[wfte_barcode_url]" style="">
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="wfte_row wfte_padding_left_right clearfix">
        <div class="wfte_col-1">
            <div class="wfte_footer wfte_text_left clearfix">
                [wfte_footer]
            </div>
        </div>
    </div>
</div>