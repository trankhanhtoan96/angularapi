<?php


require_once('include/Pear/Crypt_Blowfish/Blowfish.php');
// require_once('include/dir_inc.php');


///////////////////////////////////////////////////////////////////////////////
////	BLOWFISH
/**
 * @deprecated moved to EncryptionUtils
 * retrives the system's private key; will build one if not found, but anything encrypted before is gone...
 * @param string type
 * @return string key
 */
function blowfishGetKey($type) {
	$key = [];

	$type = str_rot13($type);

	$keyCache = "custom/blowfish/{$type}.php";

	// build cache dir if needed
	if(!file_exists('custom/blowfish')) {
		mkdir_recursive('custom/blowfish');
	}

	// get key from cache, or build if not exists
	if(file_exists($keyCache)) {
		include($keyCache);
	} else {
		// create a key
		$key[0] = create_guid();
		write_array_to_file('key', $key, $keyCache);
	}
	return $key[0];
}

/**
 * @deprecated moved to EncryptionUtils
 * Uses blowfish to encrypt data and base 64 encodes it. It stores the iv as part of the data
 * @param STRING key - key to base encoding off of
 * @param STRING data - string to be encrypted and encoded
 * @return string
 */
function blowfishEncode($key, $data){
	$bf = new Crypt_Blowfish($key);
	$encrypted = $bf->encrypt($data);
	return base64_encode($encrypted);
}

/**
 * @deprecated moved to EncryptionUtils
 * Uses blowfish to decode data assumes data has been base64 encoded with the iv stored as part of the data
 * @param STRING key - key to base decoding off of
 * @param STRING encoded base64 encoded blowfish encrypted data
 * @return string
 */
function blowfishDecode($key, $encoded){
	$data = base64_decode($encoded);
	$bf = new Crypt_Blowfish($key);
	return trim($bf->decrypt($data));
}
