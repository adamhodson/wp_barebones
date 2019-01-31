<?php

use ioncube\phpOpensslCryptor\Cryptor;

class SWPSMTPUtils {

    var $enc_key;
    protected static $instance = null;

    function __construct() {
	require_once('inc/Cryptor.php');
	$key = get_option( 'swpsmtp_enc_key', false );
	if ( empty( $key ) ) {
	    $key = wp_salt();
	    update_option( 'swpsmtp_enc_key', $key );
	}
	$this->enc_key = $key;
    }

    public static function get_instance() {

	// If the single instance hasn't been set, set it now.
	if ( null == self::$instance ) {
	    self::$instance = new self;
	}

	return self::$instance;
    }

    static function base64_decode_maybe( $str ) {
	if ( ! function_exists( 'mb_detect_encoding' ) ) {
	    return base64_decode( $str );
	}
	if ( mb_detect_encoding( $str ) === mb_detect_encoding( base64_decode( base64_encode( base64_decode( $str ) ) ) ) ) {
	    $str = base64_decode( $str );
	}
	return $str;
    }

    function encrypt_password( $pass ) {
	if ( $pass === '' ) {
	    return '';
	}

	$password = Cryptor::Encrypt( $pass, $this->enc_key );
	return $password;
    }

    function decrypt_password( $pass ) {

	$password = Cryptor::Decrypt( $pass, $this->enc_key );
	return $password;
    }

}
