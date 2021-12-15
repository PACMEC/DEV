=== EAN for WooCommerce ===
Contributors: algoritmika, anbinder
Tags: woocommerce, ean, barcode, gtin, woo commerce
Requires at least: 4.4
Tested up to: 5.8
Stable tag: 2.7.0
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Manage product EAN in WooCommerce. Beautifully.

== Description ==

**EAN for WooCommerce** plugin lets you manage product EAN in WooCommerce.

Currently supported standards: EAN-13, UPC-A, EAN-8, CODE 128.

= Main Features =

* **Save product's EAN** in backend.
* For **variable products** set EAN for each variation individually or set single EAN for all variations at once.
* **Generate** EANs automatically.
* **Search by EAN** in backend (including AJAX search) and in frontend.
* Add sortable EAN column to **admin products list**.
* Optionally **show EAN** on **single product page**, **shop pages** and/or in **cart** on frontend.
* Add EAN to **product structured data**, e.g. for Google Search Console.
* Show EAN in **order items table**, including emails, "thank you" page, etc.
* **Export** and **import** EAN.
* Use product **quick** and **bulk edit** to manage EAN.
* Output EAN with a **shortcode**.
* **Compatible** with the **Point of Sale for WooCommerce** plugin.
* **Compatible** with the **Dokan marketplace** and **WCFM** plugins.
* **Compatible** with the **Print Invoice & Delivery Notes for WooCommerce** plugin.
* **Compatible** with the **WooCommerce PDF Invoices & Packing Slips** plugin.
* And more...

= Premium Version =

