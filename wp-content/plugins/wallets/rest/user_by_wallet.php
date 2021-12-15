<?php

add_action('ethereum_wallet_rest_api_endpoint', function() {
  register_rest_route( 'ethereumwallet/v1', '/user_by_wallet' . '/(?P<wallet>[a-zA-Z0-9]+)', array(
    'args'   => array(
      'wallet' => array(
        'description'=> esc_html__( 'Wallet address', 'ethereum-wallet' ),
        'type' => 'string',
        'default' => "",
        'required' => true,
        'sanitize_callback' => function($param, $request, $key) {
            return esc_attr( $param );
        }
      ),
    ),
    array (
      'methods' => ['GET'],
      'callback' => 'ETHEREUM_WALLET_get_user_by_wallet_endpoint',
      'permission_callback' => function (WP_REST_Request $request) {
        return true;
        // return current_user_can( 'edit_others_posts' );
      },
      'args' => array(
      ),
    ),
    'schema' => 'ETHEREUM_WALLET_get_user_by_wallet_schema',
  ) );
}, 10, 0);

function ETHEREUM_WALLET_get_user_by_wallet_schema() {
    $schema = array(
        // This tells the spec of JSON Schema we are using which is draft 4.
        '$schema'              => 'http://json-schema.org/draft-04/schema#',
        // The title property marks the identity of the resource.
        'title'                => 'User Account Info',
        'type'                 => 'object',
        // In JSON Schema you can specify object properties in the properties attribute.
        'properties'           => array(
            'userName' => array(
                'description'  => esc_html__( 'The user\'s display name or login.', 'ethereum-wallet' ),
                'type'         => 'string',
            ),
            'userUrl' => array(
                'description'  => esc_html__( 'The user\'s home page URL.', 'ethereum-wallet' ),
                'type'         => 'string',
            ),
            'userAvatarUrl' => array(
                'description'  => esc_html__( 'The user\'s avatar URL.', 'ethereum-wallet' ),
                'type'         => 'string',
            ),
        ),
    );

    return $schema;
}

function ETHEREUM_WALLET_get_user_by_wallet_endpoint( WP_REST_Request $request ) {
    global $wpdb;

    $account = $request->get_param( 'wallet' );

    if (empty($account)) {
      return [];
    }

    $user_id = ETHEREUM_WALLET_get_user_id_by_wallet( $account );

    if (empty($user_id)) {
      return [];
    }

    $ownerURL = ETHEREUM_WALLET_get_address_path($account);
    $userUrl = ETHEREUM_WALLET_get_user_page_url($ownerURL, $user_id);
    $userName = ETHEREUM_WALLET_get_user_name($user_id);
    $userAvatarUrl = ETHEREUM_WALLET_get_avatar_url($user_id);

    return [
      'userName'  => $userName,
      'userUrl'   => $userUrl,
      'userAvatarUrl' => $userAvatarUrl,
    ];
}
