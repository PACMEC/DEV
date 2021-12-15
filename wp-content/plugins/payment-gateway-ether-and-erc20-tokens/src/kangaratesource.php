<?php

namespace Ethereumico\Epg;

class KangaRateSource {

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
	 * The https://kanga.exchange API Key
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
        if (null === $rate) {
            return null;
        }
		set_transient( $transient_key, $rate
            , apply_filters( 'epg_token_exchange_rate_cache_duration_KangaRateSource', 1800 ) );
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

        $source = $this->source;
        $destination = $this->destination;
        $url = 'https://kanga.exchange/api/markets';
        $args = array(
            'method'      => 'POST',
            'timeout'     => 5,
            'redirection' => 5,
            'httpversion' => '1.1',
//            'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
            'user-agent'  => 'curl/7.58.0',
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
        if ( ! isset( $body->{'items'} ) ) {
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('Could not fetch ETH token pricing - missing items after decoding.');
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('URL: ' . $url);
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('args: ' . print_r($args, true));
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('response: ' . print_r($response, true));
			throw new \Exception( 'Could not fetch ETH token pricing - missing items after decoding.' );
		}
        $items = $body->{'items'};
        $lastPrice = null;
        foreach ($items as $item) {
            if ($item->buyingCurrency != $source) {
                continue;
            }
            $lastPrice = $item->lastPrice;
            break;
        }
        if (null === $lastPrice) {
            return null;
        }
        return (float)$lastPrice;
	}
}
