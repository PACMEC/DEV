<?php
/**
** A base module for [acceptance]
**/

/* form_tag handler */

add_action( 'pcf_init', 'pcf_add_form_tag_acceptance' );

function pcf_add_form_tag_acceptance() {
	pcf_add_form_tag( 'acceptance',
		'pcf_acceptance_form_tag_handler',
		array(
			'name-attr' => true,
			'do-not-store' => true,
		)
	);
}

function pcf_acceptance_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = pcf_get_validation_error( $tag->name );

	$class = pcf_form_controls_class( $tag->type );

	if ( $validation_error ) {
		$class .= ' pcf-not-valid';
	}

	if ( $tag->has_option( 'invert' ) ) {
		$class .= ' pcf-invert';
	}

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

	if ( $tag->has_option( 'default:on' ) ) {
		$atts['checked'] = 'checked';
	}

	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

	$atts['type'] = 'checkbox';
	$atts['name'] = $tag->name;
	$atts['value'] = '1';

	$atts = pcf_format_atts( $atts );

	$html = sprintf(
		'<span class="pcf-form-control-wrap %1$s"><input %2$s />%3$s</span>',
		sanitize_html_class( $tag->name ), $atts, $validation_error );

	return $html;
}


/* Validation filter */

add_filter( 'pcf_validate_acceptance', 'pcf_acceptance_validation_filter', 10, 2 );

function pcf_acceptance_validation_filter( $result, $tag ) {
	if ( ! pcf_acceptance_as_validation() ) {
		return $result;
	}

	$name = $tag->name;
	$value = ( ! empty( $_POST[$name] ) ? 1 : 0 );

	$invert = $tag->has_option( 'invert' );

	if ( $invert && $value || ! $invert && ! $value ) {
		$result->invalidate( $tag, pcf_get_message( 'accept_terms' ) );
	}

	return $result;
}


/* Acceptance filter */

add_filter( 'pcf_acceptance', 'pcf_acceptance_filter' );

function pcf_acceptance_filter( $accepted ) {
	if ( ! $accepted ) {
		return $accepted;
	}

	$fes = pcf_scan_form_tags( array( 'type' => 'acceptance' ) );

	foreach ( $fes as $fe ) {
		$name = $fe['name'];
		$options = (array) $fe['options'];

		if ( empty( $name ) ) {
			continue;
		}

		$value = ( ! empty( $_POST[$name] ) ? 1 : 0 );

		$invert = (bool) preg_grep( '%^invert$%', $options );

		if ( $invert && $value || ! $invert && ! $value ) {
			$accepted = false;
		}
	}

	return $accepted;
}

add_filter( 'pcf_form_class_attr', 'pcf_acceptance_form_class_attr' );

function pcf_acceptance_form_class_attr( $class ) {
	if ( pcf_acceptance_as_validation() ) {
		return $class . ' pcf-acceptance-as-validation';
	}

	return $class;
}

function pcf_acceptance_as_validation() {
	if ( ! $contact_form = pcf_get_current_contact_form() ) {
		return false;
	}

	return $contact_form->is_true( 'acceptance_as_validation' );
}


/* Tag generator */

add_action( 'pcf_admin_init', 'pcf_add_tag_generator_acceptance', 35 );

function pcf_add_tag_generator_acceptance() {
	$tag_generator = PCF_TagGenerator::get_instance();
	$tag_generator->add( 'acceptance', __( 'acceptance', 'contact-form-7' ),
		'pcf_tag_generator_acceptance' );
}

function pcf_tag_generator_acceptance( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = 'acceptance';

	$description = __( "Generate a form-tag for an acceptance checkbox. For more details, see %s.", 'contact-form-7' );

	$desc_link = pcf_link( __( 'https://contactform7.com/acceptance-checkbox/', 'contact-form-7' ), __( 'Acceptance Checkbox', 'contact-form-7' ) );

?>
<div class="control-box">
<fieldset>
<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

<table class="form-table">
<tbody>
	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><?php echo esc_html( __( 'Options', 'contact-form-7' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Options', 'contact-form-7' ) ); ?></legend>
		<label><input type="checkbox" name="default:on" class="option" /> <?php echo esc_html( __( 'Make this checkbox checked by default', 'contact-form-7' ) ); ?></label><br />
		<label><input type="checkbox" name="invert" class="option" /> <?php echo esc_html( __( 'Make this work inversely', 'contact-form-7' ) ); ?></label>
		</fieldset>
	</td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
	</tr>

</tbody>
</table>
</fieldset>
</div>

<div class="insert-box">
	<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

	<div class="submitbox">
	<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
	</div>
</div>
<?php
}
