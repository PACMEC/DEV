=== Ether and ERC20 tokens WooCommerce Payment Gateway ===
Contributors: ethereumicoio, freemius
Tags: woocommerce, ethereum, erc20, erc777, erc223, bep20, bsc, token, payment, crypto, cryptocurrency, blockchain, e-commerce, binance smart chain
Requires at least: 4.7
Tested up to: 5.8.2
Stable tag: 4.12.5
Donate link: https://etherscan.io/address/0x476Bb28Bc6D0e9De04dB5E19912C392F9a76535d
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 7.1

Ether and ERC20 tokens WooCommerce Payment Gateway enables customers to pay with Ether
or any ERC20, ERC777 or ERC223 tokens on your WooCommerce store.

== Description ==

Ether and ERC20 tokens WooCommerce Payment Gateway is the only one true decentralized ether and ERC20, ERC777 and ERC223 token payment plugin. It enables customers to pay with Ether or any ERC20, ERC777 or ERC223 token on your WooCommerce store. Your customers will be offered the option of paying with Ether or some ERC20, ERC777 or ERC223 token at checkout. If they choose that option then they will be quoted a price in Ether or ERC20, ERC777 or ERC223 token for their order automatically.

After submitting their order they will be given the details of the Ether or ERC20, ERC777 or ERC223 token transaction they should make.

> Binance Smart Chain (BSC) and Polygon (Matic) are also supported!

https://vimeo.com/556820096

== Features ==

