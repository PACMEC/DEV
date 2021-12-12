<div class="wrap">
	<h2><?php esc_html_e( $this->page_title ); ?></h2>
	<div class="row clearfix">
		<form method="post" id="form" action="<?php echo esc_url( $this->plugin_post ); ?>">
		<?php settings_fields( $this->setting_name.'_group' ); ?>
		<div class="col col-4">
			<?php do_action('admin_screen_col_1'); ?>
		</div>
		<div class="col col-4">
			<?php do_action('admin_screen_col_2'); ?>
		</div>
		<div class="col col-3">
			<div class="box admin-theme-option">
				<div class="box-body">
					<p>
						<input type="submit" class="button button-primary button-block button-lg m-b" value="<?php esc_attr_e('Save Changes', 'ffl_admin_theme') ?>" />
					</p>
					</form>
					<form method="post" enctype="multipart/form-data">
						<p>
							<input type="file" name="import_file"/>
						</p>
						<p>
							<input type="hidden" name="setting_action" value="import_setting" />
							<?php wp_nonce_field( 'setting_import_nonce', 'setting_import_nonce' ); ?>
							<?php submit_button( esc_html('Import theme', 'ffl_admin_theme'), 'button-block', 'submit', false, ! current_user_can( 'manage_options' ) ? array( 'disabled' => 'disabled' ) : null ); ?>
						</p>
					</form>
					<form method="post">
						<p><input type="hidden" name="setting_action" value="export_setting" /></p>
						<p>
							<?php wp_nonce_field( 'setting_export_nonce', 'setting_export_nonce' ); ?>
							<?php submit_button( esc_html('Export theme', 'ffl_admin_theme'), 'button-block', 'submit', false, ! current_user_can( 'manage_options' ) ? array( 'disabled' => 'disabled' ) : null ); ?>
						</p>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
