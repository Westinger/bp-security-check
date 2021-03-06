<?php

/**
 * Plugin Name: BuddyPress Security Check
 * Plugin URI:  http://bungeshea.com/plugins/bp-security-check/
 * Description: Help combat spam registrations by forcing the user to answer a simple math sum while registering for your BuddyPress-powered site
 * Author:      Shea Bunge
 * Author URI:  http://bungeshea.com
 * Version:     1.4.0
 * License:     MIT
 * License URI: http://opensource.org/licenses/MIT
 * Text Domain: bp-security-check
 * Domain Path: /languages/
 */

/**
 * Adds a maths sum to the BuddyPress registration page that the user
 * must answer correctly before registering
 * @version 1.4.0
 * @license http://opensource.org/licenses/MIT MIT
 * @author Shea Bunge (http://bungeshea.com)
 */

namespace Shea\BP_Security_Check;

require __DIR__ . '/vendor/autoload.php';

/**
 * Retrieve the instance of the plugin class
 * @return Plugin
 */
function plugin() {
	static $plugin;

	if ( is_null( $plugin ) ) {
		$plugin = new Plugin( '1.3.2', __FILE__ );
	}

	return $plugin;
}

add_action( 'plugins_loaded', array( plugin(), 'run' ) );

