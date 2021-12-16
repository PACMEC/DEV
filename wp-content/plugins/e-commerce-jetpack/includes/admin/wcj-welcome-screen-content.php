<?php

/**
 * Booster for WooCommerce - Welcome Screen Content
 *
 * @version 5.4.8
 * @author  Pluggabl LLC.
 */
?>
<div class="wcj-welcome-page">
	<div class="wcj-welcome-container">
		<div class="wcj-welcome-content-main">
			<div class="wcj-welcome-content-logo-main">
				<div class="wcj-welcome-content-logo">
				</div>
			</div>
			<div class="wcj-welcome-content-inner">
				<h3> <?php esc_html_e('Welcome to booster.', 'e-commerce-jetpack'); ?> </h3>
				<p> <?php esc_html_e('Thank you for choosing Booster - Supercharge your WooCommerce site with these awesome powerful features. More than 100 modules. All in one WooCommerce plugin.', 'e-commerce-jetpack'); ?> </p>
				<a href="<?php echo admin_url('admin.php?page=wc-settings&tab=jetpack'); ?>" class="wcj-buy-puls-btn"> <?php esc_html_e('Launch Booster Settings', 'e-commerce-jetpack'); ?> </a>
			</div>
		</div>
		<div class="wcj-welcome-content-main wcj-welcome-padding-top-0">
			<div class="wcj-welcome-content-inner">
				<div class="wcj-buy-puls-btn-main">
					<a target="_blank" href="https://booster.io/buy-booster/" class="wcj-buy-puls-btn"> <?php esc_html_e('Upgrade Booster to unlock this feature.', 'e-commerce-jetpack'); ?> </a>
				</div>
				<div class="wcj-welcome-content-inner wcj-buy-puls-content-row">
					<div class="wcj-buy-puls-content-col-4">
						<div class="wcj-badge">
							<img src="<?php echo wcj_plugin_url(); ?>/assets/images/30day-guarantee.png">
							<span class="wcj-badge-sp-cn"> <?php esc_html_e('30-Day Risk Free', 'e-commerce-jetpack'); ?> <br> <?php esc_html_e('Money Back Guarantee', 'e-commerce-jetpack'); ?> </span>
						</div>
					</div>
					<div class="wcj-buy-puls-content-col-4">
						<div class="wcj-badge">
							<img src="<?php echo wcj_plugin_url(); ?>/assets/images/wp-logo.svg">
							<span class="wcj-badge-sp-cn"><?php esc_html_e('400+ 5-Star', 'e-commerce-jetpack'); ?> <br> <?php esc_html_e('Reviews', 'e-commerce-jetpack'); ?></span>
						</div>
					</div>
					<div class="wcj-buy-puls-content-col-4">
						<div class="wcj-badge">
							<img src="<?php echo wcj_plugin_url(); ?>/assets/images/trust-icon.png">
							<span class="wcj-badge-sp-cn"><?php esc_html_e('Trusted by', 'e-commerce-jetpack'); ?> <br> <?php esc_html_e('100,000+', 'e-commerce-jetpack'); ?> <br> <?php esc_html_e('Websites', 'e-commerce-jetpack'); ?></span>
						</div>
					</div>
				</div>
			</div>
			<div class="wcj-welcome-content-inner wcj-welcome-padding-top-0">
				<div class="wcj-buy-puls-head">
					<h3> <?php esc_html_e('Tons of Customizations and Zero Coding.', 'e-commerce-jetpack'); ?> </h3>
					<p>
						<?php esc_html_e('Access more than one hundred easy-to-use modules to quickly add customized functionality to your WooCommerce business', 'e-commerce-jetpack'); ?>
						<strong><?php esc_html_e('- Without writing a line of code.', 'e-commerce-jetpack'); ?> </strong>
					</p>
				</div>
				<div class="wcj-welcome-content-inner wcj-buy-puls-content-row">
					<div class="wcj-buy-puls-content-col-6 wcj-feature">
						<div class="wcj-feature-img">
							<img src="<?php echo wcj_plugin_url(); ?>/assets/images/feature-pdf.png">
						</div>
						<div class="wcj-feature-text">
							<h4> <?php esc_html_e('PDF Invoicing and Packing Slips', 'e-commerce-jetpack'); ?> </h4>
							<p> <?php esc_html_e('Streamline your WooCommerce orders and paperwork, and deliver a seamless customer experience with the PDF Invoicing and Packing Slips module.', 'e-commerce-jetpack'); ?> </p>
						</div>
					</div>
					<div class="wcj-buy-puls-content-col-6 wcj-feature">
						<div class="wcj-feature-img">
							<img src="<?php echo wcj_plugin_url(); ?>/assets/images/feature-add-on.png">
						</div>
						<div class="wcj-feature-text">
							<h4> <?php esc_html_e('Product Addons', 'e-commerce-jetpack'); ?> </h4>
							<p> <?php esc_html_e('Create addons for your WooCommerce products like support service or special offers with the Product Addons Module.', 'e-commerce-jetpack'); ?> </p>
						</div>
					</div>
					<div class="wcj-buy-puls-content-col-6 wcj-feature">
						<div class="wcj-feature-img">
							<img src="<?php echo wcj_plugin_url(); ?>/assets/images/feature-input-field.png">
						</div>
						<div class="wcj-feature-text">
							<h4> <?php esc_html_e('Product Input Fields', 'e-commerce-jetpack'); ?> </h4>
							<p> <?php esc_html_e('Allow your customers to provide more details about their order with the Product Input Fields module. Super handy when selling customized products.', 'e-commerce-jetpack'); ?> </p>
						</div>
					</div>
					<div class="wcj-buy-puls-content-col-6 wcj-feature">
						<div class="wcj-feature-img">
							<img src="<?php echo wcj_plugin_url(); ?>/assets/images/feature-button-prices.png">
						</div>
						<div class="wcj-feature-text">
							<h4> <?php esc_html_e('Button and Price Labels', 'e-commerce-jetpack'); ?> </h4>
							<p> <?php esc_html_e('Add custom buttons and price labels to your products with this popular module. Set automatic price for products with an empty price field.', 'e-commerce-jetpack'); ?> </p>
						</div>
					</div>
					<div class="wcj-buy-puls-content-col-6 wcj-feature">
						<div class="wcj-feature-img">
							<img src="<?php echo wcj_plugin_url(); ?>/assets/images/feature-prices-currency.png">
						</div>
						<div class="wcj-feature-text">
							<h4> <?php esc_html_e('Prices and Currencies', 'e-commerce-jetpack'); ?> </h4>
							<p> <?php esc_html_e('Make it easy for customers around the globe to make purchases on your site by displaying their currency with the Prices and Currencies by Country module.', 'e-commerce-jetpack'); ?> </p>
						</div>
					</div>
					<div class="wcj-buy-puls-content-col-6 wcj-feature">
						<div class="wcj-feature-img">
							<img src="<?php echo wcj_plugin_url(); ?>/assets/images/feature-payment-getway.png">
						</div>
						<div class="wcj-feature-text">
							<h4> <?php esc_html_e('Payment Gateways', 'e-commerce-jetpack'); ?> </h4>
							<p> <?php esc_html_e('Set up multiple payment gateways based on currency, shipping method, country, or state.', 'e-commerce-jetpack'); ?> </p>
						</div>
					</div>
					<div class="wcj-buy-puls-content-col-6 wcj-feature">
						<div class="wcj-feature-img">
							<img src="<?php echo wcj_plugin_url(); ?>/assets/images/feature-cart-checkout.png">
						</div>
						<div class="wcj-feature-text">
							<h4> <?php esc_html_e('Cart and Checkout', 'e-commerce-jetpack'); ?> </h4>
							<p> <?php esc_html_e('Customize the shopping cart and checkout experience. Add coupons, additional fees, custom fields, and buttons with the Cart and Checkout modules.', 'e-commerce-jetpack'); ?> </p>
						</div>
					</div>
					<div class="wcj-buy-puls-content-col-6 wcj-feature">
						<div class="wcj-feature-img">
							<img src="<?php echo wcj_plugin_url(); ?>/assets/images/feature-emails-addtool.png">
						</div>
						<div class="wcj-feature-text">
							<h4> <?php esc_html_e('Emails & Additional Tools', 'e-commerce-jetpack'); ?> </h4>
							<p> <?php esc_html_e('Add custom emails, additional recipients, and verification for increased security. Explore miscellaneous reporting and customization tools for increased functionality.', 'e-commerce-jetpack'); ?> </p>
						</div>
					</div>
					<div class="wcj-buy-puls-btn-main">
						<a target="_blank" href="https://booster.io/category/features/" class="wcj-buy-puls-btn"> <?php esc_html_e('See All Features', 'e-commerce-jetpack'); ?> </a>
					</div>
				</div>
				<div id="subscribe-email" class="wcj-welcome-content-inner wcj-welcome-subscribe-email">
					<h3> <?php esc_html_e("Don't miss updates from us!", "e-commerce-jetpack"); ?> </h3>
					<form method="post" name="subscribe-email-form">
						<input class="form-control user_email" type="email" required="true" name="user_email" placeholder="Enter your email">
						<input class="subscribe-email-btn" type="button" name="submit_email_to_klaviyo" value="Submit">
					</form>
					<?php
					if (isset($_REQUEST['msg'])) {
						$subscribe_message 		= "";
						$subscribe_message_id 	= $_REQUEST['msg'];
						if ($subscribe_message_id == 1) {
							$subscribe_message = "Thank you for subscribing your email";
						} else if ($subscribe_message_id == 2) {
							$subscribe_message = "You have already subscribed your email";
						} else if ($subscribe_message_id == 3) {
							$subscribe_message = "Something went wrong with your subscription. Please after some time !";
						}
						echo '<p style="color: #f46c5e;">' . sprintf(__('%s', 'e-commerce-jetpack'), $subscribe_message) . '</p>';
					}
					?>
				</div>
				<div class="wcj-welcome-content-inner wcj-welcome-subscribe-email">
					<h3> <?php esc_html_e("Contact Us", "e-commerce-jetpack"); ?> </h3>
					<div class="wcj-support">
						<p><?php esc_html_e("Booster Plus customers get access to Premium Support and we respond within 24 business hours.", "e-commerce-jetpack"); ?></p>
						<a target="_blank" href="https://booster.io/my-account/booster-contact/"><?php esc_html_e("Booster Plus Premium Support", "e-commerce-jetpack"); ?></a>
					</div>
					<div class="wcj-support">
						<p><?php esc_html_e("Free users post on WordPress Plugin Support forum here. We check these threads twice every week Mon and Frid to respond.", "e-commerce-jetpack"); ?></p>
						<a target="_blank" href="https://wordpress.org/support/plugin/e-commerce-jetpack/"><?php esc_html_e("Booster Free Plugin Support", "e-commerce-jetpack"); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>