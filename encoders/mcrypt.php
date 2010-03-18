<?php
/**
 * Mcrypt - Mcrypt data encoder for Cryptool
 * Copyright 2007 Olle Johansson <Olle@Johansson.com>
 * Released under the GNU General Public License v2
 */

function dataEncoder( $data, $dir ) {
    $key = getVal('key', 'GET', 1);
    $encoder = getVal('type', 'GET', 1);
    if (($encoder == 'rc4') || ($encoder == 'wake')) {
    	$mode = "stream";
    } else {
    	$mode = 'ecb';
    }

    // Make sure there is an encoder.
    if (!trim($encoder)) {
    	return false;
    }

	try {
		list($usec, $sec) = explode(' ', microtime());
		$seed = (float) $sec + ((float) $usec * 100000);
		srand($seed);
	    $td = mcrypt_module_open($encoder, '', $mode, '');
	    if (!$td) return false;
	    $ivsize = mcrypt_enc_get_iv_size($td);
	    $iv = "";
	    if ($ivsize > 0) $iv = mcrypt_create_iv ($ivsize, MCRYPT_RAND);
	    mcrypt_generic_init($td, $key, $iv);
	    if ( $dir == "1" ) {
		    $encrypted_data = mcrypt_generic($td, $data);
	    } else {
	    	$encrypted_data = mdecrypt_generic($td, $data);
	    }
	    mcrypt_generic_deinit($td);
	    mcrypt_module_close($td);
	} catch (Exception $e) {
		echo "Error: " . $e->getMessage();
		return false;
	}

    return $encrypted_data;
}

?>
