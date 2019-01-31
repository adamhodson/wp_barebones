<?php
/*
  Plugin Name: Easy WP SMTP
  Version: 1.3.8
  Plugin URI: https://wp-ecommerce.net/easy-wordpress-smtp-send-emails-from-your-wordpress-site-using-a-smtp-server-2197
  Author: wpecommerce, alexanderfoxc
  Author URI: https://wp-ecommerce.net/
  Description: Send email via SMTP from your WordPress Blog
  Text Domain: easy-wp-smtp
  Domain Path: /languages
 */

//Prefix/Slug - swpsmtp

class EasyWPSMTP {

    var $opts;
    var $plugin_file;
    protected static $instance = null;

    function __construct() {
	$this->opts		 = get_option( 'swpsmtp_options' );
	$this->opts		 = ! is_array( $this->opts ) ? array() : $this->opts;
	$this->plugin_file	 = plugin_basename( __FILE__ );
	require_once('easy-wp-smtp-utils.php');

	add_action( 'plugins_loaded', array( $this, 'plugins_loaded_handler' ) );
	add_filter( 'wp_mail', array( $this, 'wp_mail' ), 2147483647 );
	add_action( 'phpmailer_init', array( $this, 'init_smtp' ), 999 );

	if ( is_admin() ) {
	    require_once('easy-wp-smtp-admin-menu.php');
	    register_activation_hook( __FILE__, array( $this, 'activate' ) );
	    register_uninstall_hook( __FILE__, 'swpsmtp_uninstall' );
	    add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
	    add_filter( 'plugin_row_meta', array( $this, 'register_plugin_links' ), 10, 2 );
	    add_action( 'admin_init', array( $this, 'admin_init' ) );
	    add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}
    }

    public static function get_instance() {

	if ( null == self::$instance ) {
	    self::$instance = new self;
	}

	return self::$instance;
    }

    function wp_mail( $args ) {
	$domain = $this->is_domain_blocked();
	if ( $domain !== false && (isset( $this->opts[ 'block_all_emails' ] ) && $this->opts[ 'block_all_emails' ] === 1) ) {
	    $this->log(
	    "\r\n------------------------------------------------------------------------------------------------------\r\n" .
	    "Domain check failed: website domain (" . $domain . ") is not in allowed domains list.\r\n" .
	    "Following email not sent (block all emails option is enabled):\r\n" .
	    "To: " . $args[ 'to' ] . "; Subject: " . $args[ 'subject' ] . "\r\n" .
	    "------------------------------------------------------------------------------------------------------\r\n\r\n" );
	}
	return $args;
    }

