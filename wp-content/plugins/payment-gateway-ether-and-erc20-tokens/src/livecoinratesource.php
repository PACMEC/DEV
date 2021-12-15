<?php

namespace Ethereumico\Epg;

class LivecoinRateSource {

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
	 * The https://livecoin.com API Key
	 *
	 * @var string
	 */
	private $apiKey;
    
    /**
     * The cached rate value
     * 
     * @var number Cached rate value
     */
    private $rate;
    
    /**
	 * Construct the class. Store the source and destination.
	 *
	 * @param string $source       The source currency code.
	 * @param string $destination  The destination currency code.
	 */
	public function __construct( $source, $tokenSymbol, $destination, $apiKey = '' ) {
		$this->source = $tokenSymbol;
		$this->destination = $destination;
		$this->apiKey = $apiKey;
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
		$transient_key = 'epg_token_exchange_rate_' . $this->source . '_' . $this->destination;
		// Check for a cached rate first. Use it if present.
		$rate = get_transient( $transient_key );
		if ( false !== $rate ) {
			return apply_filters( 'epg_token_exchange_rate', (float) $rate );
		}
		$rate = $this->get_rate_from_api();
		set_transient( $transient_key, $rate
            , apply_filters( 'epg_token_exchange_rate_cache_duration_LivecoinRateSource', 1800 ) );
		$this->rate = apply_filters( 'epg_token_exchange_rate', (float) $rate );
        return $this->rate;
	}

	/**
	 * Retrieve the exchange rate from the API.
	 *
	 * @throws \Exception    Throws exception on error.
	 *
	 * @return float  The exchange rate.
	 */
	private function get_rate_from_api() {
        global $wp_version;
//        curl 'https://api.livecoin.net/exchange/ticker?currencyPair=ELF/ETH'
//{"cur":"ELF","symbol":"ELF/ETH","last":0,"high":0,"low":0,"volume":0,"vwap":0,"max_bid":0.00014659,"min_ask":0,"best_bid":0.00014659,"best_ask":0.00989999}

//        curl 'https://api.livecoin.net/exchange/ticker?currencyPair=ELF111/ETH'
//{"success":false,"errorCode":1,"errorMessage":"Unknown currency pair [currencyPair={1}]|ELF111/ETH"}

        $source = $this->source;
        $url = 'https://api.livecoin.net/exchange/ticker?currencyPair=' . strtoupper($source) . '/' . strtoupper($this->destination);
        $args = array(
            'timeout'     => 5,
            'redirection' => 5,
            'httpversion' => '1.1',
            'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
            'blocking'    => true,
            'headers'     => array(
                "Accept" => "application/json",
            ),
            'cookies'     => array(),
            'body'        => null,
            'compress'    => false,
            'decompress'  => true,
            'sslverify'   => true,
            'stream'      => false,
            'filename'    => null
        ); 
		$response = wp_remote_get( $url, $args );
		if ( is_wp_error( $response ) || 200 !== $response['response']['code'] ) {
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('Could not fetch ETH token pricing');
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('URL: ' . $url);
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('args: ' . print_r($args, true));
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('response: ' . print_r($response, true));
			throw new \Exception( 'Could not fetch ETH token pricing' );
		}
		$body = json_decode( $response['body'] );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('Could not fetch ETH token pricing - JSON error.');
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('response[data]: ' . print_r($response['data'], true));
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('URL: ' . $url);
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('args: ' . print_r($args, true));
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('response: ' . print_r($response, true));
			throw new \Exception( 'Could not fetch ETH token pricing - JSON error.' );
		}
        if ( isset( $body->{'success'} ) ) {
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('Could not fetch ETH token pricing: ' . $body->{'errorMessage'});
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('URL: ' . $url);
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('args: ' . print_r($args, true));
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('response: ' . print_r($response, true));
			throw new \Exception( 'Could not fetch ETH token pricing: ' . $body->{'errorMessage'} );
		}
        if ( ! isset( $body->{'best_bid'} ) ) {
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('Could not fetch ETH token pricing - missing best_bid after decoding.');
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('URL: ' . $url);
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('args: ' . print_r($args, true));
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('response: ' . print_r($response, true));
			throw new \Exception( 'Could not fetch ETH token pricing - missing best_bid after decoding.' );
		}
        return (float) $body->{'best_bid'};
	}
}
