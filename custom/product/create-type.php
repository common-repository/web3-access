<?php

class MetaPress_Custom_Post_Type_Manager {

    public function __construct() {
        add_action('init', array($this, 'create_metapress_product_post_type'));
        add_action('pre_get_posts', array($this, 'set_metapress_post_type_query_vars') );
    }

    public function create_metapress_product_post_type() {
      global $wp_metapress_textdomain;
      $labels = array(
        'name'               => _x( 'Web3Products', $wp_metapress_textdomain ),
        'singular_name'      => _x( 'Web3Products', $wp_metapress_textdomain ),
        'add_new'            => _x( 'Add New', $wp_metapress_textdomain ),
        'add_new_item'       => __( 'Add New ', $wp_metapress_textdomain ),
        'edit_item'          => __( 'Edit Product', $wp_metapress_textdomain ),
        'new_item'           => __( 'New Product', $wp_metapress_textdomain ),
        'all_items'          => __( 'All Product', $wp_metapress_textdomain ),
        'view_item'          => __( 'View Product', $wp_metapress_textdomain ),
        'search_items'       => __( 'Search Product', $wp_metapress_textdomain ),
        'not_found'          => __( 'No product found', $wp_metapress_textdomain ),
        'not_found_in_trash' => __( 'No product found in the Trash', $wp_metapress_textdomain ),
        'parent_item_colon'  => '',
        'menu_name'          => 'Web3Products'
      );
      $args = array(
        'labels'        => $labels,
        'description'   => get_bloginfo('name').' Web3 Access products.',
        'public'        => true,
        'supports'      => array( 'title', 'excerpt', 'thumbnail'),
        'has_archive'   => false,
        'rewrite'       => array( 'slug' => 'web3-product' ),
        'menu_icon'     => 'dashicons-cloud',
        'menu_position' => 29,
        'show_in_rest'  => true,
        'template_lock' => 'all'
      );
      register_post_type( 'metapress_product', $args );
    }

    public function set_metapress_post_type_query_vars($query) {
        if ( is_post_type_archive( 'metapress_product' ) ) {
            //$query->set( 'order','ASC' );
        }
    }

}
$metapress_custom_post_type_manager = new MetaPress_Custom_Post_Type_Manager();