    function init_smtp( &$phpmailer ) {
	//check if SMTP credentials have been configured.
	if ( ! $this->credentials_configured() ) {
	    return;
	}
	//check if Domain Check enabled
	$domain = $this->is_domain_blocked();
	if ( $domain !== false ) {
	    //domain check failed
	    //let's check if we have block all emails option enabled
	    if ( isset( $this->opts[ 'block_all_emails' ] ) && $this->opts[ 'block_all_emails' ] === 1 ) {
		// it's enabled. Let's use gag mailer class that would prevent emails from being sent out.
		$phpmailer = new swpsmtp_gag_mailer();
	    } else {
		// it's disabled. Let's write some info to the log
		$this->log(
		"\r\n------------------------------------------------------------------------------------------------------\r\n" .
		"Domain check failed: website domain (" . $domain . ") is not in allowed domains list.\r\n" .
		"SMTP settings won't be used.\r\n" .
		"------------------------------------------------------------------------------------------------------\r\n\r\n" );
	    }
	    return;
	}

	/* Set the mailer type as per config above, this overrides the already called isMail method */
	$phpmailer->IsSMTP();
	if ( isset( $this->opts[ 'force_from_name_replace' ] ) && $this->opts[ 'force_from_name_replace' ] === 1 ) {
	    $from_name = $this->opts[ 'from_name_field' ];
	} else {
	    $from_name = ! empty( $phpmailer->FromName ) ? $phpmailer->FromName : $this->opts[ 'from_name_field' ];
	}
	$from_email = $this->opts[ 'from_email_field' ];
	//set ReplyTo option if needed
	//this should be set before SetFrom, otherwise might be ignored
	if ( ! empty( $this->opts[ 'reply_to_email' ] ) ) {
	    $phpmailer->AddReplyTo( $this->opts[ 'reply_to_email' ], $from_name );
	}
	// let's see if we have email ignore list populated
	if ( isset( $this->opts[ 'email_ignore_list' ] ) && ! empty( $this->opts[ 'email_ignore_list' ] ) ) {
	    $emails_arr = explode( ',', $this->opts[ 'email_ignore_list' ] );
	    if ( is_array( $emails_arr ) && ! empty( $emails_arr ) ) {
		//we have coma-separated list
	    } else {
		//it's single email
		unset( $emails_arr );
		$emails_arr = array( $this->opts[ 'email_ignore_list' ] );
	    }
	    $from		 = $phpmailer->From;
	    $match_found	 = false;
	    foreach ( $emails_arr as $email ) {
		if ( strtolower( trim( $email ) ) === strtolower( trim( $from ) ) ) {
		    $match_found = true;
		    break;
		}
	    }
	    if ( $match_found ) {
		//we should not override From and Fromname
		$from_email	 = $phpmailer->From;
		$from_name	 = $phpmailer->FromName;
	    }
	}
	$phpmailer->From	 = $from_email;
	$phpmailer->FromName	 = $from_name;
	$phpmailer->SetFrom( $phpmailer->From, $phpmailer->FromName );
	//This should set Return-Path header for servers that are not properly handling it, but needs testing first
	//$phpmailer->Sender	 = $phpmailer->From;
	/* Set the SMTPSecure value */
	if ( $this->opts[ 'smtp_settings' ][ 'type_encryption' ] !== 'none' ) {
	    $phpmailer->SMTPSecure = $this->opts[ 'smtp_settings' ][ 'type_encryption' ];
	}

	/* Set the other options */
	$phpmailer->Host = $this->opts[ 'smtp_settings' ][ 'host' ];
	$phpmailer->Port = $this->opts[ 'smtp_settings' ][ 'port' ];

	/* If we're using smtp auth, set the username & password */
	if ( 'yes' == $this->opts[ 'smtp_settings' ][ 'autentication' ] ) {
	    $phpmailer->SMTPAuth	 = true;
	    $phpmailer->Username	 = $this->opts[ 'smtp_settings' ][ 'username' ];
	    $phpmailer->Password	 = $this->get_password();
	}
//PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate.
	$phpmailer->SMTPAutoTLS = false;

	if ( isset( $this->opts[ 'smtp_settings' ][ 'insecure_ssl' ] ) && $this->opts[ 'smtp_settings' ][ 'insecure_ssl' ] !== false ) {
	    // Insecure SSL option enabled
	    $phpmailer->SMTPOptions = array(
		'ssl' => array(
		    'verify_peer'		 => false,
		    'verify_peer_name'	 => false,
		    'allow_self_signed'	 => true
		) );
	}

	if ( isset( $this->opts[ 'smtp_settings' ][ 'enable_debug' ] ) && $this->opts[ 'smtp_settings' ][ 'enable_debug' ] ) {
	    $phpmailer->Debugoutput = function($str, $level) {
		$this->log( $str );
	    };
	    $phpmailer->SMTPDebug = 1;
	}
	//set reasonable timeout
	$phpmailer->Timeout = 10;
    }

