<?php

namespace Ethereumico\Epg;

use  Ethereumico\Epg\Dependencies\Web3\Web3 ;
use  Ethereumico\Epg\Dependencies\Web3\Providers\HttpProvider ;
use  Ethereumico\Epg\Dependencies\Web3\RequestManagers\HttpRequestManager ;
use  Ethereumico\Epg\Dependencies\Web3\Contract ;
use  Ethereumico\Epg\PaymentReceivedEmail ;
class Main
{
    /**
     * The base URL of the plugin.
     *
     * @var string
     */
    public  $base_url ;
    /**
     * The base path of the plugin files.
     *
     * @var string
     */
    public  $base_path ;
    /**
     * The Gateway smart contract ABI
     *
     * @var string The Gateway smart contract ABI
     * @see http://www.webtoolkitonline.com/json-minifier.html
     */
    public  $gatewayContractABI = '[{"constant":true,"inputs":[],"name":"maxFee","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"},{"name":"","type":"bytes16"},{"name":"","type":"address"}],"name":"currencyStorage","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"interfaceHash","type":"bytes32"},{"name":"account","type":"address"}],"name":"canImplementInterfaceForAddress","outputs":[{"name":"","type":"bytes32"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"account","type":"address"}],"name":"isAdmin","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"},{"name":"","type":"bytes16"},{"name":"","type":"uint256"}],"name":"payment","outputs":[{"name":"buyerAddress","type":"address"},{"name":"value","type":"uint256"},{"name":"sellerValue","type":"uint256"},{"name":"currency","type":"address"},{"name":"paymentDate","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"},{"name":"","type":"bytes16"},{"name":"","type":"address"}],"name":"tokenAddressNum","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"},{"name":"","type":"bytes16"},{"name":"","type":"uint256"}],"name":"tokenNumAddress","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"},{"name":"","type":"bytes16"}],"name":"tokenNumber","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"totalFeeValue","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"feeAccount2","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"account","type":"address"}],"name":"addAdmin","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[],"name":"renounceOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"feePercent","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[],"name":"renounceAdmin","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"owner","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"isOwner","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"feeAccount1","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"finalized","outputs":[{"name":"","type":"bool"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"","type":"bytes16"}],"name":"affiliate","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"","type":"bytes16"}],"name":"affiliateDev","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"}],"name":"affiliateDevTotalFee","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"feeAccountToken","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"newOwner","type":"address"}],"name":"transferOwnership","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"affiliatePercent","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"},{"name":"","type":"bytes16"}],"name":"refundDay","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"feeAffiliateDevPercent","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"inputs":[{"name":"_subscription","type":"address"}],"payable":false,"stateMutability":"nonpayable","type":"constructor"},{"payable":true,"stateMutability":"payable","type":"fallback"},{"anonymous":false,"inputs":[{"indexed":true,"name":"_currencyAddress","type":"address"},{"indexed":true,"name":"_sellerAddress","type":"address"},{"indexed":false,"name":"_siteAddress","type":"bytes16"},{"indexed":true,"name":"_orderId","type":"uint256"},{"indexed":false,"name":"_value","type":"uint256"}],"name":"PaymentOccured","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"name":"_methodSignature","type":"bytes4"},{"indexed":false,"name":"_methodSignatureExpected1","type":"bytes4"},{"indexed":false,"name":"_methodSignatureExpected2","type":"bytes4"},{"indexed":false,"name":"_methodSignatureExpected3","type":"bytes4"},{"indexed":false,"name":"_from","type":"address"},{"indexed":false,"name":"_value","type":"uint256"},{"indexed":false,"name":"_data","type":"bytes"}],"name":"DebugLog","type":"event"},{"anonymous":false,"inputs":[],"name":"Finalized","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"account","type":"address"}],"name":"AdminAdded","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"account","type":"address"}],"name":"AdminRemoved","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"previousOwner","type":"address"},{"indexed":true,"name":"newOwner","type":"address"}],"name":"OwnershipTransferred","type":"event"},{"constant":false,"inputs":[{"name":"_siteAddress","type":"bytes16"},{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"withdrawEthSeller","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"withdrawEthAffiliate","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_siteAddress","type":"bytes16"},{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"withdrawEthVendor","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_value","type":"uint256"}],"name":"withdrawEthOwner","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_tokenAddress","type":"address"},{"name":"_siteAddress","type":"bytes16"},{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"withdrawTokenSeller","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_tokenAddress","type":"address"},{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"withdrawTokenAffiliate","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_tokenAddress","type":"address"},{"name":"_siteAddress","type":"bytes16"},{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"withdrawTokenVendor","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_tokenAddress","type":"address"},{"name":"_value","type":"uint256"}],"name":"withdrawTokenOwner","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"_sellerAddress","type":"address"},{"name":"_siteAddress","type":"bytes16"}],"name":"getSumEthSeller","outputs":[{"name":"_sumEth","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_affiliateAddress","type":"address"}],"name":"getSumEthAffiliate","outputs":[{"name":"sumEth","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_vendorAddress","type":"address"},{"name":"_siteAddress","type":"bytes16"}],"name":"getSumEthVendor","outputs":[{"name":"_sumEth","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_ownerAddress","type":"address"}],"name":"getSumEthOwner","outputs":[{"name":"_sumEth","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_sellerAddress","type":"address"},{"name":"_siteAddress","type":"bytes16"}],"name":"getTokenNumberSeller","outputs":[{"name":"_num","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_affiliateAddress","type":"address"}],"name":"getTokenNumberAffiliate","outputs":[{"name":"_num","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_vendorAddress","type":"address"},{"name":"_siteAddress","type":"bytes16"}],"name":"getTokenNumberVendor","outputs":[{"name":"_num","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_ownerAddress","type":"address"}],"name":"getTokenNumberOwner","outputs":[{"name":"_num","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_sellerAddress","type":"address"},{"name":"_siteAddress","type":"bytes16"},{"name":"_id","type":"uint256"}],"name":"getTokenAddressSeller","outputs":[{"name":"_tokenAddress","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_affiliateAddress","type":"address"},{"name":"_id","type":"uint256"}],"name":"getTokenAddressAffiliate","outputs":[{"name":"_tokenAddress","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_vendorAddress","type":"address"},{"name":"_siteAddress","type":"bytes16"},{"name":"_id","type":"uint256"}],"name":"getTokenAddressVendor","outputs":[{"name":"_tokenAddress","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_ownerAddress","type":"address"},{"name":"_id","type":"uint256"}],"name":"getTokenAddressOwner","outputs":[{"name":"_tokenAddress","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_sellerAddress","type":"address"},{"name":"_siteAddress","type":"bytes16"},{"name":"_id","type":"uint256"}],"name":"getTokenSumSeller","outputs":[{"name":"_tokenSum","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_affiliateAddress","type":"address"},{"name":"_id","type":"uint256"}],"name":"getTokenSumAffiliate","outputs":[{"name":"_tokenSum","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_vendorAddress","type":"address"},{"name":"_siteAddress","type":"bytes16"},{"name":"_id","type":"uint256"}],"name":"getTokenSumVendor","outputs":[{"name":"_tokenSum","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_ownerAddress","type":"address"},{"name":"_id","type":"uint256"}],"name":"getTokenSumOwner","outputs":[{"name":"_tokenSum","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_siteAddress","type":"bytes16"},{"name":"_refundPeriod","type":"uint256"}],"name":"refundSupport","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_siteAddress","type":"bytes16"},{"name":"_orderId","type":"uint256"}],"name":"orderRefund","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_siteAddress","type":"bytes16"},{"name":"_affiliate","type":"address"}],"name":"setAffiliate","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_siteAddress","type":"bytes16"},{"name":"_addressAffiliate","type":"address"}],"name":"setAffiliateAddress","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[],"name":"finalizeGateway","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"_sellerAddress","type":"address"},{"name":"_siteAddress","type":"bytes16"},{"name":"_orderId","type":"uint256"}],"name":"getBuyerAddressPayment","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_sellerAddress","type":"address"},{"name":"_siteAddress","type":"bytes16"},{"name":"_orderId","type":"uint256"}],"name":"getValuePayment","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_sellerAddress","type":"address"},{"name":"_siteAddress","type":"bytes16"},{"name":"_orderId","type":"uint256"}],"name":"getCurrencyPayment","outputs":[{"name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"_feePercent","type":"uint256"}],"name":"setFeePercent","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_feeAffiliateDevPercent","type":"uint256"}],"name":"setFeeAffiliateDevPercent","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"},{"name":"","type":"bytes16"},{"name":"","type":"uint256"}],"name":"payTokenA_Fake","outputs":[],"payable":false,"stateMutability":"pure","type":"function"},{"constant":false,"inputs":[{"name":"_tokenAddress","type":"address"},{"name":"_sellerAddress","type":"address"},{"name":"_siteAddress","type":"bytes16"},{"name":"_orderId","type":"uint256"},{"name":"_value","type":"uint256"}],"name":"payTokenA","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"name":"","type":"address"},{"name":"","type":"bytes16"},{"name":"","type":"uint256"},{"name":"","type":"address"},{"name":"","type":"uint256"}],"name":"payTokenVA_Fake","outputs":[],"payable":false,"stateMutability":"pure","type":"function"},{"constant":false,"inputs":[{"name":"_tokenAddress","type":"address"},{"name":"_sellerAddress","type":"address"},{"name":"_siteAddress","type":"bytes16"},{"name":"_orderId","type":"uint256"},{"name":"_value","type":"uint256"},{"name":"_masterVendor","type":"address"},{"name":"_masterVendorFee","type":"uint256"}],"name":"payTokenVA","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"","type":"address"},{"name":"_from","type":"address"},{"name":"","type":"address"},{"name":"_value","type":"uint256"},{"name":"_userData","type":"bytes"},{"name":"","type":"bytes"}],"name":"tokensReceived","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_from","type":"address"},{"name":"_value","type":"uint256"},{"name":"_data","type":"bytes"}],"name":"tokenFallback","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_sellerAddress","type":"address"},{"name":"_siteAddress","type":"bytes16"},{"name":"_orderId","type":"uint256"}],"name":"payEthA","outputs":[],"payable":true,"stateMutability":"payable","type":"function"},{"constant":false,"inputs":[{"name":"_sellerAddress","type":"address"},{"name":"_siteAddress","type":"bytes16"},{"name":"_orderId","type":"uint256"},{"name":"_masterVendor","type":"address"},{"name":"_masterVendorFee","type":"uint256"}],"name":"payEthVA","outputs":[],"payable":true,"stateMutability":"payable","type":"function"},{"constant":false,"inputs":[{"name":"_addressSeller","type":"address"},{"name":"_siteAddress","type":"bytes16"}],"name":"buySubscription","outputs":[],"payable":true,"stateMutability":"payable","type":"function"},{"constant":false,"inputs":[{"name":"_to","type":"address"},{"name":"_siteAddress","type":"bytes16"}],"name":"transferEthToAccount","outputs":[],"payable":true,"stateMutability":"payable","type":"function"},{"constant":false,"inputs":[{"name":"_siteAddress","type":"bytes16"},{"name":"_to","type":"address"},{"name":"_tokenAddress","type":"address"},{"name":"_value","type":"uint256"}],"name":"transferTokenToAccount","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"}]' ;
    /**
     * The ERC20 smart contract ABI
     *
     * @var string The ERC20 smart contract ABI
     * @see http://www.webtoolkitonline.com/json-minifier.html
     */
    public  $erc20ContractABI = '[{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"name":"supply","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"name","outputs":[{"name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_owner","type":"address"}],"name":"balanceOf","outputs":[{"name":"balance","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"name":"_owner","type":"address"},{"name":"_spender","type":"address"}],"name":"allowance","outputs":[{"name":"remaining","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"anonymous":false,"inputs":[{"indexed":true,"name":"_owner","type":"address"},{"indexed":true,"name":"_spender","type":"address"},{"indexed":false,"name":"_value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"name":"_from","type":"address"},{"indexed":true,"name":"_to","type":"address"},{"indexed":false,"name":"_value","type":"uint256"}],"name":"Transfer","type":"event"},{"constant":false,"inputs":[{"name":"_spender","type":"address"},{"name":"_value","type":"uint256"}],"name":"approve","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"transfer","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"_from","type":"address"},{"name":"_to","type":"address"},{"name":"_value","type":"uint256"}],"name":"transferFrom","outputs":[{"name":"success","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"}]' ;
    /**
     * The ERC223 smart contract ABI
     *
     * @var string The ERC20 smart contract ABI
     * @see http://www.webtoolkitonline.com/json-minifier.html
     */
    public  $erc223ContractABI = '[{"anonymous":false,"inputs":[{"indexed":true,"name":"from","type":"address"},{"indexed":true,"name":"to","type":"address"},{"indexed":false,"name":"value","type":"uint256"},{"indexed":true,"name":"data","type":"bytes"}],"name":"Transfer","type":"event"},{"constant":true,"inputs":[{"name":"who","type":"address"}],"name":"balanceOf","outputs":[{"name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"name","outputs":[{"name":"_name","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"name":"_symbol","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"name":"_decimals","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"name":"_supply","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"name":"to","type":"address"},{"name":"value","type":"uint256"}],"name":"transfer","outputs":[{"name":"ok","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"to","type":"address"},{"name":"value","type":"uint256"},{"name":"data","type":"bytes"}],"name":"transfer","outputs":[{"name":"ok","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"name":"to","type":"address"},{"name":"value","type":"uint256"},{"name":"data","type":"bytes"},{"name":"custom_fallback","type":"string"}],"name":"transfer","outputs":[{"name":"ok","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"}]' ;
    /**
     * The ERC777 smart contract ABI
     *
     * @var string The ERC20 smart contract ABI
     * @see http://www.webtoolkitonline.com/json-minifier.html
     */
    public  $erc777ContractABI = '[{"constant":true,"inputs":[],"name":"defaultOperators","outputs":[{"internalType":"address[]","name":"","type":"address[]"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"granularity","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"sender","type":"address"},{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"},{"internalType":"bytes","name":"data","type":"bytes"},{"internalType":"bytes","name":"operatorData","type":"bytes"}],"name":"operatorSend","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"owner","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"operator","type":"address"}],"name":"authorizeOperator","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"recipient","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"},{"internalType":"bytes","name":"data","type":"bytes"}],"name":"send","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"operator","type":"address"},{"internalType":"address","name":"tokenHolder","type":"address"}],"name":"isOperatorFor","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"operator","type":"address"}],"name":"revokeOperator","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"account","type":"address"},{"internalType":"uint256","name":"amount","type":"uint256"},{"internalType":"bytes","name":"data","type":"bytes"},{"internalType":"bytes","name":"operatorData","type":"bytes"}],"name":"operatorBurn","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"uint256","name":"amount","type":"uint256"},{"internalType":"bytes","name":"data","type":"bytes"}],"name":"burn","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"operator","type":"address"},{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"},{"indexed":false,"internalType":"bytes","name":"data","type":"bytes"},{"indexed":false,"internalType":"bytes","name":"operatorData","type":"bytes"}],"name":"Sent","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"operator","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"},{"indexed":false,"internalType":"bytes","name":"data","type":"bytes"},{"indexed":false,"internalType":"bytes","name":"operatorData","type":"bytes"}],"name":"Minted","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"operator","type":"address"},{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount","type":"uint256"},{"indexed":false,"internalType":"bytes","name":"data","type":"bytes"},{"indexed":false,"internalType":"bytes","name":"operatorData","type":"bytes"}],"name":"Burned","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"operator","type":"address"},{"indexed":true,"internalType":"address","name":"tokenHolder","type":"address"}],"name":"AuthorizedOperator","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"operator","type":"address"},{"indexed":true,"internalType":"address","name":"tokenHolder","type":"address"}],"name":"RevokedOperator","type":"event"}]' ;
    /**
     * Constructor.
     *
     * Store variables for use later.
     *
     * @param string $base_url  The base URL of the plugin.
     */
    function __construct( $base_url, $base_path )
    {
        $this->base_url = $base_url;
        $this->base_path = $base_path;
    }
    
