<?php
/*
Plugin Name: 	SSL Secure Seal for WooCommerce
Plugin URI:   	http://arifwidianto.com/plugins/ssl-secure-seal-for-woocommerce/
Description:  	Show and manage SSL secure seal or custom image on WooCommerce pages.
Version:      	0.3.1
Author: 		Arif Widianto
Author URI: 	https://arifwidianto.com/
License:      	GPL2
License URI:  	https://www.gnu.org/licenses/gpl-2.0.html

Credits: JavaScript code and some inspiration taken from wpifixit's plugins: https://wordpress.org/plugins/wc-ssl-seal/
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Preventive check. This plugin requires WooCommerce to run its functionality
 **/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	
	// Provide Media Uploader
	function sswc_media_uploader_enqueue() {
		wp_enqueue_media();
		wp_register_script( 'sswc_media-uploader-js', plugins_url( 'sswcmedia.js' , __FILE__ ), array('jquery') );
		wp_enqueue_script( 'sswc_media-uploader-js' );
	}
	add_action('admin_enqueue_scripts', 'sswc_media_uploader_enqueue');

	/* 
	 * Display SSL Seal if enabled on selected WooCommerce pages
	 */
	function sswc_image_show($image) {
		if ( strlen($image) > 0 ) return '<img src="' . $image . '"/>';
	}
	
	function sswc_show_seal( $seal_showed, $seal_image, $seal_embed ) {
		$seal_code = "";
		if ( isset($seal_showed) ) {
			if ( $seal_showed == "yes" ) {
				if ( isset($seal_image) ) {
					$seal_code .= ( strlen($seal_image) > 0 )? sswc_image_show($seal_image) : "";
				}
				if ( isset($seal_embed) ) {
					$seal_code .= ( strlen($seal_embed) > 0 )? $seal_embed : "";
				}
			}
		}
		echo '<div class="secure_seal right">' . $seal_code . '</div>';
	}

	function sswc_seal_checkout() {
		sswc_show_seal( get_option( 'sswc_show_on_checkout' ), get_option( 'sswc_image' ), get_option( 'sswc_embed' ) );
	}
	add_action('woocommerce_checkout_after_order_review', 'sswc_seal_checkout');

	function sswc_seal_cart() {
		sswc_show_seal( get_option( 'sswc_show_on_cart' ), get_option( 'sswc_image' ), get_option( 'sswc_embed' ) );
	}
	add_action('woocommerce_after_cart_totals', 'sswc_seal_cart');

	function sswc_seal_shop() {
		sswc_show_seal( get_option( 'sswc_show_on_product' ), get_option( 'sswc_image' ), get_option( 'sswc_embed' ) );
	}
	add_action('woocommerce_after_shop_loop', 'sswc_seal_shop');

	function sswc_seal_account() {
		sswc_show_seal( get_option( 'sswc_show_on_account' ), get_option( 'sswc_image' ), get_option( 'sswc_embed' ) );
	}
	add_action('woocommerce_after_my_account', 'sswc_seal_account');
	
	function sswc_add_settings_tab( $settings_tabs ) {
		$settings_tabs['sslseal'] = __( 'SSL Seal', 'sslseal_tab' );
		return $settings_tabs;
	}
	
	function sswc_settings_tab() {
		woocommerce_admin_fields( sswc_get_settings() );
	}
	
	function sswc_update_settings() {
		woocommerce_update_options( sswc_get_settings() );
	}
	
	function sswc_validate($input) {
		return $input;
	}
	
	function sswc_get_settings() {
		$settings_sswc = ( isset($settings) )? $settings : null;

		// Add Title to the Settings
		$settings_sswc[] = array( 
		'name' => __( 'SSL Secure Seal for WooCommerce Settings', 'sslseal' ), 
		'type' => 'title', 
		'desc' => __( 'Configure SSL Secure Seal for WooCommerce pages. You may use custom seal image, seal embed code, or both. SSL Secure Seal plugin will output custom image and embed code to designated WooCommerce pages. All code wrapped inside a div with "secure_seal" class placeholder, so you may style them later (if necessary).', 'sslseal' ), 
		'id' => 'sslseal' );

		// Add text field option for image of the seal
		$settings_sswc[] = array(
			'name'     => __( 'Custom Seal Image', 'text-domain' ),
			'desc_tip' => __( 'Upload Your Custom SSL Seal Image', 'text-domain' ),
			'id'       => 'sswc_image',
			'type'     => 'text',
			'default'  => plugins_url() . '/ssl-secure-seal-for-woocommerce/secure-seal.png',
			'css'      => 'min-width:400px;',
			'desc'     => __( '', 'text-domain' ),
		);

		// SSL Seal Embed textfield option
		$settings_sswc[] = array(
			'name'     => __( 'Embed Seal Code', 'text-domain' ),
			'desc_tip' => __( 'This can be used for your SSL certificate embed code.', 'text-domain' ),
			'id'       => 'sswc_embed',
			'type'     => 'textarea',
			'css'      => 'min-width:400px;min-height:75px;',
			'desc'     => __( 'Put HTML embed code here. (<strong>No SCRIPT tag allowed!</strong>)' ),
		);

		// SSL Seal Embed textfield option
		$settings_sswc[] = array(
			'name'     => __( 'Show On Checkout Page', 'text-domain' ),
			'id'       => 'sswc_show_on_checkout',
			'type'     => 'checkbox',
			'css'      => '',
			'default'  => 'yes',
			'desc'     => __( 'Show the seal on checkout page' ),
		);

		$settings_sswc[] = array(
			'name'     => __( 'Show On Product Page', 'text-domain' ),
			'id'       => 'sswc_show_on_product',
			'type'     => 'checkbox',
			'css'      => '',
			'desc'     => __( 'Show the seal on product page' ),
		);

		$settings_sswc[] = array(
			'name'     => __( 'Show On Cart Page', 'text-domain' ),
			'id'       => 'sswc_show_on_cart',
			'type'     => 'checkbox',
			'css'      => '',
			'desc'     => __( 'Show the seal on cart page' ),
		);

		$settings_sswc[] = array(
			'name'     => __( 'Show On Account Page', 'text-domain' ),
			'id'       => 'sswc_show_on_account',
			'type'     => 'checkbox',
			'css'      => '',
			'desc'     => __( 'Show the seal on account page' ),
		);

		$settings_sswc[] = array( 'type' => 'sectionend', 'id' => 'sslseal' );
		return $settings_sswc;

        return apply_filters( 'sslseal_tab_settings', $settings );
	}
	
	add_filter( 'woocommerce_settings_tabs_array', 'sswc_add_settings_tab', 50 );
	add_action( 'woocommerce_settings_tabs_sslseal', 'sswc_settings_tab' );
	add_action( 'woocommerce_update_options_sslseal', 'sswc_update_settings' );	
}

