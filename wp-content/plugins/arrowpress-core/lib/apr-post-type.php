<?php

class ArrowPressPostTypes {

    function __construct() {
        // Register post types
    add_action('init', array($this, 'addBlockPostType'));
    add_action('init', array($this, 'addProjectPostType'));
    add_action('init', array($this, 'addServicePostType'));
    add_action('init', array($this, 'addEventsPostType'));
    add_filter('manage_service_posts_columns', array($this, 'addGallery_columns')); 
    add_action('manage_service_posts_custom_column', array($this, 'addGallery_columns_content'), 10, 2); 
	add_filter('manage_projects_posts_columns', array($this, 'addGallery_columns'));  
    add_action('manage_projects_posts_custom_column', array($this, 'addGallery_columns_content'), 10, 2);  
	add_action( 'cmb2_admin_init', array($this, 'arrowpress_core_add_checkbox_field_metabox')); 
    add_action( 'cmb2_admin_init', array($this, 'arrowpress_core_add_text_field_metabox')); 
    }            
    // Register static block post type
    function addBlockPostType() {
        register_post_type(
            'block', array(
            'labels' => $this->getLabels(esc_html__('Static Block', 'arrowpress-core'), esc_html__('Static Block', 'arrowpress-core')),
            'exclude_from_search' => true,
            'has_archive' => false,
            'publicly_queryable'  => false,
            'public' => true,
            'rewrite' => array('slug' => 'block'),
            'supports' => array('title', 'editor', 'thumbnail', 'comments', 'page-attributes'),
            'can_export' => true
                )
        );
    }
    // Register Project post type
    function addProjectPostType() {
        global $cryptcio_settings;
        if(isset($cryptcio_settings['project_slug'])){
            $project_slug = $cryptcio_settings['project_slug'];
        }
        else {$project_slug = "project"; }
        if(isset($cryptcio_settings['project_cat_slug'])){
            $project_cat_slug = $cryptcio_settings['project_cat_slug'];
        }
        else {$project_cat_slug = "project_cat"; }                 
        register_post_type(
            'project', array(
            'labels' => $this->getLabels(esc_html__('Project', 'arrowpress-core'), esc_html__('Project', 'arrowpress-core')),
            'exclude_from_search' => false,
            'has_archive' => true,
            // 'publicly_queryable'  => false,
            'public' => true,
            'rewrite' => array('slug' => $project_slug),
            'supports' => array('title', 'editor', 'thumbnail', 'comments', 'page-attributes'),
            'can_export' => true
            )
        );
        register_taxonomy(
            'project_cat', 'project', array(
            'hierarchical' => true,
            'show_in_nav_menus' => true,
            'labels' => $this->getTaxonomyLabels(esc_html__('Project Category', 'arrowpress-core'), esc_html__('Project Categories', 'arrowpress-core')),
            'query_var' => true,
            'rewrite' => array('slug' => $project_cat_slug),
            'show_admin_column' => true,
                )
        );
    }
    // Register Member post type
    function addMemberPostType() {
        global $cryptcio_settings;
        if(isset($cryptcio_settings['member_slug'])){
            $member_slug = $cryptcio_settings['member_slug'];
        }
        else {$member_slug = "member"; }
        if(isset($cryptcio_settings['member_cat_slug'])){
            $member_cat_slug = $cryptcio_settings['member_cat_slug'];
        }
        else {$member_cat_slug = "member_cat"; }                 
        register_post_type(
            'member', array(
            'labels' => $this->getLabels(esc_html__('Member', 'arrowpress-core'), esc_html__('Member', 'arrowpress-core')),
            'exclude_from_search' => false,
            'has_archive' => true,
            // 'publicly_queryable'  => false,
            'public' => true,
            'rewrite' => array('slug' => $member_slug),
            'supports' => array('title', 'editor', 'thumbnail', 'comments', 'page-attributes'),
            'can_export' => true
            )
        );
        register_taxonomy(
            'member_cat', 'member', array(
            'hierarchical' => true,
            'show_in_nav_menus' => true,
            'labels' => $this->getTaxonomyLabels(esc_html__('Member Category', 'arrowpress-core'), esc_html__('Member Categories', 'arrowpress-core')),
            'query_var' => true,
            'rewrite' => array('slug' => $member_cat_slug),
            'show_admin_column' => true,
                )
        );
    }  
    // Register Service post type
    function addServicePostType() {
        global $cryptcio_settings;
        if(isset($cryptcio_settings['service_slug'])){
            $service_slug = $cryptcio_settings['service_slug'];
        }
        else {$service_slug = "service"; }
        if(isset($cryptcio_settings['service_cat_slug'])){
            $service_cat_slug = $cryptcio_settings['service_cat_slug'];
        }
        else {$service_cat_slug = "service_cat"; }                 
        register_post_type(
            'service', array(
            'labels' => $this->getLabels(esc_html__('Service', 'arrowpress-core'), esc_html__('Service', 'arrowpress-core')),
            'exclude_from_search' => false,
            'has_archive' => true,
            // 'publicly_queryable'  => false,
            'public' => true,
            'rewrite' => array('slug' => $service_slug),
            'supports' => array('title', 'editor', 'thumbnail', 'comments', 'page-attributes'),
            'can_export' => true
            )
        );
        register_taxonomy(
            'service_cat', 'service', array(
            'hierarchical' => true,
            'show_in_nav_menus' => true,
            'labels' => $this->getTaxonomyLabels(esc_html__('Service Category', 'arrowpress-core'), esc_html__('Service Categories', 'arrowpress-core')),
            'query_var' => true,
            'rewrite' => array('slug' => $service_cat_slug),
            'show_admin_column' => true,
                )
        );        
    }   
	// Register events post type
		function addEventsPostType() {
			global $cryptcio_settings;
			if(isset($cryptcio_settings['events_slug'])){
				$events_slug = $cryptcio_settings['events_slug'];
			}
			else {$events_slug = "events"; }
			if(isset($cryptcio_settings['events_cat_slug'])){
				$events_cat_slug = $cryptcio_settings['events_cat_slug'];
			}
			else {$events_cat_slug = "events_cat"; }                 
			register_post_type(
				'events', array(
				'labels' => $this->getLabels(esc_html__('Events', 'arrowpress-core'), esc_html__('Events', 'arrowpress-core')),
				'exclude_from_search' => false,
				'has_archive' => true,
				// 'publicly_queryable'  => false,
				'public' => true,
				'rewrite' => array('slug' => $events_slug),
				'supports' => array('title', 'editor', 'thumbnail', 'comments', 'page-attributes'),
				'can_export' => true
				)
			);
			register_taxonomy(
				'events_cat', 'events', array(
				'hierarchical' => true,
				'show_in_nav_menus' => true,
				'labels' => $this->getTaxonomyLabels(esc_html__('Events Category', 'arrowpress-core'), esc_html__('Events Categories', 'arrowpress-core')),
				'query_var' => true,
				'rewrite' => array('slug' => $events_cat_slug),
				'show_admin_column' => true,
					)
			);
			register_taxonomy(
				'events_loaction', 'events', array(
				'hierarchical' => true,
				'show_in_nav_menus' => true,
				'labels' => $this->getTaxonomyLabels(esc_html__('Events Loaction', 'arrowpress-core'), esc_html__('Events Loaction', 'arrowpress-core')),
				'query_var' => true,
				'show_admin_column' => true,
					)
			);
		}	
	/**
     * Hook in and add a metabox to demonstrate repeatable checkbox fields
     */
    function arrowpress_core_add_checkbox_field_metabox() {
        $prefix = 'arrowpress_core_';
        /**
         * Repeatable Field Groups
         */
        $cmb_term = new_cmb2_box( array(
            'id'               => $prefix . 'edit',
            'title'            => __( 'Settings', 'arrowpress-core' ), 
            'object_types'     => array( 'term' ),
            'taxonomies'       => array( 'product_cat','gallery_cat' ), // CHANGE THIS TO YOUR CUSTOM TAXONOMY
            'new_term_section' => true, // This is important as well
            'classes' => array( 'arrowpress_checkbox')
        ) );

        $cmb_term->add_field( array(
            'name' => esc_html__( 'Active Category Filter', 'arrowpress-core' ),
            'desc' => esc_html__( 'Active category filter', 'arrowpress-core' ),
            'id'   => 'arrowpress_core_checkbox',
            'type' => 'checkbox',
        ) );
    } 
    function arrowpress_core_add_text_field_metabox() {
        $prefix = 'arrowpress_core_';
        /**
         * Repeatable Field Groups
         */
        $cmb_term = new_cmb2_box( array(
            'id'               => $prefix . 'edit',
            'title'            => __( 'Settings', 'arrowpress-core' ), 
            'object_types'     => array( 'term' ),
            'taxonomies'       => array( 'product_cat','gallery_cat','category' ), // CHANGE THIS TO YOUR CUSTOM TAXONOMY
            'new_term_section' => true, // This is important as well
            'classes' => array( 'arrowpress_checkbox')
        ) );
        $cmb_term->add_field( array(
            'name'    => esc_html__( 'Font Icon', 'arrowpress-core' ),
            'desc'    => esc_html__( 'Enter class font icon', 'arrowpress-core' ),
            'id'      => 'icon_text',
            'type'    => 'text'
        ) );
    } 
    function get_the_image( $post_id = false ) {
        
        $post_id    = (int) $post_id;
        $cache_key  = "featured_image_post_id-{$post_id}-_thumbnail";
        $cache      = wp_cache_get( $cache_key, null );
        
        if ( !is_array( $cache ) )
            $cache = array();
    
        if ( !array_key_exists( $cache_key, $cache ) ) {
            if ( empty( $cache) || !is_string( $cache ) ) {
                $output = '';
                    
                if ( has_post_thumbnail( $post_id ) ) {
                    $image_array = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), array( 36, 32 ) );
                    
                    if ( is_array( $image_array ) && is_string( $image_array[0] ) )
                        $output = $image_array[0];
                }
                
                if ( empty( $output ) ) {
                    // $output = plugins_url( 'images/default.png', __FILE__ );
                    // $output = apply_filters( 'featured_image_column_default_image', $output );
                }
                
                $output = esc_url( $output );
                $cache[$cache_key] = $output;
                
                wp_cache_set( $cache_key, $cache, null, 60 * 60 * 24 /* 24 hours */ );
            }
        }
        return isset( $cache[$cache_key] ) ? $cache[$cache_key] : $output;
    } 
    function addGallery_columns($defaults) {  

        if ( !is_array( $defaults ) )
            $defaults = array();       
        $new = array();      
        foreach( $defaults as $key => $title ) {
            if ( $key == 'title' ) 
                $new['featured_image'] = 'Image';
            
            $new[$key] = $title;
        }
        
        return $new;         
    }        
    // SHOW THE FEATURED IMAGE
    function addGallery_columns_content($column_name, $post_id) {
        if ( 'featured_image' != $column_name )
                    return;         
                
                $image_src = self::get_the_image( $post_id );
                            
                if ( empty( $image_src ) ) {
                    echo "&nbsp;"; // This helps prevents issues with empty cells
                    return;
                }
                
                echo '<img alt="' . esc_attr( get_the_title() ) . '" src="' . esc_url( $image_src ) . '" />';
    }     
    // Get content type labels
    function getLabels($singular_name, $name, $title = FALSE) {
        if (!$title)
            $title = $name;

        return array(
            "name" => $title,
            "singular_name" => $singular_name,
            "add_new" => esc_html__("Add New", 'arrowpress-core'),
            "add_new_item" => sprintf(esc_html__("Add New %s", 'arrowpress-core'), $singular_name),
            "edit_item" => sprintf(esc_html__("Edit %s", 'arrowpress-core'), $singular_name),
            "new_item" => sprintf(esc_html__("New %s", 'arrowpress-core'), $singular_name),
            "view_item" => sprintf(esc_html__("View %s", 'arrowpress-core'), $singular_name),
            "search_items" => sprintf(esc_html__("Search %s", 'arrowpress-core'), $name),
            "not_found" => sprintf(esc_html__("No %s found", 'arrowpress-core'), $name),
            "not_found_in_trash" => sprintf(esc_html__("No %s found in Trash", 'arrowpress-core'), $name),
            "parent_item_colon" => ""
        );
    }

    // Get content type taxonomy labels
    function getTaxonomyLabels($singular_name, $name) {
        return array(
            "name" => $name,
            "singular_name" => $singular_name,
            "search_items" => sprintf(esc_html__("Search %s", 'arrowpress-core'), $name),
            "all_items" => sprintf(esc_html__("All %s", 'arrowpress-core'), $name),
            "parent_item" => sprintf(esc_html__("Parent %s", 'arrowpress-core'), $singular_name),
            "parent_item_colon" => sprintf(esc_html__("Parent %s:", 'arrowpress-core'), $singular_name),
            "edit_item" => sprintf(esc_html__("Edit %", 'arrowpress-core'), $singular_name),
            "update_item" => sprintf(esc_html__("Update %s", 'arrowpress-core'), $singular_name),
            "add_new_item" => sprintf(esc_html__("Add New %s", 'arrowpress-core'), $singular_name),
            "new_item_name" => sprintf(esc_html__("New %s Name", 'arrowpress-core'), $singular_name),
            "menu_name" => $name,
        );
    }

}

new ArrowPressPostTypes();