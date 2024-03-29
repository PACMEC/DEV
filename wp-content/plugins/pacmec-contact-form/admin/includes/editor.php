<?php

class PCF_Editor {

	private $contact_form;
	private $panels = array();

	public function __construct( PCF_ContactForm $contact_form ) {
		$this->contact_form = $contact_form;
	}

	public function add_panel( $id, $title, $callback ) {
		if ( pcf_is_name( $id ) ) {
			$this->panels[$id] = array(
				'title' => $title,
				'callback' => $callback,
			);
		}
	}

	public function display() {
		if ( empty( $this->panels ) ) {
			return;
		}

		echo '<ul id="contact-form-editor-tabs">';

		foreach ( $this->panels as $id => $panel ) {
			echo sprintf( '<li id="%1$s-tab"><a href="#%1$s">%2$s</a></li>',
				esc_attr( $id ), esc_html( $panel['title'] ) );
		}

		echo '</ul>';

		foreach ( $this->panels as $id => $panel ) {
			echo sprintf( '<div class="contact-form-editor-panel" id="%1$s">',
				esc_attr( $id ) );

			if ( is_callable( $panel['callback'] ) ) {
				$this->notice( $id, $panel );
				call_user_func( $panel['callback'], $this->contact_form );
			}

			echo '</div>';
		}
	}

	public function notice( $id, $panel ) {
		echo '<div class="config-error"></div>';
	}
}

function pcf_editor_panel_form( $post ) {
?>
<h2><?php echo esc_html( __( 'Form', 'contact-form-7' ) ); ?></h2>

<?php
	$tag_generator = PCF_TagGenerator::get_instance();
	$tag_generator->print_buttons();
?>

<textarea id="pcf-form" name="pcf-form" cols="100" rows="24" class="large-text code" data-config-field="form.body"><?php echo esc_textarea( $post->prop( 'form' ) ); ?></textarea>
<?php
}

function pcf_editor_panel_mail( $post ) {
	pcf_editor_box_mail( $post );

	echo '<br class="clear" />';

	pcf_editor_box_mail( $post, array(
		'id' => 'pcf-mail-2',
		'name' => 'mail_2',
		'title' => __( 'Mail (2)', 'contact-form-7' ),
		'use' => __( 'Use Mail (2)', 'contact-form-7' ),
	) );
}

