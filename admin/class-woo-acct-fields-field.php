<?php

class Woo_Acct_Fields_Item {

    private $waf_item;
    private $waf_account;
    private $waf_class;

    public function __construct() {
        $this->waf_item = 'waf_item';
        $this->waf_account = 'waf_account';
        $this->waf_class = 'waf_class';
    }

    public function init() {
      add_action( 'woocommerce_product_options_general_product_data',
        array ($this, 'product_options_general'));

      add_action('woocommerce_process_product_meta',
        array ($this, 'add_custom_linked_field_save'));
    }

    public function product_options_general() {
      global $woocommerce, $post;
      // Accounting Item Field
      woocommerce_wp_text_input(
        array(
            'id'            => $this->waf_item,
            'label'         => __( 'Accounting Item', 'woocommerce'),
            'placeholder'   => 'Provide Item from Accounting Software for this Product',
            'desc_tip'      => 'true',
            'description'   => __('Enter the name of Item found in your Accounting Package','woocommerce')
          )
      );
      // Accounting Account Field
      woocommerce_wp_text_input(
        array(
          'id'            => $this->waf_account,
          'label'         => __( 'Accounting Account', 'woocommerce'),
          'placeholder'   => 'Account for Product',
          'desc_tip'      => 'true',
          'description'   => __('Enter the name of Account found in your Accounting Package','woocommerce')
        )
      );
      // Accounting Class Field
      woocommerce_wp_text_input(
        array(
          'id'            => $this->waf_class,
          'label'         => __( 'Accounting Class', 'woocommerce'),
          'placeholder'   => 'Class for Product',
          'desc_tip'      => 'true',
          'description'   => __('Enter the name of Class found in your Accounting Package','woocommerce')
        )
      );
    }
    public function add_custom_linked_field_save( $post_id ) {
        //     if ( ! ( isset( $_POST['woocommerce_meta_nonce'], $_POST[ $this->textfield_id ] ) || wp_verify_nonce( sanitize_key( $_POST['woocommerce_meta_nonce'] ), 'woocommerce_save_data' ) ) ) {
        //     return false;
        // }
        $product_item = $_POST[ $this->waf_item ];
        update_post_meta(
            $post_id,
            $this->waf_item,
            esc_html( $product_item )
        );
        $product_account = $_POST[ $this->waf_account ];
        update_post_meta(
            $post_id,
            $this->waf_account,
            esc_html( $product_account )
        );
        $product_class = $_POST[ $this->waf_class ];
        update_post_meta(
            $post_id,
            $this->waf_class,
            esc_html( $product_class )
        );
    }
}