    /**
     * Trigger the plugin to run.
     */
    public function run()
    {
        add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
        add_action( 'init', array( $this, 'on_init' ) );
        add_filter( 'woocommerce_email_classes', array( $this, 'register_eth_payment_completed_email' ) );
        add_action(
            'woocommerce_email_order_details',
            array( $this, 'email_content' ),
            1,
            4
        );
        // add on-hold status to a list of statuses that needs payment
        add_filter(
            'woocommerce_valid_order_statuses_for_payment',
            array( $this, 'valid_order_statuses_for_payment' ),
            10,
            2
        );
        add_filter(
            'woocommerce_payment_complete_order_status',
            array( $this, 'woocommerce_payment_complete_order_status_hook' ),
            1000,
            3
        );
        // @see https://stackoverflow.com/a/41987077/4256005
        add_filter(
            'woocommerce_email_customer_details_fields',
            array( $this, 'woocommerce_email_customer_details_fields_hook' ),
            20,
            3
        );
    }
    
    public function woocommerce_email_customer_details_fields_hook( $fields, $sent_to_admin = false, $order = null )
    {
        if ( is_null( $order ) ) {
            return $fields;
        }
        $order_id = $order->get_id();
        $order_items = $order->get_items();
        foreach ( $order_items as $item ) {
            $product_id = $item['product_id'];
            $txhash = get_post_meta( $order_id, 'ethereum_txhash', true );
            if ( !empty($txhash) ) {
                $fields['cryptocurrency_ethereum_txhash'] = array(
                    'label' => __( 'Payment Crypto Tx Hash', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ),
                    'value' => $txhash,
                );
            }
        }
        return $fields;
    }
    
