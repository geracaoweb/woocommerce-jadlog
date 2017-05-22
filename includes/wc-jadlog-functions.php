<?php
/**
 * Jadlog functions.
 *
 * @package WooCommerce_Jadlog/Functions
 * @since   0.0.0
 * @version 0.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Safe load XML.
 *
 * @throws Exception Show detected errors while parsing the data.
 *
 * @param  string $source Source XML.
 * @param  int    $options Reading options.
 *
 * @return SimpleXMLElement|bool
 */
function wc_jadlog_safe_load_xml( $source, $options = 0 ) {
	$old = null;

	if ( function_exists( 'libxml_disable_entity_loader' ) ) {
		$old = libxml_disable_entity_loader( true );
	}

	$dom    = new DOMDocument();
	$return = $dom->loadXML( trim( $source ), $options );

	if ( ! is_null( $old ) ) {
		libxml_disable_entity_loader( $old );
	}

	if ( ! $return ) {
		return false;
	}

	if ( isset( $dom->doctype ) ) {
		throw new Exception( 'Unsafe DOCTYPE Detected while XML parsing' );
		return false;
	}

	return simplexml_import_dom( $dom );
}
