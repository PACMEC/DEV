<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.webtoffee.com/
 * @since      2.5.0
 *
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wf_Woocommerce_Packing_List
 * @subpackage Wf_Woocommerce_Packing_List/admin
 * @author     WebToffee <info@webtoffee.com>
 */
class Wf_Woocommerce_Packing_List_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.5.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.5.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/*
	 * module list, Module folder and main file must be same as that of module name
	 * Please check the `register_modules` method for more details
	 */
	public static $modules=array(
		'customizer',
		'uninstall-feedback',
		'freevspro',
	);

	public static $existing_modules=array();

	public $bulk_actions=array();

	public static $tooltip_arr=array();

	/**
	*	To store the RTL needed or not status
	*	@since 2.6.6
	*/
	public static $is_enable_rtl=null;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.5.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    2.5.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wf-woocommerce-packing-list-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    2.5.0
	 */
	public function enqueue_scripts() 
	{
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wf-woocommerce-packing-list-admin.js', array( 'jquery','wp-color-picker','jquery-tiptip'), $this->version, false );
		//order list page bulk action filter
		$this->bulk_actions=apply_filters('wt_print_bulk_actions',$this->bulk_actions);

		$params=array(
			'nonces' => array(
		            'wf_packlist' => wp_create_nonce(WF_PKLIST_PLUGIN_NAME),
		     ),
			'ajaxurl' => admin_url('admin-ajax.php'),
			'no_image'=>Wf_Woocommerce_Packing_List::$no_image,
			'bulk_actions'=>array_keys($this->bulk_actions),
			'print_action_url'=>admin_url('?print_packinglist=true'),
			'msgs'=>array(
				'settings_success'=>__('Settings updated.','print-invoices-packing-slip-labels-for-woocommerce'),
				'all_fields_mandatory'=>__('All fields are mandatory','print-invoices-packing-slip-labels-for-woocommerce'),
				'settings_error'=>sprintf(__('Unable to update settings due to an internal error. %s To troubleshoot please click %s here. %s', 'print-invoices-packing-slip-labels-for-woocommerce'), '<br />', '<a href="https://www.webtoffee.com/how-to-fix-the-unable-to-save-settings-issue/" target="_blank">', '</a>'),
				'select_orders_first'=>__('You have to select order(s) first!','print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_not_gen_bulk'=>__('One or more order do not have invoice generated. Generate manually?','print-invoices-packing-slip-labels-for-woocommerce'),
				'error'=>__('Error','print-invoices-packing-slip-labels-for-woocommerce'),
				'please_wait'=>__('Please wait','print-invoices-packing-slip-labels-for-woocommerce'),
				'is_required'=>__("is required",'print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_title_prompt' => __("Invoice",'print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_number_prompt' => __("number has not been generated yet. Do you want to manually generate one ?",'print-invoices-packing-slip-labels-for-woocommerce'),
				'invoice_number_prompt_free_order' => __("‘Generate invoice for free orders’ is disabled in Invoice settings > Advanced. You are attempting to generate invoice for this free order. Proceed?",'print-invoices-packing-slip-labels-for-woocommerce'),
			)
		);
		wp_localize_script($this->plugin_name, 'wf_pklist_params', $params);

	}


	/**
    * 	@since 2.5.8
    * 	Set tooltip for form fields 
    */
    public static function set_tooltip($key,$base_id="",$custom_css="")
    {
    	$tooltip_text=self::get_tooltips($key,$base_id);
    	if($tooltip_text!="")
    	{
    		$tooltip_text='<span style="color:#aaa; '.($custom_css!="" ? $custom_css : 'margin-top:-1px; margin-left:2px; position:absolute;').'" class="dashicons dashicons-editor-help wt-tips" data-wt-tip="'.$tooltip_text.'"></span>';
    	}
    	return $tooltip_text;
    }

    /**
    * 	@since 2.5.8
    * 	Get tooltip config data for non form field items
    * 	@return array 'class': class name to enable tooltip, 'text': tooltip text including data attribute if not empty
    */
    public static function get_tooltip_configs($key,$base_id="")
    {
    	$out=array('class'=>'','text'=>'');
    	$text=self::get_tooltips($key,$base_id);
    	if($text!="")
    	{
    		$out['text']=' data-wt-tip="'.$text.'"';
    		$out['class']=' wt-tips';
    	}  	
    	return $out;
    }

    /**
    *	@since 2.5.8
	* 	This function will take tooltip data from modules and store ot 
	*
	*/
	public function register_tooltips()
	{
		include(plugin_dir_path( __FILE__ ).'data/data.tooltip.php');
		self::$tooltip_arr=array(
			'main'=>$arr
		);
		/* hook for modules to register tooltip */
		self::$tooltip_arr=apply_filters('wt_pklist_alter_tooltip_data',self::$tooltip_arr);
	}

	/**
	* 	Get tooltips
	*	@since 2.5.8
	*	@param string $key array key for tooltip item
	*	@param string $base module base id
	* 	@return tooltip content, empty string if not found
	*/
	public static function get_tooltips($key,$base_id='')
	{
		$arr=($base_id!="" && isset(self::$tooltip_arr[$base_id]) ? self::$tooltip_arr[$base_id] : self::$tooltip_arr['main']);
		return (isset($arr[$key]) ? $arr[$key] : '');
	}

	/**
	 * Function to add Items to Orders Bulk action dropdown
	 *
	 * @since    2.5.0
	 */
	public function alter_bulk_action($actions)
	{
        return array_merge($actions,$this->bulk_actions);
	}
	

	/**
	 * Function to add print button in order list page action column
	 *
	 * @since    2.5.0
	 */
	public function add_checkout_fields($fields) 
	{
		$additional_options=Wf_Woocommerce_Packing_List::get_option('wf_invoice_additional_checkout_data_fields');
        if(is_array($additional_options) && count(array_filter($additional_options))>0)
        {
            foreach ($additional_options as $value)
            {
                $fields['billing']['billing_' . $value] = array(
                    'text' => 'text',
                    'label' => __(str_replace('_', ' ', $value), 'woocommerce'),
                    'placeholder' => _x('Enter ' . str_replace('_', ' ', $value), 'placeholder', 'woocommerce'),
                    'required' => false,
                    'class' => array('form-row-wide', 'align-left'),
                    'clear' => true
                );
            }
        }
		return $fields;
	}

	/**
	 * Function to add print button in order list page action column
	 *
	 * @since    2.5.0
	 */
	public function add_print_action_button($actions,$order)
	{
        $order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
        $wf_pklist_print_options=array(
            array(
                'name' => '',
                'action' => 'wf_pklist_print_document',
                'url' => sprintf('#%s',$order_id)
            ),
        );
        return array_merge($actions,$wf_pklist_print_options);
    } 

    /**
	 * Function to add email attachments to order email
	 *
	 * @since    2.5.0
	 */
	public function add_email_attachments($attachments, $status=null, $order=null)
	{
		if(is_object($order) && is_a($order,'WC_Order') && isset($status))
		{
            $order=( WC()->version < '2.7.0' ) ? new WC_Order($order) : new wf_order($order);
			$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
			$attachments=apply_filters('wt_email_attachments',$attachments,$order,$order_id,$status);
        }
		return $attachments;	
	}
   
    /**
	 * Function to add action buttons in order email
	 *
	 * 	@since    2.5.0
	 *	@since 	  2.6.5 	[Bug fix] Print button missing in email 
	 */
	public function add_email_print_actions($order)
	{
		if(is_object($order) && is_a($order,'WC_Order'))
		{
			$order=( WC()->version < '2.7.0' ) ? new WC_Order($order) : new wf_order($order);
			$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
			$html='';
			$html=apply_filters('wt_email_print_actions',$html,$order,$order_id);	
		}
	}

    /**
	 * Function to add action buttons in user dashboard order list page
	 *
	 * @since    2.5.0
	 */
	public function add_fontend_print_actions($order)
	{
		$order=( WC()->version < '2.7.0' ) ? new WC_Order($order) : new wf_order($order);
		$order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
		$html='';
		$html=apply_filters('wt_frontend_print_actions',$html,$order,$order_id);	
	}

	public static function get_print_url($order_id, $action)
	{
		$url=wp_nonce_url(admin_url('?print_packinglist=true&post='.($order_id).'&type='.$action), WF_PKLIST_PLUGIN_NAME);
		$url=(isset($_GET['debug']) ? $url.'&debug' : $url);
		return $url;
	}

	public static function generate_print_button_data($order,$order_id,$action,$label,$icon_url,$is_show_prompt,$button_location="detail_page")
	{
		$url=self::get_print_url($order_id, $action);
		
		$href_attr='';
		$onclick='';
		$confirmation_clss='';
		if(($is_show_prompt==1) || ($is_show_prompt==2))
		{
			$confirmation_clss='wf_pklist_confirm_'.$action;
			$onclick='onclick=" return wf_Confirm_Notice_for_Manually_Creating_Invoicenumbers(\''.$url.'\','.$is_show_prompt.');"';
		}else
		{
			$href_attr=' href="'.$url.'"';
		}
		if($button_location=="detail_page")
        {
        ?>
		<tr>
			<td>
				<a class="button tips wf-packing-list-link" <?php echo $onclick;?> <?php echo $href_attr;?> target="_blank" data-tip="<?php echo strip_tags($label);?>" >
				<?php
				if($icon_url!="")
				{
				?>
					<img src="<?php echo $icon_url;?>" alt="<?php echo $label;?>" width="14"> 
				<?php
				}
				?>
				<?php echo $label;?>
				</a>
			</td>
		</tr>
		<?php
        }elseif($button_location=="list_page")
        {
        ?>
			<li>
				<a class="<?php echo $confirmation_clss;?>" data-id="<?php echo $order_id;?>" <?php echo $onclick;?> <?php echo $href_attr;?> target="_blank"><?php echo $label;?></a>
			</li>
		<?php
        }
	}

	/**
	 * Function to add action buttons in order list page
	 *
	 * @since    2.5.0
	 */
	public function add_print_actions($column)
	{
		global $post, $woocommerce, $the_order;
		if($column=='order_actions' || $column=='wc_actions')
		{
			$order = ( WC()->version < '2.7.0' ) ? new WC_Order($post->ID) : new wf_order($post->ID);
            $order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
			$html='';
			?>
			<div id="wf_pklist_print_document-<?php echo $order_id;?>" class="wf-pklist-print-tooltip-order-actions">				
				<div class="wf-pklist-print-tooltip-content">
                    <ul>
                    <?php
					$html=apply_filters('wt_print_actions',$html,$order,$order_id);
					?>
					</ul>
                </div>
                <div class="wf_arrow"></div>	
			</div>
			<?php
		}
		return $column;
	}

	/**
	 * Registers meta box and printing options
	 *
	 * @since    2.5.0
	 */
	public function add_meta_boxes()
	{
		add_meta_box('woocommerce-packinglist-box', __('Print Actions','print-invoices-packing-slip-labels-for-woocommerce'), array($this,'create_metabox_content'),'shop_order', 'side', 'default');
	}

	/**
	 * Add plugin action links
	 *
	 * @param array $links links array
	 */
	public function plugin_action_links($links) 
	{
	   $links[] = '<a href="'.admin_url('admin.php?page='.WF_PKLIST_POST_TYPE).'">'.__('Settings', 'print-invoices-packing-slip-labels-for-woocommerce').'</a>';
	   $links[] = '<a href="https://www.webtoffee.com/product/woocommerce-pdf-invoices-packing-slips/?utm_source=free_plugin_listing&utm_medium=pdf_basic&utm_campaign=PDF_invoice&utm_content='.WF_PKLIST_VERSION.'" target="_blank" style="color:#3db634;">'.__('Upgrade to premium','print-invoices-packing-slip-labels-for-woocommerce').'</a>';
	   $links[] = '<a href="https://wordpress.org/support/plugin/print-invoices-packing-slip-labels-for-woocommerce" target="_blank">'.__('Support','print-invoices-packing-slip-labels-for-woocommerce').'</a>';
	   $links[] = '<a href="https://wordpress.org/support/plugin/print-invoices-packing-slip-labels-for-woocommerce/reviews/?rate=5#new-post" target="_blank">' . __('Review','print-invoices-packing-slip-labels-for-woocommerce') . '</a>';
	   return $links;
	}


	/**
	 * create content for metabox
	 *
	 * @since    2.5.0
	 */
	public function create_metabox_content()
	{
		global $post;
        $order = ( WC()->version < '2.7.0' ) ? new WC_Order($post->ID) : new wf_order($post->ID);
        $order_id = (WC()->version < '2.7.0') ? $order->id : $order->get_id();
        $html='';
		?>
		<table class="wf_invoice_metabox">
			<?php
			$html=apply_filters('wt_print_metabox',$html,$order,$order_id);
			?>
		</table>
		<?php
	}


	/**
	 * Registers menu options
	 * Hooked into admin_menu
	 *
	 * @since    2.5.0
	 */
	public function admin_menu()
	{
		$menus=array(
			array(
				'menu',
				__('General Settings','print-invoices-packing-slip-labels-for-woocommerce'),
				__('Invoice/Packing','print-invoices-packing-slip-labels-for-woocommerce'),
				'manage_woocommerce',
				WF_PKLIST_POST_TYPE,
				array($this,'admin_settings_page'),
				'dashicons-media-text',
				56
			)
		);

		$menus=apply_filters('wt_admin_menu',$menus);

		$menus[]=array(
			'submenu',
			WF_PKLIST_POST_TYPE,
			__('Other Documents','print-invoices-packing-slip-labels-for-woocommerce'),
			__('Other Documents','print-invoices-packing-slip-labels-for-woocommerce'),
			'manage_woocommerce',
			WF_PKLIST_POST_TYPE.'_document_settings_page',
			array($this,'admin_document_settings_page')
		);


		if(count($menus)>0)
		{
			add_submenu_page(WF_PKLIST_POST_TYPE,__('General Settings','print-invoices-packing-slip-labels-for-woocommerce'),__('General Settings','print-invoices-packing-slip-labels-for-woocommerce'), "manage_woocommerce",WF_PKLIST_POST_TYPE,array($this,'admin_settings_page'));
			foreach($menus as $menu)
			{
				if($menu[0]=='submenu')
				{
					add_submenu_page($menu[1],$menu[2],$menu[3],$menu[4],$menu[5],$menu[6]);
				}else
				{
					add_menu_page($menu[1],$menu[2],$menu[3],$menu[4],$menu[5],$menu[6],$menu[7]);	
				}
			}
		}

		if(function_exists('remove_submenu_page')){
			//remove_submenu_page(WF_PKLIST_POST_TYPE,WF_PKLIST_POST_TYPE);
		}
	}

	/**
	* @since 2.5.9
	* Is allowed to print
	*/
	public static function check_role_access()
	{
		$admin_print_role_access=array('manage_options', 'manage_woocommerce');
    	$admin_print_role_access=apply_filters('wf_pklist_alter_admin_print_role_access', $admin_print_role_access);  
    	$admin_print_role_access=(!is_array($admin_print_role_access) ? array() : $admin_print_role_access);
    	$is_allowed=false;
    	foreach($admin_print_role_access as $role) //checking access
    	{
    		if(current_user_can($role)) //any of the role is okay then allow to print
    		{
    			$is_allowed=true;
    			break;
    		}
    	}
    	return $is_allowed;
	}

	/**
	 * function to render printing window
	 *
	 */
    public function print_window() 
    {       
        $attachments = array();
        if(isset($_GET['print_packinglist'])) 
        {
        	//checkes user is logged in
        	if(!is_user_logged_in())
        	{
        		auth_redirect();
        	}
        	$not_allowed_msg=__('You are not allowed to view this page.','print-invoices-packing-slip-labels-for-woocommerce');
        	$not_allowed_title=__('Access denied !!!.','print-invoices-packing-slip-labels-for-woocommerce');

            $client = false;
            //	to check current user has rights to get invoice and packing list
            if(!isset($_GET['attaching_pdf']))
            {
	            $nonce=isset($_GET['_wpnonce']) ? sanitize_text_field($_GET['_wpnonce']) : ''; 
	            if(!(wp_verify_nonce($nonce,WF_PKLIST_PLUGIN_NAME)))
	            {
	                wp_die($not_allowed_msg,$not_allowed_title);
	            }else
	            {
	            	if(!$this->check_role_access()) //Check access
	                {
	                	wp_die($not_allowed_msg,$not_allowed_title);
	                }
	            	$orders = explode(',', sanitize_text_field($_GET['post']));
	            }
        	}else 
        	{
        		// to get the orders number
	            if(isset($_GET['email']) && isset($_GET['post']) && isset($_GET['user_print']))
	            {
	                $email_data_get =Wf_Woocommerce_Packing_List::wf_decode(sanitize_text_field($_GET['email']));
	                $order_data_get =Wf_Woocommerce_Packing_List::wf_decode(sanitize_text_field($_GET['post']));
	                $order_data = wc_get_order($order_data_get);
	                if(!$order_data)
	                {
	                	wp_die($not_allowed_msg,$not_allowed_title);
	                }
	                $logged_in_userid=get_current_user_id();
	                $order_user_id=((WC()->version < '2.7.0') ? $order_data->user_id : $order_data->get_user_id());
	                if($logged_in_userid!=$order_user_id) //the current order not belongs to the current logged in user
	                { 
	  	             	if(!$this->check_role_access()) //Check access
	                	{
	                		wp_die($not_allowed_msg,$not_allowed_title);
	                	}
	                }

	                //checks the email parameters belongs to the given order
	                if($email_data_get === ((WC()->version < '2.7.0') ? $order_data->billing_email : $order_data->get_billing_email())) 
	                {
	                    $orders=explode(",",$order_data_get); //must be an array
	                }else
	                {
	                    wp_die($not_allowed_msg,$not_allowed_title);
	                }
	            }else
	            {
	            	wp_die($not_allowed_msg,$not_allowed_title);
	            }
        	} 
            $orders=array_values(array_filter($orders));
            $orders=$this->verify_order_ids($orders);
            if(count($orders)>0)
            {
	            remove_action('wp_footer', 'wp_admin_bar_render', 1000);
	            $action = (isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '');
	            //action for modules to hook print function
	            do_action('wt_print_doc', $orders, $action);
	        }
            exit();
        }
    }

    /**
	* Check for valid order ids
	* @since 2.5.4
	* @since 2.5.7 Added compatiblity for `Sequential Order Numbers for WooCommerce`
	*/
    public static function verify_order_ids($order_ids)
    {
    	$out=array();
    	foreach ($order_ids as $order_id)
    	{
    		if(wc_get_order($order_id)===false)
    		{
    			/* compatibility for sequential order number */
    			$order_data=wc_get_orders(
    				array(
    					'limit' => 1,
    					'return' => 'ids',
    					'meta_query'=>array(
    						'key'=>'_order_number',
    						'value'=>$order_id,
    					)
    			));
    			if($order_data!=false && is_array($order_data) && count($order_data)==1)
    			{
    				$order_id=(int) $order_data[0];
    				if($order_id>0 && wc_get_order($order_id)!=false)
    				{
    					$out[]=$order_id;
    				}
    			}
    		}else
    		{
    			$out[]=$order_id;
    		}
    	}
    	return $out;
    }

    public function load_address_from_woo()
    {
    	if(!self::check_write_access()) 
		{
			exit();
		}
    	$out=array(
    		'status'=>1,
    		'address_line1'=>get_option('woocommerce_store_address'),
    		'address_line2'=>get_option('woocommerce_store_address_2'),
    		'city'=>get_option('woocommerce_store_city'),
    		'country'=>get_option('woocommerce_default_country'),
    		'postalcode'=>get_option('woocommerce_store_postcode'),
    	);
    	echo json_encode($out);
    	exit();
    }

	private function dismiss_notice()
	{
		$allowd_items=array();
		if(isset($_GET['wf_pklist_notice_dismiss']) && trim($_GET['wf_pklist_notice_dismiss'])!="")
		{
			if(in_array(sanitize_text_field($_GET['wf_pklist_notice_dismiss']),$allowd_items))
			{
				update_option(sanitize_text_field($_GET['wf_pklist_notice_dismiss']),1);
			}
		}
	}

	/**
	 * Admin document settings page
	 *
	 * @since    2.5.0
	 */
	public function admin_document_settings_page()
	{
		//dismiss the notice if exists
		$this->dismiss_notice();
		wp_enqueue_style( 'woocommerce_admin_styles' );
		include WF_PKLIST_PLUGIN_PATH.'admin/partials/wf-woocommerce-packing-list-admin-document-settings.php';
	}

	/**
	 * Admin settings page
	 *
	 * @since    2.5.0
	 */
	public function admin_settings_page()
	{
		//dismiss the notice if exists
		$this->dismiss_notice();

		$the_options=Wf_Woocommerce_Packing_List::get_settings();
		$no_image=Wf_Woocommerce_Packing_List::$no_image;
		$order_statuses = wc_get_order_statuses();
		$wf_generate_invoice_for=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_orderstatus');
		
		/**
		*	@since 2.6.6
		*	Get available PDF libraries
		*/
		$pdf_libs=Wf_Woocommerce_Packing_List::get_pdf_libraries();

		wp_enqueue_media();
		wp_enqueue_script('wc-enhanced-select');
		wp_enqueue_style('woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css');

		/* enable/disable modules */
		if(isset($_POST['wf_update_module_status']))
		{
			// Check nonce:
	        if(!Wf_Woocommerce_Packing_List_Admin::check_write_access()) 
    		{
    			exit();
    		}

		    $wt_pklist_common_modules=get_option('wt_pklist_common_modules');
		    if($wt_pklist_common_modules===false)
		    {
		        $wt_pklist_common_modules=array();
		    }
		    if(isset($_POST['wt_pklist_common_modules']))
		    {
		        $wt_pklist_post=self::sanitize_text_arr($_POST['wt_pklist_common_modules']);
		        foreach($wt_pklist_common_modules as $k=>$v)
		        {
		            if(isset($wt_pklist_post[$k]) && $wt_pklist_post[$k]==1)
		            {
		                $wt_pklist_common_modules[$k]=1;
		            }else
		            {
		                $wt_pklist_common_modules[$k]=0;
		            }
		        }
		    }else
		    {
		    	foreach($wt_pklist_common_modules as $k=>$v)
		        {
					$wt_pklist_common_modules[$k]=0;
		        }
		    }
		    update_option('wt_pklist_common_modules',$wt_pklist_common_modules);
		    wp_redirect($_SERVER['REQUEST_URI']); exit();
		}

		include WF_PKLIST_PLUGIN_PATH.'admin/partials/wf-woocommerce-packing-list-admin-display.php';
	}

	/**
	* @since 2.6.2
	* Is user allowed 
	*/
	public static function check_write_access($nonce_id='')
	{
		$er=true;
		//checkes user is logged in
    	if(!is_user_logged_in())
    	{
    		$er=false;
    	}

    	if($er===true) //no error then proceed
    	{
    		$nonce=sanitize_text_field($_REQUEST['_wpnonce']);
    		$nonce=(is_array($nonce) ? $nonce[0] : $nonce);
    		$nonce_id=($nonce_id=="" ? WF_PKLIST_PLUGIN_NAME : $nonce_id);
    		if(!(wp_verify_nonce($nonce, $nonce_id)))
	        {
	            $er=false;
	        }else
	        {
	        	if(!Wf_Woocommerce_Packing_List_Admin::check_role_access()) //Check access
	            {
	            	$er=false;
	            }
	        }
    	}
    	return $er;
	}

	/**
	* Form action for debug settings tab
	* @since 2.6.7
	*/
	public function debug_save()
	{	
		if(isset($_POST['wt_pklist_admin_modules_btn']))
		{
		    if(!Wf_Woocommerce_Packing_List_Admin::check_write_access()) 
	    	{
	    		return;
	    	}
	        
		    $wt_pklist_common_modules=get_option('wt_pklist_common_modules');
		    if($wt_pklist_common_modules===false)
		    {
		        $wt_pklist_common_modules=array();
		    }
		    if(isset($_POST['wt_pklist_common_modules']))
		    {
		        $wt_pklist_post=self::sanitize_text_arr($_POST['wt_pklist_common_modules']);
		        foreach($wt_pklist_common_modules as $k=>$v)
		        {
		            if(isset($wt_pklist_post[$k]) && $wt_pklist_post[$k]==1)
		            {
		                $wt_pklist_common_modules[$k]=1;
		            }else
		            {
		                $wt_pklist_common_modules[$k]=0;
		            }
		        }
		    }else
		    {
		    	foreach($wt_pklist_common_modules as $k=>$v)
		        {
					$wt_pklist_common_modules[$k]=0;
		        }
		    }

		    $wt_pklist_admin_modules=get_option('wt_pklist_admin_modules');
		    if($wt_pklist_admin_modules===false)
		    {
		        $wt_pklist_admin_modules=array();
		    }
		    if(isset($_POST['wt_pklist_admin_modules']))
		    {
		        $wt_pklist_post=self::sanitize_text_arr($_POST['wt_pklist_admin_modules']);
		        foreach($wt_pklist_admin_modules as $k=>$v)
		        {
		            if(isset($wt_pklist_post[$k]) && $wt_pklist_post[$k]==1)
		            {
		                $wt_pklist_admin_modules[$k]=1;
		            }else
		            {
		                $wt_pklist_admin_modules[$k]=0;
		            }
		        }
		    }else
		    {
		    	foreach($wt_pklist_admin_modules as $k=>$v)
		        {
					$wt_pklist_admin_modules[$k]=0;
		        }
		    }
		    update_option('wt_pklist_admin_modules',$wt_pklist_admin_modules);
		    update_option('wt_pklist_common_modules',$wt_pklist_common_modules);
		    wp_redirect($_SERVER['REQUEST_URI']); exit();
		}

		if(Wf_Woocommerce_Packing_List_Admin::check_role_access()) //Check access
	    {
			//module debug settings saving hook
	    	do_action('wt_pklist_module_save_debug_settings');
	    }
	}

	/**
	*	@since 2.6.2 
	* 	Validate array data
	*/
	public static function sanitize_text_arr($arr, $type='text')
	{
		if(is_array($arr))
		{
			$out=array();
			foreach($arr as $k=>$arrv)
			{
				if(is_array($arrv))
				{
					$out[$k]=self::sanitize_text_arr($arrv, $type);
				}else
				{
					if($type=='int')
					{
						$out[$k]=intval($arrv);
					}else
					{
						$out[$k]=sanitize_text_field($arrv);
					}
				}
			}
			return $out;
		}else
		{
			if($type=='int')
			{
				return intval($arr);
			}else
			{
				return sanitize_text_field($arr);
			}
		}
	}

	/**
	*	@since 2.6.2 
	* 	Settings validation function for modules and plugin settings
	*/
	public function validate_settings_data($val, $key, $validation_rule=array())
	{		
		if(isset($validation_rule[$key]) && is_array($validation_rule[$key])) /* rule declared/exists */
		{
			if(isset($validation_rule[$key]['type']))
			{
				if($validation_rule[$key]['type']=='text')
				{
					$val=sanitize_text_field($val);
				}elseif($validation_rule[$key]['type']=='text_arr')
				{
					$val=self::sanitize_text_arr($val);
				}elseif($validation_rule[$key]['type']=='int')
				{
					$val=intval($val);
				}elseif($validation_rule[$key]['type']=='float')
				{
					$val=floatval($val);
				}elseif($validation_rule[$key]['type']=='int_arr')
				{
					$val=self::sanitize_text_arr($val, 'int');
				}
				elseif($validation_rule[$key]['type']=='textarea')
				{
					$val=sanitize_textarea_field($val);
				}else
				{
					$val=sanitize_text_field($val);
				}
			}
		}else
		{
			$val=sanitize_text_field($val);
		}
		return $val;
	}

	public function validate_box_packing_field($value)
	{           
        $new_boxes = array();
        foreach ($value as $key => $value)
        {
            if ($value['length'] != '') {
                $value['enabled'] = isset($value['enabled']) ? true : false;
                $new_boxes[] = $value;
            }
        }
        return $new_boxes;
    }
	public static function generate_form_field($args,$base='')
	{		
		include WF_PKLIST_PLUGIN_PATH."admin/views/_form_field_generator.php";
	}

	public static function generate_form_advanced_fields($args,$base='')
	{		
		include WF_PKLIST_PLUGIN_PATH."admin/views/_form_advanced_field_generator.php";
	}
	/**
	 * Envelope settings tab content with tab div.
	 * relative path is not acceptable in view file
	 */
	public static function envelope_settings_tabcontent($target_id,$view_file="",$html="",$variables=array(),$need_submit_btn=0)
	{
		extract($variables);
	?>
		<div class="wf-tab-content" data-id="<?php echo $target_id;?>">
			<?php
			if($view_file!="" && file_exists($view_file))
			{
				include_once $view_file;
			}else
			{
				echo $html;
			}
			?>
			<?php 
			if($need_submit_btn==1)
			{
				include plugin_dir_path(WF_PKLIST_PLUGIN_FILENAME)."admin/views/admin-settings-save-button.php";
			}
			?>
		</div>
	<?php
	}

	/**
	 * Envelope settings subtab content with subtab div.
	 * relative path is not acceptable in view file
	 */
	public static function envelope_settings_subtabcontent($target_id,$view_file="",$html="",$variables=array(),$need_submit_btn=0)
	{
		extract($variables);
	?>
		<div class="wf_sub_tab_content" data-id="<?php echo $target_id;?>">
			<?php
			if($view_file!="" && file_exists($view_file))
			{
				include_once $view_file;
			}else
			{
				echo $html;
			}
			?>
			<?php 
			if($need_submit_btn==1)
			{
				include plugin_dir_path(WF_PKLIST_PLUGIN_FILENAME)."admin/views/admin-settings-save-button.php";
			}
			?>
		</div>
	<?php
	}

	/**
	 Registers modules: public+admin	 
	 */
	public function admin_modules()
	{ 	
		$admin_module_save = 0;
		$wt_pklist_admin_modules=get_option('wt_pklist_admin_modules');
		if($wt_pklist_admin_modules===false)
		{
			$wt_pklist_admin_modules=array();
			$admin_module_save = 1;
		}elseif(empty($wt_pklist_admin_modules)){
			$admin_module_save = 1;
		}

		foreach (self::$modules as $module) //loop through module list and include its file
		{
			$is_active=1;
			if(isset($wt_pklist_admin_modules[$module]))
			{
				$is_active=$wt_pklist_admin_modules[$module]; //checking module status
			}else
			{
				$wt_pklist_admin_modules[$module]=1; //default status is active
			}
			$module_file=plugin_dir_path( __FILE__ )."modules/$module/$module.php";
			if(file_exists($module_file) && $is_active==1)
			{
				self::$existing_modules[]=$module; //this is for module_exits checking
				require_once $module_file;
			}else
			{
				$wt_pklist_admin_modules[$module]=0;	
			}
		}
		$out=array();
		foreach($wt_pklist_admin_modules as $k=>$m)
		{
			if(in_array($k,self::$modules))
			{
				$out[$k]=$m;
			}
		}

		if($admin_module_save == 1){
			update_option('wt_pklist_admin_modules',$out);
		}
	}

	public static function module_exists($module)
	{
		return in_array($module,self::$existing_modules);
	}

	/**
	*	@since 2.6.2
	* 	Save document settings ajax hook
	*/
	public function save_document_settings()
	{
		$out=array(
			'status'=>false,
			'msg'=>__('Error', 'print-invoices-packing-slip-labels-for-woocommerce'),
		);
		if(Wf_Woocommerce_Packing_List_Admin::check_write_access()) 
    	{
			do_action('wt_pklist_document_save_settings');
			$out['status']=true;
	        $out['msg']=__('Settings Updated', 'print-invoices-packing-slip-labels-for-woocommerce');
    	}
		echo json_encode($out);
		exit();
	}

	/**
	*	@since 2.6.2
	* 	Save admin settings and module settings ajax hook
	*/
	public function save_settings()
	{
		$out=array(
			'status'=>false,
			'msg'=>__('Error', 'print-invoices-packing-slip-labels-for-woocommerce'),
		);

		$base=(isset($_POST['wf_settings_base']) ? sanitize_text_field($_POST['wf_settings_base']) : 'main');
		$base_id=($base=='main' ? '' : Wf_Woocommerce_Packing_List::get_module_id($base));
		if(Wf_Woocommerce_Packing_List_Admin::check_write_access()) 
    	{
    		$the_options=Wf_Woocommerce_Packing_List::get_settings($base_id);
    		
    		//multi select form fields array. (It will not return a $_POST val if it's value is empty so we need to set default value)
	        $default_val_needed_fields=array();

	        /* this is an internal filter */
	        $default_val_needed_fields=apply_filters('wt_pklist_intl_alter_multi_select_fields', $default_val_needed_fields, $base_id);

	        $validation_rule=array(				
				'woocommerce_wf_packinglist_boxes'=>array('type'=>'text_arr'),
				'woocommerce_wf_packinglist_footer'=>array('type'=>'textarea'),
				'woocommerce_wf_generate_for_taxstatus'=>array('type'=>'text_arr'),
		    ); //this is for plugin settings default. Modules can alter

	        $validation_rule=apply_filters('wt_pklist_intl_alter_validation_rule', $validation_rule, $base_id);

	        foreach($the_options as $key => $value) 
	        {
	            if(isset($_POST[$key]))
	            {
	            	$the_options[$key]=$this->validate_settings_data($_POST[$key], $key, $validation_rule);
	            	if($key=='woocommerce_wf_packinglist_boxes')
	            	{
	            		$the_options[$key]=$this->validate_box_packing_field($_POST[$key]);
	            	}
	            }else
	            {
	            	if(array_key_exists($key,$default_val_needed_fields))
	            	{
	            		/* Set a hidden field for every multi-select field in the form. This will be used to populate the multi-select field with an empty array when it does not have any value. */
	            		if(isset($_POST[$key.'_hidden']))
	            		{
	            			$the_options[$key]=$default_val_needed_fields[$key];
	            		}
	            	}
	            }
	        }
	        Wf_Woocommerce_Packing_List::update_settings($the_options, $base_id);

	        do_action('wf_pklist_intl_after_setting_update', $the_options, $base_id);

	        $out['status']=true;
	        $out['msg']=__('Settings Updated', 'print-invoices-packing-slip-labels-for-woocommerce');
    	}
		echo json_encode($out);
		exit();
	}

	public static function strip_unwanted_tags($html)
	{
		$html=html_entity_decode(stripcslashes($html));
		$html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
		$html = preg_replace('#<iframe(.*?)>(.*?)</iframe>#is', '', $html);
		$html = preg_replace('#<audio(.*?)>(.*?)</audio>#is', '', $html);
		$html = preg_replace('#<video(.*?)>(.*?)</video>#is', '', $html);
		return $html;
	}

	/**
	*	@since 2.6.6
	* 	List of all languages with locale name and native name
	* 	@return array An associative array of languages.
	*/
	public static function get_language_list()
	{
		include plugin_dir_path(__FILE__).'data/data.language-list.php';
		
		/**
		*	Alter language list.
		*	@param array An associative array of languages.
		*/
		$wt_pklist_language_list=apply_filters('wt_pklist_alter_language_list', $wt_pklist_language_list);

		return $wt_pklist_language_list;
	}

	/**
	*	@since 2.6.6 Get list of RTL languages
	*	@return array an associative array of RTL languages with locale name, native name, locale code, WP locale code
	*/
	public static function get_rtl_languages()
	{
		$rtl_lang_keys=array('ar', 'dv', 'he_IL', 'ps', 'fa_IR', 'ur');

		/**
		*	Alter RTL language list.
		*	@param array RTL language locale codes (WP specific locale codes)
		*/
		$rtl_lang_keys=apply_filters('wt_pklist_alter_rtl_language_list', $rtl_lang_keys);

		$lang_list=self::get_language_list(); //taking full language list		
		
		$rtl_lang_keys=array_flip($rtl_lang_keys);
		return array_intersect_key($lang_list, $rtl_lang_keys);
	}

	/**
	*	@since 2.6.6 Checks user enabled RTL and current language needs RTL support.
	*	@return boolean 
	*/
	public static function is_enable_rtl_support()
	{
		if(!is_null(self::$is_enable_rtl)) /* already checked then return the stored result */
		{
			return self::$is_enable_rtl;
		}
		$rtl_languages=self::get_rtl_languages();
		$current_lang=get_locale();
		
		self::$is_enable_rtl=isset($rtl_languages[$current_lang]); 
		return self::$is_enable_rtl;
	}

	/**
    * @since 2.7.8
    * Compatible with multi currency and currency switcher plugin
    * 2.7.9 - bug fix - compatible with WC version below 4.1.0
    */
    public static function wf_display_price($user_currency,$order,$price){

    	$order_id=WC()->version<'2.7.0' ? $order->id : $order->get_id();
    	$price = (float)$price;

    	if(WC()->version<'4.1.0'){
    		$symbols = self::wf_get_woocommerce_currency_symbols();
    	}else{
    		$symbols = get_woocommerce_currency_symbols();
    	}

    	if(get_option('woocommerce_currency_pos')){
    		$currency_pos = get_option('woocommerce_currency_pos');
    	}else{
    		$currency_pos = "left";
    	}
    	
    	$wc_currency_symbol = isset( $symbols[ $user_currency ] ) ? $symbols[ $user_currency ] : '';

    	$wc_currency_symbol = apply_filters('wt_pklist_alter_currency_symbol',$wc_currency_symbol,$symbols,$user_currency,$order,$price);

    	if(get_option('woocommerce_price_num_decimals') == true){
    		$decimal = wc_get_price_decimals();
    	}else{
    		$decimal = 0;
    	}
    	
    	if(get_option('woocommerce_price_decimal_sep')){
    		$decimal_sep = wc_get_price_decimal_separator();
    	}else{
    		$decimal_sep = ".";
    	} 

    	if(get_option('woocommerce_price_thousand_sep')){
    		$thousand_sep = wc_get_price_thousand_separator();
    	}else{
    		$thousand_sep = ",";
    	}

    	if(is_plugin_active('woocommerce-currency-switcher/index.php'))
		{
			if(class_exists('WOOCS')){
				global $WOOCS;
				$multi_currencies = $WOOCS->get_currencies();
				$user_selected_currency = $multi_currencies[$user_currency];
				$currency_symbol = "";
				if(!empty($user_selected_currency)){
					if(array_key_exists('position', $user_selected_currency))
					{
						$currency_pos = $user_selected_currency["position"];
					}
					if(array_key_exists('decimals', $user_selected_currency))
					{
						$decimal = $user_selected_currency["decimals"];
					}
				}
			}
		}elseif(is_plugin_active('woo-multi-currency/woo-multi-currency.php'))
		{
			if ( metadata_exists( 'post', $order_id, 'wmc_order_info' ) ) 
			{
			   	$wmc_order_info = $order->get_meta('wmc_order_info');
			   	if(array_key_exists($user_currency, $wmc_order_info))
			   	{
			   		if(array_key_exists('pos', $wmc_order_info[$user_currency]))
			   		{
				   		$currency_pos = $wmc_order_info[$user_currency]['pos'];
				   	}
			   		if(array_key_exists('decimals', $wmc_order_info[$user_currency]))
			   		{
				   		$decimal = $wmc_order_info[$user_currency]['decimals'];
				   	}
			   	}
			}
		}

		if(trim($decimal) == ""){
			$decimal = 0;
		}
		if(trim($decimal_sep) == ""){
			$decimal_sep = ".";
		}
		if(trim($thousand_sep) == ""){
			$thousand_sep = ",";
		}

		$currency_pos = apply_filters('wt_pklist_alter_currency_symbol_position',$currency_pos,$symbols,$wc_currency_symbol,$user_currency,$order,$price);
		$decimal = apply_filters('wt_pklist_alter_currency_decimal',$decimal,$wc_currency_symbol,$user_currency,$order,$price);
		$decimal_sep = apply_filters('wt_pklist_alter_currency_decimal_seperator',$decimal_sep,$symbols,$wc_currency_symbol,$user_currency,$order,$price);
    	$thousand_sep = apply_filters('wt_pklist_alter_currency_thousand_seperator',$thousand_sep,$symbols,$wc_currency_symbol,$user_currency,$order,$price);
    	$wf_formatted_price = number_format($price,$decimal,$decimal_sep,$thousand_sep);
    	// echo $wf_formatted_price;
		if(trim($wc_currency_symbol) != ""){
			switch ($currency_pos) {
				case 'left':
					$result = $wc_currency_symbol.$wf_formatted_price;
					break;
				case 'right':
					$result = $wf_formatted_price.$wc_currency_symbol;
					break;
				case 'left_space':
					$result = $wc_currency_symbol.' '.$wf_formatted_price;
					break;
				case 'right_space':
					$result = $wf_formatted_price.' '.$wc_currency_symbol;
					break;
				default:
					$result = $wc_currency_symbol.$wf_formatted_price;
					break;
			}
		}else{
			$result = $wf_formatted_price.' '.$user_currency;
		}

		$result = apply_filters('wt_pklist_change_currency_format',$result,$symbols,$wc_currency_symbol,$currency_pos,$decimal,$decimal_sep,$thousand_sep,$user_currency,$price,$order);

		return "<span>".$result."</span>";	
    }

    public static function wf_get_decimal_price($user_currency,$order){
    	$order_id=WC()->version<'2.7.0' ? $order->id : $order->get_id();
    	if(get_option('woocommerce_price_num_decimals') == true){
    		$decimal = wc_get_price_decimals();
    	}else{
    		$decimal = 0;
    	}

    	if(is_plugin_active('woocommerce-currency-switcher/index.php'))
		{
			if(class_exists('WOOCS')){
				global $WOOCS;
				$multi_currencies = $WOOCS->get_currencies();
				$user_selected_currency = $multi_currencies[$user_currency];
				if(!empty($user_selected_currency)){
					if(array_key_exists('decimals', $user_selected_currency))
					{
						$decimal = $user_selected_currency["decimals"];
					}
				}
			}
		}elseif(is_plugin_active('woo-multi-currency/woo-multi-currency.php'))
		{
			if ( metadata_exists( 'post', $order_id, 'wmc_order_info' ) ) 
			{
			   	$wmc_order_info = $order->get_meta('wmc_order_info');
			   	if(array_key_exists($user_currency, $wmc_order_info))
			   	{
			   		if(array_key_exists('decimals', $wmc_order_info[$user_currency]))
			   		{
				   		$decimal = $wmc_order_info[$user_currency]['decimals'];
				   	}
			   	}
			}
		}

		if(trim($decimal) == ""){
			$decimal = 0;
		}

		return $decimal;
    }
    /**
    * @since 2.7.8
    * Convert the price with multi currency and currency switcher plugin
    */
    public static function wf_convert_to_user_currency($item_price,$user_currency,$order){

    	$rate = 1;
    	$order_id=WC()->version<'2.7.0' ? $order->id : $order->get_id();
    	$item_price = (float)$item_price;

    	/* currency switcher - packinglist product table */
    	if(is_plugin_active('woocommerce-currency-switcher/index.php')) 
		{	
			if ( metadata_exists( 'post', $order_id, '_woocs_order_rate' ) ) {
			    $rate = get_post_meta( $order_id, '_woocs_order_rate', true );
			}elseif( metadata_exists( 'post', $order_id, 'wmc_order_info' ) ) {
			   	$wmc_order_info = $order->get_meta('wmc_order_info');
				$rate = $wmc_order_info[$user_currency]['rate'];
			}
		}
		elseif(is_plugin_active('woo-multi-currency/woo-multi-currency.php')) /* Multi currency - packinglist product table */
		{
			if ( metadata_exists( 'post', $order_id, 'wmc_order_info' ) ) {
			   	$wmc_order_info = $order->get_meta('wmc_order_info');
				$rate = $wmc_order_info[$user_currency]['rate'];
			}elseif ( metadata_exists( 'post', $order_id, '_woocs_order_rate' ) ) {
			    $rate = get_post_meta( $order_id, '_woocs_order_rate', true );
			}
		}
		else
		{
			/* currency switcher / multicurrency even plugins are not available - packinglist product table */
			if ( metadata_exists( 'post', $order_id, '_woocs_order_rate' ) ) {
			    $rate = get_post_meta( $order_id, '_woocs_order_rate', true );
			}elseif( metadata_exists( 'post', $order_id, 'wmc_order_info' ) ) {
			   	$wmc_order_info = $order->get_meta('wmc_order_info');
				$rate = $wmc_order_info[$user_currency]['rate'];
			}
		}
		return $item_price * (float)$rate;
    }

    /**
    * @since 2.7.9
    * Get the currecy symbols array for the WC < 4.1.0
    */
    public static function wf_get_woocommerce_currency_symbols(){
    	$symbols = array(
			'AED' => '&#x62f;.&#x625;',
			'AFN' => '&#x60b;',
			'ALL' => 'L',
			'AMD' => 'AMD',
			'ANG' => '&fnof;',
			'AOA' => 'Kz',
			'ARS' => '&#36;',
			'AUD' => '&#36;',
			'AWG' => 'Afl.',
			'AZN' => 'AZN',
			'BAM' => 'KM',
			'BBD' => '&#36;',
			'BDT' => '&#2547;&nbsp;',
			'BGN' => '&#1083;&#1074;.',
			'BHD' => '.&#x62f;.&#x628;',
			'BIF' => 'Fr',
			'BMD' => '&#36;',
			'BND' => '&#36;',
			'BOB' => 'Bs.',
			'BRL' => '&#82;&#36;',
			'BSD' => '&#36;',
			'BTC' => '&#3647;',
			'BTN' => 'Nu.',
			'BWP' => 'P',
			'BYR' => 'Br',
			'BYN' => 'Br',
			'BZD' => '&#36;',
			'CAD' => '&#36;',
			'CDF' => 'Fr',
			'CHF' => '&#67;&#72;&#70;',
			'CLP' => '&#36;',
			'CNY' => '&yen;',
			'COP' => '&#36;',
			'CRC' => '&#x20a1;',
			'CUC' => '&#36;',
			'CUP' => '&#36;',
			'CVE' => '&#36;',
			'CZK' => '&#75;&#269;',
			'DJF' => 'Fr',
			'DKK' => 'DKK',
			'DOP' => 'RD&#36;',
			'DZD' => '&#x62f;.&#x62c;',
			'EGP' => 'EGP',
			'ERN' => 'Nfk',
			'ETB' => 'Br',
			'EUR' => '&euro;',
			'FJD' => '&#36;',
			'FKP' => '&pound;',
			'GBP' => '&pound;',
			'GEL' => '&#x20be;',
			'GGP' => '&pound;',
			'GHS' => '&#x20b5;',
			'GIP' => '&pound;',
			'GMD' => 'D',
			'GNF' => 'Fr',
			'GTQ' => 'Q',
			'GYD' => '&#36;',
			'HKD' => '&#36;',
			'HNL' => 'L',
			'HRK' => 'kn',
			'HTG' => 'G',
			'HUF' => '&#70;&#116;',
			'IDR' => 'Rp',
			'ILS' => '&#8362;',
			'IMP' => '&pound;',
			'INR' => '&#8377;',
			'IQD' => '&#x639;.&#x62f;',
			'IRR' => '&#xfdfc;',
			'IRT' => '&#x062A;&#x0648;&#x0645;&#x0627;&#x0646;',
			'ISK' => 'kr.',
			'JEP' => '&pound;',
			'JMD' => '&#36;',
			'JOD' => '&#x62f;.&#x627;',
			'JPY' => '&yen;',
			'KES' => 'KSh',
			'KGS' => '&#x441;&#x43e;&#x43c;',
			'KHR' => '&#x17db;',
			'KMF' => 'Fr',
			'KPW' => '&#x20a9;',
			'KRW' => '&#8361;',
			'KWD' => '&#x62f;.&#x643;',
			'KYD' => '&#36;',
			'KZT' => '&#8376;',
			'LAK' => '&#8365;',
			'LBP' => '&#x644;.&#x644;',
			'LKR' => '&#xdbb;&#xdd4;',
			'LRD' => '&#36;',
			'LSL' => 'L',
			'LYD' => '&#x644;.&#x62f;',
			'MAD' => '&#x62f;.&#x645;.',
			'MDL' => 'MDL',
			'MGA' => 'Ar',
			'MKD' => '&#x434;&#x435;&#x43d;',
			'MMK' => 'Ks',
			'MNT' => '&#x20ae;',
			'MOP' => 'P',
			'MRU' => 'UM',
			'MUR' => '&#x20a8;',
			'MVR' => '.&#x783;',
			'MWK' => 'MK',
			'MXN' => '&#36;',
			'MYR' => '&#82;&#77;',
			'MZN' => 'MT',
			'NAD' => 'N&#36;',
			'NGN' => '&#8358;',
			'NIO' => 'C&#36;',
			'NOK' => '&#107;&#114;',
			'NPR' => '&#8360;',
			'NZD' => '&#36;',
			'OMR' => '&#x631;.&#x639;.',
			'PAB' => 'B/.',
			'PEN' => 'S/',
			'PGK' => 'K',
			'PHP' => '&#8369;',
			'PKR' => '&#8360;',
			'PLN' => '&#122;&#322;',
			'PRB' => '&#x440;.',
			'PYG' => '&#8370;',
			'QAR' => '&#x631;.&#x642;',
			'RMB' => '&yen;',
			'RON' => 'lei',
			'RSD' => '&#1088;&#1089;&#1076;',
			'RUB' => '&#8381;',
			'RWF' => 'Fr',
			'SAR' => '&#x631;.&#x633;',
			'SBD' => '&#36;',
			'SCR' => '&#x20a8;',
			'SDG' => '&#x62c;.&#x633;.',
			'SEK' => '&#107;&#114;',
			'SGD' => '&#36;',
			'SHP' => '&pound;',
			'SLL' => 'Le',
			'SOS' => 'Sh',
			'SRD' => '&#36;',
			'SSP' => '&pound;',
			'STN' => 'Db',
			'SYP' => '&#x644;.&#x633;',
			'SZL' => 'L',
			'THB' => '&#3647;',
			'TJS' => '&#x405;&#x41c;',
			'TMT' => 'm',
			'TND' => '&#x62f;.&#x62a;',
			'TOP' => 'T&#36;',
			'TRY' => '&#8378;',
			'TTD' => '&#36;',
			'TWD' => '&#78;&#84;&#36;',
			'TZS' => 'Sh',
			'UAH' => '&#8372;',
			'UGX' => 'UGX',
			'USD' => '&#36;',
			'UYU' => '&#36;',
			'UZS' => 'UZS',
			'VEF' => 'Bs F',
			'VES' => 'Bs.S',
			'VND' => '&#8363;',
			'VUV' => 'Vt',
			'WST' => 'T',
			'XAF' => 'CFA',
			'XCD' => '&#36;',
			'XOF' => 'CFA',
			'XPF' => 'Fr',
			'YER' => '&#xfdfc;',
			'ZAR' => '&#82;',
			'ZMW' => 'ZK',
		);
		return $symbols;
    }

    /**
    * @since 2.8.0
    * Shipping address with order currency symbol
    */
	public static function wf_shipping_formated_price($order){
		$order_id=(WC()->version<'2.7.0' ? $order->id : $order->get_id());
		$user_currency = get_post_meta($order_id,'_order_currency',true);
		$tax_display = get_option( 'woocommerce_tax_display_cart' );

		$tax_type=Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_generate_for_taxstatus');
		$incl_tax=in_array('in_tax', $tax_type);

		if ( 0 < abs( (float) $order->get_shipping_total() ) ) {
			if(!$incl_tax){
				// Show shipping excluding tax.
				$shipping = apply_filters('wt_pklist_change_price_format',$user_currency,$order,$order->get_shipping_total());
				if ( (float) $order->get_shipping_tax() > 0 && $order->get_prices_include_tax() ) {
					$shipping .= apply_filters( 'woocommerce_order_shipping_to_display_tax_label', '&nbsp;<small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>', $order, $tax_display );
				}
			} else {
				// Show shipping including tax.
				$tot_shipping_amount = $order->get_shipping_total() + $order->get_shipping_tax();
				$shipping = apply_filters('wt_pklist_change_price_format',$user_currency,$order,$tot_shipping_amount);
				if ( (float) $order->get_shipping_tax() > 0 && ! $order->get_prices_include_tax() ) {
				$shipping .= apply_filters( 'woocommerce_order_shipping_to_display_tax_label', '&nbsp;<small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>', $order, $tax_display );
				}
			}
			/* translators: %s: method */
			$shipping .= apply_filters( 'woocommerce_order_shipping_to_display_shipped_via', '&nbsp;<small class="shipped_via">' . sprintf( __( 'via %s', 'woocommerce' ), $order->get_shipping_method() ) . '</small>', $order );

		} elseif ( $order->get_shipping_method() ) {
			$shipping = $order->get_shipping_method();
		} else {
			$shipping = __( 'Free!', 'woocommerce' );
		}
		return $shipping;
	}

    /**
    * @since 2.8.0
    * Generate PDF file name for invoice template
    */

    public static function get_invoice_pdf_name($template_type,$order_ids,$module_id){

		$order = wc_get_order( $order_ids[0] );

		Wf_Woocommerce_Packing_List_Invoice::generate_invoice_number($order,true,'set');
		
		$wf_invoice_pdf_name_format = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_custom_pdf_name', $module_id);
		$wf_invoice_pdf_name_prefix = Wf_Woocommerce_Packing_List::get_option('woocommerce_wf_custom_pdf_name_prefix', $module_id);

		if($wf_invoice_pdf_name_format == "[prefix][order_no]"){
			$invoice_pdf_name_number_pos = $order_ids[0];
		}else{
			$invoice_pdf_name_number_pos = get_post_meta($order_ids[0],'wf_invoice_number',true);
		}

		if(trim($wf_invoice_pdf_name_prefix) == ""){
			$invoice_pdf_name_prefix_pos = "Invoice_";
		}else{
			$invoice_pdf_name_prefix_pos = $wf_invoice_pdf_name_prefix;
		}

		if($wf_invoice_pdf_name_format == "[prefix][invoice_no]"){
			$invoice_pdf_name_format = $wf_invoice_pdf_name_format;
		}else{
			$invoice_pdf_name_format = "[prefix][order_no]";
		}

		return str_replace(array('[prefix]','[order_no]','[invoice_no]'),array($invoice_pdf_name_prefix_pos,$invoice_pdf_name_number_pos,$invoice_pdf_name_number_pos),$invoice_pdf_name_format); 
	}

	public static function wf_search_order_by_invoice_number($search_fields){
		array_push($search_fields, 'wf_invoice_number');
		return $search_fields;
	}

	public function save_plugin_version_in_db(){
		if(get_option('wfpklist_basic_version') === false){
            update_option('wfpklist_basic_version_prev','none');
        }else{
            $prev_version = get_option('wfpklist_basic_version','none');
            update_option('wfpklist_basic_version_prev',$prev_version);
        }
        update_option('wfpklist_basic_version',WF_PKLIST_VERSION);
	}
}