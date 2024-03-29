<?php
/**
** A base module for [quiz]
**/

/* form_tag handler */

add_action( 'pcf_init', 'pcf_add_form_tag_quiz' );

function pcf_add_form_tag_quiz() {
	pcf_add_form_tag( 'quiz',
		'pcf_quiz_form_tag_handler',
		array(
			'name-attr' => true,
			'do-not-store' => true,
		)
	);
}

function pcf_quiz_form_tag_handler( $tag ) {
	if ( empty( $tag->name ) ) {
		return '';
	}

	$validation_error = pcf_get_validation_error( $tag->name );

	$class = pcf_form_controls_class( $tag->type );

	if ( $validation_error ) {
		$class .= ' pcf-not-valid';
	}

	$atts = array();

	$atts['size'] = $tag->get_size_option( '40' );
	$atts['maxlength'] = $tag->get_maxlength_option();
	$atts['minlength'] = $tag->get_minlength_option();

	if ( $atts['maxlength'] && $atts['minlength'] && $atts['maxlength'] < $atts['minlength'] ) {
		unset( $atts['maxlength'], $atts['minlength'] );
	}

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );
	$atts['autocomplete'] = 'off';
	$atts['aria-required'] = 'true';
	$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

	$pipes = $tag->pipes;

	if ( $pipes instanceof PCF_Pipes && ! $pipes->zero() ) {
		$pipe = $pipes->random_pipe();
		$question = $pipe->before;
		$answer = $pipe->after;
	} else {
		// default quiz
		$question = '1+1=?';
		$answer = '2';
	}

	$answer = pcf_canonicalize( $answer );

	$atts['type'] = 'text';
	$atts['name'] = $tag->name;

	$atts = pcf_format_atts( $atts );

	$html = sprintf(
		'<span class="pcf-form-control-wrap %1$s"><label><span class="pcf-quiz-label">%2$s</span> <input %3$s /></label><input type="hidden" name="_pcf_quiz_answer_%4$s" value="%5$s" />%6$s</span>',
		sanitize_html_class( $tag->name ),
		esc_html( $question ), $atts, $tag->name,
		wp_hash( $answer, 'pcf_quiz' ), $validation_error );

	return $html;
}


/* Validation filter */

add_filter( 'pcf_validate_quiz', 'pcf_quiz_validation_filter', 10, 2 );

function pcf_quiz_validation_filter( $result, $tag ) {
	$name = $tag->name;

	$answer = isset( $_POST[$name] ) ? pcf_canonicalize( $_POST[$name] ) : '';
	$answer = wp_unslash( $answer );

	$answer_hash = wp_hash( $answer, 'pcf_quiz' );

	$expected_hash = isset( $_POST['_pcf_quiz_answer_' . $name] )
		? (string) $_POST['_pcf_quiz_answer_' . $name]
		: '';

	if ( $answer_hash != $expected_hash ) {
		$result->invalidate( $tag, pcf_get_message( 'quiz_answer_not_correct' ) );
	}

	return $result;
}


/* Ajax echo filter */

add_filter( 'pcf_ajax_onload', 'pcf_quiz_ajax_refill' );
add_filter( 'pcf_ajax_json_echo', 'pcf_quiz_ajax_refill' );

function pcf_quiz_ajax_refill( $items ) {
	if ( ! is_array( $items ) ) {
		return $items;
	}

	$fes = pcf_scan_form_tags( array( 'type' => 'quiz' ) );

	if ( empty( $fes ) ) {
		return $items;
	}

	$refill = array();

	foreach ( $fes as $fe ) {
		$name = $fe['name'];
		$pipes = $fe['pipes'];

		if ( empty( $name ) ) {
			continue;
		}

		if ( $pipes instanceof PCF_Pipes && ! $pipes->zero() ) {
			$pipe = $pipes->random_pipe();
			$question = $pipe->before;
			$answer = $pipe->after;
		} else {
			// default quiz
			$question = '1+1=?';
			$answer = '2';
		}

		$answer = pcf_canonicalize( $answer );

		$refill[$name] = array( $question, wp_hash( $answer, 'pcf_quiz' ) );
	}

	if ( ! empty( $refill ) ) {
		$items['quiz'] = $refill;
	}

	return $items;
}


/* Messages */

add_filter( 'pcf_messages', 'pcf_quiz_messages' );

function pcf_quiz_messages( $messages ) {
	$messages = array_merge( $messages, array(
		'quiz_answer_not_correct' => array(
			'description' =>
				__( "Sender doesn't enter the correct answer to the quiz", 'contact-form-7' ),
			'default' =>
				__( "The answer to the quiz is incorrect.", 'contact-form-7' ),
		),
	) );

	return $messages;
}


/* Tag generator */

add_action( 'pcf_admin_init', 'pcf_add_tag_generator_quiz', 40 );

function pcf_add_tag_generator_quiz() {
	$tag_generator = PCF_TagGenerator::get_instance();
	$tag_generator->add( 'quiz', __( 'quiz', 'contact-form-7' ),
		'pcf_tag_generator_quiz' );
}

function pcf_tag_generator_quiz( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );
	$type = 'quiz';

	$description = __( "Generate a form-tag for a question-answer pair. For more details, see %s.", 'contact-form-7' );

	$desc_link = pcf_link( __( 'https://contactform7.com/quiz/', 'contact-form-7' ), __( 'Quiz', 'contact-form-7' ) );

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
	<th scope="row"><?php echo esc_html( __( 'Questions and answers', 'contact-form-7' ) ); ?></th>
	<td>
		<fieldset>
		<legend class="screen-reader-text"><?php echo esc_html( __( 'Questions and answers', 'contact-form-7' ) ); ?></legend>
		<textarea name="values" class="values" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>"></textarea><br />
		<label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><span class="description"><?php echo esc_html( __( "One pipe-separated question-answer pair (e.g. The capital of Brazil?|Rio) per line.", 'contact-form-7' ) ); ?></span></label>
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
