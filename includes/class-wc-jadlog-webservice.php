<?php
/**
 * Jadlog Webservice.
 *
 * @package WooCommerce_Jadlog/Classes/Webservice
 * @since 0.0.0
 * @version 0.0.0
 */

if ( ! defined( 'ABSPATH') ) {
    exit;
}

/**
 * Jadlog Webservice integration class.
 */
class WC_Jadlog_Webservice {

    /**
     * Webservice URL.
     *
     * @var string
     */
    private $_webservice = "http://www.jadlog.com.br:8080/JadlogEdiWs/services/NotfisBean?";

    /**
     * Webservice redundant URL.
     *
     * @var string
     */
    private $_webserviceRedundant = "http://www.jadlog.com/JadlogEdiWs/services/NotfisBean?";

    /**
     * Client Id (CNPJ in Brazil).
     *
     * @var string
     */
     protected $client_id = '';

    /**
     * Password.
     *
     * @var string
     */
    protected $password = '';

    /**
     * WooCommerce package containing the products.
     *
     * @var array
     */
    protected $package = array();

    /**
     * Shipping method.
     *
     * 0  - EXPRESSO.
     * 3  - PACKAGE.
     * 4  - RODOVIÁRIO.
     * 5  - ECONÔMICO.
     * 6  - DOC.
     * 7  - CORPORATE.
     * 9  - .COM.
     * 10 - INTERNACIONAL.
     * 12 - CARGO.
     * 14 - EMERGÊNCIAL.
     *
     * @var string
     */
    protected $modality = '';

    protected $modality_list = array();

    /**
     * Insurance, 'A' or 'N'.
     *
     * A - Proprietary apolice.
     * N - Normal.
     *
     * @var string
     */
    protected $insurance_type = '';

    /**
     * Invoice value.
     *
     * @var float
     */
    protected $invoice_value = 0.0;

    /**
     * Collect value.
     *
     * @var float
     */
    protected $collect_value = 0.0;

    /**
     * Zipcode origin.
     *
     * @var string
     */
    protected $zip_origin = '';

    /**
     * Zipcode destination.
     *
     * @var string
     */
    protected $zip_destination = '';

    /**
     * Shipping payment mode.
     *
     * S or N (yes or no), if payment at destination.
     *
     * @var string
     */
     protected $payment_mode = 'S';

     /**
      * Delivery mode.
      *
      * D - Home delivery.
      * R - Pickup in the local unit.
      *
      * @var string
      */
     protected $delivery_mode = 'D';

     /**
      * Package height.
      *
      * @var float
      */
     protected $height = 0;

     /**
      * Package width.
      *
      * @var float
      */
     protected $width = 0;

     /**
      * Package diameter.
      *
      * @var float
      */
     protected $diameter = 0;

     /**
      * Package length.
      *
      * @var float
      */
     protected $length = 0;

     /**
      * Package weight.
      *
      * @var float
      */
     protected $weight = 0;

     /**
      * Minimum height.
      *
      * @var float
      */
     protected $minimum_height = 2;

     /**
      * Minimum width.
      *
      * @var float
      */
     protected $minimum_width = 11;

     /**
      * Minimum length.
      *
      * @var float
      */
     protected $minimum_length = 16;

    /**
 	 * Debug mode.
 	 *
 	 * @var string
 	 */
 	protected $debug = 'no';

 	/**
 	 * Logger.
 	 *
 	 * @var WC_Logger
 	 */
 	protected $log = null;

    /**
     * Ship to destination 'S' or customer will get the
     * product on Jadlog's distribution center 'N'.
     *
     * @var string
     */
    protected $woocommerce_jadlogship_to_destination = '';

    /**
	 * Initialize webservice.
	 *
	 * @param string $id Method ID.
	 * @param int    $instance_id Instance ID.
	 */
	public function __construct( $id = 'jadlog', $instance_id = 0 ) {
		$this->id           = $id;
		$this->instance_id  = $instance_id;
		$this->log          = new WC_Logger();

        $this->modality_list = array(
            6  => 3333,
            7  => 6000,
            9  => 6000,
            10 => 6000,
            12 => 6000,
            14 => 3333
        );
	}

