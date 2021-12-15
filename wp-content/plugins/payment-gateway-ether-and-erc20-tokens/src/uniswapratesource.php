<?php

namespace Ethereumico\Epg;

use \Ethereumico\Epg\Dependencies\phpseclib3\Math\BigInteger;
use \Ethereumico\Epg\Dependencies\Web3\Web3;
use \Ethereumico\Epg\Dependencies\Web3\Providers\HttpProvider;
use \Ethereumico\Epg\Dependencies\Web3\RequestManagers\HttpRequestManager;
use \Ethereumico\Epg\Dependencies\Web3\Contract;


class UniswapV2RateSource {

    private static $uniswapFactoryAddress = '0x5C69bEe701ef814a2B6a3EDD4B1652CB9cc5aA6f';
    private static $uniswapFactoryABI = '[{"inputs":[{"internalType":"address","name":"_feeToSetter","type":"address"}],"payable":false,"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"token0","type":"address"},{"indexed":true,"internalType":"address","name":"token1","type":"address"},{"indexed":false,"internalType":"address","name":"pair","type":"address"},{"indexed":false,"internalType":"uint256","name":"","type":"uint256"}],"name":"PairCreated","type":"event"},{"constant":true,"inputs":[{"internalType":"uint256","name":"","type":"uint256"}],"name":"allPairs","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"allPairsLength","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"tokenA","type":"address"},{"internalType":"address","name":"tokenB","type":"address"}],"name":"createPair","outputs":[{"internalType":"address","name":"pair","type":"address"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"feeTo","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"feeToSetter","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"","type":"address"},{"internalType":"address","name":"","type":"address"}],"name":"getPair","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"_feeTo","type":"address"}],"name":"setFeeTo","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"_feeToSetter","type":"address"}],"name":"setFeeToSetter","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"}]';
    private static $uniswapPairABI = '[{"inputs":[],"payable":false,"stateMutability":"nonpayable","type":"constructor"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"owner","type":"address"},{"indexed":true,"internalType":"address","name":"spender","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Approval","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"sender","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount0","type":"uint256"},{"indexed":false,"internalType":"uint256","name":"amount1","type":"uint256"},{"indexed":true,"internalType":"address","name":"to","type":"address"}],"name":"Burn","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"sender","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount0","type":"uint256"},{"indexed":false,"internalType":"uint256","name":"amount1","type":"uint256"}],"name":"Mint","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"sender","type":"address"},{"indexed":false,"internalType":"uint256","name":"amount0In","type":"uint256"},{"indexed":false,"internalType":"uint256","name":"amount1In","type":"uint256"},{"indexed":false,"internalType":"uint256","name":"amount0Out","type":"uint256"},{"indexed":false,"internalType":"uint256","name":"amount1Out","type":"uint256"},{"indexed":true,"internalType":"address","name":"to","type":"address"}],"name":"Swap","type":"event"},{"anonymous":false,"inputs":[{"indexed":false,"internalType":"uint112","name":"reserve0","type":"uint112"},{"indexed":false,"internalType":"uint112","name":"reserve1","type":"uint112"}],"name":"Sync","type":"event"},{"anonymous":false,"inputs":[{"indexed":true,"internalType":"address","name":"from","type":"address"},{"indexed":true,"internalType":"address","name":"to","type":"address"},{"indexed":false,"internalType":"uint256","name":"value","type":"uint256"}],"name":"Transfer","type":"event"},{"constant":true,"inputs":[],"name":"DOMAIN_SEPARATOR","outputs":[{"internalType":"bytes32","name":"","type":"bytes32"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"MINIMUM_LIQUIDITY","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"PERMIT_TYPEHASH","outputs":[{"internalType":"bytes32","name":"","type":"bytes32"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"","type":"address"},{"internalType":"address","name":"","type":"address"}],"name":"allowance","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"value","type":"uint256"}],"name":"approve","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"balanceOf","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"to","type":"address"}],"name":"burn","outputs":[{"internalType":"uint256","name":"amount0","type":"uint256"},{"internalType":"uint256","name":"amount1","type":"uint256"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"decimals","outputs":[{"internalType":"uint8","name":"","type":"uint8"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"factory","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"getReserves","outputs":[{"internalType":"uint112","name":"_reserve0","type":"uint112"},{"internalType":"uint112","name":"_reserve1","type":"uint112"},{"internalType":"uint32","name":"_blockTimestampLast","type":"uint32"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"_token0","type":"address"},{"internalType":"address","name":"_token1","type":"address"}],"name":"initialize","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"kLast","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"to","type":"address"}],"name":"mint","outputs":[{"internalType":"uint256","name":"liquidity","type":"uint256"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"name","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[{"internalType":"address","name":"","type":"address"}],"name":"nonces","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"owner","type":"address"},{"internalType":"address","name":"spender","type":"address"},{"internalType":"uint256","name":"value","type":"uint256"},{"internalType":"uint256","name":"deadline","type":"uint256"},{"internalType":"uint8","name":"v","type":"uint8"},{"internalType":"bytes32","name":"r","type":"bytes32"},{"internalType":"bytes32","name":"s","type":"bytes32"}],"name":"permit","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"price0CumulativeLast","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"price1CumulativeLast","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"to","type":"address"}],"name":"skim","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"uint256","name":"amount0Out","type":"uint256"},{"internalType":"uint256","name":"amount1Out","type":"uint256"},{"internalType":"address","name":"to","type":"address"},{"internalType":"bytes","name":"data","type":"bytes"}],"name":"swap","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"symbol","outputs":[{"internalType":"string","name":"","type":"string"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[],"name":"sync","outputs":[],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":true,"inputs":[],"name":"token0","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"token1","outputs":[{"internalType":"address","name":"","type":"address"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":true,"inputs":[],"name":"totalSupply","outputs":[{"internalType":"uint256","name":"","type":"uint256"}],"payable":false,"stateMutability":"view","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256","name":"value","type":"uint256"}],"name":"transfer","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"},{"constant":false,"inputs":[{"internalType":"address","name":"from","type":"address"},{"internalType":"address","name":"to","type":"address"},{"internalType":"uint256","name":"value","type":"uint256"}],"name":"transferFrom","outputs":[{"internalType":"bool","name":"","type":"bool"}],"payable":false,"stateMutability":"nonpayable","type":"function"}]';

