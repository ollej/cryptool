<?php
/**
 * Cryptool - A tool to encrypt or decrypt data and choose different visualisations.
 * License: GNU General Public License v2
 * Copyright: Olle Johansson 2007 - <Olle@Johansson.com>
 *
 * <h1>License</h1>
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * <h1>Documentation</h1>
 * If you want to add encoders just create a php script in the "encoder" directory and name it
 * after the encoding type. The script should include a function called "dataEncoder" which takes
 * an argument with the data to encode and an argument with a boolean setting if it should be
 * encoded or decoded. The function should return the encoded string.
 *
 * Make sure not to name a script after an encoder available in the PHP function hash_algos()
 * as cryptool will default to those algorithms first.
 *
 * <h1>Changelog</h1>
 * version 1.1 - (2007-12-18) Added functionality for base32 and hash_algos()
 *
 * <h1>TODO</h1>
 * There seems to be a bug with not stripping slashes when magic_quotes_gpc is set.
 */

// Set this to the path where this script is installed.
// If you have problems loading templates and encoders, set this to the
// full server path where cryptool is installed.
DEFINE( 'JX_SERVERPATH', '/home/gardener/public_html/scripts/cryptool' );

$cfg = array();

// List of allowed html tags, separated by the pipe symbol (|).
// Example: br|b|i|p|u|pre|center|hr|blockquote|em|strong|big|small|h1|h2|h3|h4|h5|h6|q|sub|sup|tt|cite|code|address|abbr
// Some possibly malicious tags are always removed, like script, style, applet, object, head, title
$cfg['allowedhtml'] = "";

// Select the language you want to use.
$cfg['language'] = "english";

// ** End of configurable options. **

// Read the language file.
if ( file_exists( JX_SERVERPATH . "/languages/{$cfg['language']}.php" ) ) {
	include( JX_SERVERPATH . "/languages/{$cfg['language']}.php" );
} else {
	include( JX_SERVERPATH . "/languages/english.php" );
}
$lang = new Language();

// Read some variables.
$task = getVal( 'task', 'GET', 1 );
$data = getVal( 'data', 'GET', 1 );
$type = getVal( 'type', 'GET', 1 );
$key = getVal( 'key', 'GET', 1 );
$eord = getVal( 'eord', 'GET', null );
if ( !isset($eord) ) $eord = "1";

// Let's find out what to do.
$content = "";
switch ( $task ) {
	case 'encode': $content .= encodeData( $data, $type, $eord, $key ); break;
	default: $content .= showForm( $data, $type, $eord, $key );
}

// Show the data.
$vars = array( 'content' => $content );
echo readTemplate( "main", $vars );

// Let's quit nicely.
exit();

// Start of functions **********************************************************

function encodeData( $data, $type, $eord, $key ) {
	global $lang, $cfg;
	if (in_array($type, hash_algos())) {
		$encoded = hash($type, $data);
	} else if ( !readEncoderScript( $type ) ) {
		return showError( "Couldn't load the selected encoder." );
	} else {
		$encoded = dataEncoder( trim( $data ), $eord );
	}
	$vars = array(
		'encoded' => $encoded,
		);
	//return readTemplate( 'display', $vars );
	return showForm($data, $type, $eord, $key, $vars);
}

function showForm( $data="", $type="", $eord="", $key="", $morevars=array() ) {
	global $lang, $cfg;
	$encoders = listEncoders();
	if ( count( $encoders ) <= 0 ) {
		return showError( "No encoders found." );
	}

	$vars = array(
		'encoders' => $encoders,
		'data' => $data,
		'type' => $type,
		'eord' => $eord,
		'key' => $key,
		);
	if (is_array($morevars))
	{
		$vars = array_merge($vars, $morevars);
	}
	return readTemplate( 'form', $vars );
}

function listEncoders() {
	global $lang, $cfg;
	$path = JX_SERVERPATH . "/encoders";
	$files = listdir( $path, false );

	// Add encoders from hash_algos()
	$files = array_merge($files, hash_algos());

	// Add encryptions from mcrypt
	$files[] = '3des';
	$files[] = 'arcfour';
	$files[] = 'arcfour_iv';
	$files[] = 'blowfish';
	$files[] = 'cast_128';
	$files[] = 'cast_256';
	$files[] = 'des';
	$files[] = 'enigma';
	$files[] = 'loki97';
	$files[] = 'panama';
	$files[] = 'rijndael_128';
	$files[] = 'rijndael_192';
	$files[] = 'rijndael_256';
	$files[] = 'rc2';
	$files[] = 'rc4';
	$files[] = 'rc6';
	$files[] = 'rc6_128';
	$files[] = 'rc6_192';
	$files[] = 'rc6_256';
	$files[] = 'safer64';
	$files[] = 'safer128';
	$files[] = 'saferplus';
	$files[] = 'serpent';
	$files[] = 'serpent_128';
	$files[] = 'serpent_192';
	$files[] = 'serpent_256';
	$files[] = 'skipjack';
	$files[] = 'tean';
	$files[] = 'threeway';
	$files[] = 'tripledes';
	$files[] = 'twofish';
	$files[] = 'twofish128';
	$files[] = 'twofish192';
	$files[] = 'twofish256';
	$files[] = 'wake';
	$files[] = 'xtea';

	natsort($files);
	return $files;
}

function listdir( $path, $withsuffix=true ) {
	$files = array();
	if ( is_dir( $path ) )
	{
		if ( $dh = opendir( $path ) )
		{
			# Read the directory
			while ( ( $file = readdir( $dh ) ) !== false ) {
				if ( $file != '.' && $file != '..' && strpos($file, '.') !== 0 && $file ) {
					$files[] = ($withsuffix) ? $file : stripSuffix( $file );
				}
			}
			closedir( $dh );
		}
	}
	return $files;
}

