<?php
/**
** A base module for [select] and [select*]
**/

/* form_tag handler */

add_action( 'pcf_init', 'pcf_add_form_tag_select' );

function pcf_add_form_tag_select() {
	pcf_add_form_tag( array( 'select', 'select*' ),
		'pcf_select_form_tag_handler',
		array(
			'name-attr' => true,
			'selectable-values' => true,
		)
	);
}

function pcf_select_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = pcf_get_validation_error( $tag->name );

	$class = pcf_form_controls_class( $tag->type );

	if ( $validation_error ) {
		$class .= ' pcf-not-valid';
	}

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

	if ( $tag->is_required() ) {
		$atts['aria-required'] = 'true';
	}

	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

	$multiple = $tag->has_option( 'multiple' );
	$include_blank = $tag->has_option( 'include_blank' );
	$first_as_label = $tag->has_option( 'first_as_label' );

	if ( $tag->has_option( 'size' ) ) {
		$size = $tag->get_option( 'size', 'int', true );

		if ( $size ) {
			$atts['size'] = $size;
		} elseif ( $multiple ) {
			$atts['size'] = 4;
		} else {
			$atts['size'] = 1;
		}
	}

	$values = $tag->values;
	$labels = $tag->labels;

	if ( $data = (array) $tag->get_data_option() ) {
		$values = array_merge( $values, array_values( $data ) );
		$labels = array_merge( $labels, array_values( $data ) );
	}

	$defaults = array();

	$default_choice = $tag->get_default_option( null, 'multiple=1' );

	foreach ( $default_choice as $value ) {
		$key = array_search( $value, $values, true );

		if ( false !== $key ) {
			$defaults[] = (int) $key + 1;
		}
	}

	if ( $matches = $tag->get_first_match_option( '/^default:([0-9_]+)$/' ) ) {
		$defaults = array_merge( $defaults, explode( '_', $matches[1] ) );
	}

	$defaults = array_unique( $defaults );

	$shifted = false;

	if ( $include_blank || empty( $values ) ) {
		array_unshift( $labels, '---' );
		array_unshift( $values, '' );
		$shifted = true;
	} elseif ( $first_as_label ) {
		$values[0] = '';
	}

	$html = '';
	$hangover = pcf_get_hangover( $tag->name );

	foreach ( $values as $key => $value ) {
		$selected = false;

		if ( $hangover ) {
			if ( $multiple ) {
				$selected = in_array( esc_sql( $value ), (array) $hangover );
			} else {
				$selected = ( $hangover == esc_sql( $value ) );
			}
		} else {
			if ( ! $shifted && in_array( (int) $key + 1, (array) $defaults ) ) {
				$selected = true;
			} elseif ( $shifted && in_array( (int) $key, (array) $defaults ) ) {
				$selected = true;
			}
		}

		$item_atts = array(
			'value' => $value,
			'selected' => $selected ? 'selected' : '',
		);

		$item_atts = pcf_format_atts( $item_atts );

		$label = isset( $labels[$key] ) ? $labels[$key] : $value;

		$html .= sprintf( '<option %1$s>%2$s</option>',
			$item_atts, esc_html( $label ) );
	}

	if ( $multiple ) {
		$atts['multiple'] = 'multiple';
	}

	$atts['name'] = $tag->name . ( $multiple ? '[]' : '' );

	$atts = pcf_format_atts( $atts );

	$html = sprintf(
		'<span class="pcf-form-control-wrap %1$s"><select %2$s>%3$s</select>%4$s</span>',
		sanitize_html_class( $tag->name ), $atts, $html, $validation_error );

	return $html;
}


/* Validation filter */

add_filter( 'pcf_validate_select', 'pcf_select_validation_filter', 10, 2 );
add_filter( 'pcf_validate_select*', 'pcf_select_validation_filter', 10, 2 );

function pcf_select_validation_filter( $result, $tag ) {
	$name = $tag->name;

	if ( isset( $_POST[$name] ) && is_array( $_POST[$name] ) ) {
		foreach ( $_POST[$name] as $key => $value ) {
			if ( '' === $value ) {
				unset( $_POST[$name][$key] );
			}
		}
	}

	$empty = ! isset( $_POST[$name] ) || empty( $_POST[$name] ) && '0' !== $_POST[$name];

	if ( $tag->is_required() && $empty ) {
		$result->invalidate( $tag, pcf_get_message( 'invalid_required' ) );
	}

	return $result;
}


/* Tag generator */

add_action( 'pcf_admin_init', 'pcf_add_tag_generator_menu', 25 );

function pcf_add_tag_generator_menu() {
	$tag_generator = PCF_TagGenerator::get_instance();
	$tag_generator->add( 'menu', __( 'drop-down menu', 'contact-form-7' ),
		'pcf_tag_generator_menu' );
}

function pcf_tag_generator_menu( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );

	$description = __( "Generate a form-tag for a drop-down menu. For more details, see %s.", 'contact-form-7' );

	$desc_link = pcf_link( __( 'https://contactform7.com/checkboxes-radio-buttons-and-menus/', 'contact-form-7' ), __( 'Checkboxes, Radio Buttons and Menus', 'contact-form-7' ) );

?>
<div class="control-box">
<fieldset>
<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

<table class="form-table">
<tbody>
	<tr>
	<th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
		<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
		</fieldset>
	</td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
	<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><?php echo esc_html( __( 'Options', 'contact-form-7' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Options', 'contact-form-7' ) ); ?></legend>
		<textarea name="values" class="values" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>"></textarea>
		<label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><span class="description"><?php echo esc_html( __( "One option per line.", 'contact-form-7' ) ); ?></span></label><br />
		<label><input type="checkbox" name="multiple" class="option" /> <?php echo esc_html( __( 'Allow multiple selections', 'contact-form-7' ) ); ?></label><br />
		<label><input type="checkbox" name="include_blank" class="option" /> <?php echo esc_html( __( 'Insert a blank item as the first option', 'contact-form-7' ) ); ?></label>
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
	<input type="text" name="select" class="tag code" readonly="readonly" onfocus="this.select()" />

	<div class="submitbox">
	<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
	</div>

	<br class="clear" />

	<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
</div>
<?php
}