    function test_mail( $to_email, $subject, $message ) {
	$ret = array();
	if ( ! $this->credentials_configured() ) {
	    return false;
	}

	require_once( ABSPATH . WPINC . '/class-phpmailer.php' );
	$mail = new PHPMailer( true );

	try {

	    $charset	 = get_bloginfo( 'charset' );
	    $mail->CharSet	 = $charset;

	    $from_name	 = $this->opts[ 'from_name_field' ];
	    $from_email	 = $this->opts[ 'from_email_field' ];

	    $mail->IsSMTP();

	    // send plain text test email
	    $mail->ContentType = 'text/plain';
	    $mail->IsHTML( false );

	    /* If using smtp auth, set the username & password */
	    if ( 'yes' == $this->opts[ 'smtp_settings' ][ 'autentication' ] ) {
		$mail->SMTPAuth	 = true;
		$mail->Username	 = $this->opts[ 'smtp_settings' ][ 'username' ];
		$mail->Password	 = $this->get_password();
	    }

	    /* Set the SMTPSecure value, if set to none, leave this blank */
	    if ( $this->opts[ 'smtp_settings' ][ 'type_encryption' ] !== 'none' ) {
		$mail->SMTPSecure = $this->opts[ 'smtp_settings' ][ 'type_encryption' ];
	    }

	    /* PHPMailer 5.2.10 introduced this option. However, this might cause issues if the server is advertising TLS with an invalid certificate. */
	    $mail->SMTPAutoTLS = false;

	    if ( isset( $this->opts[ 'smtp_settings' ][ 'insecure_ssl' ] ) && $this->opts[ 'smtp_settings' ][ 'insecure_ssl' ] !== false ) {
		// Insecure SSL option enabled
		$mail->SMTPOptions = array(
		    'ssl' => array(
			'verify_peer'		 => false,
			'verify_peer_name'	 => false,
			'allow_self_signed'	 => true
		    ) );
	    }

	    /* Set the other options */
	    $mail->Host	 = $this->opts[ 'smtp_settings' ][ 'host' ];
	    $mail->Port	 = $this->opts[ 'smtp_settings' ][ 'port' ];
	    if ( ! empty( $this->opts[ 'reply_to_email' ] ) ) {
		$mail->AddReplyTo( $this->opts[ 'reply_to_email' ], $from_name );
	    }
	    $mail->SetFrom( $from_email, $from_name );
	    //This should set Return-Path header for servers that are not properly handling it, but needs testing first
	    //$mail->Sender		 = $mail->From;
	    $mail->Subject		 = $subject;
	    $mail->Body		 = $message;
	    $mail->AddAddress( $to_email );
	    global $debugMSG;
	    $debugMSG		 = '';
	    $mail->Debugoutput	 = function($str, $level) {
		global $debugMSG;
		$debugMSG .= $str;
	    };
	    $mail->SMTPDebug = 1;
	    //set reasonable timeout
	    $mail->Timeout	 = 10;

	    /* Send mail and return result */
	    $mail->Send();
	    $mail->ClearAddresses();
	    $mail->ClearAllRecipients();
	} catch ( Exception $e ) {
	    $ret[ 'error' ] = $mail->ErrorInfo;
	}

	$ret[ 'debug_log' ] = $debugMSG;

	return $ret;
    }

    function admin_init() {
	if ( wp_doing_ajax() ) {
	    add_action( 'wp_ajax_swpsmtp_clear_log', array( $this, 'clear_log' ) );
	}
//view log file
	if ( isset( $_GET[ 'swpsmtp_action' ] ) ) {
	    if ( $_GET[ 'swpsmtp_action' ] === 'view_log' ) {
		$log_file_name = $this->opts[ 'smtp_settings' ][ 'log_file_name' ];
		if ( ! file_exists( plugin_dir_path( __FILE__ ) . $log_file_name ) ) {
		    if ( $this->log( "Easy WP SMTP debug log file\r\n\r\n" ) === false ) {
			wp_die( 'Can\'t write to log file. Check if plugin directory  (' . plugin_dir_path( __FILE__ ) . ') is writeable.' );
		    };
		}
		$logfile = fopen( plugin_dir_path( __FILE__ ) . $log_file_name, 'rb' );
		if ( ! $logfile ) {
		    wp_die( 'Can\'t open log file.' );
		}
		header( 'Content-Type: text/plain' );
		fpassthru( $logfile );
		die;
	    }
	}
    }

    function admin_notices() {
	if ( ! $this->credentials_configured() ) {
	    $settings_url = admin_url() . 'options-general.php?page=swpsmtp_settings';
	    ?>
	    <div class="error">
	        <p><?php printf( __( 'Please configure your SMTP credentials in the <a href="%s">settings menu</a> in order to send email using Easy WP SMTP plugin.', 'easy-wp-smtp' ), esc_url( $settings_url ) ); ?></p>
	    </div>
	    <?php
	}
    }

    function clear_log() {
	if ( $this->log( "Easy WP SMTP debug log file\r\n\r\n", true ) !== false ) {
	    echo '1';
	} else {
	    echo 'Can\'t clear log - log file is not writeable.';
	}
	die;
    }

