<?php
/**
 * Jadlog Normal shipping method.
 *
 * @package WooCommerce_Jadlog/Classes/Shipping
 * @since   0.0.0
 * @version 0.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * PAC shipping method class.
 */
class WC_Jadlog_Shipping_Normal extends WC_Jadlog_Shipping {

	/**
	 * Service code.
	 * 04510 - PAC without contract.
	 *
	 * @var string
	 */
	protected $code = '04510';

	/**
	 * Corporate code.
	 * 04669 - PAC with contract.
	 *
	 * @var string
	 */
	protected $corporate_code = '04669';

	/**
	 * Initialize PAC.
	 *
	 * @param int $instance_id Shipping zone instance.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id           = 'jadlog-normal';
		$this->method_title = __( 'Jadlog Normal', 'woocommerce-jadlog' );
        $this->more_link    = '';

		parent::__construct( $instance_id );
	}
}