    /**
     * Set Client Id code.
     *
     * @param string $client_id Client Id code.
     */
    public function set_client_id( $client_id = '' ) {
        $this->client_id = $client_id;
    }

    /**
     * Set shipping package.
     *
     * @param array $package Shipping package.
     */
    public function set_package( $package = array() ) {
        $this->package = $package;
        $jadlog_package = new WC_Jadlog_Package( $package );

        if ( ! is_null( $jadlog_package ) ) {
            $data = $jadlog_package->get_data();
            $this->set_height( $data['height'] );
            $this->set_width( $data['width'] );
            $this->set_length( $data['length'] );
        }

        if ( 'yes' === $this->debug ) {
            if ( ! empty( $data ) ) {
                $data = array(
                    'weight' => $this->get_weight(),
                    'height' => $this->get_height(),
                    'width'  => $this->get_width(),
                    'length' => $this->get_length(),
                );
            }
            $this->log->add( $this->id, 'Weight and cubage of the order: ' . print_r( $data, true ) );
        }
    }

    /**
     * Set origin zipcode.
     *
     * @param string $zipcode Origin zipcode.
     */
    public function set_zip_origin( $zipcode = '' ) {
        $this->zip_origin = $zipcode;
    }

    /**
     * Set destinaton zipcode.
     *
     * @param string $zipcode Destination zipcode.
     */
    public function set_zip_destination( $zipcode = '' ) {
        $this->zip_destination = $zipcode;
    }

    /**
	 * Set password.
	 *
	 * @param string $password User login.
	 */
	public function set_password( $password = '' ) {
		$this->password = $password;
	}

    /**
     * Set modality divisor.
     * @param string $modality Modality position value.
     */
    public function set_modality( $modality = '' ) {
        $this->modality = $modality_list[ $modality ];
    }

	/**
	 * Set shipping package height.
	 *
	 * @param float $height Package height.
	 */
	public function set_height( $height = 0 ) {
		$this->height = (float) $height;
	}

	/**
	 * Set shipping package width.
	 *
	 * @param float $width Package width.
	 */
	public function set_width( $width = 0 ) {
		$this->width = (float) $width;
	}

	/**
	 * Set shipping package diameter.
	 *
	 * @param float $diameter Package diameter.
	 */
	public function set_diameter( $diameter = 0 ) {
		$this->diameter = (float) $diameter;
	}

	/**
	 * Set shipping package length.
	 *
	 * @param float $length Package length.
	 */
	public function set_length( $length = 0 ) {
		$this->length = (float) $length;
	}

	/**
	 * Set shipping package weight.
	 *
	 * @param float $weight Package weight.
	 */
	public function set_weight( $weight = 0 ) {
		$this->weight = (float) $weight;
	}

	/**
	 * Set minimum height.
	 *
	 * @param float $minimum_height Package minimum height.
	 */
	public function set_minimum_height( $minimum_height = 2 ) {
		$this->minimum_height = 2 <= $minimum_height ? $minimum_height : 2;
	}

	/**
	 * Set minimum width.
	 *
	 * @param float $minimum_width Package minimum width.
	 */
	public function set_minimum_width( $minimum_width = 11 ) {
		$this->minimum_width = 11 <= $minimum_width ? $minimum_width : 11;
	}

	/**
	 * Set minimum length.
	 *
	 * @param float $minimum_length Package minimum length.
	 */
	public function set_minimum_length( $minimum_length = 16 ) {
		$this->minimum_length = 16 <= $minimum_length ? $minimum_length : 16;
	}

	/**
	 * Set declared value.
	 *
	 * @param string $declared_value Declared value.
	 */
	public function set_declared_value( $declared_value = '0' ) {
		$this->declared_value = $declared_value;
	}

	/**
	 * Set own hands.
	 *
	 * @param string $own_hands Use 'N' for no and 'S' for yes.
	 */
	public function set_own_hands( $own_hands = 'N' ) {
		$this->own_hands = $own_hands;
	}

    /**
	 * Get webservice URL.
	 *
	 * @return string
	 */
	public function get_webservice_url() {
		return apply_filters( 'woocommerce_jadlog_webservice_url', $this->_webservice, $this->id, $this->instance_id, $this->package );
	}