function stripSuffix( $file ) {
	return substr( $file, 0, strrpos( $file, "." ) );
}

function readTemplate( $tmpl, $vars ) {
	global $lang, $cfg;
	$tmpl = stripChars( $tmpl );
	$templatepath = JX_SERVERPATH . "/templates/$tmpl.tmpl";
	if ( !file_exists( $templatepath ) ) {
		return showError( "Couldn't find template file." );
	}
	ob_start();
	include( $templatepath );
	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}

function showError( $error ) {
	global $lang, $cfg;
	return "<div class='error'><h1>" . $lang->str( "Error" ) . "</h1><p>$error</p>";
}

function readEncoderScript( $type ) {
	$type = stripChars( $type );
	$encoderfile = JX_SERVERPATH . "/encoders/" . $type . ".php";
	if ( file_exists( $encoderfile ) ) {
		include_once( $encoderfile );
		return true;
	} else if (function_exists('mcrypt_generic_init')) {
		include_once( JX_SERVERPATH . '/encoders/mcrypt.php' );
		return true;
	} else {
		return false;
	}
}

/**
 * Cleans strings for filenames, removes accents on characters and then removes unwanted characters.
 */
function stripChars( $str ) {
	// First we'll replace some characters with accents with their alphabetical counterparts.
	strtr( $str, "\x{0160}\x{0152}\x{017D}\x{0161}\x{0153}\x{017E}\x{0178}¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ", "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy" );
	// Now we'll remove any unwanted characters that are left.
	$set = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
	$first = strtr( $str, $set, str_repeat( "#", strlen( $set ) ) );
	$second = strtr( $str, $first, str_repeat( "_", strlen( $first ) ) );
	return $second;
}

/**
 * Cleans the strings in filenames, only allowing for one dot.
 */
function cleanFilename( $file ) {
	$filename = substr( $file, 0, strrpos( $file, "." ) );
	$suffix = substr( $file, strrpos( $file, "." ) + 1 );
	#print "filename: $filename, suffix: $suffix <br />";
	$filename = stripChars( $filename );
	$suffix = stripChars( $suffix );
	#print "filename: $filename, suffix: $suffix <br />";
	return "$filename.$suffix";
}

/**
 * Read get/post/cookie data, removing slashes and doing some integrity checks.
 */
function getVal( $val, $type="POST", $nukehtml=1 ) {
	if ( PHP_VERSION < 4.1 ) {
		global $HTTP_GET_VARS, $HTTP_POST_VARS;
	}
	$type = strtoupper( $type );
	if ( $type == "GET" ) {
		if ( PHP_VERSION < 4.1 ) {
			$ret = isset( $HTTP_GET_VARS[$val] ) ? $HTTP_GET_VARS[$val] : null;
		} else {
			$ret = isset( $_GET[$val] ) ? $_GET[$val] : null;
		}
	} else if ( $type == "POST" ) {
		if ( PHP_VERSION < 4.1 ) {
			$ret = isset( $HTTP_POST_VARS[$val] ) ? $HTTP_POST_VARS[$val] : null;
		} else {
			$ret = isset( $_POST[$val] ) ? $_POST[$val] : null;
		}
	} else if ( $type == "COOKIE" ) {
		if ( PHP_VERSION < 4.1 ) {
			$ret = isset( $HTTP_COOOKIE_VARS[$val] ) ? $HTTP_COOOKIE_VARS[$val] : null;
		} else {
			$ret = isset( $_COOKIE[$val] ) ? $_COOKIE[$val] : null;
		}
	} else {
		$ret = null;
	}
	if ( $ret ) {
		if ( is_numeric( $ret ) ) {
			$ret = intval( $ret );
		} else {
			if ( get_magic_quotes_gpc() ) {
				stripslashes( $ret );
			}
		}
	}
	// Remove all unwanted html.
	if ( $nukehtml ) {
		$ret = safehtml_multi( $ret );
	}
	return $ret;
}

/*
Functions to strip unsafe html out of a string.
Based on code found in comments to the strip_tags() function of the online
PHP manual at www.php.net.
*/

/**
 * Parses a string until there is no unsafe html code left.
 * This is done multiple times to ensure that there is some
 * nested tags that won't show up until unsafe code is stripped.
 */
function safehtml_multi( $str ) {
    $newstr = safehtml( $str );
    while ( $newstr != $str ) {
        $str = $newstr;
        $newstr = safehtml( $str );
    }
    return $newstr;
}

/**
 * Nuke tags and their contents.
 */
function nuke_contents( $str ) {
	$disallowed = array( "script", "head", "title", "style", "applet", "object" );
    foreach ( $disallowed as $tag ) {
        $str = preg_replace( "'<\s*?{$tag}[^>]*?>.*?<\s*?/\s*?{$tag}[^>]*?>'si", "", $str );
    }
    return $str;
}

/**
 * Strip unwanted tags.
 */
function safehtml( $str ) {
	global $cfg;

    // Nuke some tags and anything inbetween
    $str = nuke_contents( $str );

    // Listed of tags that will not be stripped but whose attributes will be.
    $allowed = $cfg['allowedhtml'];
    // Start removing unwanted tags and attributes to wanted tags.
    $str = preg_replace( "/<((?!\/?($allowed)\b)[^>]*>)/xis", "", $str );
    $str = preg_replace( "/<($allowed)[^>]*?>/xis", "<\\1>", $str );
    $str = str_replace( "<br>", "<br />", $str );
    $str = str_replace( "<hr>", "<hr />", $str );

    return $str;
}
