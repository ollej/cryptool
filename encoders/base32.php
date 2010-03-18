<?php
/**
 * Base32 - Data encoder for Cryptool
 * Copyright 2007 Olle Johansson <Olle@Johansson.com>
 * Released under the GNU General Public License v2
 */
include_once('./includes/class.base32.php5');

function dataEncoder( $data, $dir ) {
	$b = new Base32;
	if ( $dir == "1" ) {
		return $b->fromString( $data );
	} else {
		return $b->toString( $data );
	}
}

?>