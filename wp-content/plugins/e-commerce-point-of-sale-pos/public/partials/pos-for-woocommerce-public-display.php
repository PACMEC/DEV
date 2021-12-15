<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    MWB_Point_Of_Sale_Woocommerce
 * @subpackage MWB_Point_Of_Sale_Woocommerce/public/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit(); //phpcs:disabled
}

global $wp_query;
$mwb_pos_includesurl = site_url( '/' . WPINC . '/', null );
$mwb_pos_includesurl = $mwb_pos_includesurl . 'js/jquery/jquery.min.js';
?>
<head>	
	<?php wp_head(); ?>
</head>
<body>
	<?php
	$mwb_pos_config = array();
	$mwb_pos_config = array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'imgUrl' => trailingslashit( POS_FOR_WOOCOMMERCE_DIR_URL ),
	);
	if ( isset( $wp_query->query['pagename'] ) && 'point-of-sale' === $wp_query->query['pagename'] ) {
		?>
		<div id="mwb-pos-root" mwb-config="<?php echo esc_attr( wp_json_encode( $mwb_pos_config ) ); ?>"></div>
		<input type="hidden" id="mwb-secure-nonce" value="<?php echo esc_attr( wp_create_nonce( 'mwb-pos-operarions' ) ); ?>"/>
		<?php
	}
	?>
</body>
	<?php wp_footer(); ?>
</html>

