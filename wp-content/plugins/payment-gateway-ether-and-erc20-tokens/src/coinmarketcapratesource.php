<?php

namespace Ethereumico\Epg;

class CoinmarketcapRateSource {

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
	 * The https://coinmarketcap.com API Key
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

    private static $dictionary;

    /**
	 * Construct the class. Store the source and destination.
	 *
	 * @param string $source       The source currency code.
	 * @param string $destination  The destination currency code.
	 */
	public function __construct( $source, $tokenSymbol, $destination, $apiKey ) {
		$this->source = $source;
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
		$transient_key = 'epg_token_exchange_rate_' . strtolower($this->source) . '_' . $this->destination;
		// Check for a cached rate first. Use it if present.
		$rate = get_transient( $transient_key );
		if ( false !== $rate ) {
			return apply_filters( 'epg_token_exchange_rate', (float) $rate );
		}
		$rate = $this->get_rate_from_api();
		set_transient( $transient_key, $rate
            , apply_filters( 'epg_token_exchange_rate_cache_duration_CoinmarketcapRateSource', 1800 ) );
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
        if (!isset(self::$dictionary) || is_null(self::$dictionary)) {
            self::$dictionary = self::loadDictionary();
        }
        if (!isset(self::$dictionary[strtolower($this->source)])) {
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log(
                'Could not fetch ETH token pricing - Unknown token: ' . strtolower($this->source));
            throw new \Exception( 'Could not fetch ETH token pricing - Unknown token: ' . strtolower($this->source) );
        }
        $id = self::$dictionary[strtolower($this->source)]["id"];
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest?id=' . $id . '&convert=' . strtoupper($this->destination);
        $args = array(
            'timeout'     => 5,
            'redirection' => 5,
            'httpversion' => '1.1',
            'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
            'blocking'    => true,
            'headers'     => array(
                "Accept" => "application/json",
                "X-CMC_PRO_API_KEY" => $this->apiKey,
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
		// $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('get_rate_from_api: ' . $url);
		// $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('get_rate_from_api: ' . $response['body']);
		if ( json_last_error() !== JSON_ERROR_NONE ) {
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('Could not fetch ETH token pricing - JSON error.');
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('response[data]: ' . print_r($response['data'], true));
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('URL: ' . $url);
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('args: ' . print_r($args, true));
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('response: ' . print_r($response, true));
			throw new \Exception( 'Could not fetch ETH token pricing - JSON error.' );
		}
        if ( ! isset( $body->{'data'} ) ) {
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('Could not fetch ETH token pricing - missing data after decoding.');
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('URL: ' . $url);
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('args: ' . print_r($args, true));
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('response: ' . print_r($response, true));
			throw new \Exception( 'Could not fetch ETH token pricing - missing id after decoding.' );
		}
        if ( ! isset( $body->{'data'}->{$id} ) ) {
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('Could not fetch ETH token pricing - missing id after decoding.');
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('URL: ' . $url);
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('args: ' . print_r($args, true));
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('response: ' . print_r($response, true));
			throw new \Exception( 'Could not fetch ETH token pricing - missing id after decoding.' );
		}
        if ( ! isset( $body->{'data'}->{$id}->{"quote"} ) ) {
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('Could not fetch ETH token pricing - missing quote after decoding.');
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('URL: ' . $url);
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('args: ' . print_r($args, true));
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('response: ' . print_r($response, true));
			throw new \Exception( 'Could not fetch ETH token pricing - missing quote after decoding.' );
		}
        if ( ! isset( $body->{'data'}->{$id}->{"quote"}->{$this->destination} ) ) {
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('Could not fetch ETH token pricing - missing destination after decoding.');
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('URL: ' . $url);
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('args: ' . print_r($args, true));
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('response: ' . print_r($response, true));
			throw new \Exception( 'Could not fetch ETH token pricing - missing destination after decoding.' );
		}
        if ( ! isset( $body->{'data'}->{$id}->{"quote"}->{$this->destination}->{'price'} ) ) {
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('Could not fetch ETH token pricing - missing price after decoding.');
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('URL: ' . $url);
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('args: ' . print_r($args, true));
            $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->log('response: ' . print_r($response, true));
			throw new \Exception( 'Could not fetch ETH token pricing - missing destination after decoding.' );
		}
        return (float) $body->{'data'}->{$id}->{"quote"}->{$this->destination}->{'price'};
	}

    private static function loadDictionary() {
        $base_path = $GLOBALS['ether-and-erc20-tokens-woocommerce-payment-gateway']->base_path;
        $data = file_get_contents($base_path . '/coinmarketcap.json');
        return json_decode($data, true);
    }
}
