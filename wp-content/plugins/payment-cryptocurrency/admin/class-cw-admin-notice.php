<?php
/**
 * CryptoWoo Admin Notice class
 *
 * @package CryptoWoo
 */

/**
 * Class for displaying admin notices.
 *
 * @package    CryptoWoo
 * @subpackage Admin
 */
class CW_Admin_Notice {

	/**
	 *
	 * The id (required for dismissible)
	 *
	 * @var string
	 */
	private $id;
	/**
	 *
	 * Array of messages to be printed in the notice
	 *
	 * @var array
	 */
	private $messages;
	/**
	 *
	 * The class(es) to use for the output
	 *
	 * @var string
	 */
	private $class;
	/**
	 *
	 * If the notice can be dismissed or not
	 *
	 * @var bool
	 */
	private $is_dismissible;
	/**
	 *
	 * Extra buttons in the notice.
	 *
	 * @var string
	 */
	private $extra_buttons;

	const NOTICE_ERROR   = 'notice-error';
	const NOTICE_WARNING = 'notice-warning';
	const NOTICE_SUCCESS = 'notice-success';
	const NOTICE_INFO    = 'notice-info';

	/**
	 * CW_Admin_Notice generator.
	 *
	 * @param string $class The class to use (for example 'notice_error'). Use constants.
	 *
	 * @return CW_Admin_Notice
	 */
	public static function generate( $class ) {
		return new self( $class );
	}

	/**
	 * CW_Admin_Notice constructor.
	 *
	 * @param string $class The class to use (for example 'notice_error').
	 */
	private function __construct( $class ) {
		$this->class          = "notice $class";
		$this->is_dismissible = false;
		$this->extra_buttons  = '';
	}

	/**
	 * Make the notice dismissible.
	 *
	 * @param string $id The notice id/name, f.ex 'license_inactive'.
	 *
	 * @return CW_Admin_Notice
	 */
	public function make_dismissible( $id ) {
		$this->id             = $id;
		$this->is_dismissible = true;
		return $this;
	}

	/**
	 * Make the notice dismissible.
	 *
	 * @param string $message A message to add.
	 *
	 * @return CW_Admin_Notice
	 */
	public function add_message( $message ) {
		$this->messages[] = $message;
		return $this;
	}

	/**
	 * Add extra button to the notice linking to a page
	 *
	 * @param string $label       Label for the button (button text).
	 * @param string $hint        The hint for the button (on mouse hover text).
	 * @param string $target_page The target page when clicking the button.
	 *
	 * @return CW_Admin_Notice
	 */
	public function add_button_menu_link( $label, $hint, $target_page ) {
		// Only add button if menu link exists.
		if ( empty( $GLOBALS['admin_page_hooks'][ $target_page ] ) ) {
			return $this;
		}

		return $this->add_button( $label, $hint, 'admin.php?page=', $target_page, "link-page_$target_page" );
	}

	/**
	 * Add extra button to the notice for activation of a plugin
	 *
	 * @param string $label     Label for the button (button text).
	 * @param string $hint      The hint for the button (on mouse hover text).
	 * @param string $dir_name  The directory name of the plugin to activate when clicking the button.
	 * @param string $file_name The name of the plugin to activate when clicking the button.
	 *
	 * @return CW_Admin_Notice
	 */
	public function add_button_plugin_activate( $label, $hint, $dir_name, $file_name = null ) {
		if ( ! isset( $file_name ) ) {
			$file_name = $dir_name;
		}
		$target = "$dir_name/$file_name.php";

		// Do not add button if plugin is already active.
		if ( is_plugin_active( $target ) ) {
			return $this;
		}

		return $this->add_button( $label, $hint, 'plugins.php?action=activate&plugin=', $target, "activate-plugin_$target" );
	}

	/**
	 * Add extra button to the notice
	 *
	 * @param string $label       Label for the button (button text).
	 * @param string $hint        The hint for the button (on mouse hover text).
	 * @param string $target_base The target base url when clicking the button (e.g. 'admin.php?page=').
	 * @param string $target      The target when clicking the button (e.g. 'cryptowoo').
	 * @param string $nonce       The nonce to add to the url.
	 *
	 * @return CW_Admin_Notice
	 */
	public function add_button( $label, $hint, $target_base, $target, $nonce ) {
		$path = wp_nonce_url( admin_url( $target_base . rawurlencode( $target ) ), $nonce );

		return $this->add_button_with_full_path_url( $label, $hint, $path );
	}

	/**
	 * Add extra button with url to the notice
	 *
	 * @param string $label      Label for the button (button text).
	 * @param string $hint       The hint for the button (on mouse hover text).
	 * @param string $target_url The target website url when clicking the button (e.g. 'cryptowoo.com').
	 *
	 * @return CW_Admin_Notice
	 */
	public function add_button_with_full_path_url( $label, $hint, $target_url ) {
		$this->extra_buttons .= '<p><a class="button" href="' . $target_url . '" title="' . esc_attr( $hint ) . '" target="_parent">' . esc_html( $label ) . '</a></p>';

		return $this;
	}

	/**
	 * For printing the admin notice in addons for backwards compatibility
	 *
	 * @param string $name      Method name.
	 * @param array  $arguments Arguments.
	 *
	 * @throws Exception Throws Exception if the method is not 'print'.
	 * @deprecated Kept for backwards compatibility of older versions of addons, use print_notice instead of print
	 */
	public function __call( $name, $arguments ) {
		if ( 'print' === $name ) {
			$this->print_notice();
		} else {
			throw new Exception( 'Method "' . $name . '" does not exist.' );
		}
	}

	/**
	 * Print the admin notice.
	 */
	public function print_notice() {

		if ( 'dismissed' === get_option( 'cryptowoo_' . $this->id . '_notice' ) ) {
			return;
		}

		$message = '';

		foreach ( $this->messages as $msg ) {
			// wp_strip_all_tags removes php and html tags, so with this we can check if $msg is html or not.
			if ( wp_strip_all_tags( $msg ) === $msg ) {
				// Print as an html escaped string with paragraph tags.
				$message .= '<p>' . esc_html( $msg ) . '</p>';
			} else {
				// Print as sanitized content with HTML tags.
				$message .= wp_kses_post( $msg );
			}
		}

		echo wp_kses_post( sprintf( '<div class="%1$s">%2$s', esc_attr( $this->class ), $message ) );

		if ( $this->extra_buttons ) {
			echo wp_kses_post( $this->extra_buttons );
		}

		if ( $this->is_dismissible ) {
			$this->print_dismissible();
		}

		echo '</div>';
	}

	/**
	 *  Print dismissible button if not already dismissed
	 */
	private function print_dismissible() {
		$option_name = 'dismiss_' . $this->id . '_notice';

		if ( isset( $_POST[ $option_name ] ) ) {
			update_option( 'cryptowoo_' . $this->id . '_notice', 'dismissed' );
		} else {
			?>
			<p>
			<form id="<?php esc_attr( $option_name ); ?>" action="" method="post">
				<fieldset>
					<input type="hidden" name="<?php esc_attr( $option_name ); ?>" value="<?php esc_attr( $option_name ); ?>"/>
					<input id="<?php esc_attr( $option_name ); ?>" type="submit" name="submit" class="button"
						value="<?php esc_html_e( 'Dismiss', 'cryptowoo' ); ?>" onClick=""/>
				</fieldset>
			</form>
			</p>
			<?php
		}
	}

}
