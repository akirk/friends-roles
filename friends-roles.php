<?php
/**
 * Plugin name: Friends Roles
 * Plugin author: Alex Kirk
 * Plugin URI: https://github.com/akirk/friends-roles
 * Version: 0.1
 *
 * Description: Activates a debug mode for the Friends plugin and outputs some debug data.
 *
 * License: GPL2
 * Text Domain: friends
 *
 * @package Friends_Roles
 */

defined( 'ABSPATH' ) || exit;
define( 'FRIENDS_ROLES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once __DIR__ . '/includes/class-roles.php';

add_filter( 'friends_roles', '__return_true' );
add_action(
	'friends_loaded',
	function( $friends ) {
		new Friends\Roles( $friends );
	}
);
