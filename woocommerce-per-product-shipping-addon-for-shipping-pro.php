<?php
/*
	Plugin Name: WooCommerce Per Product Shipping AddOn For Shipping Pro  
	Plugin URI: https://www.xadapter.com/product/per-product-shipping-plugin-for-woocommerce/
	Description: AddOn Plugin for WooCommerce Shipping Pro. Designed to configure shipping costs at product level.
	Version: 1.0.5
	Author: PluginHive
	Author URI: https://www.pluginhive.com/
	Copyright: 2014-2018 PluginHive.
	WC tested up to: 3.4
	*/
class wf_per_product_shipping_addon_setup {
	public function __construct() {
		add_action( 'woocommerce_init', array( $this, 'wf_woocommerce_init' ));
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );			
	}
	
	public function wf_woocommerce_init(){		
		// Display Fields
		add_action( 'woocommerce_product_options_shipping', array( $this, 'wf_add_product_field_shipping_unit' ));
 
		// Save Fields
		add_action( 'woocommerce_process_product_meta', array( $this, 'wf_save_product_field_shipping_unit' ));		
		
		add_filter( 'wf_shipping_pro_item_quantity', array( $this, 'wf_shipping_pro_item_quantity' ),10, 2 );			
	}
	
	public function wf_shipping_pro_item_quantity( $quantity, $productid ) {

		$product 	= wc_get_product($productid);
		if( $product instanceof WC_Product_Variation ) {
			$productid = $product->get_parent_id();
		}
		$unit 		= get_post_meta( $productid, '_wf_shipping_unit', true );
		if(!empty($unit) && is_numeric($unit) && !empty($quantity) && is_numeric($quantity)){
			return $quantity * $unit;
		}
		return $quantity;
	}
	
	public function plugin_action_links( $links ) {
		$plugin_links = array(
			'<a href="https://wordpress.org/support/plugin/per-product-addon-for-woocommerce-shipping-pro" target="_blank">' . __( 'Support', 'wf_per_product_shipping_addon' ) . '</a>'
		);
		return array_merge( $plugin_links, $links );
	}

	public function wf_add_product_field_shipping_unit(){ 
		global $woocommerce, $post;

		echo '<p>';


		woocommerce_wp_text_input( 
			array( 
				'id'          => '_wf_shipping_unit', 
				'label'       => __( 'Shipping Unit', 'wf_per_product_shipping_addon' ), 
				'placeholder' => '1',
				'desc_tip'    => 'true',
				'description' => __( 'Product unit to be used for shipping calculation', 'wf_per_product_shipping_addon' ) 
			)
			
		);
		

		echo '</p>';
	}
	
	public function wf_save_product_field_shipping_unit( $post_id ){
	
		// Text Field
		$shipping_unit = $_POST['_wf_shipping_unit'];
		if( !empty( $shipping_unit ) )
			update_post_meta( $post_id, '_wf_shipping_unit', esc_attr( $shipping_unit ) );
	}	
		
	private function wf_get_settings_url()
	{
		return version_compare(WC()->version, '2.1', '>=') ? "wc-settings" : "woocommerce_settings";
	}
}	
new wf_per_product_shipping_addon_setup();