* Accept payment in Ether or any ERC20, ERC777 or ERC223 token of your choice
* Second payment method can be configured to accept Ether or token only.
* Mobile ready with [WalletConnect](https://walletconnect.com/) payment method
* Free to use. Fixed fee per purchase
* User friendly payment wizard
* Automatically convert order value to Ether or ERC20, ERC777 or ERC223 token at checkout
* Option for adding a percentage mark-up to the converted price of Ether and/or tokens to help cover currency fluctuations.
* Allows easy payment via [MetaMask](https://metamask.io/) or any other Web3 Wallet client. If customer do not want to use [MetaMask](https://metamask.io/), she can just copy and paste Value, Address, and Data fields in her favorite wallet software.
* Provides a link to install [MetaMask](https://metamask.io/) on desktop and deep link to install [MetaMask Wallet](https://metamask.io/) or [Trust Wallet](https://trustwallet.com/) on mobile
* The `Disallow customer to pay with Ether` option is useful to accept only some token
* Automatic transaction tracking / reconciliation and order updates
* Integration with the [Ethereum Wallet](https://wordpress.org/plugins/ethereum-wallet/) plugin is provided. [Ethereum Wallet PRO](https://checkout.freemius.com/mode/dialog/plugin/4542/plan/7314/) is required.
* Token rate can be specified in the store's base currency code
* myCRED [Point Based Stores](https://codex.mycred.me/chapter-iii/gateway/woocommerce/point-based-stores/) are supported: points can be sold for ERC20 tokens
* Custom WooCommerce currency based stores support
* `Payment Complete Order Status` setting can be used to configure the status to apply for order after payment is complete
* Dynamic token prices from [UniswapV2](https://uniswap.org/), [livecoin.net](https://www.livecoin.net), [coinmarketcap.com](https://coinmarketcap.com), [coinbase.com](https://coinbase.com), [cryptocompare.com](https://cryptocompare.com), [kanga.exchange](https://kanga.exchange)
* Markup per token
* Digits after a decimal point display count setting
* Binance Smart Chain support
* Polygon (Matic) support
* `Ethereum Node JSON-RPC Endpoint` and `Ethereum Node Websocket Endpoint` admin settings allows you to use your own Ethereum node.
* `Transaction explorer URL` setting can be used to set your own blockchain explorer for tx links shown
* The [WooCommerce Deposits](https://woocommerce.com/products/woocommerce-deposits/?aff=9181&cid=2167410) and other similar plugins are supported
* [WPJobster](https://wpjobster.com/) theme addon compatibility.
* The [Cryptocurrency Product for WooCommerce](https://wordpress.org/plugins/cryptocurrency-product-for-woocommerce/) plugin token product icons are shown on the WooCommerce Checkout page.
* The only one true decentralized ether and ERC20, ERC777 or ERC223 token payment plugin. There are no service other that Ethereum blockchain is used in this plugin. You do not need to trust us or any other party. This plugin uses a public smart contract in the Ethereum blockchain to record and confirm orders
* The Payment Gateway smart contract: [0xd0E4e3A739A454386DA9957432b170C006327B0d](https://etherscan.io/address/0xd0E4e3A739A454386DA9957432b170C006327B0d)
* The Payment Gateway smart contract on the Binance Smart Chain: [0x77913766661274651d367A013861B64111E77A3f](https://bscscan.com/address/0x77913766661274651d367a013861b64111e77a3f)
* The Payment Gateway smart contract on the Polygon (Matic): [0x77913766661274651d367A013861B64111E77A3f](https://polygonscan.com/address/0x77913766661274651d367A013861B64111E77A3f)

> Combined with the [Cryptocurrency Product for WooCommerce](https://wordpress.org/plugins/cryptocurrency-product-for-woocommerce/) plugin it can allow you to sell ERC20, ERC777 or ERC223 tokens for Ether or Ether for ERC20, ERC777 or ERC223 tokens.

= Ether, ERC777 or ERC223 payment =

The payment with Ether, ERC777 or ERC223 tokens is a simple one step process. Customer have to send one transactions to the Ethereum blockchain.

= ERC20 token payment =

The ERC20 token payment consists of two steps:

* Deposit funds to the payment gateway smart contract in the Ethereum blockchain, and
* Use this deposit to pay for your order

Customer have to send two transactions to the Ethereum blockchain:

* first for deposit and
* second for the real payment

 > There are no need to refund the deposit to cancel the first step, since it is actually a `Token.approve` call that doesn't transfer any tokens.

== Business Version Features ==

> This feature is only supported for the Business plan. [Upgrade to Business](https://checkout.freemius.com/mode/dialog/plugin/4817/plan/7748/)

 * Custom or private network. Support for your own custom or private Ethereum fork or `Quorum` network.

== Disclaimer ==

**By using this free plugin you accept all responsibility for handling the account balances for all your users.**

Under no circumstances is **ethereumico.io** or any of its affiliates responsible for any damages incurred by the use of this plugin.

Every effort has been made to harden the security of this plugin, but its safe operation depends on your site being secure overall. You, the site administrator, must take all necessary precautions to secure your WordPress installation before you connect it to any live wallets.

You are strongly advised to take the following actions (at a minimum):

- [Educate yourself about cold and hot cryptocurrency storage](https://en.bitcoin.it/wiki/Cold_storage)
- Obtain hardware wallet to store your coins, like [Ledger Nano S](https://www.ledgerwallet.com/r/4caf109e65ab?path=/products/ledger-nano-s)
- [Educate yourself about hardening WordPress security](https://wordpress.org/support/article/hardening-wordpress/)
- [Install a security plugin such as Jetpack](https://jetpack.com/pricing/?aff=9181&cid=886903) or any other security plugin
- **Enable SSL on your site** if you have not already done so.

> By continuing to use the Ether and ERC20 tokens WooCommerce Payment Gateway plugin, you indicate that you have understood and agreed to this disclaimer.

== Screenshots ==

1. The payment method choice
2. MetaMask payment method
3. Advanced panel expanded
4. QR-code dialog
5. ERC777 token payment
6. ERC20 token payment. The first (deposit) step
7. [WalletConnect](https://walletconnect.com/) payment method account input dialog with QR-code to scan from mobile phones
8. Confirmations waiting dialog
9. Payment succeeded indication panel
10. Order notes with transaction link
11. Settings: payment address and supported tokens
12. Settings: Disable Ether and Mark up percents
13. Blockchain settings
14. Gas and confirmations number settings
15. API Credentials
16. Advanced settings
17. Binance Smart Chain settings

== Installation ==

* Make sure that [System Requirements](https://ethereumico.io/knowledge-base/cryptocurrency-product-for-woocommerce-plugin-system-requirements/) are met on your hosting provider. These providers are tested for compliance: [Cloudways](https://www.cloudways.com/en/?id=462243), [Bluehost](https://www.bluehost.com/track/olegabr/), [SiteGround](https://www.siteground.com/go/ethereumico)
* Install and activate it as you would any other plugin
* Head over to WooCommerce » Settings » Checkout » Ether and ERC20 tokens WooCommerce Payment Gateway
* Enter your Ethereum address to receive payments and confirm markup %
* Register for an Infura.io and Coinmarketcap.com API keys and put it in admin settings. It is required to interact with Ethereum blockchain and obtain rates. Use this guide for Infura.io: [Get infura API Key](https://ethereumico.io/knowledge-base/infura-api-key-guide/).
* Tune other options if you need to

https://youtu.be/fDvxsPqelOI

= Binance Smart Chain (BSC) =

https://youtu.be/05SkyYDj7i4

== Troubleshooting ==

= WooCommerce session broken =

If you are getting this message: `ETH price quote has been updated, please check and confirm before proceeding` it means that your server installation settings broke the WooCommerse session somehow. Install the [WordPress Native PHP Sessions](https://wordpress.org/plugins/wp-native-php-sessions/) in this case.

= Configure for woocommerce-deposits plugin =

Use this snippet in your `function.php` file if the [woocommerce-deposits](https://codecanyon.net/item/woocommerce-deposits-partial-payments-plugin/9249233) plugin is used and you want to disable tokens payment for the full payment case.

`
// @see https://wordpress.stackexchange.com/a/138598/137915
add_filter('woocommerce_available_payment_gateways','my_filter_gateways',1);
function my_filter_gateways($gateways) {
    global $woocommerce;
    //Remove a specific payment option
    if (isset($gateways['ether-and-erc20-tokens-woocommerce-payment-gateway']) &&
		!(isset(WC()->cart->deposit_info['deposit_enabled']) &&
		  true === WC()->cart->deposit_info['deposit_enabled'])
	) {
        unset($gateways['ether-and-erc20-tokens-woocommerce-payment-gateway']);
	}
    return $gateways;
}
`

== Testing ==

You can test this plugin in some test network for free.

= Testing in ropsten =

* Set the `Blockchain` setting to `ropsten`
* Buy some `0x6Fe928d427b0E339DB6FF1c7a852dc31b651bD3a` TSX token by sending some Ropsten Ether amount to it's Crowdsale contract: `0x773F803b0393DFb7dc77e3f7a012B79CCd8A8aB9`
* You can "buy" some Ropsten Ether for free using [MetaMask](https://metamask.io/)
* Set the `Supported ERC20 tokens list` setting to support the `0x6Fe928d427b0E339DB6FF1c7a852dc31b651bD3a` token
* Create a cheap test product in your store
* Buy this product with Ropsten Ether and/or this TSX token
* Check that proper amount of Ropsten Ether and/or TSX token has been sent to your payment address

= Testing in rinkeby =

* Set the `Blockchain` setting to `rinkeby`
* Buy some `0x194c35B62fF011507D6aCB55B95Ad010193d303E` TSX token by sending some Rinkeby Ether amount to it's Crowdsale contract: `0x669519e1e150dfdfcf0d747d530f2abde2ab3f0e`
* You can "buy" some Rinkeby Ether for free here: [rinkeby.io](https://www.rinkeby.io/#faucet)
* Set the `Supported ERC20 tokens list` setting to support the `0x194c35B62fF011507D6aCB55B95Ad010193d303E`
* Create a cheap test product in your store
* Buy this product with Rinkeby Ether and/or this TSX token
* Check that proper amount of Rinkeby Ether and/or TSX token has been sent to your payment address

== Fees ==

The fee is published in a blockchain and is limited by a maxFee property in smart contract.
This guaranties your safety as a plugin customer. The feePercent and maxFee values a saved as % * 10^6:

* The maxFee is 3% which is saved as 3000000 and can not be changed.
* The feePercent is 1,5% which is saved as 1500000 and can be changed in a 0% to 3% range.

> We reserve the right to change the fee in the 0% to 3% range to reflect the market changes.

== l10n ==

This plugin is localization ready.

Languages this plugin is available now:

* English
* Russian(Русский)
* Polish(Polski) by Jacek from btcleague dot net
* Spanish(Español)
* Chinese(中文)
* German(Deutsche)

Feel free to translate this plugin to your language.

== Changelog ==


= 4.12.5 =

* Fix error log output

= 4.12.4 =

* Fix for double value calculations rounding errors that cause paid orders marked as `Failed`

= 4.12.3 =

* [WalletConnect](https://walletconnect.com/) payment method fix for non-Ethereum networks

= 4.12.2 =

* [WalletConnect](https://walletconnect.com/) payment method fix for non-MetaMask wallets

= 4.12.1 =

* The email `Payment page` link fix

= 4.12.0 =

* QR-code payment method is replaced with the [WalletConnect](https://walletconnect.com/) payment method

= 4.11.2 =

* Rate source dictionaries updated

= 4.11.1 =

* WooCommerce `action-scheduler` [issue workaround](https://github.com/woocommerce/action-scheduler/issues/730#issuecomment-880586544)

= 4.11.0 =

* Polygon mainnet and mumbai testnet support is added

= 4.10.1 =

* Fix floating point error that leads to order fails
* `action-scheduler` lib update

= 4.10.0 =

* CSS class is added dynamically to help customize look of the payment button on the deposit and payment steps separately.

= 4.9.3 =

* Fix fatal error when incorrect endpoints are entered.

= 4.9.2 =

* Fix fatal error when incorrect endpoints are entered.

= 4.9.1 =

* [EIP-1559](https://github.com/ethereum/EIPs/blob/master/EIPS/eip-1559.md) new gas oracle version and better estimateGas call

= 4.9.0 =

* [EIP-1559](https://github.com/ethereum/EIPs/blob/master/EIPS/eip-1559.md) support
