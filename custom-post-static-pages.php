<?php
/**
 * Plugin Name: Custom Post Static Index
 * Plugin URI: https://levidps.com
 * Description: A plugin to add static page selection for custom post types, visible on the 'reading' options page
 * Version: 1.0
 * Author: Levidps
 * Author URI: https://levidps.com/
 * License: GPL2
 */

/*  Copyright 2017 Levidps

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Prevent Direct Access */
if ( !defined( 'ABSPATH' ) ) exit;

/** Check if function exists */
if ( !function_exists('ldps_custom_posts_init') ) :

	/** Init Funciton */
	function ldps_custom_posts_init() {

		/* Create Settings Section */
		add_settings_section(
			'ldps_custom_post_section',
			'<hr><br/>Index Pages for Custom Posts',
			'',
			'reading'
		);

		/* Create Settings Field */
		add_settings_field(
			'ldps_page_indexes',
			'Custom post index pages',
			'ldps_page_indexes_callback',
			'reading',
			'ldps_custom_post_section'
		);

		/** Get Custom Post Types */
		$_type_args = array('_builtin' => false );
		$_types = get_post_types($_type_args, 'names');

		/**
		 * For each post type register setting
		 * - skip any post type that includes 'acf'
		 */
		if ($_types) :
			foreach ($_types as $_type) :
				if ( !strpos($_type, 'acf') ) :

					register_setting('reading', 'ldps_' . $_type . '_page');

				endif;
			endforeach;
		endif;

	}

	/** On Admin Init -> init settings function */
	add_action('admin_init', 'ldps_custom_posts_init');

	/** Call back to create dropdowns */
	function ldps_page_indexes_callback() {

		/** Get Custom Post Types */
		$_type_args = array('_builtin' => false );
		$_types = get_post_types($_type_args, 'names');

		/** If gets $_types return elements */
		if ($_types) :

			// Wrapper Element
			echo '<ul>';

			/**
			 * For each post type create page dropdown
			 * - skip any post type that includes 'acf'
			 */
			foreach ($_types as $_type) :
				if ( strpos($_type, 'acf') === false ) :

					/** Menu Dropdown Args */
					$args = array(
						'post_type' => 'page',
						'name' => 'ldps_' . $_type . '_page',
						'id' => 'ldps_' . $_type . '_page',
						'show_option_none' => 'Select Page',
						'echo' => 1,
						'selected' => get_option('ldps_' . $_type . '_page'),
					);

					/** Return Menu Dropdown with $options selected */
					echo '<li><label for="ldps_' . $_type . '_page">' . ucfirst($_type) . ' Page: ';
					wp_dropdown_pages($args);
					echo '</label></li>';

				endif;
			endforeach;

			echo '</ul>';

		endif;
	}

	/**
	 * Function for if page is static page for custom post type
	 *
	 * @var arg {custom post type}
	 * @return boolean
	 */
	function is_custom_posts_page( $arg ) {
		global $wp_query;
		$queried_obj = $wp_query->queried_object;

		// Ensure global query is correct (taken from WP core for is_front_page()
		if ( ! isset( $wp_query ) ) {
			_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.' ), '3.1.0' );
			return false;
		}

		// Compare queried page ID to options storage
		if ( get_option( 'ldps_' . $arg . '_page' ) == $queried_obj->ID )
			return true;
		else
			return false;
	}

endif;