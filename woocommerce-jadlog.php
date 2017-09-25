<?php

/**
 * Plugin Name: WooCommerce Jadlog
 * Plugin URI:  https://github.com/marksabbath/woocommerce-jadlog
 * Description: Adds Jadlog shipping method to WooCommerce, based on woocomerce-correios.
 * Author:      Marcos Schratzenstaller
 * Author URI:  https://schratzenstaller.com.br
 * Version:     0.0.0
 * License:     BSD
 * Text Domain: woocommerce-jadlog
 * Domain Path: /languages
 *
 * @package WooCommerce_Jadlog
 */

if ( ! defined( 'ABSPATH' ) ) {
 	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_Jadlog' ) ) :

    /**
     * WooCommerce Jadlog main class.
     */
    class WC_Jadlog {

        /**
         * Plugin version.
         *
         * @var string
         */

        const VERSION = '0.0.0';

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

        /**
         * Constructor.
         */
        public function __construct() {
            add_action( 'init', array( $this, 'load_plugin_textdomain' ), -1 );

            // Checks with WooCommerce is installed.
			if ( class_exists( 'WC_Integration' ) ) {
				$this->includes();
				if ( is_admin() ) {
					$this->admin_includes();
				}

				//add_filter( 'woocommerce_integrations', array( $this, 'include_integrations' ) );
				add_filter( 'woocommerce_shipping_methods', array( $this, 'include_methods' ) );
				//add_filter( 'woocommerce_email_classes', array( $this, 'include_emails' ) );
			} else {
				add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			}
		}

        /**
         * Return an instance of this class.
         *
         * @return object A single instance of this class.
         */
        public static function get_instance() {
            // If the single instance hasn't been set, set it now.
            if ( null === self::$instance ) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        /**
         * Load the plugin text domain for translation.
         */
        public function load_plugin_textdomain() {
            load_plugin_textdomain( 'woocommerce-jadlog', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        }

	/**
	 * Admin includes.
	 */
	private function admin_includes() {
	}

        /**
         * Includes.
         */
        private function includes() {
            include_once dirname( __FILE__ ) . '/includes/wc-jadlog-functions.php';
            include_once dirname( __FILE__ ) . '/includes/class-wc-jadlog-install.php';
            include_once dirname( __FILE__ ) . '/includes/class-wc-jadlog-package.php';
            include_once dirname( __FILE__ ) . '/includes/class-wc-jadlog-webservice.php';
            include_once dirname( __FILE__ ) . '/includes/abstracts/abstract-wc-jadlog-shipping.php';

            foreach ( glob( plugin_dir_path( __FILE__ ) . '/includes/shipping/*.php' ) as $filename ) {
                include_once $filename;
            }


        }

        /**
         * Include Jadlog shipping methods to WooCommerce.
         *
         * @param  array $methods Default shipping methods.
         *
         * @return array
         */
        public function include_methods( $methods ) {
                $methods['jadlog-normal'] = 'WC_Jadlog_Shipping_Normal';

            return $methods;
        }

        /**
         * WooCommerce fallback notice.
         */
        public function woocommerce_missing_notice() {
            include_once dirname( __FILE__ ) . '/includes/admin/views/html-admin-missing-dependencies.php';
        }

        /**
         * Get main file.
         *
         * @return string
         */
        public static function get_main_file() {
            return __FILE__;
        }

        /**
         * Get plugin path.
         *
         * @return string
         */
        public static function get_plugin_path() {
            return plugin_dir_path( __FILE__ );
        }

        /**
         * Get templates path.
         *
         * @return string
         */
        public static function get_templates_path() {
            return self::get_plugin_path() . 'templates/';
        }
    }

    add_action( 'plugins_loaded', array( 'WC_Jadlog', 'get_instance' ) );
endif;