    function log( $str, $overwrite = false ) {
	if ( isset( $this->opts[ 'smtp_settings' ] [ 'log_file_name' ]
	) ) {
	    $log_file_name = $this->
	    opts[ 'smtp_settings' ][ 'log_file_name' ];
	} else {
	    // let's generate log file name
	    $log_file_name						 = uniqid() . '_debug_log.txt';
	    $this->opts[ 'smtp_settings' ][ 'log_file_name' ]	 = $log_file_name;
	    update_option( 'swpsmtp_options', $this->opts );
	    file_put_contents( plugin_dir_path( __FILE__ ) . $log_file_name, "Easy WP SMTP debug log file\r\n\r\n" );
	}
	return(file_put_contents( plugin_dir_path( __FILE__ ) . $log_file_name, $str, ( ! $overwrite ? FILE_APPEND : 0 ) ));
    }

    function plugin_action_links( $links, $file ) {
	if ( $file == $this->plugin_file ) {
	    $settings_link = '<a href="options-general.php?page=swpsmtp_settings">' . __( 'Settings', 'easy-wp-smtp' ) . '</a>';
	    array_unshift( $links, $settings_link );
	}
	return $links;
    }

    function register_plugin_links( $links, $file ) {
	if ( $file == $this->plugin_file ) {
	    $links[] = '<a href="options-general.php?page=swpsmtp_settings">' . __( 'Settings', 'easy-wp-smtp' ) . '</a>';
	}
	return $links;
    }

    function plugins_loaded_handler() {
	load_plugin_textdomain( 'easy-wp-smtp', false, dirname( $this->plugin_file ) . '/languages/' );
    }

    function is_domain_blocked() {
	//check if Domain Check enabled
	if ( isset( $this->opts[ 'enable_domain_check' ] ) && $this->opts[ 'enable_domain_check' ] ) {
	    //check if allowed domains list is not blank
	    if ( isset( $this->opts[ 'allowed_domains' ] ) && ! empty( $this->opts[ 'allowed_domains' ] ) ) {
		$this->opts[ 'allowed_domains' ] = SWPSMTPUtils::base64_decode_maybe( $this->opts[ 'allowed_domains' ] );
		//let's see if we have one domain or coma-separated domains
		$domains_arr			 = explode( ',', $this->opts[ 'allowed_domains' ] );
		if ( is_array( $domains_arr ) && ! empty( $domains_arr ) ) {
		    //we have coma-separated list
		} else {
		    //it's single domain
		    unset( $domains_arr );
		    $domains_arr = array( $this->opts[ 'allowed_domains' ] );
		}
		$site_domain	 = parse_url( get_site_url(), PHP_URL_HOST );
		$match_found	 = false;
		foreach ( $domains_arr as $domain ) {
		    if ( strtolower( trim( $domain ) ) === strtolower( trim( $site_domain ) ) ) {
			$match_found = true;
			break;
		    }
		}
		if ( ! $match_found ) {
		    return $site_domain;
		}
	    }
	}
	return false;
    }

    function get_password() {
	$temp_password = isset( $this->opts[ 'smtp_settings' ][ 'password' ] ) ? $this->opts[ 'smtp_settings' ][ 'password' ] : '';
	if ( $temp_password == '' ) {
	    return '';
	}

	try {

	    if ( get_option( 'swpsmtp_pass_encrypted' ) ) {
		//this is encrypted password
		$cryptor	 = SWPSMTPUtils::get_instance();
		$decrypted	 = $cryptor->decrypt_password( $temp_password );
		//check if encryption option is disabled
		if ( empty( $this->opts[ 'smtp_settings' ][ 'encrypt_pass' ] ) ) {
		    //it is. let's save decrypted password
		    $this->opts[ 'smtp_settings' ][ 'password' ] = $this->encrypt_password( addslashes( $decrypted ) );
		    update_option( 'swpsmtp_options', $this->opts );
		}
		return $decrypted;
	    }
	} catch ( Exception $e ) {
	    $this->log( $e->getMessage() );
	    return '';
	}

	$password	 = "";
	$decoded_pass	 = base64_decode( $temp_password );
	/* no additional checks for servers that aren't configured with mbstring enabled */
	if ( ! function_exists( 'mb_detect_encoding' ) ) {
	    return $decoded_pass;
	}
	/* end of mbstring check */
	if ( base64_encode( $decoded_pass ) === $temp_password ) {  //it might be encoded
	    if ( false === mb_detect_encoding( $decoded_pass ) ) {  //could not find character encoding.
		$password = $temp_password;
	    } else {
		$password = base64_decode( $temp_password );
	    }
	} else { //not encoded
	    $password = $temp_password;
	}
	return stripslashes( $password );
    }

