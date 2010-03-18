<?php
/**
 * Rotate 13 - Data encoder for Cryptool
 * Copyright 2007 Olle Johansson <Olle@Johansson.com>
 * Released under the GNU General Public License v2
 */

$rot13_count = 13;

function dataEncoder( $text, $dir ) {
	global $rot13_count;
	$alphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	$rot13_count = intval( $rot13_count );
	$rot13_count = ( $rot13_count <= 26 && $rot13_count > 0 ) ? $rot13_count : 13;
	$rot = substr( $alphabet, $rot13_count );
	$rot .= substr( $alphabet, 0, $rot13_count - 26 );
	$rot .= strtolower( $rot );
	$alphabet .= strtolower( $alphabet );

	$text = strtr( $text, $alphabet, $rot );

	return $text;
}

?>