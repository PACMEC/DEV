<?php

add_action('ethereum_wallet_rest_api_endpoint', function() {
  register_rest_route( 'ethereumwallet/v1', '/web3_signTransaction', array(
      array (
          'methods' => ['POST'],
          'callback' => 'ETHEREUM_WALLET_web3_signTransaction_endpoint',
          'permission_callback' => function (WP_REST_Request $request) {
              return true;
//                return current_user_can( 'edit_others_posts' );
          },
          'args' => array(
              'from' => array(
                  'description'=> esc_html__( 'The From ethereum address', 'wallets-ethereum' ),
                  'type' => 'string',
                  'default' => "",
                  'required' => true,
                  'sanitize_callback' => function($param, $request, $key) {
                      return esc_attr( $param );
                  }
              ),
              'to' => array(
                  'description'=> esc_html__( 'The To ethereum address', 'wallets-ethereum' ),
                  'type' => 'string',
                  'default' => "",
                  'required' => true,
                  'sanitize_callback' => function($param, $request, $key) {
                      return esc_attr( $param );
                  }
              ),
              'value' => array(
                  'description'=> esc_html__( 'The Value field in hex format', 'wallets-ethereum' ),
                  'type' => 'string',
                  'default' => "",
                  'required' => true,
                  'sanitize_callback' => function($param, $request, $key) {
                      return esc_attr( $param );
                  }
              ),
              'gas' => array(
                  'description'=> esc_html__( 'The Gas Limit value in hex format', 'wallets-ethereum' ),
                  'type' => 'string',
                  'default' => "",
                  'required' => true,
                  'sanitize_callback' => function($param, $request, $key) {
                      return esc_attr( $param );
                  }
              ),
              'gasPrice' => array(
                  'description'=> esc_html__( 'The Gas Price value in hex format', 'wallets-ethereum' ),
                  'type' => 'string',
                  'default' => "",
                  'required' => true,
                  'sanitize_callback' => function($param, $request, $key) {
                      return esc_attr( $param );
                  }
              ),
              'data' => array(
                  'description'=> esc_html__( 'The Data value in hex format', 'wallets-ethereum' ),
                  'type' => 'string',
                  'default' => "",
                  'required' => true,
                  'sanitize_callback' => function($param, $request, $key) {
                      $res = esc_attr( $param );
                      if ("0x" == $res) {
                          $res = "";
                      }
                      return $res;
                  }
              ),
              'nonce' => array(
                  'description'=> esc_html__( 'The Nonce value in hex format', 'wallets-ethereum' ),
                  'type' => 'string',
                  'default' => "",
                  'required' => true,
                  'sanitize_callback' => function($param, $request, $key) {
                      return esc_attr( $param );
                  }
              ),
          ),
      ),
      'schema' => 'ETHEREUM_WALLET_web3_signTransaction_schema',
  ) );
}, 10, 0);

function ETHEREUM_WALLET_web3_signTransaction_schema() {
    $schema = array(
        // This tells the spec of JSON Schema we are using which is draft 4.
        '$schema'              => 'http://json-schema.org/draft-04/schema#',
        // The title property marks the identity of the resource.
        'title'                => 'TxRawSigned',
        'type'                 => 'string'
    );

    return $schema;
}

function ETHEREUM_WALLET_web3_signTransaction_endpoint( WP_REST_Request $request ) {
//    $blockchainNetwork = ETHEREUM_WALLET_getBlockchainNetwork();
    $params = $request->get_params();
    ETHEREUM_WALLET_log("ETHEREUM_WALLET_web3_signTransaction_endpoint: " . print_r($params, true));
    unset($params['_wpnonce']);

    $user_id = get_current_user_id();
    if ( $user_id <= 0 ) {
        ETHEREUM_WALLET_log("ETHEREUM_WALLET_web3_signTransaction_endpoint: no user is logged in");
        return;
    }
    $from = get_user_meta( $user_id, 'user_ethereum_wallet_address', true);
    if (empty($from)) {
        ETHEREUM_WALLET_log("ETHEREUM_WALLET_web3_signTransaction_endpoint: empty user_ethereum_wallet_address address");
        return '';
    }
    if (strtolower($from) != strtolower($params['from'])) {
        ETHEREUM_WALLET_log("ETHEREUM_WALLET_web3_signTransaction_endpoint: from address is invalid");
        return '';
    }
    ob_start();
    $txRawSigned = ETHEREUM_WALLET_sign_transaction(
        $params['to'],
        $params['value'],
        $params['data'],
        $params['gas'],
        isset($params['gasPrice']) ? $params['gasPrice'] : null,
        $params['nonce'],
        $params
    );
    $errors = ob_get_contents();
    if (!empty($errors)) {
        ETHEREUM_WALLET_log("ETHEREUM_WALLET_web3_signTransaction_endpoint PHP Warnings: " . $errors);
    }
    ob_end_clean();
    if (false === $txRawSigned) {
        ETHEREUM_WALLET_log("ETHEREUM_WALLET_web3_signTransaction_endpoint: empty ETHEREUM_WALLET_send_transaction result");
        return '';
    }
    ETHEREUM_WALLET_log("ETHEREUM_WALLET_web3_signTransaction_endpoint: signed raw tx: " . $txRawSigned);
    return $txRawSigned;
}
