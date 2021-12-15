(function( $ ) {
	//'use strict';
	$(function() {
		wf_invoice_update_order_status_to_email_select_box();
		$('#woocommerce_wf_generate_for_orderstatus_st').on('change',function(){
			wf_invoice_update_order_status_to_email_select_box();
		})

	});
	function wf_invoice_update_order_status_to_email_select_box()
	{
		var attch_inv_elm=$('#woocommerce_wf_attach_invoice_st');
		var attch_inv_vl=attch_inv_elm.val();
		attch_inv_vl=attch_inv_vl!==null ? attch_inv_vl : new Array();
		var html='';
		$('#woocommerce_wf_generate_for_orderstatus_st').find('option:selected').each(function(){
			var slcted=$.inArray($(this).val(),attch_inv_vl)==-1 ? '' : 'selected';
			html+='<option value="'+$(this).val()+'" '+slcted+'>'+$(this).html()+'</option>';
		});
		attch_inv_elm.html(html).trigger('change');
	}

	function wf_toggle_invoice_number_fields()
	{
		var vl=$('#woocommerce_wf_invoice_number_format').val();
		var number_tr=$('[name="woocommerce_wf_invoice_as_ordernumber"]').parents('tr');
		var prefix_tr=$('[name="woocommerce_wf_invoice_number_prefix"]').parents('tr');
		var postfix_tr=$('[name="woocommerce_wf_invoice_number_postfix"]').parents('tr');
		var start_tr=$('#woocommerce_wf_invoice_start_number_tr');
		number_tr.hide().find('th label').css({'padding-left':'0px'});
		prefix_tr.hide().find('th label').css({'padding-left':'0px'});
		postfix_tr.hide().find('th label').css({'padding-left':'0px'});
		start_tr.hide().find('th label').css({'padding-left':'0px'});

		$('.form-table th label').css({'float':'left','width':'100%'});

		var num_reg=/\[number\]/gm;
		var pre_reg=/\[prefix\]/gm;
		var pos_reg=/\[suffix\]/gm;

		if(vl.search(num_reg)>=0)
		{
			number_tr.show().find('th label').animate({'padding-left':'15px'});
			if($('[name="woocommerce_wf_invoice_as_ordernumber"]:checked').val()=='No')
			{
				start_tr.show().find('th label').animate({'padding-left':'30px'});
			}
		}
		if(vl.search(pre_reg)>=0)
		{  
			prefix_tr.show().find('th label').animate({'padding-left':'15px'});
		}
		if(vl.search(pos_reg)>=0)
		{
			postfix_tr.show().find('th label').animate({'padding-left':'15px'});
		}
	}
	$('#woocommerce_wf_invoice_number_format').change(function(){
		wf_toggle_invoice_number_fields();
	});
	wf_toggle_invoice_number_fields();

	$('.wf_inv_num_frmt_hlp_btn').click(function(){
		var trgt_field=$(this).attr('data-wf-trget');
		$('.wf_inv_num_frmt_hlp').attr('data-wf-trget',trgt_field);
		wf_popup.showPopup($('.wf_inv_num_frmt_hlp'));
	});

	$('.wf_inv_num_frmt_append_btn').click(function(){
		var trgt_elm_name=$(this).parents('.wf_inv_num_frmt_hlp').attr('data-wf-trget');
		var trgt_elm=$('[name="'+trgt_elm_name+'"]'); 
		var exst_vl=trgt_elm.val();
		var cr_vl=$(this).text();
		trgt_elm.val(exst_vl+cr_vl);
		wf_popup.hidePopup();
	});
	
})( jQuery );