function pcf_editor_box_mail( $post, $args = '' ) {
	$args = wp_parse_args( $args, array(
		'id' => 'pcf-mail',
		'name' => 'mail',
		'title' => __( 'Mail', 'contact-form-7' ),
		'use' => null,
	) );

	$id = esc_attr( $args['id'] );

	$mail = wp_parse_args( $post->prop( $args['name'] ), array(
		'active' => false,
		'recipient' => '',
		'sender' => '',
		'subject' => '',
		'body' => '',
		'additional_headers' => '',
		'attachments' => '',
		'use_html' => false,
		'exclude_blank' => false,
	) );

?>
<div class="contact-form-editor-box-mail" id="<?php echo $id; ?>">
<h2><?php echo esc_html( $args['title'] ); ?></h2>

<?php
	if ( ! empty( $args['use'] ) ) :
?>
<label for="<?php echo $id; ?>-active"><input type="checkbox" id="<?php echo $id; ?>-active" name="<?php echo $id; ?>[active]" class="toggle-form-table" value="1"<?php echo ( $mail['active'] ) ? ' checked="checked"' : ''; ?> /> <?php echo esc_html( $args['use'] ); ?></label>
<p class="description"><?php echo esc_html( __( "Mail (2) is an additional mail template often used as an autoresponder.", 'contact-form-7' ) ); ?></p>
<?php
	endif;
?>

<fieldset>
<legend><?php echo esc_html( __( "In the following fields, you can use these mail-tags:", 'contact-form-7' ) ); ?><br />
<?php $post->suggest_mail_tags( $args['name'] ); ?></legend>
<table class="form-table">
<tbody>
	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-recipient"><?php echo esc_html( __( 'To', 'contact-form-7' ) ); ?></label>
	</th>
	<td>
		<input type="text" id="<?php echo $id; ?>-recipient" name="<?php echo $id; ?>[recipient]" class="large-text code" size="70" value="<?php echo esc_attr( $mail['recipient'] ); ?>" data-config-field="<?php echo sprintf( '%s.recipient', esc_attr( $args['name'] ) ); ?>" />
	</td>
	</tr>

	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-sender"><?php echo esc_html( __( 'From', 'contact-form-7' ) ); ?></label>
	</th>
	<td>
		<input type="text" id="<?php echo $id; ?>-sender" name="<?php echo $id; ?>[sender]" class="large-text code" size="70" value="<?php echo esc_attr( $mail['sender'] ); ?>" data-config-field="<?php echo sprintf( '%s.sender', esc_attr( $args['name'] ) ); ?>" />
	</td>
	</tr>

	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-subject"><?php echo esc_html( __( 'Subject', 'contact-form-7' ) ); ?></label>
	</th>
	<td>
		<input type="text" id="<?php echo $id; ?>-subject" name="<?php echo $id; ?>[subject]" class="large-text code" size="70" value="<?php echo esc_attr( $mail['subject'] ); ?>" data-config-field="<?php echo sprintf( '%s.subject', esc_attr( $args['name'] ) ); ?>" />
	</td>
	</tr>

	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-additional-headers"><?php echo esc_html( __( 'Additional Headers', 'contact-form-7' ) ); ?></label>
	</th>
	<td>
		<textarea id="<?php echo $id; ?>-additional-headers" name="<?php echo $id; ?>[additional_headers]" cols="100" rows="4" class="large-text code" data-config-field="<?php echo sprintf( '%s.additional_headers', esc_attr( $args['name'] ) ); ?>"><?php echo esc_textarea( $mail['additional_headers'] ); ?></textarea>
	</td>
	</tr>

	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-body"><?php echo esc_html( __( 'Message Body', 'contact-form-7' ) ); ?></label>
	</th>
	<td>
		<textarea id="<?php echo $id; ?>-body" name="<?php echo $id; ?>[body]" cols="100" rows="18" class="large-text code" data-config-field="<?php echo sprintf( '%s.body', esc_attr( $args['name'] ) ); ?>"><?php echo esc_textarea( $mail['body'] ); ?></textarea>

		<p><label for="<?php echo $id; ?>-exclude-blank"><input type="checkbox" id="<?php echo $id; ?>-exclude-blank" name="<?php echo $id; ?>[exclude_blank]" value="1"<?php echo ( ! empty( $mail['exclude_blank'] ) ) ? ' checked="checked"' : ''; ?> /> <?php echo esc_html( __( 'Exclude lines with blank mail-tags from output', 'contact-form-7' ) ); ?></label></p>

		<p><label for="<?php echo $id; ?>-use-html"><input type="checkbox" id="<?php echo $id; ?>-use-html" name="<?php echo $id; ?>[use_html]" value="1"<?php echo ( $mail['use_html'] ) ? ' checked="checked"' : ''; ?> /> <?php echo esc_html( __( 'Use HTML content type', 'contact-form-7' ) ); ?></label></p>
	</td>
	</tr>

	<tr>
	<th scope="row">
		<label for="<?php echo $id; ?>-attachments"><?php echo esc_html( __( 'File Attachments', 'contact-form-7' ) ); ?></label>
	</th>
	<td>
		<textarea id="<?php echo $id; ?>-attachments" name="<?php echo $id; ?>[attachments]" cols="100" rows="4" class="large-text code" data-config-field="<?php echo sprintf( '%s.attachments', esc_attr( $args['name'] ) ); ?>"><?php echo esc_textarea( $mail['attachments'] ); ?></textarea>
	</td>
	</tr>
</tbody>
</table>
</fieldset>
</div>
<?php
}

function pcf_editor_panel_messages( $post ) {
	$messages = pcf_messages();

	if ( isset( $messages['captcha_not_match'] )
	&& ! pcf_use_really_simple_captcha() ) {
		unset( $messages['captcha_not_match'] );
	}

?>
<h2><?php echo esc_html( __( 'Messages', 'contact-form-7' ) ); ?></h2>
<fieldset>
<legend><?php echo esc_html( __( 'Edit messages used in the following situations.', 'contact-form-7' ) ); ?></legend>
<?php

	foreach ( $messages as $key => $arr ) {
		$field_id = sprintf( 'pcf-message-%s', strtr( $key, '_', '-' ) );
		$field_name = sprintf( 'pcf-messages[%s]', $key );

?>
<p class="description">
<label for="<?php echo $field_id; ?>"><?php echo esc_html( $arr['description'] ); ?><br />
<input type="text" id="<?php echo $field_id; ?>" name="<?php echo $field_name; ?>" class="large-text" size="70" value="<?php echo esc_attr( $post->message( $key, false ) ); ?>" data-config-field="<?php echo sprintf( 'messages.%s', esc_attr( $key ) ); ?>" />
</label>
</p>
<?php
	}
?>
</fieldset>
<?php
}

function pcf_editor_panel_additional_settings( $post ) {
	$desc_link = pcf_link(
		__( 'https://contactform7.com/additional-settings/', 'contact-form-7' ),
		__( 'Additional Settings', 'contact-form-7' ) );
	$description = __( "You can add customization code snippets here. For details, see %s.", 'contact-form-7' );
	$description = sprintf( esc_html( $description ), $desc_link );

?>
<h2><?php echo esc_html( __( 'Additional Settings', 'contact-form-7' ) ); ?></h2>
<fieldset>
<legend><?php echo $description; ?></legend>
<textarea id="pcf-additional-settings" name="pcf-additional-settings" cols="100" rows="8" class="large-text" data-config-field="additional_settings.body"><?php echo esc_textarea( $post->prop( 'additional_settings' ) ); ?></textarea>
</fieldset>
<?php
}
