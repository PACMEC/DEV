<?php
/**
 * Credits administration panel.
 *
 * @package PACMEC
 * @subpackage Administration
 */

/** PACMEC Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

$title = __( 'Credits' );

include( ABSPATH . 'wp-admin/admin-header.php' );
?>
<div class="wrap about-wrap full-width-layout">

<h1><?php _e( 'Welcome to PACMEC!' ); ?></h1>

<p class="about-text">
	<?php printf( __( 'Version %s' ), pacmec_version() ); ?>
	<?php pacmec_dev_version_info(); ?>
</p>
<p class="about-text">
	<?php printf(
		/* translators: link to "business-focused CMS" article */
		__( 'Thank you for using PACMEC, the <a href="%s">CMS for Creators</a>.' ),
		'https://link.pacmec.com.co/the-cms-for-creators'
	); ?>
	<br>
	<?php _e( 'Stable. Lightweight. Instantly Familiar.' ); ?>
</p>

<div class="wp-badge"></div>

<h2 class="nav-tab-wrapper wp-clearfix">
	<a href="about.php" class="nav-tab"><?php _e( 'About' ); ?></a>
	<a href="credits.php" class="nav-tab nav-tab-active"><?php _e( 'Credits' ); ?></a>
	<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
	<a href="freedoms.php?privacy-notice" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
</h2>

<div class="about-wrap-content">
<?php

echo '<p class="about-description">' . sprintf(
	/* translators: %s: https://www.pacmec.com.co/contributors/ */
	__( 'PACMEC is created by a <a href="%1$s">worldwide team</a> of passionate individuals.' ),
	'https://www.pacmec.com.co/contributors/'
) . '</p>';

echo '<p class="about-description">' . sprintf(
	/* translators: %s: https://www.pacmec.com.co/get-involved/ */
	__( 'Interested in helping out with development? <a href="%s">Get involved in PACMEC</a>.' ),
	'https://www.pacmec.com.co/get-involved/'
) . '</p>';

?>
</div>
</div>
<?php

include( ABSPATH . 'wp-admin/admin-footer.php' );

return;
