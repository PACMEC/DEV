<?php
/**
 * About This Version administration panel.
 *
 * @package PACMEC
 * @subpackage Administration
 */

/** PACMEC Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

wp_enqueue_script( 'underscore' );

/* translators: Page title of the About PACMEC page in the admin. */
$title = _x( 'About', 'page title' );

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
			<br />
			<?php _e( 'Stable. Lightweight. Instantly Familiar.' ); ?>
		</p>
		<div class="wp-badge"></div>

		<h2 class="nav-tab-wrapper wp-clearfix">
			<a href="about.php" class="nav-tab nav-tab-active"><?php _e( 'About' ); ?></a>
			<a href="credits.php" class="nav-tab"><?php _e( 'Credits' ); ?></a>
			<a href="freedoms.php" class="nav-tab"><?php _e( 'Freedoms' ); ?></a>
			<a href="freedoms.php?privacy-notice" class="nav-tab"><?php _e( 'Privacy' ); ?></a>
		</h2>

		<div class="changelog point-releases about-wrap-content">

			<?php if ( get_locale() !== 'en_US' ) { ?>
				<p class="about-inline-notice notice-warning">
					<?php printf(
						/* translators: link to learn more about translating PACMEC */
						__( 'Help us translate PACMEC into your language! <a href="%s">Learn more</a>.' ),
						'https://www.pacmec.com.co/translating-pacmec/'
					); ?>
				</p>
			<?php } ?>

			<h3><?php _e( 'About PACMEC' ); ?></h3>

			<p>
				<?php printf(
					/* translators: link to PACMEC site */
					__( '<a href="%s"><strong>PACMEC</strong></a> is a fork of the WordPress 4.9 branch, including the battle-tested and proven classic editor interface using TinyMCE.' ),
					'https://www.pacmec.com.co'
				); ?>
			</p>
			<p>
				<?php _e(
					'This has been a solid foundation for millions of sites for many years, and we believe it will also be an excellent foundation for the future.'
				); ?>
			</p>
			<h3><?php _e( 'Join our growing community' ); ?></h3>
			<p>
				<?php printf(
					/* translators: 1: link with instructions to join PACMEC Slack, 2: link to community forums */
					__( 'For general discussion about PACMEC, <a href="%1$s"><strong>join our Slack group</strong></a> or our <a href="%2$s"><strong>community forum</strong></a>.' ),
					'https://www.pacmec.com.co/join-slack/',
					'https://forums.pacmec.com.co/c/support'
				); ?>
			</p>
			<p>
				<?php printf(
					/* translators: link to PACMEC Petitions site for new features */
					__( 'Suggestions for improvements to future versions of PACMEC are welcome at <a href="%s"><strong>our petitions site</strong></a>.' ),
					'https://petitions.pacmec.com.co/'
				); ?>
			</p>
			<p>
				<?php printf(
					/* translators: 1: link to PACMEC FAQs page, 2: link to PACMEC support forum */
					__( 'If you need help with something else, please see our <a href="%1$s"><strong>FAQs page</strong></a>. If your question is not answered there, you can make a new post on our <a href="%2$s"><strong>support forum</strong></a>.' ),
					'https://docs.pacmec.com.co/faq-support/',
					'https://forums.pacmec.com.co/c/support/'
				); ?>
			</p>
			<p>
				<?php printf(
					/* translators: 1: link to PACMEC GitHub repository, 2: link to GitHub issues list */
					__( 'PACMEC is developed <a href="%1$s"><strong>on GitHub</strong></a>. For specific bug reports or technical suggestions, see the <a href="%1$s"><strong>issues list</strong></a> and add your report if it is not already present.' ),
					'https://github.com/PACMEC/PACMEC',
					'https://github.com/PACMEC/PACMEC/issues'
				); ?>
			</p>
			<h3><?php _e( 'PACMEC changelogs' ); ?></h3>
			<h4><?php printf(
				/* translators: current PACMEC version */
				__( 'PACMEC 1.0.1 - %s' ),
				pacmec_version()
			); ?></h4>
			<p>
				<?php printf(
					/* translators: link to PACMEC release announcements subforum */
					__( 'The changes and new features included in recent versions of PACMEC can be found in our <a href="%s"><strong>Release Announcements subforum</strong></a>.' ),
					'https://forums.pacmec.com.co/c/announcements/release-notes'
				);
				?>
			</p>
			<h4><?php _e( 'PACMEC 1.0.0' ); ?></h4>
			<p>
				<?php printf(
					/* translators: link to PACMEC 1.0.0 changelog */
					__( 'For a list of new features and other changes from WordPress 4.9.x, see the <a href="%s"><strong>PACMEC 1.0.0 (Aurora) release notes</strong></a>.' ),
					'https://forums.pacmec.com.co/t/pacmec-1-0-0-aurora-release-notes/910'
				);
				?>
			</p>
			<h3><?php _e( 'WordPress Maintenance and Security Releases' ); ?></h3>
			<p>
				<?php _e(
					'This version of PACMEC includes all changes from the following versions of WordPress:'
				); ?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>WordPress version %s</strong> addressed some security issues.' ),
					'4.9.18'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '4.9.18' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>WordPress version %s</strong> addressed some security issues.' ),
					'4.9.17'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '4.9.17' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>WordPress version %s</strong> addressed some security issues.' ),
					'4.9.16'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '4.9.16' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>WordPress version %s</strong> addressed some security issues.' ),
					'4.9.15'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '4.9.15' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>WordPress version %s</strong> addressed some security issues.' ),
					'4.9.14'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '4.9.14' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>WordPress version %s</strong> addressed some security issues.' ),
					'4.9.13'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '4.9.13' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>WordPress version %s</strong> addressed some security issues.' ),
					'4.9.12'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '4.9.12' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>WordPress version %s</strong> addressed some security issues.' ),
					'4.9.11'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '4.9.11' )
					)
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: %s: WordPress version number */
					__( '<strong>WordPress version %s</strong> addressed some security issues.' ),
					'4.9.10'
				);
				?>
				<?php
				printf(
					/* translators: %s: HelpHub URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					sprintf(
						/* translators: %s: WordPress version */
						esc_url( __( 'https://wordpress.org/support/wordpress-version/version-%s/' ) ),
						sanitize_title( '4.9.10' )
					)
				);
				?>
			</p>
			<p>
				<?php
				/* translators: %s: WordPress version number */
				printf( __( '<strong>WordPress version %s</strong> addressed some security issues.' ), '4.9.9' );
				?>
				<?php
				/* translators: %s: Codex URL */
				printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.9.9' );
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: 1: WordPress version number, 2: plural number of bugs. */
					_n(
						'<strong>WordPress version %1$s</strong> addressed %2$s bug.',
						'<strong>WordPress version %1$s</strong> addressed %2$s bugs.',
						46
					),
					'4.9.8',
					number_format_i18n( 46 )
				);
				?>
				<?php
				printf(
					/* translators: %s: Codex URL */
					__( 'For more information, see <a href="%s">the release notes</a>.' ),
					'https://codex.wordpress.org/Version_4.9.8'
				);
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: 1: WordPress version number, 2: plural number of bugs. */
					_n(
						'<strong>WordPress version %1$s</strong> addressed some security issues and fixed %2$s bug.',
						'<strong>WordPress version %1$s</strong> addressed some security issues and fixed %2$s bugs.',
						17
					),
					'4.9.7',
					number_format_i18n( 17 )
				);
				?>
				<?php
				/* translators: %s: Codex URL */
				printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.9.7' );
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: 1: WordPress version number, 2: plural number of bugs. */
					_n(
						'<strong>WordPress version %1$s</strong> addressed %2$s bug.',
						'<strong>WordPress version %1$s</strong> addressed %2$s bugs.',
						18
					),
					'4.9.6',
					number_format_i18n( 18 )
				);
				?>
				<?php
				/* translators: %s: Codex URL */
				printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.9.6' );
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: 1: WordPress version number, 2: plural number of bugs. */
					_n(
						'<strong>WordPress version %1$s</strong> addressed some security issues and fixed %2$s bug.',
						'<strong>WordPress version %1$s</strong> addressed some security issues and fixed %2$s bugs.',
						28
					),
					'4.9.5',
					number_format_i18n( 28 )
				);
				?>
				<?php
				/* translators: %s: Codex URL */
				printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.9.5' );
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: 1: WordPress version number, 2: plural number of bugs. */
					_n(
						'<strong>WordPress version %1$s</strong> addressed %2$s bug.',
						'<strong>WordPress version %1$s</strong> addressed %2$s bugs.',
						1
					),
					'4.9.4',
					number_format_i18n( 1 )
				);
				?>
				<?php
				/* translators: %s: Codex URL */
				printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.9.4' );
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: 1: WordPress version number, 2: plural number of bugs. */
					_n(
						'<strong>WordPress version %1$s</strong> addressed %2$s bug.',
						'<strong>WordPress version %1$s</strong> addressed %2$s bugs.',
						34
					),
					'4.9.3',
					number_format_i18n( 34 )
				);
				?>
				<?php
				/* translators: %s: Codex URL */
				printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.9.3' );
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: 1: WordPress version number, 2: plural number of bugs. */
					_n(
						'<strong>WordPress version %1$s</strong> addressed some security issues and fixed %2$s bug.',
						'<strong>WordPress version %1$s</strong> addressed some security issues and fixed %2$s bugs.',
						22
					),
					'4.9.2',
					number_format_i18n( 22 )
				);
				?>
				<?php
				/* translators: %s: Codex URL */
				printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.9.2' );
				?>
			</p>
			<p>
				<?php
				printf(
					/* translators: 1: WordPress version number, 2: plural number of bugs. */
					_n(
						'<strong>WordPress version %1$s</strong> addressed some security issues and fixed %2$s bug.',
						'<strong>WordPress version %1$s</strong> addressed some security issues and fixed %2$s bugs.',
						11
					),
					'4.9.1',
					number_format_i18n( 11 )
				);
				?>
				<?php
				/* translators: %s: Codex URL */
				printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://codex.wordpress.org/Version_4.9.1' );
				?>
			</p>
		</div>
	</div>
