<?php
/**
 * Crypt - Data encoder for Cryptool
 * Copyright 2007 Olle Johansson <Olle@Johansson.com>
 * Released under the GNU General Public License v2
 */
 
function dataEncoder( $data, $dir ) {
	return crypt( $data );
}

?>