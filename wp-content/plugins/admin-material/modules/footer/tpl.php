<div class="box">
	<h4><span><?php esc_html_e('Footer', 'ffl_admin_theme'); ?></span></h4>
	<div class="box-body b-t hide">
		<p>
			<label>
				<?php esc_html_e('Text', 'ffl_admin_theme'); ?>
				<input name="<?php esc_attr_e($this->setting->setting_name); ?>[footer_text]" value="<?php esc_attr_e( $this->setting->get_setting('footer_text') ); ?>" type="text" class="widefat">
			</label>
		</p>
		<p>
			<label>
				<input name="<?php esc_attr_e($this->setting->setting_name); ?>[footer_text_hide]" type="checkbox" <?php if ($this->setting->get_setting('footer_text_hide') == true) echo 'checked="checked" '; ?>> 
				<?php esc_html_e('Hide "Text"', 'ffl_admin_theme'); ?>
			</label>
		</p>
		<p>
			<label>
				<?php esc_html_e('Version', 'ffl_admin_theme'); ?>
				<input name="<?php esc_attr_e($this->setting->setting_name); ?>[footer_version]" value="<?php esc_attr_e( $this->setting->get_setting('footer_version') ); ?>" type="text" class="widefat">
			</label>
		</p>
		<p>
			<label>
				<input name="<?php esc_attr_e($this->setting->setting_name); ?>[footer_version_hide]" type="checkbox" <?php if ($this->setting->get_setting('footer_version_hide') == true) echo 'checked="checked" '; ?>> 
				<?php esc_html_e('Hide "Version"', 'ffl_admin_theme'); ?>
			</label>
		</p>
	</div>
	<?php if ( is_multisite() && get_current_blog_id() == 1 && current_user_can( 'manage_options' ) ) { ?>
	<h4 class="b-t"><span><?php esc_html_e('Network', 'ffl_admin_theme'); ?></span></h4>
	<div class="box-body b-t hide">
		<p>
			<label>
				<input name="<?php esc_attr_e($this->setting->setting_name); ?>[network]" type="checkbox" <?php if ($this->setting->get_setting('network') == true) echo 'checked="checked" '; ?>> 
				<?php esc_html_e('Disable on sub sites', 'ffl_admin_theme'); ?>
			</label>
		</p>
	</div>
	<?php } ?>
	<h4 class="b-t"><span><?php esc_html_e('Custom css', 'ffl_admin_theme'); ?></span></h4>
	<div class="box-body b-t hide">
		<p>
			<label>
				<textarea name="<?php esc_attr_e($this->setting->setting_name); ?>[admin_css]" class="widefat" rows="4" placeHolder="a{color: #888}"><?php esc_html_e( $this->setting->get_setting('admin_css') ); ?></textarea>
			</label>
		</p>
	</div>
	<h4 class="b-t"><span><?php esc_html_e('Custom JS', 'ffl_admin_theme'); ?></span></h4>
	<div class="box-body b-t hide">
		<p>
			<label>
				<textarea name="<?php esc_attr_e($this->setting->setting_name); ?>[admin_js]" class="widefat" rows="4" placeHolder="alert(1);"><?php esc_html_e( $this->setting->get_setting('admin_js') ); ?></textarea>
			</label>
		</p>
	</div>
</div>
