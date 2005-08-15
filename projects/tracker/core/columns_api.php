<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2005  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: columns_api.php,v 1.7 2005/04/19 00:52:20 thraxisp Exp $
	# --------------------------------------------------------
?>
<?php

	function print_column_title_selection( $p_sort, $p_dir, $p_print = false ) {
		echo '<td> &nbsp; </td>';
	}

	function print_column_title_edit( $p_sort, $p_dir, $p_print = false ) {
		echo '<td> &nbsp; </td>';
	}

	function print_column_title_id( $p_sort, $p_dir, $p_print = false ) {
		echo '<td>';
		print_view_bug_sort_link( lang_get( 'id' ), 'id', $p_sort, $p_dir, $p_print );
		print_sort_icon( $p_dir, $p_sort, 'id' );
		echo '</td>';
	}

	function print_column_title_priority( $p_sort, $p_dir, $p_print = false ) {
		echo '<td>';
		print_view_bug_sort_link( 'P', 'priority', $p_sort, $p_dir, $p_print );
		print_sort_icon( $p_dir, $p_sort, 'priority' );
		echo '</td>';
	}

	function print_column_title_attachment( $p_sort, $p_dir, $p_print = false  ) {
		global $t_icon_path;

		$t_show_attachments = config_get( 'show_attachment_indicator' );

		if ( ON == $t_show_attachments ) {
			echo "\t<td>";
			echo '<img src="' . $t_icon_path . 'attachment.png' . '" alt="" />';
			echo "</td>\n";
		}
	}

	function print_column_title_category( $p_sort, $p_dir, $p_print = false ) {
		echo '<td>';
		print_view_bug_sort_link( lang_get( 'category' ), 'category', $p_sort, $p_dir, $p_print );
		print_sort_icon( $p_dir, $p_sort, 'category' );
		echo '</td>';
	}

	function print_column_title_sponsorship( $p_sort, $p_dir, $p_print = false ) {
		$t_enable_sponsorship = config_get( 'enable_sponsorship' );

		if ( ON == $t_enable_sponsorship ) {
			echo "\t<td>";
			print_view_bug_sort_link( sponsorship_get_currency(), 'sponsorship_total', $p_sort, $p_dir, $p_print );
			print_sort_icon( $p_dir, $p_sort, 'sponsorship_total' );
			echo "</td>\n";
		}
	}

	function print_column_title_severity( $p_sort, $p_dir, $p_print = false ) {
		echo '<td>';
		print_view_bug_sort_link( lang_get( 'severity' ), 'severity', $p_sort, $p_dir, $p_print );
		print_sort_icon( $p_dir, $p_sort, 'severity' );
		echo '</td>';
	}

	function print_column_title_status( $p_sort, $p_dir, $p_print = false ) {
		echo '<td>';
		print_view_bug_sort_link( lang_get( 'status' ), 'status', $p_sort, $p_dir, $p_print );
		print_sort_icon( $p_dir, $p_sort, 'status' );
		echo '</td>';
	}

	function print_column_title_last_updated( $p_sort, $p_dir, $p_print = false ) {
		echo '<td>';
		print_view_bug_sort_link( lang_get( 'updated' ), 'last_updated', $p_sort, $p_dir, $p_print );
		print_sort_icon( $p_dir, $p_sort, 'last_updated' );
		echo '</td>';
	}

	function print_column_title_summary( $p_sort, $p_dir, $p_print = false ) {
		echo '<td>';
		print_view_bug_sort_link( lang_get( 'summary' ), 'summary', $p_sort, $p_dir, $p_print );
		print_sort_icon( $p_dir, $p_sort, 'summary' );
		echo '</td>';
	}

	function print_column_title_bugnotes_count( $p_sort, $p_dir, $p_print = false  ) {
		echo '<td> # </td>';
	}

	function print_column_selection( $p_row, $p_print = false ) {
		global $t_checkboxes_exist, $t_update_bug_threshold;

		echo '<td>';
		if ( access_has_bug_level( $t_update_bug_threshold, $p_row['id'] ) ) {
			$t_checkboxes_exist = true;
			printf( "<input type=\"checkbox\" name=\"bug_arr[]\" value=\"%d\" />" , $p_row['id'] );
		} else {
			echo "&nbsp;";
		}
		echo '</td>';
	}

	function print_column_edit( $p_row, $p_print = false ) {
		global $t_icon_path, $t_update_bug_threshold;

		echo '<td>';
		if ( !bug_is_readonly( $p_row['id'] )
		  && access_has_bug_level( $t_update_bug_threshold, $p_row['id'] ) ) {
			echo '<a href="' . string_get_bug_update_url( $p_row['id'] ) . '">';
			echo '<img border="0" width="16" height="16" src="' . $t_icon_path . 'update.png';
			echo '" alt="' . lang_get( 'update_bug_button' ) . '"';
			echo ' title="' . lang_get( 'update_bug_button' ) . '" /></a>';
		} else {
			echo '&nbsp;';
		}
		echo '</td>';
	}

	function print_column_priority( $p_row, $p_print = false ) {
		echo '<td>';
		if ( ON == config_get( 'show_priority_text' ) ) {
			print_formatted_priority_string( $p_row['status'], $p_row['priority'] );
		} else {
			print_status_icon( $p_row['priority'] );
		}
		echo '</td>';
	}

	function print_column_id( $p_row, $p_print = false ) {
		echo '<td>';
		print_bug_link( $p_row['id'], false );
		echo '</td>';
	}

	function print_column_sponsorship( $p_row, $p_print = false ) {
		$t_enable_sponsorship = config_get( 'enable_sponsorship' );

		if ( $t_enable_sponsorship == ON ) {
			echo "\t<td class=\"right\">";
			if ( $p_row['sponsorship_total'] > 0 ) {
				$t_sponsorship_amount = sponsorship_format_amount( $p_row['sponsorship_total'] );
				echo string_no_break( $t_sponsorship_amount );
			}
			echo "</td>\n";
		}
	}

	function print_column_bugnotes_count( $p_row, $p_print = false ) {
		global $t_filter;

		# grab the bugnote count
		$t_bugnote_stats = bug_get_bugnote_stats( $p_row['id'] );
		if ( NULL != $t_bugnote_stats ) {
			$bugnote_count = $t_bugnote_stats['count'];
			$v_bugnote_updated = $t_bugnote_stats['last_modified'];
		} else {
			$bugnote_count = 0;
		}

		echo '<td class="center">';
		if ( $bugnote_count > 0 ) {
			$t_bugnote_link = '<a href="' . string_get_bug_view_url( $p_row['id'] )
				. '&amp;nbn=' . $bugnote_count . '#bugnotes">'
				. $bugnote_count . '</a>';

			if ( $v_bugnote_updated > strtotime( '-'.$t_filter['highlight_changed'].' hours' ) ) {
				printf( '<span class="bold">%s</span>', $t_bugnote_link );
			} else {
				echo $t_bugnote_link;
			}
		} else {
			echo '&nbsp;';
		}
		echo '</td>';
	}

	function print_column_attachment( $p_row, $p_print = false ) {
		global $t_icon_path;

		$t_show_attachments = config_get( 'show_attachment_indicator' );

		# Check for attachments
		$t_attachment_count = 0;
		if ( ( ON == $t_show_attachments )
		  && ( file_can_view_bug_attachments( $p_row['id'] ) ) ) {
			$t_attachment_count = file_bug_attachment_count( $p_row['id'] );
		}

		if ( ON == $t_show_attachments ) {
			echo "\t<td>";
			if ( 0 < $t_attachment_count ) {
				echo '<a href="' . string_get_bug_view_url( $p_row['id'] ) . '#attachments">';
				echo '<img border="0" src="' . $t_icon_path . 'attachment.png' . '"';
				echo ' alt="' . lang_get( 'attachment_alt' ) . '"';
				echo ' title="' . $t_attachment_count . ' ' . lang_get( 'attachments' ) . '"';
				echo ' />';
				echo '</a>';
			} else {
				echo ' &nbsp; ';
			}
			echo "</td>\n";
		}
	}

	function print_column_category( $p_row, $p_print = false ) {
		global $t_sort, $t_dir;

		# grab the project name
		$t_project_name = project_get_field( $p_row['project_id'], 'name' );

		echo '<td class="center">';

		# type project name if viewing 'all projects' or if issue is in a subproject
		if ( ON == config_get( 'show_bug_project_links' )
		  && helper_get_current_project() != $p_row['project_id'] ) {
			echo '<small>[';
			print_view_bug_sort_link( $t_project_name, 'project_id', $t_sort, $t_dir, $p_print );
			echo ']</small><br />';
		}

		echo string_display( $p_row['category'] );
		echo '</td>';
	}

	function print_column_severity( $p_row, $p_print = false ) {
		echo '<td class="center">';
		print_formatted_severity_string( $p_row['status'], $p_row['severity'] );
		echo '</td>';
	}

	function print_column_status( $p_row, $p_print = false ) {
		echo '<td class="center">';
		printf( '<u><a title="%s">%s</a></u>'
			, get_enum_element( 'resolution', $p_row['resolution'] )
			, get_enum_element( 'status', $p_row['status'] )
		);

		# print username instead of status
		if ( ON == config_get( 'show_assigned_names' )
		  && $p_row['handler_id'] > 0 ) {
			printf( ' (%s)', prepare_user_name( $p_row['handler_id'] ) );
		}
		echo '</td>';
	}

	function print_column_last_updated( $p_row, $p_print = false ) {
		global $t_filter;

		$t_last_updated = date( config_get( 'short_date_format' ), $p_row['last_updated'] );

		echo '<td class="center">';
		if ( $p_row['last_updated'] > strtotime( '-'.$t_filter['highlight_changed'].' hours' ) ) {
			printf( '<span class="bold">%s</span>', $t_last_updated );
		} else {
			echo $t_last_updated;
		}
		echo '</td>';
	}

	function print_column_summary( $p_row, $p_print = false ) {
		global $t_icon_path;

		$t_summary = string_attribute( $p_row['summary'] );

		echo '<td class="left">', $t_summary;
		if ( VS_PRIVATE == $p_row['view_state'] ) {
			printf( ' <img src="%s" alt="(%s)" title="%s" />'
				, $t_icon_path . 'protected.gif'
				, lang_get( 'private' )
				, lang_get( 'private' )
			);
		}
		echo '</td>';
	}
?>
