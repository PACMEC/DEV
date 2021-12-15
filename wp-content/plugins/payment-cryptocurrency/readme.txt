=== Cryptocurrency Payment Gateway ===
Contributors: weprogramit
Tags: bitcoin, bitcoincash, litecoin, dogecoin, cryptocurrency, gateway, woocommerce
Requires at least: 4.7
Tested up to: 5.8.2
Stable tag: 1.0.0
Requires PHP: 7.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Digital Currency Payment Gateway for WooCommerce. Easily accept Bitcoin, Bitcoin Cash, Litecoin, Dogecoin, and more in your store.

== Description ==
Our Cryptocurrency Payment Gateway was built with the core values of Cryptocurrency in mind with regards to anonymity and security. The plugin seamlessly enables your store to accept  Bitcoin, Bitcoin Cash, Litecoin, Dogecoin, and more, right away by simply adding your Wallet addresses.

The plugin was created to solve a solution that we and several merchants were facing, which was a gateway that respects customer privacy, no external redirects, seamless integration, has no middleman fees, and we achieved it. Over the years our plugin has been improved to also offer Zeroconf support enabling customers to instantly receive their products without risk to the merchant.

### Plugin Features:

* Provide a list of your own Bitcoin, Bitcoin Cash, Dogecoin, Litecoin, and other cryptocurrency wallet addresses or use a Block.io premium account  (get our [HD Wallet Add-on](https://www.cryptowoo.com/shop/cryptowoo-hd-wallet-addon/) to receive payments directly to HD wallets such as Electrum, Mycelium, Trezor, or Ledger Wallet)
* Keep the customer on your website: No redirection to third-party websites or iframes during checkout.
* Keep your data: No need to give customer data to a third party.
* Optional payment completion at zero confirmations using transaction confidence metrics.
* Set per-currency maximum order amount thresholds for zeroconf payments.
* Collect refund addresses during checkout.
* Support for all WooCommerce store currencies except Lao KIP.
* Supported exchange rate APIs: Bitcoinaverage, Bitcoincharts, Bitfinex, BitPay, BitTrex, Blockchain.info, CoinCap, Coindesk, CoinGecko, GDAX, Shapeshift, Kraken, Luno.com. OKCoin.com, OKCoin.cn, Poloniex
* Apply discounts and markups individually for each currency.
* Integrated into WooCommerce order emails and admin backend.
* WordPress Multisite compatible
* Supports “WooCommerce Currency Switcher” and “Aelia Currency Switcher for WooCommerce“
* No full node required – choose between different blockchain data providers or connect to your own private Esplora or Insight API instance

### HD Wallet Add-on features [premium]

* Accept more cryptocurrencies such as Monero, Dash, and Vertcoin.
* Derive a virtually unlimited number of addresses from the extended public key of your wallet.
* Generates one address per order automatically.
* The payments from your customers go straight into your own HD wallet such as Electrum, Trezor, Ledger Nano, or any other wallet with HD support.

You can get the HD Wallet Add-on [on our website](https://www.cryptowoo.com/shop/cryptowoo-hd-wallet-addon/).

### Ethereum and ERC-20 Add-on features [premium]

* Accept ERC-20 cryptocurrencies or tokens such as Ether (ETH), Tether USD (USDT), USD Coin (USDC), Dai (DAI), Gem Exchange and Trading (GXT), and many more.
* Web3 wallet support allows your customers to easily pay with the click of a button from their wallets such as MetaMask, Brave Browser, WalletConnect, Torus, Fortmatic.
* Ethereum fallback address allows you to receive all payments into a single Ethereum address.

You can get the Ethereum and ERC-20 Add-on [on our website](https://www.cryptowoo.com/shop/cryptowoo-ethereum-add-on/).

### Pay for development to add support for additional cryptocurrencies:

* [Make a Custom Cryptocurrency Add-on Request](https://www.cryptowoo.com/shop/add-a-coin/)
* [Add Custom ERC-20 Token](https://www.cryptowoo.com/shop/add-erc-20-token/)


== Installation ==
1. Install the plugin into your WordPress website.
2. Add cryptocurrency addresses in the Address List in the settings.
3. Choose a payment processing API that will be used to check the blockchain for incoming payments
5. Enable the payment gateway in the settings and click save.
6. Disable internal WordPress Cron jobs and setup external Cron jobs (recommended)

Done!

Optionally you may navigate through the settings to customize the payment gateway to your preferences. If you are using the HD Wallet Add-on, you can add the master public key of your wallet instead of adding addresses to the address list in step 2.

== Screenshots ==
1. This is what the customers see on your site while viewing a product.
2. This is what the customers see on your site when checking out.
3. This is the checkout page that the customer will see when paying for an order.
4. This is the checkout flow configuration page in wp-admin.
5. This is the checkout settings page in wp-admin where you can customize the checkout to your liking.
6. This is payment settings page in wp-admin where you can customize the countdown, instructions, etc.
7. This is the thank you page where you can customize your successful payment message the customer receives.
8. This is the address list page in wp-admin where you can customize your Cryptocurrency addresses and specify email alerts.
9. This is the configuation page for Block.io which allows you to set your Block.io API keys.
10. This is the HD wallet settings page which enables enhanced feature set of the plugin for various Cryptocurrencies.
11. This is the Cron Scheduling settings where it will generate your Cron job commands to setup outside of WordPress.
12. This is the confirmation settings page where you can specify your minimum confirmations for all the available Cryptocurrencies.
13. This is the zeroconf settings where you can specify maximum order values that zeroconf will be accepted for.
14. This is what the WooCommerce order page will look like when using our plugin.
15. This is the transaction confidence page where you can specify your own thresholds of trust for unconfirmed transactions.
16. This is the Blockchain Access settings where you can specify your preferred processing API
17. This is the API resources control settings where you can configure Fallback API processing.
18. This is the advanced settings of the Payment Processing section with settings such as show/hide countdown, order expiration configuration and underpayment settings.
19. This is the exchange rate settings where you can specify which providers you want to pull the live prices from.
20. This is the decimal settings where you can specify how many decimal places you want your prices to be rounded to.
21. This is the multiplier settings for discount and surcharges. This enables you to customize on a per crypto basis the price increases for using certain cryptocurrencies.
22. This is the display settings where you can customize icon colors, further explorers and pricing tables.