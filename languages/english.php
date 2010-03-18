<?php

class Language {
	var $_strings = array(
		'Cryptool' => "Cryptool",
		'' => "",
	);
	
	function str( $str ) {
		if ( isset( $this->_strings[$str] ) ) {
			return $this->_strings[$str];
		} else {
			return $str;
		}
	}
	
	function setStr( $str, $string ) {
		$this->_strings[$str] = $string;
	}
}

?>