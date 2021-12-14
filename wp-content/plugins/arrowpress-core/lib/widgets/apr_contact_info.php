<?php

add_action('widgets_init','apr_contact_info_init_widget');

function apr_contact_info_init_widget(){
	register_widget('apr_contact_info_widget');
}

class apr_contact_info_widget extends WP_Widget{

	function __construct(){
		parent::__construct( 'apr_contact_info_widget','APR Contact Info',array('description' => 'Show contact info'));
	}


	/*-------------------------------------------------------
	 *				Front-end display of widget
	 *-------------------------------------------------------*/

	function widget($args, $instance){

		extract($args);

		$title 			= apply_filters('widget_title', $instance['title'] );
		$address 	= $instance['address'];
		$phone 	= $instance['phone'];
		$mail 	= $instance['mail'];
		$output = '';
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

?>		
		<ul class="list-info-footer">
			<li class="info-address"><i class="lnr lnr-map-marker"></i><?php echo esc_html($address);?></li>
			<li class="info-mail"> <i class="lnr lnr-envelope"></i><a href="mailto:<?php echo esc_attr($mail);?>"><?php echo esc_html($mail);?></a></li>	
			<li class="info-phone"> <i class="lnr lnr-phone"></i><a href="tel:<?php echo esc_attr($phone);?>"><?php echo esc_html($phone);?> </a> </li>
				
		</ul>

<?php
		echo $after_widget;

	}


	function update( $new_instance, $old_instance ){
		$instance = $old_instance;
		$instance['title'] 			= strip_tags( $new_instance['title'] );
		$instance['address'] 	= strip_tags( $new_instance['address'] );
		$instance['phone'] 	= strip_tags( $new_instance['phone'] );
		$instance['mail'] 	= strip_tags( $new_instance['mail'] );	
		return $instance;
	}


	function form($instance){
		$defaults = array( 
			'title' 		=> 'Popular News',
			'address' 	=> 'Lorem Ipsum has been the industry &rsquo; s standard',
			'phone' 	=> '+84 (1) 234 567 891',
			'mail' 			=> 'contact@medic.com',

		);
		$instance = wp_parse_args( (array) $instance, $defaults );
	?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Widget Title', 'arrowpress-core'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" style="width:100%;" />
		</p>
		
		
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'address' )); ?>"><?php esc_html_e('Address', 'arrowpress-core'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'address' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'address' )); ?>" value="<?php echo esc_attr($instance['address']); ?>" style="width:100%;" />
		</p>

		<p >
			<label for="<?php echo esc_attr($this->get_field_id( 'phone' )); ?>"><?php esc_html_e('Phone Number', 'arrowpress-core'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'phone' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'phone' )); ?>" value="<?php echo esc_attr($instance['phone']); ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'mail' )); ?>"><?php esc_html_e('Email', 'arrowpress-core'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'mail' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'mail' )); ?>" value="<?php echo esc_attr($instance['mail']); ?>" style="width:100%;" />
		</p>		
	<?php
	}
}