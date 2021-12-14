<?php

function arrowpress_core_like_scripts() {
    wp_localize_script('jquery', 'ajax_var', array(
        'url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('ajax-nonce')
            )
    );
}

add_action('init', 'arrowpress_core_like_scripts');

add_action('wp_ajax_nopriv_arrowpress_core-post-like', 'arrowpress_core_post_like');
add_action('wp_ajax_arrowpress_core-post-like', 'arrowpress_core_post_like');

function arrowpress_core_post_like() {
    $nonce = $_POST['nonce'];
    if (!wp_verify_nonce($nonce, 'ajax-nonce'))
        die('Nope!');

    if (isset($_POST['arrowpress_core_post_like'])) {
        $like_type = 'post';
        if (isset($_POST['like_type']) && !empty($_POST['like_type'])) {
            $like_type = $_POST['like_type'];
        }
        $post_id = $_POST['post_id']; // post id
        if($like_type == 'comment') {
            $post_like_count = get_comment_meta($post_id, "_post_like_count", true); // post like count
        } else {
            $post_like_count = get_post_meta($post_id, "_post_like_count", true); // post like count
        }

        if (function_exists('wp_cache_post_change')) { // invalidate WP Super Cache if exists
            $GLOBALS["super_cache_enabled"] = 1;
            wp_cache_post_change($post_id);
        }

        if (is_user_logged_in()) { // user is logged in
            $user_id = get_current_user_id(); // current user
            $meta_POSTS = get_user_option("_liked_posts", $user_id); // post ids from user meta
            if($like_type == 'comment') {
                $meta_USERS = get_comment_meta($post_id, "_user_liked"); // user ids from post meta
            } else {
                $meta_USERS = get_post_meta($post_id, "_user_liked"); // user ids from post meta
            }
            
            $liked_POSTS = NULL; // setup array variable
            $liked_USERS = NULL; // setup array variable

            if (count($meta_POSTS) != 0) { // meta exists, set up values
                $liked_POSTS = $meta_POSTS;
            }

            if (!is_array($liked_POSTS)) // make array just in case
                $liked_POSTS = array();

            if (count($meta_USERS) != 0) { // meta exists, set up values
                $liked_USERS = $meta_USERS[0];
            }

            if (!is_array($liked_USERS)) // make array just in case
                $liked_USERS = array();

            $liked_POSTS['post-' . $post_id] = $post_id; // Add post id to user meta array
            $liked_USERS['user-' . $user_id] = $user_id; // add user id to post meta array
            $user_likes = count($liked_POSTS); // count user likes

            if (!arrowpress_core_AlreadyLiked($post_id)) { // like the post
                if($like_type == 'comment') {
                    update_comment_meta($post_id, "_user_liked", $liked_USERS); // Add user ID to post meta
                    update_comment_meta($post_id, "_post_like_count", ++$post_like_count); // +1 count post meta
                } else {
                    update_post_meta($post_id, "_user_liked", $liked_USERS); // Add user ID to post meta
                    update_post_meta($post_id, "_post_like_count", ++$post_like_count); // +1 count post meta
                }
                
                update_user_option($user_id, "_liked_posts", $liked_POSTS); // Add post ID to user meta
                update_user_option($user_id, "_user_like_count", $user_likes); // +1 count user meta
                echo esc_html($post_like_count); // update count on front end
            } else { // unlike the post
                $pid_key = array_search($post_id, $liked_POSTS); // find the key
                $uid_key = array_search($user_id, $liked_USERS); // find the key
                unset($liked_POSTS[$pid_key]); // remove from array
                unset($liked_USERS[$uid_key]); // remove from array
                $user_likes = count($liked_POSTS); // recount user likes
                if($like_type == 'comment') {
                    update_comment_meta($post_id, "_user_liked", $liked_USERS); // Remove user ID from post meta
                    update_comment_meta($post_id, "_post_like_count", --$post_like_count); // -1 count post meta
                } else {
                    update_post_meta($post_id, "_user_liked", $liked_USERS); // Remove user ID from post meta
                    update_post_meta($post_id, "_post_like_count", --$post_like_count); // -1 count post meta
                }
                
                update_user_option($user_id, "_liked_posts", $liked_POSTS); // Remove post ID from user meta			
                update_user_option($user_id, "_user_like_count", $user_likes); // -1 count user meta
                echo "already" . $post_like_count; // update count on front end
            }
        } else { // user is not logged in (anonymous)
            $ip = $_SERVER['REMOTE_ADDR']; // user IP address
            $meta_IPS = get_post_meta($post_id, "_user_IP"); // stored IP addresses
            $liked_IPS = NULL; // set up array variable

            if (count($meta_IPS) != 0) { // meta exists, set up values
                $liked_IPS = $meta_IPS[0];
            }

            if (!is_array($liked_IPS)) // make array just in case
                $liked_IPS = array();

            if (!in_array($ip, $liked_IPS)) // if IP not in array
                $liked_IPS['ip-' . $ip] = $ip; // add IP to array

            if (!arrowpress_core_AlreadyLiked($post_id)) { // like the post
                if($like_type == 'comment') {
                    update_comment_meta($post_id, "_user_IP", $liked_IPS); // Add user IP to post meta
                    update_comment_meta($post_id, "_post_like_count", ++$post_like_count); // +1 count post meta
                } else {
                    update_post_meta($post_id, "_user_IP", $liked_IPS); // Add user IP to post meta
                    update_post_meta($post_id, "_post_like_count", ++$post_like_count); // +1 count post meta
                }
                
                echo esc_html($post_like_count); // update count on front end
            } else { // unlike the post
                $ip_key = array_search($ip, $liked_IPS); // find the key
                unset($liked_IPS[$ip_key]); // remove from array
                if($like_type == 'comment') {
                    update_comment_meta($post_id, "_user_IP", $liked_IPS); // Remove user IP from post meta
                    update_comment_meta($post_id, "_post_like_count", --$post_like_count); // -1 count post meta
                } else {
                    update_post_meta($post_id, "_user_IP", $liked_IPS); // Remove user IP from post meta
                    update_post_meta($post_id, "_post_like_count", --$post_like_count); // -1 count post meta
                }
                
                echo "already" . $post_like_count; // update count on front end
            }
        }
    }

    exit;
}

