<?php
add_action('ethereum_wallet_rest_api_endpoint', function() {
  register_rest_route( 'ethereumwallet/v1', '/web3_signPersonalMessage', array(
      array (
          'methods' => ['POST'],
          'callback' => 'ETHEREUM_WALLET_web3_signPersonalMessage_endpoint',
          'permission_callback' => function (WP_REST_Request $request) {
              return true;
//                return current_user_can( 'edit_others_posts' );
          },
//            'args' => array(
//            ),
      ),
      'schema' => 'ETHEREUM_WALLET_web3_signPersonalMessage_schema',
  ) );
}, 10, 0);

function ETHEREUM_WALLET_web3_signPersonalMessage_schema() {
    $schema = array(
        // This tells the spec of JSON Schema we are using which is draft 4.
        '$schema'              => 'http://json-schema.org/draft-04/schema#',
        // The title property marks the identity of the resource.
        'title'                => 'TxRawMsgSigned',
        'type'                 => 'string'
    );

    return $schema;
}

function ETHEREUM_WALLET_web3_signPersonalMessage_endpoint( WP_REST_Request $request ) {
//    $blockchainNetwork = ETHEREUM_WALLET_getBlockchainNetwork();
    $params = $request->get_body_params();
    ETHEREUM_WALLET_log("ETHEREUM_WALLET_web3_signPersonalMessage_endpoint: ", print_r($params, true));
    unset($params['_wpnonce']);

    $txRawSigned = '';
    return $txRawSigned;
}