	/**
	 * The source currency code.
	 *
	 * @var string
	 */
	private $source;

	/**
	 * The destination currency code.
	 *
	 * @var string
	 */
	private $destination;

	/**
	 * The JSON-RPC Node endpoint URL
	 *
	 * @var string
	 */
	private $providerUrl;

    /**
     * The cached rate value
     *
     * @var number Cached rate value
     */
    private $rate;

    private static $token2decimals = [];

    /**
	 * Construct the class. Store the source and destination.
	 *
	 * @param string $source       The source currency code.
	 * @param string $destination  The destination currency code.
	 */
	public function __construct( $source, $tokenSymbol, $destination, $providerUrl ) {
        if ('ETH' !== $destination) {
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('Bad destination: ' . $destination);
            throw new \Exception("Bad destination");
        }
		$this->source = $source;
		$this->destination = '0xC02aaA39b223FE8D0A0e5C4F27eAD9083C756Cc2'; //WETH
		$this->providerUrl = $providerUrl;
	}

	/**
	 * Retrieve the current exchange rate for this currency combination.
	 *
	 * Caches the value in a transient for 30 minutes (filterable), if no
	 * cached value available then calls out to API to retrieve current value.
	 *
	 * @return float  The exchange rate.
	 */
	public function get_rate($eth_value) {
        if (isset($this->rate) && !is_null($this->rate)) {
            return $this->rate;
        }

		$transient_key = 'epg_token_exchange_rate_' . strtolower($this->source) . '_' . $this->destination;
		// Check for a cached rate first. Use it if present.
		$rate = get_transient( $transient_key );
		if ( false !== $rate ) {
			return apply_filters( 'epg_token_exchange_rate', (float) $rate );
		}
		$rate = $this->get_rate_from_api($eth_value);
		set_transient( $transient_key, $rate
            , apply_filters( 'epg_token_exchange_rate_cache_duration_UniswapV2RateSource', 1800 ) );
		$this->rate = apply_filters( 'epg_token_exchange_rate', (float) $rate );
        return $this->rate;
	}

	/**
	 * Retrieve the pair contract address for sorce and target.
	 *
	 * @return float  The exchange rate.
	 */
	private function getPairContractAddress($source, $destination) {
        $abi = self::$uniswapFactoryABI;
        $contract = new Contract(new HttpProvider(new HttpRequestManager($this->providerUrl, 10/* seconds */)), $abi);

        $contractAddress = self::$uniswapFactoryAddress;
        $ret = null;
        $callback = function($error, $result) use(&$ret) {
            if ($error !== null) {
                $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log($error);
                return;
            }
//            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log("RESULT: " . print_r($result, true));
            foreach ($result as $key => $res) {
                $ret = $res;
//                $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log("key: " . $key . "; ret: " . $ret);
                break;
            }
        };
        // call contract function
//        $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log(
//            sprintf(
//                __('call contract %s method %s for market %s for order %s', 'ether-and-erc20-tokens-woocommerce-payment-gateway'), $contractAddress, $method, $marketAddress, $order_id
//            )
//        );
        // $destination is always ETH
        $contract->at($contractAddress)->call('getPair', $source, $destination, $callback);
//        $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log("ret2: " . $ret);
        return $ret;
	}

