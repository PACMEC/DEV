<?php

add_action('widgets_init','apr_social_init_widget');

function apr_social_init_widget(){
	register_widget('apr_social_widget');
}

class apr_social_widget extends WP_Widget{

	function __construct(){
		parent::__construct( 'apr_social_widget','APR Social',array('description' => 'Show social'));
	}


	/*-------------------------------------------------------
	 *				Front-end display of widget
	 *-------------------------------------------------------*/

	function widget($args, $instance){

		extract($args);

		$title 			= isset($instance['title'])?apply_filters('widget_title', $instance['title'] ):'';
		$facebook 	= isset($instance['facebook'])?$instance['facebook']:'';
		$twitter 	= isset($instance['twitter'])? $instance['twitter']:'';
		$instagram = isset($instance['instagram'])? $instance['instagram']:'';
		$pinterest = isset($instance['pinterest'])? $instance['pinterest']:'';
		$google = isset($instance['google']) ? $instance['google']: '';
		$linkedin = isset($instance['linkedin']) ? $instance['linkedin'] : '';
		$youtube = isset($instance['youtube']) ? $instance['youtube'] :'';
		$output = '';
		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

?>
	<ul class="footer-social">
		<?php if($title!=''): ?>
			<li class="title-social"><?php echo esc_attr($title);?>:</li>
		<?php endif; ?>
		<?php if($facebook != ''): ?>
			<li class="fb"><a href="<?php echo esc_attr($facebook);?>"><i class="fa fa-facebook-square" aria-hidden="true"></i></a> </li>
		<?php endif; ?>
		<?php if($twitter != ''): ?>
			<li class="tw"><a href="<?php echo esc_attr($twitter);?>"><i class="fa fa-twitter" aria-hidden="true"></i></a> </li>
		<?php endif; ?>
		<?php if($instagram != ''): ?>
			<li class="insta"><a href="<?php echo esc_attr($instagram);?>"><i class="fa fa-instagram" aria-hidden="true"></i></a> </li>
		<?php endif; ?>
		<?php if($pinterest != ''): ?>
			<li class="pin"><a href="<?php echo esc_attr($pinterest);?>"><i class="fa fa-pinterest" aria-hidden="true"></i></a> </li>
		<?php endif; ?>
		<?php if($google != ''): ?>
			<li class="gg"><a href="<?php echo esc_attr($google);?>"><i class="fa fa-google-plus" aria-hidden="true"></i></a> </li>
		<?php endif; ?>
		<?php if($linkedin != ''): ?>
			<li class="linkedin"><a href="<?php echo esc_attr($linkedin);?>"><i class="fa fa-linkedin" aria-hidden="true"></i></a> </li>
		<?php endif; ?>
		<?php if($youtube != ''): ?>
			<li class="yt"><a href="<?php echo esc_attr($youtube);?>"><i class="fa fa-youtube" aria-hidden="true"></i></a> </li>
		<?php endif; ?>
	</ul>
<?php
		echo $after_widget;
	}
	function update( $new_instance, $old_instance ){
		$instance = $old_instance;
		$instance['title'] 			= strip_tags( $new_instance['title'] );
		$instance['facebook'] 	= strip_tags( $new_instance['facebook'] );
		$instance['twitter'] = strip_tags( $new_instance['twitter'] );
		$instance['instagram'] 	= strip_tags( $new_instance['instagram'] );
		$instance['pinterest'] 	= strip_tags( $new_instance['pinterest'] );
		$instance['google'] 	= strip_tags( $new_instance['google'] );
		$instance['linkedin'] 	= strip_tags( $new_instance['linkedin'] );
		$instance['youtube'] 	= strip_tags( $new_instance['youtube'] );
		return $instance;
	}
	function form($instance){
		$defaults = array( 
			'title' 		=> 'Follow us',
			'facebook' 		=> '#',
			'twitter' 	=> '#',
			'instagram' 	=> '#',
			'pinterest' => '#',
			'google' => '',
			'linkedin' => '',
			'youtube' => '',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
	?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e('Widget Title', 'arrowpress-core'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" value="<?php echo esc_attr($instance['title']); ?>" style="width:100%;" />
		</p>


		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'facebook' )); ?>"><?php esc_html_e('Facebook', 'arrowpress-core'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'facebook' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'facebook' )); ?>" value="<?php echo esc_attr($instance['facebook']); ?>" style="width:100%;" />
		</p>

		<p >
			<label for="<?php echo esc_attr($this->get_field_id( 'twitter' )); ?>"><?php esc_html_e('Twitter', 'arrowpress-core'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'twitter' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'twitter' )); ?>" value="<?php echo esc_attr($instance['twitter']); ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'instagram' )); ?>"><?php esc_html_e('Instagram', 'arrowpress-core'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'instagram' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'instagram' )); ?>" value="<?php echo esc_attr($instance['instagram']); ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'pinterest' )); ?>"><?php esc_html_e('Pinterest', 'arrowpress-core'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'pinterest' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'pinterest' )); ?>" value="<?php echo esc_attr($instance['pinterest']); ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'google' )); ?>"><?php esc_html_e('Google Plus', 'arrowpress-core'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'google' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'google' )); ?>" value="<?php echo esc_attr($instance['google']); ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'linkedin' )); ?>"><?php esc_html_e('Linkedin', 'arrowpress-core'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'linkedin' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'linkedin' )); ?>" value="<?php echo esc_attr($instance['linkedin']); ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id( 'youtube' )); ?>"><?php esc_html_e('Youtube', 'arrowpress-core'); ?></label>
			<input id="<?php echo esc_attr($this->get_field_id( 'youtube' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'youtube' )); ?>" value="<?php echo esc_attr($instance['youtube']); ?>" style="width:100%;" />
		</p>
	<?php
	}
}