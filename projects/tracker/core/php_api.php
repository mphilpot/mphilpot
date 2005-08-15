<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: php_api.php,v 1.14 2005/04/22 18:44:57 prichards Exp $
	# --------------------------------------------------------

	### PHP Compatibility API ###

	# Functions to help in backwards compatibility of PHP versions, etc.

	# Constant for our minimum required PHP version
	define( 'PHP_MIN_VERSION', '4.0.6' );

	# --------------------
	# Returns true if the current PHP version is higher than the one
	#  specified in the given string
	function php_version_at_least( $p_version_string ) {
		$t_curver = array_pad( explode( '.', phpversion() ), 3, 0 );
		$t_minver = array_pad( explode( '.', $p_version_string ), 3, 0 );

		for ($i = 0 ; $i < 3 ; $i = $i + 1 ) {
			$t_cur = (int)$t_curver[$i];
			$t_min = (int)$t_minver[$i];

			if ( $t_cur < $t_min ) {
				return false;
			} else if ( $t_cur > $t_min ) {
				return true;
			}
		}

		# if we get here, the versions must match exactly so:
		return true;
	}

	# --------------------
	# Enforce our minimum requirements
	if ( !php_version_at_least( PHP_MIN_VERSION ) ) {
		ob_end_clean();
		PRINT '<b>Your version of PHP is too old.  Mantis requires PHP version ' . PHP_MIN_VERSION . ' or newer</b>';
		phpinfo();
		die();
	}

	# --------------------
	ini_set('magic_quotes_runtime', 0);

	# --------------------
	# Experimental support for $_* auto-global variables in PHP < 4.1.0
	if ( !php_version_at_least( '4.1.0' ) ) {
		global $_REQUEST, $_GET, $_POST, $_COOKIE, $_SERVER, $_FILES;

		$_GET = $HTTP_GET_VARS;
		$_POST = $HTTP_POST_VARS;
		$_COOKIE = $HTTP_COOKIE_VARS;
		$_SERVER = $HTTP_SERVER_VARS;
		$_FILES = $HTTP_POST_FILES;

		$_REQUEST = $HTTP_COOKIE_VARS;
		foreach ($HTTP_POST_VARS as $key => $value) {
			$_REQUEST[$key] = $value;
		}
		foreach ($HTTP_GET_VARS as $key => $value) {
			$_REQUEST[$key] = $value;
		}
	}

	# --------------------
	# array_key_exists was not available on PHP 4.0.6
	if ( !function_exists( 'array_key_exists' ) ) {
		function array_key_exists( $key, $search ) {
			return key_exists( $key, $search );
		}
	}

	# --------------------
	# file_put_contents is normally in PEAR
	if (!function_exists('file_put_contents')) {
		function file_put_contents($filename, $data) {
			if (($h = fopen($filename, 'w')) === false) {
				return false;
			}
			if (($bytes = @fwrite($h, $data)) === false) {
				return false;
			}
			fclose($h);
			return $bytes;
		}
	}

	# --------------------
	# vsprintf is normally in PEAR
	if ( !function_exists( 'vsprintf' ) ) {
		function vsprintf( $format, $args ) {
			array_unshift( $args, $format );
			return call_user_func_array( 'sprintf', $args );
		}
	}

?>