    /**
     * Designed to run actions required at WordPress' init hook.
     *
     * Triggers localisation of the plugin.
     */
    public function on_init()
    {
        // ETHEREUM_WALLET plugin integration
        if ( function_exists( 'ETHEREUM_WALLET_send_transaction' ) && get_current_user_id() > 0 ) {
            add_action( 'wp_loaded', array( $this, 'wp_loaded_hook' ), 20 );
        }
    }
    
    public function woocommerce_payment_complete_order_status_hook( $status, $order_id, $order )
    {
        $gateway = wc_get_payment_gateway_by_order( $order );
        if ( !$gateway instanceof \Ethereumico\Epg\Gateway ) {
            return $status;
        }
        $payment_complete_order_status = esc_attr( $gateway->get_setting_( 'payment_complete_order_status' ) );
        if ( empty($payment_complete_order_status) ) {
            return $status;
        }
        $payment_complete_order_status = substr( $payment_complete_order_status, 3 );
        // wc-pending -> pending
        return $payment_complete_order_status;
    }
    
    public function valid_order_statuses_for_payment( $statuses = array() )
    {
        if ( !$statuses ) {
            $statuses = array( 'pending', 'failed' );
        }
        array_push( $statuses, 'on-hold' );
        // expired orders are marked as failed
        // it is important to skip them from processing
        // to clean up cron tasks for them
        $statuses = array_filter( $statuses, function ( $s ) {
            return $s != 'failed';
        } );
        $statuses = array_values( $statuses );
        //        $this->log("statuses: " . print_r($statuses, true));
        return $statuses;
    }
    