function arrowpress_core_AlreadyLiked($post_id) { // test if user liked before
    $like_type = 'post';
    if (isset($_POST['like_type']) && !empty($_POST['like_type'])) {
        $like_type = $_POST['like_type'];
    }
    if (is_user_logged_in()) { // user is logged in
        $user_id = get_current_user_id(); // current user
        if ($like_type == 'comment') {
            $meta_USERS = get_comment_meta($post_id, "_user_liked");
        } else {
            $meta_USERS = get_post_meta($post_id, "_user_liked");
        }
        // user ids from post meta
        $liked_USERS = ""; // set up array variable

        if (count($meta_USERS) != 0) { // meta exists, set up values
            $liked_USERS = $meta_USERS[0];
        }

        if (!is_array($liked_USERS)) // make array just in case
            $liked_USERS = array();

        if (in_array($user_id, $liked_USERS)) { // True if User ID in array
            return true;
        }
        return false;
    } else { // user is anonymous, use IP address for voting
        if ($like_type == 'comment') {
            $meta_IPS = get_comment_meta($post_id, "_user_IP"); // get previously voted IP address
        } else {
            $meta_IPS = get_post_meta($post_id, "_user_IP"); // get previously voted IP address
        }

        $ip = $_SERVER["REMOTE_ADDR"]; // Retrieve current user IP
        $liked_IPS = ""; // set up array variable

        if (count($meta_IPS) != 0) { // meta exists, set up values
            $liked_IPS = $meta_IPS[0];
        }

        if (!is_array($liked_IPS)) // make array just in case
            $liked_IPS = array();

        if (in_array($ip, $liked_IPS)) { // True is IP in array
            return true;
        }
        return false;
    }
}

function arrowpress_core_getPostLikeLink($post_id, $like_type = 'post') {
    if ($like_type == 'comment') {
        $like_count = get_comment_meta($post_id, "_post_like_count", true);
    } else {
        $like_count = get_post_meta($post_id, "_post_like_count", true);
    }

    $count = ( empty( $like_count) || $like_count == "0" ) ? '0' : esc_attr( $like_count);
    if (arrowpress_core_AlreadyLiked($post_id)) {
        $class = esc_attr(' liked');
        $title = esc_attr(' Unlike');
        $heart = '';
		if($like_count == "1"){
			$like_text = esc_html('&nbsp;Like');
		}else{
			$like_text = esc_html('&nbsp;Likes');
		}
    } else {
        $class = esc_attr('');
        $title = esc_attr('Like');
        $heart = '';
		if($like_count == "1"){
			$like_text = esc_html('&nbsp;Like');
		}else{
			$like_text = esc_html('&nbsp;Likes');
		}
    }
    $output = '<a href="#" class="arrowpress_core-post-like' . $class . '"  data-like_type="'.$like_type.'" data-post_id="' . $post_id . '" title="' . $title . '">' . $heart . $count . $like_text .'</a>';
    return $output;
}