	/**
	 * Retrieve the pair contract address for sorce and target.
	 *
	 * @return float  The exchange rate.
	 */
	private function getPairReserves($pairContractAddress) {
        $abi = self::$uniswapPairABI;
        $contract = new Contract(new HttpProvider(new HttpRequestManager($this->providerUrl, 10/* seconds */)), $abi);

        $contractAddress = $pairContractAddress;
        $ret = null;
        $callback = function($error, $result) use(&$ret) {
            if ($error !== null) {
                $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log($error);
                return;
            }
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log("RESULT: " . print_r($result, true));
            $ret = $result;
        };
        // $destination is always ETH
        $contract->at($contractAddress)->call('getReserves', $callback);
        $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log("ret2: " . $ret);
        return $ret;
	}

    private function getNetworkId() {
        $_version = null;
        try {
            $requestManager = new HttpRequestManager($this->providerUrl, 10/* seconds */);
            $web3 = new Web3(new HttpProvider($requestManager));
            $net = $web3->net;
            $net->version(function ($err, $version) use (&$_version) {
                if ($err !== null) {
                    $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log("Failed to get blockchain version: " . $err);
                    return;
                }
                $_version = intval($version);
            });
        } catch (\Exception $ex) {
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log("getBlockchainNetwork: " . $ex->getMessage(), $gateway);
        }
        return $_version;
    }

	/**
	 * Retrieve the exchange rate from the API.
	 *
	 * @throws \Exception    Throws exception on error.
	 *
	 * @return float  The exchange rate.
	 */
	private function get_rate_from_api($eth_value) {
        $networkId = $this->getNetworkId();
        if (1 !== $networkId) {
            // only mainnet is allowed
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('Bad network: ' . $networkId);
            throw new \Exception("Bad network");
        }

        // 0xB41c91bF896B0ef30454Ab10271E7c8fe9A74C2F
        $pairContractAddress = $this->getPairContractAddress($this->source, $this->destination);
        if ('0x0000000000000000000000000000000000000000' === $pairContractAddress) {
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('No pair found for: ' . $this->source . ', ' . $this->destination);
            throw new \Exception("No pair found");
        }

        $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('$eth_value: ' . $eth_value);
        $pairReserves = $this->getPairReserves($pairContractAddress);

        // eth / tokens - amount of ETH for 1 token
        $_reserve0 = $pairReserves['_reserve0'];
        $_reserve1 = $pairReserves['_reserve1'];
        $_eth_value = self::double_int_multiply($eth_value, pow(10, 18));
        $_eth_multiplier = self::double_int_multiply(1.0, pow(10, 18));

        $decimals_token = intval($this->get_token_decimals($this->source)->toString());
        $_token_multiplier = self::double_int_multiply(1.0, pow(10, $decimals_token));
        $_log_2 = 0.30102999566;
        $_divider = $_reserve0->multiply($_eth_multiplier);
        $_log_divider = intval(floor($_log_2 * $_divider->getLength()));
        list($q, $r) = ($_reserve1->subtract($_eth_value))->multiply($_token_multiplier)->divide($_divider);
        $sR = $r->toString();
        $strRateDecimals = sprintf('%0' . $_log_divider . 's', $sR);
        $strRate = $q->toString() . '.' . $strRateDecimals;
        $strRate = rtrim($strRate, '0');
        $strRate = rtrim($strRate, '.');

//        $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('$_reserve0: ' . $_reserve0->toString());
//        $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('$_reserve1: ' . $_reserve1->toString());
//        $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('$_eth_value: ' . $_eth_value->toString());
//        $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('$strRate: ' . $strRate);
//        $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('$_log_divider: ' . $_log_divider);
        return doubleval($strRate);
	}

    protected function get_token_decimals($tokenAddress) {
        if (isset(static::$token2decimals[$tokenAddress])) {
            return static::$token2decimals[$tokenAddress];
        }
        $abi = $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->erc20ContractABI;
        $contract = new Contract(new HttpProvider(new HttpRequestManager($this->providerUrl, 10/* seconds */)), $abi);

        $ret = null;
        $callback = function($error, $result) use(&$ret) {
            if ($error !== null) {
                $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log($error);
                return;
            }
//            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log("RESULT: " . print_r($result, true));
            foreach ($result as $key => $res) {
                $ret = $res;
//                $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log("key: " . $key . "; ret: " . $ret);
                break;
            }
        };
        // call contract function
//        $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log(
//            sprintf('call contract %s method decimals', $tokenAddress)
//        );
        $contract->at($tokenAddress)->call("decimals", $callback);
        if (!is_null($ret)) {
            $token2decimals[$tokenAddress] = $ret;
        }
//        $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log("ret2: " . $ret);
        return $ret;
    }

    protected static function double_int_multiply($dval, $ival) {
        $dval = doubleval($dval);
        $ival = intval($ival);
        $dv1 = floor($dval);
        $ret = new BigInteger(intval($dv1));
        $ret = $ret->multiply(new BigInteger($ival));
        if ($dv1 === $dval) {
            return $ret;
        }
        $dv2 = $dval - $dv1;
        $iv1 = intval($dv2 * $ival);
        $ret = $ret->add(new BigInteger($iv1));
        return $ret;
    }

}
