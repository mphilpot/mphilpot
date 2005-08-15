<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: config_inc.php.sample,v 1.15 2005/02/12 20:01:05 jlatour Exp $
	# --------------------------------------------------------

	# This sample file contains the essential files that you MUST
	# configure to your specific settings.  You may override settings
	# from config_defaults_inc.php by assigning new values in this file

	# Rename this file to config_inc.php after configuration.

	###########################################################################
	# CONFIGURATION VARIABLES
	###########################################################################

	# In general the value OFF means the feature is disabled and ON means the
	# feature is enabled.  Any other cases will have an explanation.

	# Look in http://www.mantisbt.org/manual or config_defaults_inc.php for more
	# detailed comments.

	# --- database variables ---------

	# set these values to match your setup
	$g_hostname      = "localhost";
	$g_db_username   = "mphilpot_admin";
	$g_db_password   = "6t79n0XX";
	$g_database_name = "mphilpot_main";

	# --- email variables -------------
	$g_administrator_email  = 'webadmin@zencoding.net';
	$g_webmaster_email      = 'webadmin@zencoding.net';

	# the "From: " field in emails
	$g_from_email           = 'noreply@zencoding.net';

	# the return address for bounced mail
	$g_administrator_email  = 'webadmin@zencoding.net';
	$g_return_path_email    = 'webadmin@example.com';

	# --- file upload settings --------
	# This is the master setting to disable *all* file uploading functionality
	#
	# The default value is ON but you must make sure file uploading is enabled
	#  in PHP as well.  You may need to add "file_uploads = TRUE" to your php.ini.
	$g_allow_file_upload	= ON;
?>
