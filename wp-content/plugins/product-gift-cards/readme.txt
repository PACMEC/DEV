=== PW WooCommerce Gift Cards ===
Contributors: pimwick
Donate link: https://paypal.me/pimwick
Tags: woocommerce, gift cards, gift certificates, pimwick
Requires at least: 4.5
Tested up to: 5.8
Requires PHP: 5.6
Stable tag: 1.195
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Sell gift cards to your WooCommerce store, in just a few minutes!

== Description ==

**Your WooCommerce store should offer gift cards!**

Gift Cards are convenient and increase sales organically. the WooCommerce Gift Cards plugin makes it easy to sell gift cards to your store. So easy to get started, you can be selling gift cards for your WooCommerce store in 5 minutes!

**Purchasing** Similar to Amazon.com gift cards, the customer can specify the amount, recipient, and message when purchasing.

**Receiving** WooCommerce email template system for beautiful emails. Click the link directly in the email to add the gift card to the cart automatically!

**Redeeming** Integrates into your theme to make redeeming a gift card easy for the customer. Applies the balance after tax, just like cash. New balance shown on the cart and checkout pages.

**Guest Checkout** Gift cards are not tied to a specific account so guests can receive gift cards without having to create an account.

**Compatible with WooCommerce Pre-Orders** If you use the WooCommerce Pre-Orders plugin from WooCommerce.com.

**Setup is easy!** One-click creation of the Gift Card product. Easily customized to suit your needs.

**Gift Card Admin** See your gift card liability at a glance. View details about individual cards.


> **<a href="https://www.pimwick.com/gift-cards/">PW WooCommerce Gift Cards Pro</a> lets you do more:**
>
> * **Set Custom Amounts** - Allow customers to specify the amount. You can set a minimum and a maximum amount.
> * **Schedule delivery** - Optionally allow customers to schedule when a gift card will be delivered.
> * **Specify a Default Amount** - Choose an amount that will be pre-selected when purchasing a gift card.
> * **Customer-facing Balance Page** - A shortcode to let customers check their gift card balances.
> * **Adding funds to existing gift card** - Customers can add funds to existing gift cards from the Check Balance page.
> * **Expiration Dates** - Automatically set an expiration date based on the purchase date.
> * **Balance Adjustments** - Perform balance adjustments in the admin area.
> * **Sell Physical Gift Cards** - Import existing gift card numbers and balances.
> * **Manually Generate Gift Cards** - Specify the amount and quantity for the cards to create multiple cards in one step.
> * **REST API** - Adheres to the WordPress and WooCommerce REST API standards.

Compatible with WooCommerce 3.0 and higher.

Available in the following languages:
* Arabic
* Danish
* Dutch
* English
* Finnish
* French
* Galician
* German
* Italian
* Portuguese
* Romanian
* Russian
* Spanish
* Swedish

The following currency switcher plugins are supported:
* Aelia Currency Switcher
* WooCommerce Currency Switcher by realmag777
* WPML WooCommerce Multi-currency by OnTheGoSystems
* Multi Currency for WooCommerce by VillaTheme
* WooCommerce Ultimate Multi Currency Suite by Dev49.net (requires a patch, contact us for details)
* Polylang + Hyyan WooCommerce Polylang Integration

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/pw-gift-cards` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Navigate to Pimwick Plugins -> PW Gift Cards

== Screenshots ==

1. Similar to Amazon.com gift cards, the customer can specify the amount, recipient, and message when purchasing.
2. WooCommerce email template system for beautiful emails. Click the link directly in the email to add the gift card to the cart automatically!
3. Use the email designer to customize your gift card.
4. Integrates into your theme to make redeeming a gift card easy for the customer. Applies the balance after tax, just like cash. New balance shown on the cart and checkout pages.
5. One-click creation of the Gift Card product. Easily customized to suit your needs.
6. See your gift card liability at a glance. View details about individual cards.

== Changelog ==

= 1.195 =
* Confirmed compatibility with WooCommerce 6.0

= 1.194 =
* Fixed an issue where the Apply Gift Card and Remove fields did not work on the Cart page after changing the Shipping method. Ensure the Gift Card product data tab is the first one when other plugins are installed such as WPC Composite Products.

= 1.193 =
* Fixed a conflict with WooCommerce Multilingual that occurs when changing the shipping method on the Cart or Checkout pages. Improved the Italian translation.

= 1.192 =
* Confirmed compatibility with WooCommerce v5.9. Added new action hooks to allow other plugins to extend the functionality: pwgc_activity_create, pwgc_activity_transaction, pwgc_activity_deactivate, pwgc_activity_reactivate, pwgc_property_updated_active

= 1.191 =
* Added a new hook to allow changing the email item data: pwgc_customer_email_item_data. Fixed a conflict with PPOM for WooCommerce when changing the shipping method on the Cart or Checkout pages.

= 1.190 =
* Improved the accuracy of the Outstanding Balance amount in the admin area by rounding at each step.

= 1.189 =
* Fixed a conversion issue when using the Price Based on Country for WooCommerce plugin by Oscar Gare.

= 1.188 =
* Added a new option to not display the Gift Card Applied message when clicking the Redeem button. To disable this message, set PWGC_SHOW_GIFT_CARD_APPLIED_MESSAGE_FROM_REDEEM_BUTTON to false in wp-config.php

= 1.187 =
* Improved the cart_contains_gift_card() function so it can be used in the back end.

= 1.186 =
* Fixed a conversion issue when using the Price Based on Country for WooCommerce plugin by Oscar Gare.

= Previous versions =
* See changelog.txt

== Upgrade Notice ==

= 1.195 =
* Confirmed compatibility with WooCommerce 6.0


