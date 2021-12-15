<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the html field for general tab.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @package    MWB_Point_Of_Sale_Woocommerce
 * @subpackage MWB_Point_Of_Sale_Woocommerce/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!--  template file for admin settings. -->
<div class="pfw-section-wrap">
	<div class="mwb_pfw_table_wrapper mwb_pfw_overview-wrapper">
		<div class="pfw-overview__wrapper">
			<div class="pfw-overview__icons">
				<a href="https://makewebbetter.com/contact-us/">
					<img src="<?php echo esc_url( POS_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/dial.svg' ); ?>" alt="contact-us-img">
				</a>
				<a href="https://docs.makewebbetter.com/mwb-point-of-sale-for-woocommerce/?utm_source=MWB-POS-org&utm_medium=MWB-org-backend &utm_campaign=MWB-POS-doc">
					<img src="<?php echo esc_url( POS_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/doc.svg' ); ?>" alt="doc-img">
				</a>
			</div>
			<div class="pfw-overview__banner-img">
				<img src="<?php echo esc_url( POS_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/pos-overview-banner.png' ); ?>" alt="pfw-banner-img">
			</div>
			<div class="pfw-overview__content">
				<div class="pfw-overview__content-description">
					<h1><?php esc_html_e( 'What is MWB Point of Sale (POS) for WooCommerce?', 'mwb-point-of-sale-woocommerce' ); ?></h1>
					<p> <?php esc_html_e( 'MWB Point of Sale (POS) for WooCommerce is the missing link that can act as a tie-up knot for your online and physical stores. This plugin can help you easily distribute the workload, appoint multiple outlet/counter managers, etc. Therefore, offering complete plugin control over your store sales and customer orders, both online and offline.', 'mwb-point-of-sale-woocommerce' ); ?>
					</p>
					<div class="pfw-overview__features">
						<h2><?php esc_html_e( 'As a store owner, you can- ', 'mwb-point-of-sale-woocommerce' ); ?>
					</h2>
					<ul class="pfw-overview__features-list">
						<li><?php esc_html_e( 'Swiftly strategize your store’s inventory and plan tax imposition rates with ease', 'mwb-point-of-sale-woocommerce' ); ?></li>
						<li><?php esc_html_e( 'Effortlessly build an online register for your store transaction without even purchasing physical point-of-sale tools and services.', 'mwb-point-of-sale-woocommerce' ); ?></li>
						<li><?php esc_html_e( 'Manage orders and offer discounts/coupons at checkout.', 'mwb-point-of-sale-woocommerce' ); ?></li>
						<li><?php esc_html_e( 'Easily generate barcodes for every product, even assign a product-Id, and SKU.', 'mwb-point-of-sale-woocommerce' ); ?></li>
						<li><?php esc_html_e( 'You can easily customize your POS login page i.e. can edit tagline, can add a proper description, can change background color, etc', 'mwb-point-of-sale-woocommerce' ); ?></li>
					</ul>
					</div>
				</div>
				<div class="pfw-overview__keywords-wrap">
				<h2> <?php esc_html_e( 'The free plugin benefits include-', 'mwb-point-of-sale-woocommerce' ); ?></h2>
				<div class="pfw-overview__keywords">
					<div class="pfw-overview__keywords-item">
						<div class="pfw-overview__keywords-card">
							<div class="pfw-overview__keywords-text">

								<img src="<?php echo esc_url( POS_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/feature-1.jpg' ); ?>" alt="feature_one" width="100px">
								<h4 class="pfw-overview__keywords-heading"><?php esc_html_e( 'Product Search and Category Filter', 'mwb-point-of-sale-woocommerce' ); ?> </h4>
								<p class="pfw-overview__keywords-description">
									<?php esc_html_e( 'You can easily make use of the plugin’s outstanding category-based search to perform your billing effortlessly. You can also opt for different products as per your ease using the category filter, that filter can provide you the list of almost all product categories available at your store online.', 'mwb-point-of-sale-woocommerce' ); ?>
								</p>
							</div>
						</div>
					</div>
					<div class="pfw-overview__keywords-item">
						<div class="pfw-overview__keywords-card">
							<div class="pfw-overview__keywords-text">
								<img src="<?php echo esc_url( POS_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/feature-2.jpg' ); ?>" alt="feature_two" width="100px">
								<h4 class="pfw-overview__keywords-heading"><?php esc_html_e( 'User-Friendly Single Page Web-App on React JS', 'mwb-point-of-sale-woocommerce' ); ?></h4>
								<p class="pfw-overview__keywords-description">
									
									<?php esc_html_e( 'The Woocommerce POS system itself is built with React JS and is quite user-friendly, it offers automatic background refreshing single page web app. You need not switch between multiple tabs and refresh your page from time to time.', 'mwb-point-of-sale-woocommerce' ); ?>
								</p>
							</div>
						</div>
					</div>
					<div class="pfw-overview__keywords-item">
						<div class="pfw-overview__keywords-card">
							<div class="pfw-overview__keywords-text">
								<img src="<?php echo esc_url( POS_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/feature-3.jpg' ); ?>" alt="feature_three" width="100px">
								<h4 class="pfw-overview__keywords-heading"><?php esc_html_e( 'Effortless Inventory Management', 'mwb-point-of-sale-woocommerce' ); ?></h4>
								<p class="pfw-overview__keywords-description">
									<?php esc_html_e( 'Offers efficient inventory management that is seamless and does not require you to manually update order data. With a Point of Sale (POS) plugin, you can track the sales of a product, and you can also know when its time to upgrade your stock.', 'mwb-point-of-sale-woocommerce' ); ?>
								</p>
							</div>
						</div>
					</div>
					<div class="pfw-overview__keywords-item">
						<div class="pfw-overview__keywords-card">
							<div class="pfw-overview__keywords-text">
								<img src="<?php echo esc_url( POS_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/feature-4.jpg' ); ?>" alt="feature_four" width="100px">
								<h4 class="pfw-overview__keywords-heading"><?php esc_html_e( 'Seamless Order Synchronization', 'mwb-point-of-sale-woocommerce' ); ?></h4>
								<p class="pfw-overview__keywords-description">
									
									<?php esc_html_e( 'This plugin offers easy synchronization of all your orders in one place. You can access all your online as well offline orders and can easily schedule them for further shipping processings.', 'mwb-point-of-sale-woocommerce' ); ?>
 
								</p>
							</div>
						</div>
					</div>
					<div class="pfw-overview__keywords-item">
						<div class="pfw-overview__keywords-card">
							<div class="pfw-overview__keywords-text">
								<img src="<?php echo esc_url( POS_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/feature-5.jpg' ); ?>" alt="feature_five" width="100px">
								<h4 class="pfw-overview__keywords-heading"><?php esc_html_e( 'Outstanding Desktop and Email Order Notifications', 'mwb-point-of-sale-woocommerce' ); ?></h4>
								<p class="pfw-overview__keywords-description">
									<?php esc_html_e( 'You can easily set the desktop as well as email notifications for orders/bills generated using the POS system.', 'mwb-point-of-sale-woocommerce' ); ?>
								</p>
							</div>
						</div>
					</div>
					<div class="pfw-overview__keywords-item">
						<div class="pfw-overview__keywords-card">
							<div class="pfw-overview__keywords-text">
								<img src="<?php echo esc_url( POS_FOR_WOOCOMMERCE_DIR_URL . 'admin/src/images/feature-6.jpg' ); ?>" alt="feature_six" width="100px">
								<h4 class="pfw-overview__keywords-heading"><?php esc_html_e( 'Seller/Manager Profile Updation', 'mwb-point-of-sale-woocommerce' ); ?></h4>
								<p class="pfw-overview__keywords-description">
									<?php esc_html_e( 'This plugin offers easy customization of all user profiles for admin managers. Admin managers can assign outlets as well products to store/outlet managers and assign user roles.', 'mwb-point-of-sale-woocommerce' ); ?>
								</p>
							</div>
						</div>
					</div>
				</div>
				</div>
			</div>
		</div>
	</div>
</div>





