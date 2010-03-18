<?php
/**
 * urlencode - Data encoder for Cryptool
 * Copyright 2007 Olle Johansson <Olle@Johansson.com>
 * Released under the GNU General Public License v2
 */
 
function dataEncoder( $data, $dir ) {
	if ( $dir == "1" ) {
		return urlencode( $data );
	} else {
		return urldecode( $data );
	}
}

?>