    function encrypt_password( $pass ) {
	if ( $pass === '' ) {
	    return '';
	}

	if ( empty( $this->opts[ 'smtp_settings' ][ 'encrypt_pass' ] ) || ! extension_loaded( 'openssl' ) ) {
	    // no openssl extension loaded - we can't encrypt the password
	    $password = base64_encode( $pass );
	    update_option( 'swpsmtp_pass_encrypted', false );
	} else {
	    // let's encrypt password
	    $cryptor	 = SWPSMTPUtils::get_instance();
	    $password	 = $cryptor->encrypt_password( $pass );
	    update_option( 'swpsmtp_pass_encrypted', true );
	}
	return $password;
    }

    function credentials_configured() {
	$credentials_configured = true;
	if ( ! isset( $this->opts[ 'from_email_field' ] ) || empty( $this->opts[ 'from_email_field' ] ) ) {
	    $credentials_configured = false;
	}
	if ( ! isset( $this->opts[ 'from_name_field' ] ) || empty( $this->opts[ 'from_name_field' ] ) ) {
	    $credentials_configured = false;
	}
	return $credentials_configured;
    }

    function activate() {
	$swpsmtp_options_default = array(
	    'from_email_field'		 => '',
	    'from_name_field'		 => '',
	    'force_from_name_replace'	 => 0,
	    'smtp_settings'			 => array(
		'host'			 => 'smtp.example.com',
		'type_encryption'	 => 'none',
		'port'			 => 25,
		'autentication'		 => 'yes',
		'username'		 => '',
		'password'		 => ''
	    )
	);

	/* install the default plugin options if needed */
	if ( empty( $this->opts ) ) {
	    $this->opts = $swpsmtp_options_default;
	}
	$this->opts = array_merge( $swpsmtp_options_default, $this->opts );
	update_option( 'swpsmtp_options', $this->opts, 'yes' );
	//add current domain to allowed domains list
	if ( ! isset( $this->opts[ 'allowed_domains' ] ) ) {
	    $domain = parse_url( get_site_url(), PHP_URL_HOST );
	    if ( $domain ) {
		$this->opts[ 'allowed_domains' ] = base64_encode( $domain );
		update_option( 'swpsmtp_options', $this->opts );
	    }
	} else { // let's check if existing value should be base64 encoded
	    if ( ! empty( $this->opts[ 'allowed_domains' ] ) ) {
		if ( SWPSMTPUtils::base64_decode_maybe( $this->opts[ 'allowed_domains' ] ) === $this->opts[ 'allowed_domains' ] ) {
		    $this->opts[ 'allowed_domains' ] = base64_encode( $this->opts[ 'allowed_domains' ] );
		    update_option( 'swpsmtp_options', $this->opts );
		}
	    }
	}
	// Encrypt password if needed
	if ( ! get_option( 'swpsmtp_pass_encrypted' ) ) {
	    if ( extension_loaded( 'openssl' ) ) {
		if ( $this->opts[ 'smtp_settings' ][ 'password' ] !== '' ) {
		    $this->opts[ 'smtp_settings' ][ 'password' ] = $this->encrypt_password( $this->get_password() );
		    update_option( 'swpsmtp_options', $this->opts );
		}
	    }
	}
    }

}

function swpsmtp_uninstall() {
    // Don't delete plugin options. It is better to retain the options so if someone accidentally deactivates, the configuration is not lost.
    //delete_site_option('swpsmtp_options');
    //delete_option('swpsmtp_options');
}

new EasyWPSMTP();

class swpsmtp_gag_mailer extends stdClass {

    public function Send() {
	return true;
    }

}