    public function wp_loaded_hook()
    {
        global  $wp ;
        //        $gateways = WC_Payment_Gateways::instance()->payment_gateways();
        //        if (!gateways) {
        //            return;
        //        }
        //        if (!isset($gateways['ether-and-erc20-tokens-woocommerce-payment-gateway'])) {
        //            return;
        //        }
        //        $this_ = $gateways['ether-and-erc20-tokens-woocommerce-payment-gateway'];
        if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) ) {
            return;
        }
        if ( empty($_POST['action']) ) {
            return;
        }
        if ( 'save_order_txhash' !== $_POST['action'] && 'update_confirmed_status' !== $_POST['action'] ) {
            return;
        }
        
        if ( function_exists( 'wc_nocache_headers' ) ) {
            wc_nocache_headers();
        } else {
            nocache_headers();
        }
        
        $nonce_value = '';
        
        if ( isset( $_REQUEST['save_order_txhash_nonce'] ) ) {
            $nonce_value = $_REQUEST['save_order_txhash_nonce'];
        } else {
            if ( isset( $_REQUEST['_wpnonce'] ) ) {
                $nonce_value = $_REQUEST['_wpnonce'];
            }
        }
        
        
        if ( !wp_verify_nonce( $nonce_value, 'save_order_txhash' ) ) {
            $this->log( "wp_loaded_hook save_order_txhash: bad nonce detected: " . $nonce_value );
            return;
        }
        
        
        if ( !isset( $_REQUEST['order_id'] ) ) {
            $this->log( "order_id not set" );
            return;
        }
        
        $order_id = sanitize_text_field( $_REQUEST['order_id'] );
        
        if ( empty($order_id) ) {
            $this->log( "empty order_id" );
            return;
        }
        
        
        if ( !is_numeric( $order_id ) ) {
            $this->log( "non-numeric order_id: " . $order_id );
            return;
        }
        
        $order_id = intval( $order_id );
        $wpj_payment_type = '';
        if ( isset( $_REQUEST['wpj_payment_type'] ) ) {
            $wpj_payment_type = sanitize_text_field( $_REQUEST['wpj_payment_type'] );
        }
        ob_start();
        
        if ( 'save_order_txhash' === $_POST['action'] ) {
            
            if ( !isset( $_REQUEST['txhash'] ) ) {
                $this->log( "txhash not set" );
                return;
            }
            
            $txhash = sanitize_text_field( $_REQUEST['txhash'] );
            
            if ( empty($txhash) ) {
                $this->log( "empty txhash" );
                return;
            }
            
            
            if ( 66 != strlen( $txhash ) ) {
                $this->log( "strlen txhash != 66: " . $txhash );
                return;
            }
            
            
            if ( '0x' != substr( $txhash, 0, 2 ) ) {
                $this->log( "startsWith txhash != 0x: " . $txhash );
                return;
            }
            
            $this->set_order_txhash( $order_id, $txhash, $wpj_payment_type );
        } else {
            if ( 'update_confirmed_status' === $_POST['action'] ) {
                $this->update_confirmed_status( $order_id );
            }
        }
        
        $errors = ob_get_contents();
        if ( !empty($errors) ) {
            $this->log( "save_order_txhash_endpoint_action PHP Warnings: " . $errors );
        }
        ob_end_clean();
    }
    
    public function getAddressSite16()
    {
        $addressSite = home_url();
        $addressSite = str_replace( 'http://', '', $addressSite );
        $addressSite = str_replace( 'https://', '', $addressSite );
        $addressSite = hash( 'ripemd128', $addressSite );
        return '0x' . $addressSite;
    }
    
    public function getGatewayContractAddress( $gateway )
    {
        $blockchainNetwork = $this->getBlockchainNetwork( $gateway );
        switch ( $blockchainNetwork ) {
            case 'mainnet':
                return '0xd0E4e3A739A454386DA9957432b170C006327B0d';
                // v4
                //                return '0x75Cc8dB78d0Ea491fdF2f254ADAaFcB46BBEDE13'; // v3
                //return '0x3E0371bcb61283c036A48274AbDe0Ab3DA107a50'; // v2
            // v4
            //                return '0x75Cc8dB78d0Ea491fdF2f254ADAaFcB46BBEDE13'; // v3
            //return '0x3E0371bcb61283c036A48274AbDe0Ab3DA107a50'; // v2
            case 'ropsten':
                return '0x3B054916f1898DA6dC46Eb65ED77648339C35CA0';
            case 'rinkeby':
                return '0x634c6ce741e8c685493B84Cd01E66EAF6f376199';
            case 'bsc':
                return '0x77913766661274651d367A013861B64111E77A3f';
            case 'bsctest':
                return '0x08Be888603426580C4097AAEEE72EeABe8dAa31f';
            case 'polygon':
                return '0x77913766661274651d367A013861B64111E77A3f';
            case 'mumbai':
                return '0xf2604e68c9F5756f3643aA569E5D0520D21a152A';
        }
        return __( 'Unknown network name in configuration settings', 'ether-and-erc20-tokens-woocommerce-payment-gateway' );
    }
    
    public function getTokenRate_from_API(
        $rateSourceId,
        $tokenAddress,
        $tokenSymbol,
        $eth_value,
        $gateway
    )
    {
        try {
            $base_currency_ticker = esc_attr( $gateway->get_setting_( 'currency_ticker', 'ETH' ) );
            $rateSource = apply_filters(
                'epg_rate_source_create',
                null,
                $gateway->id,
                $rateSourceId,
                $tokenAddress,
                $tokenSymbol,
                $base_currency_ticker
            );
            if ( is_null( $rateSource ) ) {
                return null;
            }
            return $rateSource->get_rate( $eth_value );
        } catch ( \Exception $ex ) {
            $this->log( "getTokenRate_from_API: " . $ex->getMessage() );
        }
        return null;
    }
    
    // adjust token rates given in the base currency, not in Ether
    public function get_tokens_supported(
        $tokens_supported,
        $eth_rate,
        $eth_value,
        $currency,
        $gateway
    )
    {
        $tokens_supported_new = [];
        $tokensArr = explode( ",", $tokens_supported );
        if ( !$tokensArr ) {
            return implode( ',', $tokens_supported_new );
        }
        foreach ( $tokensArr as $tokenStr ) {
            $tokenPartsArr = explode( ":", $tokenStr );
            if ( count( $tokenPartsArr ) < 3 ) {
                continue;
            }
            $tokenSymbol = $tokenPartsArr[0];
            $tokenAddress = $tokenPartsArr[1];
            $rate = null;
            
            if ( count( $tokenPartsArr ) >= 5 && !empty($tokenPartsArr[4]) && __( 'Fixed', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ) !== $tokenPartsArr[4] ) {
                // Take rate from API endpoint
                $rateSourceId = $tokenPartsArr[4];
                $rate = $this->getTokenRate_from_API(
                    $rateSourceId,
                    $tokenAddress,
                    $tokenSymbol,
                    $eth_value,
                    $gateway
                );
            }
            
            
            if ( is_null( $rate ) ) {
                $rate = $tokenPartsArr[2];
                $pos = strpos( $rate, $currency );
                
                if ( $pos !== FALSE ) {
                    $rate = doubleval( substr( $rate, 0, strlen( $rate ) - strlen( $currency ) ) );
                    // e.g. rate=1USD, eth_rate=0.01ETH => 0.01
                    $rate = $rate * $eth_rate;
                }
            
            }
            
            $tokens_supported_new_elem = [ $tokenSymbol, $tokenAddress, $rate ];
            for ( $i = 3 ;  $i < count( $tokenPartsArr ) ;  $i++ ) {
                $tokens_supported_new_elem[] = $tokenPartsArr[$i];
            }
            $tokens_supported_new[] = implode( ':', $tokens_supported_new_elem );
        }
        return implode( ',', $tokens_supported_new );
    }
    
    public function getBlockchainNetwork( $gateway )
    {
        $blockchainNetwork = esc_attr( $gateway->get_setting_( 'blockchain_network', 'mainnet' ) );
        $web3Endpoint = $gateway->get_setting_( 'web3Endpoint', '' );
        
        if ( !empty($web3Endpoint) ) {
            $providerUrl = $web3Endpoint;
            try {
                $requestManager = new HttpRequestManager( $providerUrl, 10 );
                $web3 = new Web3( new HttpProvider( $requestManager ) );
                $net = $web3->net;
                $_version = null;
                $_this = $this;
                $net->version( function ( $err, $version ) use( &$_version, &$gateway, &$_this ) {
                    
                    if ( $err !== null ) {
                        $_this->log( "Failed to get blockchain version: " . $err, $gateway );
                        return;
                    }
                    
                    $_version = intval( $version );
                } );
                switch ( $_version ) {
                    case 1:
                        return 'mainnet';
                    case 3:
                        return 'ropsten';
                    case 4:
                        return 'rinkeby';
                    case 56:
                        return 'bsc';
                    case 97:
                        return 'bsctest';
                    case 137:
                        return 'polygon';
                    case 80001:
                        return 'mumbai';
                }
            } catch ( \Exception $ex ) {
                $this->log( "getBlockchainNetwork: " . $ex->getMessage(), $gateway );
            }
        }
        
        return $blockchainNetwork;
    }
    
    public function get_txhash_path( $txHash, $gateway )
    {
        $view_transaction_url = $this->get_txhash_path_template( $gateway );
        return sprintf( $view_transaction_url, $txHash );
    }
    
    public function get_txhash_path_template( $gateway )
    {
        $view_transaction_url = $gateway->get_setting_( 'view_transaction_url', '' );
        if ( !empty($view_transaction_url) ) {
            return $view_transaction_url;
        }
        $view_transaction_url = '%s';
        $blockchainNetwork = $this->getBlockchainNetwork( $gateway );
        switch ( $blockchainNetwork ) {
            case 'mainnet':
                $view_transaction_url = 'https://etherscan.io/tx/%s';
                break;
            case 'ropsten':
                $view_transaction_url = 'https://ropsten.etherscan.io/tx/%s';
                break;
            case 'rinkeby':
                $view_transaction_url = 'https://rinkeby.etherscan.io/tx/%s';
                break;
            case 'bsc':
                $view_transaction_url = 'https://bscscan.com/tx/%s';
                break;
            case 'bsctest':
                $view_transaction_url = 'https://testnet.bscscan.com/tx/%s';
                break;
            case 'polygon':
                $view_transaction_url = 'https://polygonscan.com/tx/%s';
                break;
            case 'mumbai':
                $view_transaction_url = 'https://mumbai.polygonscan.com/tx/%s';
                break;
            default:
                break;
        }
        return $view_transaction_url;
    }
    
    public function update_confirmed_status( $order_id )
    {
        $gateway = null;
        if ( !isset( $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway1'] ) ) {
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway1'] = new \Ethereumico\Epg\Gateway();
        }
        $gateway = $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway1'];
        $paymentSuccess = $gateway->complete_order( $order_id );
        if ( $paymentSuccess ) {
            $this->log( 'Order is payed: ' . $order_id, $gateway );
        }
    }
    
    public function set_order_txhash( $order_id, $txHash, $wpj_payment_type = '' )
    {
        $this->log( "set_order_txhash(order_id: {$order_id}, txHash: {$txHash}, wpj_payment_type: {$wpj_payment_type})" );
        
        if ( ('Jobster' == wp_get_theme()->name || 'Jobster' == wp_get_theme()->parent_theme) && !empty($wpj_payment_type) ) {
            //            $payment = [];
            //            $payment['order_id']         = $order_id;
            //            $payment['payment_type']     = $wpj_payment_type;
            //            $payment['transaction_id']   = $txHash;
            //            $payment['payment_response'] = '';
            //            $payment['payment_status']   = '';
            //            $payment['gateway']          = 'ether_and_erc20_tokens_payment_gateway';
            update_option( 'wpjobster_ether_and_erc20_tokens_payment_gateway_txhash_' . $order_id, $txHash );
            //            do_action( "wpjobster_store_payment_gateway_log", $payment );
        } else {
            
            if ( function_exists( 'wc_get_order' ) ) {
                $order = wc_get_order( $order_id );
                $gateway = wc_get_payment_gateway_by_order( $order );
                if ( !$gateway instanceof \Ethereumico\Epg\Gateway ) {
                    return;
                }
                $txHashPath = $this->get_txhash_path( $txHash, $gateway );
                $order->set_transaction_id( $txHash );
                $order->add_order_note( sprintf( __( 'Sent to blockchain. Transaction hash %1$s.', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ), sprintf( '<a target="_blank" href="%1$s">%2$s</a>', $txHashPath, $txHash ) ) );
                update_post_meta( $order_id, 'ethereum_txhash', sanitize_text_field( $txHash ) );
            }
        
        }
    
    }
    
    /**
     * Designed to run actions required at WordPress' plugins_loaded hook.
     *
     * - Register our gateway with WooCommerce.
     */
    public function on_plugins_loaded()
    {
        if ( !class_exists( '\\WC_Payment_Gateway', false ) ) {
            return;
        }
        add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateway' ) );
    }
    
    /**
     * Register our payment completed email.
     */
    public function register_eth_payment_completed_email( $email_classes )
    {
        $email_classes['PWE_Payment_Completed'] = new PaymentReceivedEmail();
        return $email_classes;
    }
    
    /**
     * Add the Gateway to WooCommerce.
     *
     * @param array $gateways  The current list of gateways.
     */
    public function add_gateway( $gateways )
    {
        $gateways[] = 'Ethereumico\\Epg\\Gateway';
        $gateways[] = 'Ethereumico\\Epg\\Gateway2';
        return $gateways;
    }
    
    /**
     * Add payment instructions to the "order on hold" email.
     */
    public function email_content(
        $order,
        $sent_to_admin,
        $plain_text,
        $email
    )
    {
        if ( !class_exists( '\\WC_Email_Customer_On_Hold_Order', false ) ) {
            return;
        }
        // We only interfere in the order on hold email.
        if ( !$email instanceof \WC_Email_Customer_On_Hold_Order ) {
            return;
        }
        // Check that the order was paid with this gateway.
        
        if ( is_callable( array( $order, 'get_payment_method' ) ) ) {
            $payment_method = $order->get_payment_method();
        } else {
            $payment_method = $order->payment_method;
        }
        
        if ( 'ether-and-erc20-tokens-woocommerce-payment-gateway' !== $payment_method ) {
            return;
        }
        // Retrieve the info we need.
        $gateway = wc_get_payment_gateway_by_order( $order );
        if ( !$gateway instanceof \Ethereumico\Epg\Gateway ) {
            return;
        }
        $payment_address = $gateway->get_setting_( 'payment_address', '' );
        $base_currency_ticker = $gateway->get_setting_( 'currency_ticker', 'ETH' );
        
        if ( is_callable( array( $order, 'get_id' ) ) ) {
            $order_id = $order->get_id();
        } else {
            $order_id = $order->id;
        }
        
        $eth_value = get_post_meta( $order_id, '_epg_eth_value', true );
        if ( empty($payment_address) || false === $eth_value ) {
            return;
        }
        ?>
        <h2>
            <?php 
        esc_html_e( __( 'Payment details', 'ether-and-erc20-tokens-woocommerce-payment-gateway' ) );
        ?>
        </h2>
        <ul>
            <li><?php 
        _e( 'Amount', 'ether-and-erc20-tokens-woocommerce-payment-gateway' );
        ?>: <strong><?php 
        esc_html_e( $eth_value );
        ?></strong> <?php 
        echo  $base_currency_ticker ;
        ?></li>
            <li><a href="<?php 
        esc_attr_e( $order->get_checkout_order_received_url() );
        ?>" target="_blank" rel="nofollow"><?php 
        _e( 'Payment page', 'ether-and-erc20-tokens-woocommerce-payment-gateway' );
        ?></a></li>
        </ul>
        <?php 
    }
    
    /**
     * Log information using the WC_Logger class.
     *
     * Will do nothing unless debug is enabled.
     *
     * @param string $msg   The message to be logged.
     */
    public function log( $msg, $gateway = null )
    {
        static  $logger = false ;
        $debug = 'yes';
        if ( $gateway ) {
            $debug = $gateway->get_setting_( 'debug', '' );
        }
        // Bail if debug isn't on.
        if ( 'yes' !== $debug ) {
            return;
        }
        // Create a logger instance if we don't already have one.
        if ( false === $logger ) {
            /**
             * Check if WooCommerce is active
             * https://wordpress.stackexchange.com/a/193908/137915
             **/
            
            if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && class_exists( "\\WC_Logger", false ) ) {
                $logger = new \WC_Logger();
            } else {
                $logger = new \Ethereumico\Epg\Logger();
            }
        
        }
        $logger->add( 'ether-and-erc20-tokens-woocommerce-payment-gateway', $msg );
    }

}
class Logger
{
    /**
     * Add a log entry.
     *
     * This is not the preferred method for adding log messages. Please use log() or any one of
     * the level methods (debug(), info(), etc.). This method may be deprecated in the future.
     *
     * @param string $handle
     * @param string $message
     * @param string $level
     *
     * @see https://docs.woocommerce.com/wc-apidocs/source-class-WC_Logger.html#105
     *
     * @return bool
     */
    public function add( $handle, $message, $level = 'unused' )
    {
        error_log( $handle . ': ' . $message );
        return true;
    }

}