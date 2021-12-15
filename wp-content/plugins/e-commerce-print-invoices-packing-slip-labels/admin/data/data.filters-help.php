<?php 
$wf_filters_help_doc=array(
	'wf_pklist_alter_order_date'=> array(
		'description'=>'Alter order date',
		'params'=>'$order_date, $template_type, $order',
		'function_name'=>'wt_pklist_change_order_date_format',
		'function_code'=>'
			/* new date format */ <br />
			return <i class={inbuilt_fn}>date</i>("Y-m-d",strtotime(<span class={prms_css}>$order_date</span>)); <br />
		',
	),
	'wf_pklist_alter_invoice_date'=> array(
		'description'=>'Alter invoice date',
		'params'=>'$invoice_date, $template_type, $order',
		'function_name'=>'wt_pklist_change_invoice_date_format',
		'function_code'=>'
			/* new date format */ <br />
			return <i class={inbuilt_fn}>date</i>("M d Y",strtotime(<span class={prms_css}>$invoice_date</span>)); <br />
		',
	),
	'wf_pklist_alter_dispatch_date'=> array(
		'description'=>'Alter dispatch date',
		'params'=>'$dispatch_date, $template_type, $order',
		'function_name'=>'wt_pklist_change_dispatch_date_format',
		'function_code'=>'
			/* new date format */ <br />
			return <i class={inbuilt_fn}>date</i>("d - M - y",strtotime(<span class={prms_css}>$dispatch_date</span>)); <br />
		',
	),
	'wf_pklist_add_additional_info'=> array(
		'description'=>'Add additional info',
		'params'=>'$additional_info, $template_type, $order',
		'function_name'=>'wt_pklist_add_additional_data',
		'function_code'=>'
			$additional_info.=\'Additional text\';<br />
			return $additional_info;<br />
		',
	),
	'wf_pklist_alter_subtotal'=> array(
		'description'=>'Alter subtotal',
		'params'=>'$sub_total, $template_type, $order',
		'function_name'=>'wt_pklist_alter_sub',
		'function_code'=>'
			$sub_total=\'New subtotal\';<br />
			return $sub_total;<br />
		'
	),
	'wf_pklist_alter_subtotal_formated'=> array(
		'description'=>'Alter formated subtotal',
		'params'=>'$sub_total_formated, $template_type, $sub_total, $order',
		'function_name'=>'wt_pklist_alter_formated_sub',
		'function_code'=>'
			$sub_total_formated=\'New formatted subtotal\';<br />
			return $sub_total_formated;<br />
		'
	),
	'wf_pklist_alter_shipping_method'=> array(
		'description'=>'Alter shipping method',
		'params'=>'$shipping, $template_type, $order',
		'function_name'=>'wt_pklist_alter_ship_method',
		'function_code'=>'
			$shipping=\'New shipping method\';<br />
			return $shipping;<br />'
	),
	'wf_pklist_alter_fee'=> array(
		'description'=>'Alter fee',
		'params'=>'$fee_detail_html, $template_type, $fee_detail, $user_currency, $order',
		'function_name'=>'wt_pklist_new_fee',
		'function_code'=>'
			$fee_detail_html=\'New Fee\';<br />
			return $fee_detail_html;<br />'
	),
	'wf_pklist_alter_total_fee'=> array(
		'description'=>'Alter total fee',
		'params'=>'$fee_total_amount_formated, $template_type, $fee_total_amount, $user_currency, $order',
		'function_name'=>'wt_pklist_new_formated_fee',
		'function_code'=>'
			$fee_total_amount_formated=\'New Fee Formated\';<br />
			return $fee_total_amount_formated;<br />'
	),
	'wf_pklist_alter_total_price_in_words'=> array(
		'description'=>'Alter total price in words',
		'params'=>'$total_in_words, $template_type, $order',
		'function_name'=>'wt_pklist_alter_total_price_in_words',
		'function_code'=>'
			$total_in_words=\'Price in words: \'.$total_in_words;<br />
			return $total_in_words;<br />
		',
	),
	'wf_pklist_alter_product_table_head'=> array(
		'description'=>'Alter product table head.(Add, remove, change order)',
		'params'=>'$columns_list_arr, $template_type, $order',
		'function_name'=>'wt_pklist_alter_product_columns',
		'function_code'=>'
			/* removing image column */ <br />
			unset($columns_list_arr[\'image\']); <br /><br />

			/* adding a new custom column with text align right */ <br />
			$columns_list_arr[\'new_col\']=\'&lt;th class=&quot;wfte_product_table_head_new_col wfte_text_right&quot; col-type=&quot;new_col&quot;&gt;__[New column]__&lt;/th&gt;\'; <br />
			<br />

			return $columns_list_arr;<br />
		',
	),
	'wf_pklist_alter_package_product_name'=> array(
		'description'=>'Alter product name in product (Works with Packing List, Shipping Label and Delivery note only)',
		'params'=>'$item_name, $template_type, $_product, $item, $order',
		'function_name'=>'wt_pklist_alter_product_name',
		'function_code'=>'
			$item_name=\'New product name\';<br />
			return $item_name;<br />',
	),
	'wf_pklist_add_package_product_variation'=> array(
		'description'=>'Add product variation in product (Works with Packing List, Shipping Label and Delivery note only)',
		'params'=>'$item_meta, $template_type, $_product, $item, $order',
		'function_name'=>'wt_pklist_add_meta',
		'function_code'=>'
			$item_meta=\'Enter the item meta \';<br />
			return $item_meta;<br />',
	),
	'wf_pklist_add_package_product_meta'=> array(
		'description'=>'Add product meta in product table (Works with Packing List, Shipping Label and Delivery note only)',
		'params'=>'$addional_product_meta, $template_type, $_product, $item, $order',
		'function_name'=>'wt_pklist_add_product_meta',
		'function_code'=>'
			$addional_product_meta.=\'New product meta\';<br />
			return $addional_product_meta;<br />',
	),
	'wf_pklist_alter_package_item_quantiy'=> array(
		'description'=>'Alter item quantity in product table (Works with Packing List, Shipping Label and Delivery note only)',
		'params'=>'$item_quantity, $template_type, $_product, $item, $order',
		'function_name'=>'wt_pklist_package_item_quantiy',
		'function_code'=>'
			$item_quantity=\'New quantity\';<br />
			return $item_quantity;<br />',
	),
	'wf_pklist_alter_package_item_price'=> array(
		'description'=>'Alter item total weight in product table (Works with Packing List, Shipping Label and Delivery note only)',
		'params'=>'$item_weight, $template_type, $_product, $item, $order',
		'function_name'=>'wt_pklist_package_item_weight',
		'function_code'=>'
			$item_weight=\'New weight\';<br />
			return $item_weight;<br />',
	),
	'wf_pklist_alter_package_item_total'=> array(
		'description'=>'Alter item total price in product table (Works with Packing List, Shipping Label and Delivery note only)',
		'params'=>'$product_total, $template_type, $_product, $item, $order',
		'function_name'=>'wt_pklist_alter_item_total',
		'function_code'=>'
			$product_total=\'New total price\';<br />
			return $product_total;<br />',
	),
	'wf_pklist_package_product_table_additional_column_val'=> array(
		'description'=>'You can add additional column head via `wf_pklist_alter_product_table_head` filter. You need to add column data via this filter. (Works with Packing List, Shipping Label and Delivery note only)',
		'params'=>'$column_data, $template_type, $columns_key, $_product, $item, $order',
		'function_name'=>'wt_pklist_package_add_custom_col_vl',
		'function_code'=>'				
			if($columns_key==\'new_col\')<br />
			{ <br />
				&nbsp;&nbsp;&nbsp;&nbsp; $column_data=\'Column data\'; <br />
			}<br />
			return $column_data;<br />
		',
	),
	'wf_pklist_alter_package_product_table_columns'=> array(
		'description'=>'Alter product table column. (Works with Packing List, Shipping Label and Delivery note only)',
		'params'=>'$product_row_columns, $template_type, $_product, $item, $order'
	),
	'wf_pklist_alter_product_name'=> array(
		'description'=>'Alter product name. (Works with Invoice and Dispatch label only)',
		'params'=>'$order_item_name, $template_type, $_product, $order_item, $order',
		'function_name'=>'wt_pklist_new_product_name',
		'function_code'=>'
			$order_item_name=\'New product name\';<br />
			return $order_item_name;<br />',
	),
	'wf_pklist_add_product_variation'=> array(
		'description'=>'Add product variation. (Works with Invoice and Dispatch label only)',
		'params'=>'$item_meta, $template_type, $_product, $order_item, $order',
		'function_name'=>'wt_pklist_prodct_varition',
		'function_code'=>'
			$item_meta=\'New product variation\';<br />
			return $item_meta;<br />',
	),
	'wf_pklist_add_product_meta'=> array(
		'description'=>'Add product meta. (Works with Invoice and Dispatch label only)',
		'params'=>'$addional_product_meta, $template_type, $_product, $order_item, $order',
		'function_name'=>'wt_pklist_prodct_meta',
		'function_code'=>'
			$addional_product_meta.=\'New product meta\';<br />
			return $addional_product_meta;<br />',
	),
	'wf_pklist_alter_item_quantiy'=> array(
		'description'=>'Alter item quantity. (Works with Invoice and Dispatch label only)',
		'params'=>'$order_item_qty, $template_type, $_product, $order_item, $order',
		'function_name'=>'wt_pklist_item_qty',
		'function_code'=>'
			$order_item_qty=\'New item quantity\';<br />
			return $order_item_qty;<br />',
	),
	'wf_pklist_alter_item_price'=> array(
		'description'=>'Alter item price. (Works with Invoice and Dispatch label only)',
		'params'=>'$item_price, $template_type, $_product, $order_item, $order',
		'function_name'=>'wt_pklist_item_price',
		'function_code'=>'
			$item_price=\'New item price\';<br />
			return $item_price;<br />',
	),
	'wf_pklist_alter_item_price_formated'=> array(
		'description'=>'Alter formated item price. (Works with Invoice and Dispatch label only)',
		'params'=>'$item_price_formated, $template_type, $item_price, $_product, $order_item, $order',
		'function_name'=>'wt_pklist_item_price_formatted',
		'function_code'=>'
			$item_price_formated=\'New item formatted price\';<br />
			return $item_price_formated;<br />',
	),
	'wf_pklist_alter_item_total'=> array(
		'description'=>'Alter item total. (Works with Invoice and Dispatch label only)',
		'params'=>'$product_total, $template_type, $_product, $order_item, $order',
		'function_name'=>'wt_pklist_item_total',
		'function_code'=>'
				$product_total=\'New product total\';<br />
				return $product_total;<br />'
	),
	'wf_pklist_alter_item_total_formated'=> array(
		'description'=>'Alter formated item total. (Works with Invoice and Dispatch label only)',
		'params'=>'$product_total_formated, $template_type, $product_total, $_product, $order_item, $order',
		'function_name'=>'wt_pklist_item_total_formatted',
		'function_code'=>'
				$product_total_formated=\'New product total formatted\';<br />
				return $product_total_formated;<br />'
	),
	'wf_pklist_product_table_additional_column_val'=> array(
		'description'=>'You can add additional column head via `wf_pklist_alter_product_table_head` filter. You need to add column data via this filter. (Works with Invoice and Dispatch label only)',
		'params'=>'$column_data, $template_type, $columns_key, $_product, $order_item, $order',
		'function_name'=>'wt_pklist_add_custom_col_vl',
		'function_code'=>'				
			if($columns_key==\'new_col\')<br />
			{ <br />
				&nbsp;&nbsp;&nbsp;&nbsp; $column_data=\'Column data\'; <br />
			}<br />
			return $column_data;<br />
		',
	),
	'wf_pklist_alter_product_table_columns'=> array(
		'description'=>'Alter product table column. (Works with Invoice and Dispatch label only)',
		'params'=>'$product_row_columns, $template_type, $_product, $order_item, $order'
	),
	'wf_pklist_alter_shipping_address'=> array(
		'description'=>'Alter shipping address',
		'params'=>'$shipping_address, $template_type, $order',
		'function_name'=>'wt_pklist_alter_shipping_addr',
		'function_code'=>'
			/* To unset existing field */ <br />
			if(!empty($shipping_address[\'field_name\']))<br/>
			{<br/>
				unset($shipping_address[\'field_name\']);<br/>
			} <br /><br />

			/* add a new field shipping address */ <br />
			$shipping_address[\'new_field\']=\'new field value\';<br /><br />
			return $shipping_address;<br />',
	),
	'wf_pklist_alter_billing_address'=> array(
		'description'=>'Alter billing address',
		'params'=>'$billing_address, $template_type, $order',
		'function_name'=>'wt_pklist_alter_billing_addr',
		'function_code'=>'
			/* To unset existing field */ <br />
			if(!empty($billing_address[\'field_name\']))<br/>
			{<br/>
				unset($billing_address[\'field_name\']);<br/>
			} <br /><br />

			/* add a new field billing address */ <br />
			$billing_address[\'new_field\']=\'new field value\';<br /><br />
			return $billing_address;<br />'
	),
	'wf_pklist_alter_shipping_from_address'=> array(
		'description'=>'Alter shipping from address',
		'params'=>'$fromaddress, $template_type, $order',
		'function_name'=>'wt_pklist_alter_from_addr',
		'function_code'=>'
			/* To unset existing field */ <br />
			if(!empty($fromaddress[\'field_name\']))<br/>
			{<br/>
				unset($fromaddress[\'field_name\']);<br/>
			} <br /><br />

			/* add a new field from address */ <br />
			$fromaddress[\'new_field\']=\'new field value\';<br /><br />
			return $fromaddress;<br />'
	),
	'wf_pklist_toggle_received_seal'=> array(
		'description'=>'Hide/Show received seal in invoice.',
		'params'=>'$is_enable_received_seal, $template_type, $order',
		'function_name'=>'wt_pklist_toggle_received_seal',
		'function_code'=>'
			/* hide or show received seal */  <br />
			if($order->get_status()==\'refunded\')<br />
			{ <br />
			&nbsp;&nbsp;&nbsp;&nbsp; return false;  <br />
			}<br />
			return true; <br />
		',
	),
	'wf_pklist_received_seal_extra_text'=> array(
		'description'=>'Add extra text in received seal.',
		'params'=>'$extra_text, $template_type, $order',
		'function_name'=>'wt_pklist_received_seal_extra_text',
		'function_code'=>'
			/* add invoice date in received seal */  <br />
			$order_id=$order->get_id();  <br />
			$invoice_date=get_post_meta($order_id,\'_wf_invoice_date\',true);  <br />
			if($invoice_date)   <br />
			{   <br />
				&nbsp;&nbsp;&nbsp;&nbsp; return \'&lt;br /&gt;\'.<i class={inbuilt_fn}>date</i>(\'Y-m-d\',$invoice_date);  <br />
			} <br />
			return \'\'; <br />
		',
	),
	'wf_pklist_alter_template_html'=> array(
		'description'=>'Alter template HTML before printing.',
		'params'=>'$html, $template_type',
		'function_name'=>'wt_pklist_add_custom_css_in_invoice_html',
		'function_code'=>'
			/* add cutsom css in invoice */  <br />
			if($template_type==\'invoice\')<br />
			{ <br />
				&nbsp;&nbsp;&nbsp;&nbsp; $html.=\'&lt;style type=&quot;text/css&quot;&gt; body{ font-weight:bold; } &lt;/style&gt;\'; <br />
			}<br />
			return $html;<br />
		',
	),
	'wf_pklist_alter_footer_data'=> array(
		'description'=>'Alter the footer data.',
		'params'=>'$footer_data,$template_type,$order',
		'function_name'=>'wt_pklist_alter_footer',
		'function_code'=>'
				$footer_data=\'Footer name\';<br />
				return $footer_data;<br />',
	),
	'wf_pklist_alter_package_order_items'=> array(
		'description'=>'Alter the package items.',
		'params'=>'$order_package, $template_type, $order',
	),
	'wf_pklist_order_additional_item_meta'=> array(
		'description'=>'Add additional order item meta.',
		'params'=>'order_item_meta_data,$template_type,$order',
		'function_name'=>'wt_pklist_alter_order_meta',
		'function_code'=>'
				$order_item_meta_data=\'Enter the order item meta\';<br />
				return $order_item_meta_data;<br />',
	),
	'wf_pklist_alter_meta_value'=> array(
		'description'=>'Alter meta data.',
		'params'=>'$meta_value, $meta_data, $meta_key',
		'function_name'=>'wt_pklist_alter_meta',
		'function_code'=>'
			$meta_value=\'Enter the meta value\';<br />
			return $meta_value;<br />',
	),
	'wf_alter_line_item_variation_data'=> array(
		'description'=>'Alter variation data.',
		'params'=>'$current_item, $meta_data, $id, $value',
		'function_name'=>'wt_pklist_alter_variation_data',
		'function_code'=>'
			$current_item=\'Alter the variation data\';<br />
			return $current_item;<br />',
	),
	'wf_pklist_alter_settings'=> array(
		'description'=>'Alter the settings array.',
		'params'=>'$settings,$base_id',
		'function_name'=>'wt_pklist_alter_setting',
		'function_code'=>'
			
			/* To remove a setting from the list */<br/>
			unset($settings[\'setting_name\']);<br/><br/>
			
			/* add new setting to the list */<br/>
			$settings[\'new_setting_name\']=\'new default value\';<br/><br/>			

			return $settings;<br />',
	),
	'wf_pklist_alter_additional_fields'=> array(
		'description'=>'Add additional data fields.',
		'params'=>'$extra_fields, $template_type, $order',
		'function_name'=>'wt_pklist_add_extra_fields',
		'function_code'=>'
			
			/* add new field to the list */<br/>
			$extra_fields[\'new_field_name\']=\'new field value\';<br/><br/>			

			return $extra_fields;<br />',
	),
	'wf_pklist_alter_barcode_data'=> array(
		'description'=>'Alter barcode data.',
		'params'=>'$barcode, $template_type, $order',
		'function_name'=>'wt_pklist_alter_barcode',
		'function_code'=>'
			
			$barcode=\'new barcode value\';<br/><br/>			

			return $barcode;<br />',
	),
);