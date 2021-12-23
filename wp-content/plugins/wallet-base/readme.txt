=== Ethereum Wallet ===
Contributors: ethereumicoio, freemius
Tags: ethereum, erc20, bep20, bnb, token, crypto, cryptocurrency, wallet, binance smart chain
Requires at least: 3.7
Tested up to: 5.8.1
Stable tag: 3.3.0
Donate link: https://etherscan.io/address/0x476Bb28Bc6D0e9De04dB5E19912C392F9a76535d
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Requires PHP: 7.1

The user friendly Ethereum Wallet for your WordPress site.

== Description ==

The Ethereum Wallet WordPress plugin auto-creates a user wallet upon registration and allows user to send Ether or ERC20/ERC721 tokens from it.

https://youtu.be/jB_JBlLGA6Q

> It is a valuable addition for the [Cryptocurrency Product for WooCommerce](https://ethereumico.io/product/cryptocurrency-wordpress-plugin/ "Cryptocurrency Product for WooCommerce") plugin.

Using these two plugins your non-techie customers can register to obtain an Ethereum account address and then buy your tokens to be sent to this new address.

> Binance Smart Chain, Polygon and any other EVM-compatible blockchain is supported in the [PRO version](https://checkout.freemius.com/mode/dialog/plugin/4542/plan/7314/?trial=paid "The Ethereum Wallet Professional plugin")!

== FREE Features ==

* To show user's Ethereum account address insert the `[pacmec-wallet-account]` shortcode wherever you like. You can use `label="My label"` attribute to set your own label text. And `nolabel="yes"` attribute to display no label at all.
* To show user's Ethereum account address's Ether balance insert the `[pacmec-wallet-balance]` shortcode wherever you like. Add the `displayfiat="1"` attribute to display the calculated fiat balance too.
* Use `tokendecimals` attribute to configure the desired digits after the decimal separator count for the `[pacmec-wallet-balance]` shortcode.
* Use `tokendecimalchar` attribute to configure the desired decimal separator character for the `[pacmec-wallet-balance]` shortcode.
* Dynamic CTN token price feature of the [Cryptocurrency Product for WooCommerce](https://ethereumico.io/product/cryptocurrency-wordpress-plugin/ "Cryptocurrency Product for WooCommerce") plugin is supported.
* To show the send Ether form insert the `[pacmec-wallet-sendform]` shortcode wherever you like.
* To show an account's transactions history insert the `[pacmec-wallet-history direction="in"]` shortcode wherever you like. The `direction` attribute can have values `in` to show only input transactions, `out` to show only output transactions, or `inout` to show both input and output transactions. If attribute is omitted, the `inout` is used by default.
* Pagination and filtering is available for the tx history table
* Use the `user_ethereum_wallet_address` user_meta key to display the user's account address, or for the `Ethereum Wallet meta key` setting of the [Cryptocurrency Product for WooCommerce](https://ethereumico.io/product/cryptocurrency-wordpress-plugin/ "Cryptocurrency Product for WooCommerce") plugin
* The `user_ethereum_wallet_last_tx_hash` user meta key can be used to access the user's most recent transaction
* The Ethereum Gas price is auto adjusted according to the [etherchain.org](https://www.etherchain.org) API
* Balances and tx tables of the wallet-receiver are auto-refreshed by listening to the blockchain
* Integration with the [Ether and ERC20 tokens WooCommerce Payment Gateway](https://wordpress.org/plugins/ether-and-erc20-tokens-woocommerce-payment-gateway/) plugin is provided
* New account creation form shortcode: `[pacmec-wallet-account-management-create]`
* Accounts list, select default shortcode: `[pacmec-wallet-account-management-select]`
* Private key import shortcode: `[pacmec-wallet-account-management-import]`
* Private key export shortcode: `[pacmec-wallet-account-management-export]`
* QR-code is displayed for account and private key export shortcodes
* QR Scanner for `TO` section of `SEND FORM`
* `Ethereum wallet` column with linked user's account addresses is displayed on the `Users` WordPress admin page (`/wp-admin/users.php`)
* This plugin is l10n ready

== PRO Features ==

> Full ERC20 and NFT (ERC721) tokens support!

* NFT (ERC721) tokens display and sending support: `[pacmec-wallet-nft]` shortcode, and NFT transfers display in the history table
* Admin markup feature to earn Ether fee from your site's Ethereum Wallet users
* Custom/private blockchain feature: `Ethereum Node JSON-RPC Endpoint` and other related settings to use Binance smart chain (BSC), Polygon and any other EVM compatible blockchain
* To show user's Ethereum account address's TSX ERC20 token balance insert the `[pacmec-wallet-balance tokenname="TSX" tokenaddress="0x6Fe928d427b0E339DB6FF1c7a852dc31b651bD3a"]` shortcode wherever you like.
* The [Cryptocurrency Product for WooCommerce](https://ethereumico.io/product/cryptocurrency-wordpress-plugin/ "Cryptocurrency Product for WooCommerce") plugin integration for the `[pacmec-wallet-balance]` shortcode is available. Add the `tokenwooproduct` attribute with a product id of the corresponding WooCommerce Token product as a value to display the balance in a fiat currency as well. The token to fiat currency rate would be calculated from the WooCommerce product price. Example: `[pacmec-wallet-balance tokenname="TSX" tokenaddress="0x6Fe928d427b0E339DB6FF1c7a852dc31b651bD3a" tokenwooproduct="123"]`. Result: `12.345 TSX $12.34`.
* The `tokeniconpath` attribute added to the `[pacmec-wallet-balance]` shortcode turns it to a more sophisticated widget with token icon. For token: `[pacmec-wallet-balance tokensymbol="TSX" tokenname="Test Coin" tokenaddress="0x6Fe928d427b0E339DB6FF1c7a852dc31b651bD3a" tokenwooproduct="123" tokeniconpath="https://example.com/icons/BTC.png"]`. For Ether: `[pacmec-wallet-balance displayfiat="1" tokeniconpath="https://example.com/icons/BTC.png"]`.
* Dynamic ERC20 token price feature of the [Cryptocurrency Product for WooCommerce](https://ethereumico.io/product/cryptocurrency-wordpress-plugin/ "Cryptocurrency Product for WooCommerce") plugin is supported.
* The `[pacmec-wallet-accounts-table]` shortcode can be used to display a table of all accounts with fiat balances. Avatars and logins are also shown for the admin user. Integration with [BuddyPress](https://buddypress.org/) is provided for avatars display.
* To show the send ERC20 token form insert the `[pacmec-wallet-sendform]` shortcode wherever you like.
* Multi-vendor support for the [Cryptocurrency Product for WooCommerce](https://ethereumico.io/product/cryptocurrency-wordpress-plugin/ "Cryptocurrency Product for WooCommerce") plugin
* `pacmec-wallet-dividends` shortcode can be used to display dividends payment history. See the `ERC20 Dividend Payments Add-On` of the [Cryptocurrency Product for WooCommerce](https://ethereumico.io/product/cryptocurrency-wordpress-plugin/ "Cryptocurrency Product for WooCommerce") plugin for details
* `tokenaddress` attribute for the `pacmec-wallet-accounts-table` forces this table to display token balances instead of the Ether
* `mintokenamount` attribute for the `pacmec-wallet-accounts-table` shortcode forces this table to display users with token balances greater or equal to the `mintokenamount` value set
* `Tools` / `Ethereum Wallet` submenu can be used to manually recalculate user account balances
* [ERC1404](https://erc1404.org/) support. If transfer is not allowed, corresponding error message would be displayed.
* `tokenslist` attribute for the `pacmec-wallet-sendform` shortcode can contain a single allowed token address or a comma separated list of allowed token addresses.
* [ERC2212](https://github.com/ethereum/EIPs/issues/2212) support. `pacmec-wallet-dividends` shortcode can contain all attributes the `pacmec-wallet-balance` can. It requires the token to implement the [ERC2212](https://github.com/ethereum/EIPs/issues/2212) standard.

> See the official site for a live demo: [https://ethereumico.io/pacmec-wallet/](https://ethereumico.io/pacmec-wallet/ "The Ethereum Wallet WordPress plugin")

> To use the `ERC20 Dividend Payments Add-On` for the [Cryptocurrency Product for WooCommerce](https://ethereumico.io/product/cryptocurrency-wordpress-plugin/ "Cryptocurrency Product for WooCommerce") plugin, install the [Cryptocurrency Product for WooCommerce](https://ethereumico.io/product/cryptocurrency-wordpress-plugin/ "Cryptocurrency Product for WooCommerce") and then go to Settings > Cryptocurrency Product > Add-Ons > Dividends.

== Screenshots ==

1. The `[pacmec-wallet-account]` display with QR-code opened
2. The `[pacmec-wallet-sendform]` display
3. The `[pacmec-wallet-history]` display
4. The plugin settings
5. The `[pacmec-wallet-account-management-create]` display
6. The `[pacmec-wallet-account-management-import]` display
7. The `[pacmec-wallet-account-management-select]` display
8. The `[pacmec-wallet-account-management-export]` display
9. The `[pacmec-wallet-account-management-export]` display with QR-code opened
10. The `[pacmec-wallet-balance]` display with different settings

== Disclaimer ==

**By using this plugin you accept all responsibility for handling the account balances for all your users.**

Under no circumstances is **ethereumico.io** or any of its affiliates responsible for any damages incurred by the use of this plugin.

Every effort has been made to harden the security of this plugin, but its safe operation depends on your site being secure overall. You, the site administrator, must take all necessary precautions to secure your WordPress installation before you connect it to any live wallets.

You are strongly advised to take the following actions (at a minimum):

- [Educate yourself about cold and hot cryptocurrency storage](https://en.bitcoin.it/wiki/Cold_storage)
- Obtain hardware wallet to store your coins, like [Ledger Nano S](https://www.ledgerwallet.com/r/4caf109e65ab?path=/products/ledger-nano-s)
- [Educate yourself about hardening WordPress security](https://codex.wordpress.org/Hardening_WordPress)
- [Install a security plugin such as Jetpack](https://jetpack.com/pricing/?aff=9181&cid=886903) or any other security plugin
- **Enable SSL on your site** if you have not already done so.

> By continuing to use the Ethereum Wallet WordPress plugin, you indicate that you have understood and agreed to this disclaimer.

== Installation ==

> Make sure that [System Requirements](https://ethereumico.io/knowledge-base/pacmec-wallet-plugin-system-requirements/) are met on your hosting provider. These providers are tested for compliance: [Cloudways](https://www.cloudways.com/en/?id=462243), [Bluehost](https://www.bluehost.com/track/olegabr/), [SiteGround](https://www.siteground.com/go/ethereumico)

https://youtu.be/jB_JBlLGA6Q

https://youtu.be/F9_Yp1-E7JE

* Enter your settings in admin pages and place the `[pacmec-wallet-sendform]`, `[pacmec-wallet-balance]` and other shortcodes wherever you need it.
* For the proper use of the `[pacmec-wallet-accounts-table]` shortcode, change the `Permalink Settings` to anything other than the default value.

= bcmath and gmp =

`
sudo apt-get install php-bcmath php-gmp
service apache2 restart
`

For AWS bitnami AMI restart apache2 with this command:

`
sudo /opt/bitnami/ctlscript.sh restart apache
`

= Shortcodes =

Possible shortcodes configuration:

`
[pacmec-wallet-nft columns="3" rows="2"]

[pacmec-wallet-nft columns="3" rows="2" account="0x6975be450864c02b4613023c2152ee0743572325"]

[pacmec-wallet-account label="Your wallet:"]

[pacmec-wallet-account nolabel="yes"]

[pacmec-wallet-balance]

[pacmec-wallet-balance tokenname="TSX" tokenaddress="0x6Fe928d427b0E339DB6FF1c7a852dc31b651bD3a"]

[pacmec-wallet-sendform]

[pacmec-wallet-history]

[pacmec-wallet-history direction="in"]

[pacmec-wallet-history direction="out"]

[pacmec-wallet-account-management-create]

[pacmec-wallet-account-management-select]

[pacmec-wallet-account-management-import]

[pacmec-wallet-account-management-export]
`

= Infura.io Api Key =

Register for an infura.io API key and put it in admin settings. It is required to interact with Ethereum blockchain. Use this [Get infura API Key Guide](https://ethereumico.io/knowledge-base/infura-api-key-guide/) if unsure.

== Testing ==

You can test this plugin in some test network for free.

> The `ropsten`, `rinkeby`, `goerli` and `kovan` testnets are supported.

=== Testing in ropsten ===

* Set the `Blockchain` setting to `ropsten`
* "Buy" some Ropsten Ether for free using [MetaMask](https://metamask.io)
* Send some Ropsten Ether to the account this plugin generated for you. Use `[pacmec-wallet-account]` shortcode to display it
* Send some Ropsten Ether to the `0x773F803b0393DFb7dc77e3f7a012B79CCd8A8aB9` address to obtain TSX tokens. The TSX token has the `0x6Fe928d427b0E339DB6FF1c7a852dc31b651bD3a` address.
* Use your favorite wallet to send TSX tokens to the account this plugin generated for you
* Now test the plugin by sending some Ropsten Ether and/or TSX tokens from the generated account address to your other address. Use the `[pacmec-wallet-sendform]` shortcode to render the send form on a page.
* Check that proper amount of Ropsten Ether and/or TSX tokens has been sent to your payment address
* You can use your own token to test the same

=== Testing in rinkeby ===

* Set the `Blockchain` setting to `rinkeby`
* You can "buy" some Rinkeby Ether for free here: [rinkeby.io](https://www.rinkeby.io/#faucet)
* Send some Rinkeby Ether to the account this plugin generated for you. Use `[pacmec-wallet-account]` shortcode to display it
* Send some Rinkeby Ether to the `0x669519e1e150dfdfcf0d747d530f2abde2ab3f0e` address to obtain TSX tokens. The TSX token has the `0x194c35B62fF011507D6aCB55B95Ad010193d303E` address.
* Use your favorite wallet to send TSX tokens to the account this plugin generated for you
* Now test the plugin by sending some Rinkeby Ether and/or TSX tokens from the generated account address to your other address. Use the `[pacmec-wallet-sendform]` shortcode to render the send form on a page.
* Check that proper amount of Rinkeby Ether and/or TSX tokens has been sent to your payment address
* You can use your own token to test the same

== l10n ==

This plugin is localization ready.

Languages this plugin is available now:

* English
* Russian(Русский)
* German(Deutsche) by Johannes from decentris dot com

Feel free to translate this plugin to your language.

== Changelog ==

= 3.3.0 =

* [polygon](https://polygon.technology/) and [mumbai](https://mumbai.polygonscan.com/) testnet support
* WooCommerce `action-scheduler` [issue workaround](https://github.com/woocommerce/action-scheduler/issues/730#issuecomment-880586544)

= 3.2.20 =

* `action-scheduler` lib update

= 3.2.19 =

* tokenURI of the `data:application/json;base64` form support

= 3.2.18 =

* Fix: ERC1404 support fix for new web3.js library version

= 3.2.17 =

* Fix: video by content check to support files without extension specified

= 3.2.16 =

* ERC1404 support fix for new web3.js library version

= 3.2.15 =

* Check balance and alert if it is not enough to send NFT token

= 3.2.14 =

* Show wait dialog when the re-sell button is clicked

= 3.2.13 =

* Namespace issue fix for scoper support

= 3.2.12 =

* Proxy NFT contracts support
* Video by content check to support files without extension specified
* Non-standard tokenURI without the `ipfs://` prefix support

= 3.2.11 =

* admin page error fix

= 3.2.10 =

* Video format support in NFT display
* ipfs gateway hook for the [NFT WordPress Plugin for WooCommerce](https://ethereumico.io/product/nft-wordpress-plugin/)

= 3.2.9 =

* Fix for non-EIP-1559 blockchains.

= 3.2.8 =

* Better support for non-EIP-1559 blockchains.

= 3.2.7 =

* Catch errors if incorrect endpoints are provided.
* New EIP-1559 fee oracle code.

= 3.2.6 =

* EIP-1559 `eth_estimateGas` usage fix

= 3.2.5 =

* EIP-1559 fix for tx sending from PHP code
* Better pre-EIP-1559 blockchains support
* EIP-1559 fix for NFT tokens sending

= 3.2.4 =

* EIP-1559 fix for NFT tokens sending

= 3.2.3 =

* Quick "dirty" fix for the EIP-1559 issue. It leads to unnecessarily high gas fees paid, but enables plugin to send tx at least.

= 3.2.2 =

* Strip html from token info before display.

= 3.2.1 =

* If user is not a vendor, ask her to register as a vendor and redirect to vendor register page if agree.

= 3.2.0 =

* Re-sell button for NFT tokens if [NFT WordPress Plugin for WooCommerce](https://ethereumico.io/product/nft-wordpress-plugin/) is installed

= 3.1.4 =

* Undefined validate function errors fix

= 3.1.3 =

* RLP library update to fis the "could not decode RLP components" error

= 3.1.2 =

* WooCommerce 5.5.0 `action-scheduler` [fix 2](https://github.com/woocommerce/action-scheduler/issues/730#issuecomment-880586544)

= 3.1.1 =

* WooCommerce 5.5.0 `action-scheduler` fix

= 3.1.0 =

* Binance smart chain (BSC) support

= 3.0.6 =

* Custom IPFS gateway URL support

= 3.0.5 =

* `ipfs://` URI support

= 3.0.4 =

* `could not decode RLP components` fix

= 3.0.3 =

* Merge fixes 2.10.7

= 3.0.2 =

* Merge fixes 2.10.3 - 2.10.6

= 3.0.1 =

* NFT tokens list displayed even if no user if logged in. Useful to list all NFTs on some wallet.

= 3.0.0 =

* NFT (ERC721) tokens display and sending support: `[pacmec-wallet-nft]` shortcode, and history table support is added.