With [premium plugin version](https://wpfactory.com/item/ean-for-woocommerce/) you can generate and display **barcode image** for your product EAN (frontend, backend and/or order items table, including emails).

Barcodes can be **one-dimensional** (1D barcodes) or **two-dimensional** (2D barcodes, QR codes).

Additionally you can **print** multiple EANs and barcodes to **PDF** file.

= Feedback =

* We are open to your suggestions and feedback.
* Thank you for using or trying out one of our plugins!
* [Visit plugin site](https://wpfactory.com/item/ean-for-woocommerce/).

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > EAN".

== Changelog ==

= 2.7.0 - 12/11/2021 =
* Dev - Tools - Product Tools - Generate - "Seed prefix" option added (optional). "Prefix" options renamed to "Country prefix".
* Dev - Tools - Product Tools - "Products > Bulk actions" option added (defaults to "Generate EAN" and "Delete EAN" actions).
* Dev - Print - "Print barcode" (i.e. vs "Get barcode PDF") buttons added.
* Dev - Print - Advanced Options - "Use Print.js" option added.
* Dev - Print - Advanced Options - "Skip products without EAN" option added.
* Dev - Print - Print buttons - Single product - Separate variation buttons added.
* Dev - Print - Shortcodes - `[alg_wc_ean_product_attr]` shortcode added.
* Dev - Print - Placeholders - `%product_parent_title%` placeholder added.
* Dev - Print - Placeholders - `%product_parent_sku%` placeholder added.
* Dev - Print - Placeholders - `%product_parent_id%` placeholder added.
* Dev - Print - Admin settings restyled.
* Dev - Barcodes - Shortcodes - `content` - `sku` value added.
* WC tested up to: 5.9.

= 2.6.0 - 03/11/2021 =
* Dev - Compatibility - "WooCommerce PDF Invoices & Packing Slips" plugin compatibility options added.
* Dev - Compatibility - Print Invoice & Delivery Notes for WooCommerce - Using our "General > Title" option value in PDFs now.
* Dev - Print - Print buttons - "Single order" option added.
* Dev - Print - Print buttons - "Single product" option added.
* Dev - Print - "Print buttons" option added (defaults to `Products > Bulk actions`).
* Dev - Print - Template - `%product_sku%` placeholder added.
* Dev - Print - Template - `%product_image%` - Now checking if `curl_init()` function exists. This prevents critical PHP error.
* Dev - Admin settings description updated.
* Dev - Code refactoring.

= 2.5.0 - 28/10/2021 =
* Dev - Print - "Font" and "Font size" options added. "DejaVu Sans (Unicode)" font added (normal only; italic and bold were not added to reduce the size of the plugin). All other available fonts (i.e. "Times New Roman", "Helvetica" and "Courier") have italic and bold included.
* WC tested up to: 5.8.

= 2.4.2 - 30/09/2021 =
* Dev - Search - "Flatsome" theme - Allowing partial EAN matches now.

= 2.4.1 - 29/09/2021 =
* Fix - Possible PHP parse error fixed.

= 2.4.0 - 27/09/2021 =
* Dev - Developers - `alg_wc_ean_get_type` filter added.
* Dev - Admin settings description updated.
* Dev - 1D Barcodes - Checking if EAN is valid before generating the barcode now.
* Dev - Print - Template - `%type%` placeholder added (mostly for debugging).
* Dev - Code refactoring.

= 2.3.0 - 23/09/2021 =
* Dev - Search - Safe checks added (checking for the valid `$post` variable now).

= 2.2.9 - 22/09/2021 =
* Dev - General/Barcodes - Single product page - "Variable products: Position in variation" option added.
* Dev - Compatibility - Admin settings rearranged: moved to a separate settings section.
* Dev - Advanced - "JS selector in variation" option added.
* WC tested up to: 5.7.

= 2.2.8 - 20/09/2021 =
* Dev - Tools - Product Tools - Generate - "Automatically generate EAN for new products" option added.
* Dev - Tools - Product Tools - Generate - "Automatically generate EAN on product update" option added.
* Dev - Tools - Product Tools - "Copy EAN from product meta for all products" tool added.
* Dev - Tools - Product Tools - Not overwriting EANs for products with existing EAN now.
* Dev - Developers - `alg_wc_ean_settings_page_label` filter added.

= 2.2.7 - 16/09/2021 =
* Dev - General - "Title" option added.
* Dev - Tools - Product Tools - Generate - "Prefix to" option added (optional). "Prefix" option renamed to "Prefix from".
* Dev - Tools - Product Tools - Generate - Code refactoring.

= 2.2.6 - 15/09/2021 =
* Dev - Tools - Product Tools - Generate - "Type" option added.
* Dev - Tools - Product Tools - Generate - "Prefix" option added.
* Dev - Tools - Product Tools - Generate - Code refactoring.
* Dev - Tools - Admin settings restyled.

= 2.2.5 - 14/09/2021 =
* Fix - General - Admin products list column - Validate - Fixed.
* Dev - Tools - "Generate EAN for all products" tool added.
* Dev - Tools - "Copy EAN from product SKU for all products" tool added.
* Dev - Tools - Copy EAN from product ID for all products - Showing the tool for all EAN types now (not only for `CODE 128`).
* Dev - Tools - Admin settings rearranged: moved to a separate settings section. Settings descriptions updated.
* Dev - Barcodes - Outputting barcodes even for non-valid EANs now.

= 2.2.4 - 07/09/2021 =
* Fix - Print - Page format - Custom Width/Height - Admin settings description fixed.
* Dev - Print - Advanced - "Suppress errors" option added (defaults to `yes`).
* Dev - Print - General - "Page break margin" option added.
* Dev - Print - General - All margins (top/left/right) can be zero now.
* Dev - Print - Admin settings rearranged: "Unit" option moved higher.
* Dev - Print - Admin settings descriptions updated.
* Dev - Barcodes - Advanced - "Suppress errors" options added (defaults to `yes`).

= 2.2.3 - 31/08/2021 =
* Dev - Barcodes - Shortcodes - `content` - `add_to_cart` value added.
* Dev - Barcodes - Shortcodes - `content` - `add_to_cart_url` value added.
* WC tested up to: 5.6.

= 2.2.2 - 04/08/2021 =
* Dev - Plugin Compatibility Options - "Dokan" options added.
* Dev - Plugin Compatibility Options - "WCFM" options added.
* Dev - Admin settings restyled.

= 2.2.1 - 01/08/2021 =
* Fix - Search - Our frontend search option caused issues on WooCommerce Analytics page, e.g. when searching for a coupon code in filter. This is fixed now.
* Fix - Admin settings - "Undefined property" PHP notice fixed. Was occurring in "General" settings section, when "Enable plugin" option was disabled.
* WC tested up to: 5.5.
* Tested up to: 5.8.

= 2.2.0 - 28/06/2021 =
* Dev - Print - General Options - "Use quantity" option added.
* Dev - Print - General Options - Template - `%product_name%` and `%product_title%` placeholders added.
* Dev - Compatibility - Point of Sale for WooCommerce - EAN field added to the "Register > Scanning Fields" option.
* Dev - Admin settings descriptions updated.
* Dev - Code refactoring.
* Dev - "PHP Barcode Generator" library removed.
* Dev - "TCPDF" library updated to v6.4.1 (from v6.3.5).
* WC tested up to: 5.4.

= 2.1.1 - 23/03/2021 =
* Dev - 2D Barcodes - Advanced Options - "Barcode type" option added (defaults to `QR code: Low error correction`).

= 2.1.0 - 19/03/2021 =
* Fix - Print - `%barcode_2d%` - Barcode dimension fixed (was `1d`).
* Dev - General - "Orders" options ("Add EAN to new order items meta" and "Admin order") added.
* Dev - General - Tools - "Delete EANs from all order items" tool added.
* Dev - General - Tools - "Add EANs to all order items" tool added.
* Dev - General - Tools - "Delete all EANs for all products" tool added.
* Dev - General - Tools - "Generate EANs automatically for all products from product IDs" tool added (for `CODE 128` type only).
* Dev - General - Single product page - "Template" option added.
* Dev - General - Single product page - "Position", "Position priority" options added.
* Dev - General - Search - "Flatsome theme" option added.
* Dev - Barcodes - Admin products list column - "Column title" option added.
* Dev - Barcodes - Admin products list column - "Column template" options added. Defaults to barcodes **including product children**.
* Dev - Barcodes - Shortcodes - Checking if EAN is valid now (when `content` is set to `ean`).
* Dev - Barcodes - Shortcodes - `children` (defaults to `no`) and `glue` (defaults to empty string) attributes added. This will implode all variation barcodes for variable product.
* Dev - Barcodes - Shortcodes - `template` attribute added (defaults to `%barcode_img%`). Additional placeholders: `%product_id%`, `%product_title%`, `%value%`.
* Dev - Barcodes - Shortcodes - `content` attribute added. Defaults to `ean`. Other possible values: `url`, `admin_url`, `admin_search`, `increase_stock` and `decrease_stock`.
* Dev - Barcodes - Shortcodes - `w` and `h` attributes added.
* Dev - Barcodes - Shortcodes - `product_id` defaults to `get_the_ID()` now.
* Dev - Barcodes - Shortcodes - Now accessible in "Print barcodes (PDF)" section (i.e. in "Template" option).
* Dev - Print - General Options - "Variations" option added.
* Dev - Code refactoring.
* WC tested up to: 5.1.
* Tested up to: 5.7.

= 2.0.0 - 10/01/2021 =
* Dev - "Shop pages" options added.
* Dev - "Cart" options added.
* Dev - Shortcodes - `[alg_wc_ean_barcode]` - Shortcode is now available even if "Barcodes > Single product page" option is disabled.
* Dev - Shortcodes - `[alg_wc_ean_barcode_2d]` shortcode added.
* Dev - "2D Barcodes" section added.
* Dev - "Print" section added.
* Dev - Barcodes - "Admin products list column" options added.
* Dev - Barcodes - "Enable section" option added (defaults to `no`).
* Dev - Localization - `load_plugin_textdomain` moved to the `init` action.
* Dev - Settings - All barcode options moved to new "Barcodes" section, subsections merged, etc.
* Dev - Settings - Print Invoice & Delivery Notes for WooCommerce - Link updated.
* Dev - Code refactoring.
* WC tested up to: 4.8.
* Tested up to: 5.6.

= 1.5.1 - 29/11/2020 =
* Dev - `[alg_wc_ean]` shortcode added.
* Dev - `[alg_wc_ean_barcode]` shortcode added.

= 1.5.0 - 24/11/2020 =
* Dev - Type - "Automatic (EAN-13, UPC-A, EAN-8)" option added.
* Dev - EAN field added to the WooCommerce Export and Import tools.
* Dev - EAN field added to the WooCommerce Quick and Bulk edit.
* Dev - Backend Options - Product list column - Column is sortable now.

= 1.4.0 - 24/11/2020 =
* Dev - "Type" option added. Now (in addition to the default `EAN-13`) these types are available: `CODE 128`, `EAN-8`, `UPC-A`.
* Dev - "Print Invoice & Delivery Notes for WooCommerce" plugin options added.
* WC tested up to: 4.7.

= 1.3.0 - 28/10/2020 =
* Fix - Frontend - Show barcode - Variations - It only worked if "Show EAN" option was also enabled. This is fixed now.
* Dev - Free plugin version released.
* WC tested up to: 4.6.

= 1.2.0 - 13/10/2020 =
* Dev - "Order Items Table" options added.
* Dev - Frontend - Translation domain fixed.

= 1.1.1 - 09/09/2020 =
* Dev - Backend - "Position" option added.
* WC tested up to: 4.5.

= 1.1.0 - 27/08/2020 =
* Fix - Displaying variations codes for variable products with no *main* EAN set - Fixed.
* Dev - JS files minified.
* Dev - Admin settings descriptions updated.
* Dev - Code refactoring.
* Tested up to: 5.5.
* WC tested up to: 4.4.

= 1.0.3 - 14/01/2020 =
* Fix - Backend - Search - `meta_query` fixed.

= 1.0.2 - 08/01/2020 =
* Dev - Backend - Search - "AJAX search" option added.
* Dev - Code refactoring.

= 1.0.1 - 05/01/2020 =
* Dev - EAN-13 validation added.
* Dev - Backend - EAN input pattern now set to accept numbers only; max length set to 13.

= 1.0.0 - 30/12/2019 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
