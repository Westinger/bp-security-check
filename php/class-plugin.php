<?php

namespace Shea\BP_Security_Check;

/**
 * The main plugin class
 * @package Shea\BP_Security_Check
 */
class Plugin {

	/**
	 * @var string
	 */
	public $version = '';

	/**
	 * @var string
	 */
	public $file = '';

	/**
	 * @var Settings
	 */
	public $settings;

	/**
	 * @var Math_Check|Recaptcha_Check
	 */
	public $security_check;

	/**
	 * Constructor
	 * @param $version
	 * @param $file
	 */
	function __construct( $version, $file ) {
		$this->file = $file;
		$this->version = $version;

		$this->settings = new Settings( $this );
		$this->security_check = new Recaptcha_Check( $this );
	}

	/**
	 * Run the class's actions
	 */
	function run() {
		$this->settings->run();
		$this->security_check->run();

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Load up the localization file if WordPress is in a different language.
	 */
	function load_textdomain() {
		load_plugin_textdomain( 'bp-security-check', false, dirname( plugin_basename( $this->file ) ) . '/languages/' );
	}
}
