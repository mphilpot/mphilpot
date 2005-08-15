<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: summary_graph_byseverity_mix.php,v 1.15 2005/02/12 20:01:08 jlatour Exp $
	# --------------------------------------------------------
?>
<?php
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'graph_api.php' );

	access_ensure_project_level( config_get( 'view_summary_threshold' ) );

	$f_width = gpc_get_int( 'width', 300 );
	$t_ar = config_get( 'graph_bar_aspect' );

	$f_token = gpc_get_int( 'token', 0 );
	if ( 0 == $f_token ) {
		$t_metrics = enum_bug_group( lang_get( 'severity_enum_string' ), 'severity' );
	} else {
		$t_metrics = unserialize( token_get_value( $f_token ) );
	}
	graph_group( $t_metrics, lang_get( 'by_severity_mix' ), $f_width, $f_width * $t_ar );
?>
