<?php
/**
 * Base64 - Data encoder for Cryptool
 * Copyright 2007 Olle Johansson <Olle@Johansson.com>
 * Released under the GNU General Public License v2
 */
 
function dataEncoder( $data, $dir ) {
	if ( $dir == "1" ) {
		return base64_encode( $data );
	} else {
		return base64_decode( $data );
	}
}

?>