	/**
	 * Get origin zipcode.
	 *
	 * @return string
	 */
	public function get_zip_origin() {
		return apply_filters( 'woocommerce_jadlog_zip_origin', $this->zip_origin, $this->id, $this->instance_id, $this->package );
	}

    /**
     * Get destination zipcode.
     *
     * @return string
     */
    public function get_zip_destination() {
        return apply_filters( 'woocommerce_jadlog_zip_destination', $this->zip_destination, $this->id, $this->instance_id, $this->package );
    }

	/**
	 * Get password.
	 *
	 * @return string
	 */
	public function get_password() {
		return apply_filters( 'woocommerce_jadlog_password', $this->password, $this->id, $this->instance_id, $this->package );
	}

	/**
	 * Get height.
	 *
	 * @return float
	 */
	public function get_height() {
		return $this->float_to_string( $this->minimum_height <= $this->height ? $this->height : $this->minimum_height );
	}

	/**
	 * Get width.
	 *
	 * @return float
	 */
	public function get_width() {
		return $this->float_to_string( $this->minimum_width <= $this->width ? $this->width : $this->minimum_width );
	}

	/**
	 * Get diameter.
	 *
	 * @return float
	 */
	public function get_diameter() {
		return $this->float_to_string( $this->diameter );
	}

	/**
	 * Get length.
	 *
	 * @return float
	 */
	public function get_length() {
		return $this->float_to_string( $this->minimum_length <= $this->length ? $this->length : $this->minimum_length );
	}

	/**
	 * Get weight.
	 *
	 * @return float
	 */
	public function get_weight() {
		return $this->float_to_string( $this->weight );
	}

	/**
	 * Fix number format for XML.
	 *
	 * @param  float $value  Value with dot.
	 *
	 * @return string        Value with comma.
	 */
	protected function float_to_string( $value ) {
		$value = str_replace( '.', ',', $value );

		return $value;
	}

    /**
     * Get shipping prices.
     *
     * @return SimpleXMLElement|array
     */
    public function get_shipping() {
        $shipping = null;
        // Checks if service and postcode are empty.
        if ( ! $this->is_available() ) {
            return $shipping;
        }

        $args = apply_filters( 'woocommerce_jadlog_shipping_args', array(
            'vModalidade'         => $this->modality,
            'Password'            => $this->password,
            'vSeguro'             => $this->insurance_type,
            'vVlDec'              => $this->invoice_value,
            'vVlColeta'           => $this->collect_value,
            'vCepOrig'            => $this->zip_origin,
            'vCepDest'            => $this->zip_destination,
            'vPeso'               => $this->cubage_total,
            'vFrap'               => $this->$payment_mode,
            'vEntrega'            => $this->$delivery_mode,
            'vCnpj'               => $this->client_id
        ), $this->id, $this->instance_id, $this->package );

        $url = add_query_arg( $args, $this->get_webservice_url() );

        if ( 'yes' === $this->debug ) {
            $this->log->add( $this->id, 'Requesting jadlog WebServices: ' . $url );
        }

        // Gets the WebServices response.
        $response = wp_safe_remote_get( esc_url_raw( $url ), array( 'timeout' => 30 ) );
        if ( is_wp_error( $response ) ) {
            if ( 'yes' === $this->debug ) {
                $this->log->add( $this->id, 'WP_Error: ' . $response->get_error_message() );
            }
        } elseif ( $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
            try {
                $result = wc_jadlog_safe_load_xml( $response['body'], LIBXML_NOCDATA );
            } catch ( Exception $e ) {
                if ( 'yes' === $this->debug ) {
                    $this->log->add( $this->id, 'jadlog WebServices invalid XML: ' . $e->getMessage() );
                }
            }

            if ( isset( $result->Jadlog_Valor_Frete ) ) {
                if ( 'yes' === $this->debug ) {
                    $this->log->add( $this->id, 'jadlog WebServices response: ' . print_r( $result, true ) );
                }

                $shipping = $result->Jadlog_Valor_Frete;
            }
        } else {
            if ( 'yes' === $this->debug ) {
                $this->log->add( $this->id, 'Error accessing the jadlog WebServices: ' . print_r( $response, true ) );
            }
        }

        return $shipping;
    }
}
