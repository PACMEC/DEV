<?php
/**
 * Class WC_Email_Customer_Note file.
 *
 * @package ClassicCommerce\Emails
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Email_Customer_Note', false ) ) :

	/**
	 * Customer Note Order Email.
	 *
	 * Customer note emails are sent when you add a note to an order.
	 *
	 * @class       WC_Email_Customer_Note
	 * @version     WC-3.5.0
	 * @package     ClassicCommerce/Classes/Emails
	 * @extends     WC_Email
	 */
	class WC_Email_Customer_Note extends WC_Email {

		/**
		 * Customer note.
		 *
		 * @var string
		 */
		public $customer_note;

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'customer_note';
			$this->customer_email = true;
			$this->title          = __( 'Customer note', 'classic-commerce' );
			$this->description    = __( 'Customer note emails are sent when you add a note to an order.', 'classic-commerce' );
			$this->template_html  = 'emails/customer-note.php';
			$this->template_plain = 'emails/plain/customer-note.php';
			$this->placeholders   = array(
				'{site_title}'   => $this->get_blogname(),
				'{order_date}'   => '',
				'{order_number}' => '',
			);

			// Triggers.
			add_action( 'woocommerce_new_customer_note_notification', array( $this, 'trigger' ) );

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Get email subject.
		 *
		 * @since  WC-3.1.0
		 * @return string
		 */
		public function get_default_subject() {
			return __( 'Note added to your {site_title} order from {order_date}', 'classic-commerce' );
		}

		/**
		 * Get email heading.
		 *
		 * @since  WC-3.1.0
		 * @return string
		 */
		public function get_default_heading() {
			return __( 'A note has been added to your order', 'classic-commerce' );
		}

		/**
		 * Trigger.
		 *
		 * @param array $args Email arguments.
		 */
		public function trigger( $args ) {
			$this->setup_locale();

			if ( ! empty( $args ) ) {
				$defaults = array(
					'order_id'      => '',
					'customer_note' => '',
				);

				$args = wp_parse_args( $args, $defaults );

				$order_id      = $args['order_id'];
				$customer_note = $args['customer_note'];

				if ( $order_id ) {
					$this->object = wc_get_order( $order_id );

					if ( $this->object ) {
						$this->recipient                      = $this->object->get_billing_email();
						$this->customer_note                  = $customer_note;
						$this->placeholders['{order_date}']   = wc_format_datetime( $this->object->get_date_created() );
						$this->placeholders['{order_number}'] = $this->object->get_order_number();
					}
				}
			}

			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}

		/**
		 * Get content html.
		 *
		 * @return string
		 */
		public function get_content_html() {
			return wc_get_template_html(
				$this->template_html, array(
					'order'         => $this->object,
					'email_heading' => $this->get_heading(),
					'customer_note' => $this->customer_note,
					'sent_to_admin' => false,
					'plain_text'    => false,
					'email'         => $this,
				)
			);
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			return wc_get_template_html(
				$this->template_plain, array(
					'order'         => $this->object,
					'email_heading' => $this->get_heading(),
					'customer_note' => $this->customer_note,
					'sent_to_admin' => false,
					'plain_text'    => true,
					'email'         => $this,
				)
			);
		}
	}

endif;

return new WC_Email_Customer_Note();
