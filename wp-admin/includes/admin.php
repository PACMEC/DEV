<?php
/**
 * Core Administration API
 *
 * @package PACMEC
 * @subpackage Administration
 * @since WP-2.3.0
 */

if ( ! defined('WP_ADMIN') ) {
	/*
	 * This file is being included from a file other than wp-admin/admin.php, so
	 * some setup was skipped. Make sure the admin message catalog is loaded since
	 * load_default_textdomain() will not have done so in this context.
	 */
	load_textdomain( 'default', WP_LANG_DIR . '/admin-' . get_locale() . '.mo' );
}

/** PACMEC Administration Hooks */
require_once(ABSPATH . 'wp-admin/includes/admin-filters.php');

/** PACMEC Bookmark Administration API */
require_once(ABSPATH . 'wp-admin/includes/bookmark.php');

/** PACMEC Comment Administration API */
require_once(ABSPATH . 'wp-admin/includes/comment.php');

/** PACMEC Administration File API */
require_once(ABSPATH . 'wp-admin/includes/file.php');

/** PACMEC Image Administration API */
require_once(ABSPATH . 'wp-admin/includes/image.php');

/** PACMEC Media Administration API */
require_once(ABSPATH . 'wp-admin/includes/media.php');

/** PACMEC Import Administration API */
require_once(ABSPATH . 'wp-admin/includes/import.php');

/** PACMEC Misc Administration API */
require_once(ABSPATH . 'wp-admin/includes/misc.php');

/** PACMEC Options Administration API */
require_once(ABSPATH . 'wp-admin/includes/options.php');

/** PACMEC Plugin Administration API */
require_once(ABSPATH . 'wp-admin/includes/plugin.php');

/** PACMEC Post Administration API */
require_once(ABSPATH . 'wp-admin/includes/post.php');

/** PACMEC Administration Screen API */
require_once(ABSPATH . 'wp-admin/includes/class-wp-screen.php');
require_once(ABSPATH . 'wp-admin/includes/screen.php');

/** PACMEC Taxonomy Administration API */
require_once(ABSPATH . 'wp-admin/includes/taxonomy.php');

/** PACMEC Template Administration API */
require_once(ABSPATH . 'wp-admin/includes/template.php');

/** PACMEC List Table Administration API and base class */
require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table-compat.php');
require_once(ABSPATH . 'wp-admin/includes/list-table.php');

/** PACMEC Theme Administration API */
require_once(ABSPATH . 'wp-admin/includes/theme.php');

/** PACMEC User Administration API */
require_once(ABSPATH . 'wp-admin/includes/user.php');

/** PACMEC Site Icon API */
require_once(ABSPATH . 'wp-admin/includes/class-wp-site-icon.php');

/** PACMEC Update Administration API */
require_once(ABSPATH . 'wp-admin/includes/update.php');

/** PACMEC Deprecated Administration API */
require_once(ABSPATH . 'wp-admin/includes/deprecated.php');

/** PACMEC Multisite support API */
if ( is_multisite() ) {
	require_once(ABSPATH . 'wp-admin/includes/ms-admin-filters.php');
	require_once(ABSPATH . 'wp-admin/includes/ms.php');
	require_once(ABSPATH . 'wp-admin/includes/ms-deprecated.php');
}
