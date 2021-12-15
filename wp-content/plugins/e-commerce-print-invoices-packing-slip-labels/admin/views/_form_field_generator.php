<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}

if(is_array($args))
{
	foreach ($args as $key => $value)
	{
		$type=(isset($value['type']) ? $value['type'] : 'text');
		$field_name=isset($value['field_name']) ? $value['field_name'] : $value['option_name'];
		$field_id=isset($value['field_id']) ? $value['field_id'] : $field_name;

		$after_form_field=(isset($value['after_form_field']) ? $value['after_form_field'] : ''); /* after form field */
		$before_form_field=(isset($value['before_form_field']) ? $value['before_form_field'] : '');

		/** 
		*	conditional help texts 
		*	!!Important: Using OR mixed with AND then add OR conditions first.
		*/
		$conditional_help_html='';
		if(isset($value['help_text_conditional']) && is_array($value['help_text_conditional']))
		{		
			foreach ($value['help_text_conditional'] as $help_text_config)
			{
				if(is_array($help_text_config))
				{
					$condition_attr='';
					if(is_array($help_text_config['condition']))
					{
						$previous_type=''; /* this for avoiding fields without glue */
						foreach ($help_text_config['condition'] as $condition)
						{
							if(is_array($condition))
							{
								if($previous_type!='field')
								{
									$condition_attr.='['.$condition['field'].'='.$condition['value'].']';
									$previous_type='field';
								}
							}else
							{
								if(is_string($condition))
								{
									$condition=strtoupper($condition);
									if(($condition=='AND' || $condition=='OR') && $previous_type!='glue')
									{
										$condition_attr.='['.$condition.']';
										$previous_type='glue';
									}
								}
							}
						}
					}			
					$conditional_help_html.='<span class="wf_form_help wt_pklist_conditional_help_text" data-wt_pklist-help-condition="'.esc_attr($condition_attr).'">'.$help_text_config['help_text'].'</span>';
				}	
			}
		}


		$form_toggler_p_class="";
		$form_toggler_register="";
		$form_toggler_child="";
		if(isset($value['form_toggler']))
		{
			if($value['form_toggler']['type']=='parent')
			{
				$form_toggler_p_class="wf_form_toggle";
				$form_toggler_register=' wf_frm_tgl-target="'.$value['form_toggler']['target'].'"';
			}
			elseif($value['form_toggler']['type']=='child')
			{
				$form_toggler_child=' wf_frm_tgl-id="'.$value['form_toggler']['id'].'" wf_frm_tgl-val="'.$value['form_toggler']['val'].'" '.(isset($value['form_toggler']['chk']) ? 'wf_frm_tgl-chk="'.$value['form_toggler']['chk'].'"' : '').(isset($value['form_toggler']['lvl']) ? ' wf_frm_tgl-lvl="'.$value['form_toggler']['lvl'].'"' : '');	
			}else
			{
				$form_toggler_child=' wf_frm_tgl-id="'.$value['form_toggler']['id'].'" wf_frm_tgl-val="'.$value['form_toggler']['val'].'" '.(isset($value['form_toggler']['chk']) ? 'wf_frm_tgl-chk="'.$value['form_toggler']['chk'].'"' : '').(isset($value['form_toggler']['lvl']) ? ' wf_frm_tgl-lvl="'.$value['form_toggler']['lvl'].'"' : '');	
				$form_toggler_p_class="wf_form_toggle";
				$form_toggler_register=' wf_frm_tgl-target="'.$value['form_toggler']['target'].'"';				
			}
			
		}
		$fld_attr=(isset($value['attr']) ? $value['attr'] : '');
		$field_only=(isset($value['field_only']) ? $value['field_only'] : false);
		$mandatory=(boolean) (isset($value['mandatory']) ? $value['mandatory'] : false);
		if($mandatory)
		{
			$fld_attr.=' required="required"';	
		}
		if($field_only===false)
		{
			$tooltip_html=self::set_tooltip($field_name,$base);
		?>
			<tr valign="top" <?php echo $form_toggler_child; ?>>
			    <th scope="row" >
			    	<label for="<?php echo $field_name;?>" style="float:left; width:100%;">
			    		<?php echo isset($value['label']) ? $value['label'] : ''; ?><?php echo ($mandatory ? '<span class="wt_pklist_required_field">*</span>' : ''); ?><?php echo $tooltip_html;?>
					</label>
				</th>
			    <td>
			    	<?php
			    	}
			    	$option_name=$value['option_name'];
			    	$vl=Wf_Woocommerce_Packing_List::get_option($option_name,$base);
			    	$vl=is_string($vl) ? stripslashes($vl) : $vl;

			    	echo $before_form_field;
			    	if($type=='text')
					{
			    	?>
			        	<input type="text" <?php echo $fld_attr;?> name="<?php echo $field_name;?>" value="<?php echo $vl;?>" />
			        <?php
			    	}
			    	if($type=='number')
					{
					?>
			        	<input type="number" <?php echo $fld_attr;?> name="<?php echo $field_name;?>" value="<?php echo $vl;?>" />
			        <?php
					}
			    	elseif($type=='textarea')
					{
					?>
			       		<textarea <?php echo $fld_attr;?> name="<?php echo $field_name;?>"><?php echo $vl;?></textarea>
			        <?php
					}elseif($type=='order_st_multiselect') //order status multi select
					{
						$order_statuses=isset($value['order_statuses']) ? $value['order_statuses'] : array();
						$field_vl=isset($value['field_vl']) ? $value['field_vl'] : array();
					?>
						<input type="hidden" name="<?php echo $field_name;?>_hidden" value="1" />
						<select class="wc-enhanced-select" id='<?php echo $field_name;?>_st' data-placeholder='<?php _e('Choose Order Status','print-invoices-packing-slip-labels-for-woocommerce');?>' name="<?php echo $field_name;?>[]" multiple="multiple" <?php echo $fld_attr;?>>
				            <?php
				            $Pdf_invoice=$vl ? $vl : array();
				            foreach($field_vl as $inv_key => $inv_value) 
				            {
				    			echo "<option value=$inv_value".(in_array($inv_value, $Pdf_invoice) ? ' selected="selected"' : '').">$order_statuses[$inv_value]</option>";
				                
				            }
				            ?>
				        </select>
					<?php
					}elseif($type=='checkbox') //checkbox
					{
						$field_vl=isset($value['field_vl']) ? $value['field_vl'] : "1";
					?>
						<input class="<?php echo $form_toggler_p_class;?>" type="checkbox" value="<?php echo $field_vl;?>" id="<?php echo $field_id;?>" name="<?php echo $field_name;?>" <?php echo ($field_vl==$vl ? ' checked="checked"' : '') ?> <?php echo $form_toggler_register;?> <?php echo $fld_attr;?>>
						<?php
					}
					elseif($type=='radio') //radio button
					{
						$radio_fields=isset($value['radio_fields']) ? $value['radio_fields'] : array();
						foreach ($radio_fields as $rad_vl=>$rad_label) 
						{
						?>
						<input type="radio" id="<?php echo $field_id.'_'.$rad_vl;?>" name="<?php echo $field_name;?>" class="<?php echo $form_toggler_p_class;?>" <?php echo $form_toggler_register;?> value="<?php echo $rad_vl;?>" <?php echo ($vl==$rad_vl) ? ' checked="checked"' : ''; ?> <?php echo $fld_attr;?> /> <?php echo $rad_label; ?>
						&nbsp;&nbsp;
						<?php
						}
						
					}elseif($type=='uploader') //uploader
					{
						?>
						<div class="wf_file_attacher_dv">
				            <input id="<?php echo $field_id; ?>"  type="text" name="<?php echo $field_name; ?>" value="<?php echo $vl; ?>" <?php echo $fld_attr;?>/>
							
							<input type="button" name="upload_image" class="wf_button button button-primary wf_file_attacher" wf_file_attacher_target="#<?php echo $field_name; ?>" value="<?php _e('Upload','print-invoices-packing-slip-labels-for-woocommerce'); ?>" />
						</div>
						<img class="wf_image_preview_small" src="<?php echo $vl ? $vl : Wf_Woocommerce_Packing_List::$no_image; ?>" />
						<?php
					}elseif($type=='select') //select
					{
						$select_fields=isset($value['select_fields']) ? $value['select_fields'] : array();
						?>
						<select name="<?php echo $field_name;?>" id="<?php echo $field_id;?>" class="<?php echo $form_toggler_p_class;?>" <?php echo $form_toggler_register;?> <?php echo $fld_attr;?>>
						<?php
						foreach ($select_fields as $sel_vl=>$sel_label) 
						{
							$selected_attr='';
							if((is_array($vl) && in_array($sel_vl, $vl)) || (is_string($vl) && $vl==$sel_vl))
							{
								$selected_attr=' selected="selected"';
							}
						?>
							<option value="<?php echo $sel_vl;?>" <?php echo $selected_attr; ?>><?php echo $sel_label; ?></option>
						<?php
						}
						?>
						</select>
						<?php
					}
					elseif($type=='additional_fields') //additional fields (Order meta)
					{
						$module_base=isset($value['module_base']) ? $value['module_base'] : '';
						$fields=array();
			            $add_data_flds=Wf_Woocommerce_Packing_List::$default_additional_data_fields; 
			            $user_created=Wf_Woocommerce_Packing_List::get_option('wf_additional_data_fields');		            
			            
			            if(is_array($user_created))  //user created
			            {
			                $fields=array_merge($add_data_flds,$user_created);
			            }else
			            {
			                $fields=$add_data_flds; //default
			            }
			            
		            	$user_selected_arr=$vl && is_array($vl) ? $vl : array();
						?>
						<div class="wf_select_multi">
							<input type="hidden" name="wf_<?php echo $module_base;?>_contactno_email_hidden" value="1" />
				            <select class="wc-enhanced-select" name="wf_<?php echo $module_base;?>_contactno_email[]" multiple="multiple">
				            <?php
				            
				            foreach ($fields as $id => $name) 
				            { 
				                $meta_key_display=Wf_Woocommerce_Packing_List::get_display_key($id);
				                ?>
				                <option value="<?php echo $id;?>" <?php echo in_array($id, $user_selected_arr) ? 'selected' : '';?>><?php echo $name.$meta_key_display;?></option>
				                <?php
				            }
				            ?>						 
				            </select>
				            <br>
				            <?php
				        	if(isset($value['help_text']))
							{
				            ?>
				            <span class="wf_form_help" style="display:inline;"><?php echo $value['help_text']; ?></span>
				            <?php
				            	unset($value['help_text']);
				        	}
				        	?>
				        </div>
						<?php
						
					}
					elseif($type=='multi_select')
					{
						$sele_vals=(isset($value['sele_vals']) && is_array($value['sele_vals']) ? $value['sele_vals'] : array());
						$vl=(is_array($vl) ? $vl : array($vl));
						$vl=array_filter($vl);
						?>
						<div class="wf_select_multi">
							<input type="hidden" name="<?php echo $field_name;?>_hidden" value="1" />
							<select multiple="multiple" name="<?php echo $field_name;?>[]" id="<?php echo $field_id;?>" class="wc-enhanced-select  <?php echo $form_toggler_p_class;?>" <?php echo $form_toggler_register;?> <?php echo $fld_attr;?>>
								<?php
								foreach($sele_vals as $sele_val=>$sele_lbl) 
								{
								?>
		                      		<option value="<?php echo $sele_val;?>" <?php echo (in_array($sele_val, $vl) ? 'selected' : ''); ?>> <?php echo $sele_lbl;?> </option>
		                   		<?php
		                    	}
		                   		?>
	                   		</select>
	                   	</div>
	                   	<?php
					}
					echo $after_form_field;

					if(isset($value['help_text']))
					{
			        ?>
			        	<span class="wf_form_help"><?php echo $value['help_text']; ?></span>
			        <?php
			    	}
			    	echo $conditional_help_html;

			    	if($field_only===false)
					{
			    	?>
			    </td>
			    <td></td>
			</tr>
		<?php
		}
	}
}