<?php

include( ABSPATH . 'wp-admin/admin-footer.php' );

// These are strings we may use to describe maintenance/security releases, where we aim for no new strings.
return;

__( 'Maintenance Release' );
__( 'Maintenance Releases' );

__( 'Security Release' );
__( 'Security Releases' );

__( 'Maintenance and Security Release' );
__( 'Maintenance and Security Releases' );

/* translators: %s: PACMEC version number */
__( '<strong>Version %s</strong> addressed one security issue.' );
/* translators: %s: PACMEC version number */
__( '<strong>Version %s</strong> addressed some security issues.' );

/* translators: 1: PACMEC version number, 2: plural number of bugs. */
_n_noop( '<strong>Version %1$s</strong> addressed %2$s bug.',
         '<strong>Version %1$s</strong> addressed %2$s bugs.' );

/* translators: 1: PACMEC version number, 2: plural number of bugs. Singular security issue. */
_n_noop( '<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed a security issue and fixed %2$s bugs.' );

/* translators: 1: PACMEC version number, 2: plural number of bugs. More than one security issue. */
_n_noop( '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bug.',
         '<strong>Version %1$s</strong> addressed some security issues and fixed %2$s bugs.' );

/* translators: %s: Codex URL */
__( 'For more information, see <a href="%s">the release notes</a>.' );
