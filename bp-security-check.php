<?php

/**
 * Plugin Name: BuddyPress Security Check
 * Plugin URI:  http://bungeshea.com/plugins/bp-security-check/
 * Description: Help combat spam registrations by forcing the user to answer a simple math sum while registering for your BuddyPress-powered site
 * Author:      Shea Bunge
 * Author URI:  http://bungeshea.com
 * Version:     1.0.1
 * License:     MIT
 * License URI: http://opensource.org/licenses/MIT
 */

/**
 * Adds a maths sum to the BuddyPress registration page that the user
 * must answer correctly before registering
 * @version 1.0.1
 * @license http://opensource.org/licenses/MIT MIT
 * @author Shea Bunge (http://bungeshea.com)
 */
class BuddyPress_Security_Check {

	/**
	 * The prefix prepended to for form fields
	 * @var string
	 */
	private $prefix = '';

	/**
	 * Initialize variables and register hooks
	 * @param string $prefix
	 */
	public function __construct( $prefix ) {
		add_action( 'bp_signup_validate', array( __CLASS__, 'check_validation' ) );
		add_action( 'bp_after_signup_profile_fields', array( __CLASS__, 'show_input_field' ) );

		$this->prefix = $prefix;
	}


	/**
	 * Retrieve the value for a form field
	 * @param  string $field_name The name of the field to retrieve the value for
	 * @return string             The field's value
	 */
	private function get_field_value( $field_name ) {
		$field_value = $_POST[ $this->prefix . $field_name ];
		return apply_filters( 'bp_security_check_get_field_value', $field_value, $field_name );
	}

	/**
	 * Retrieve the answer to a maths problem
	 * @param  integer $a  The first number
	 * @param  integer $b  The second number
	 * @param  integer $op The operator code
	 * @return integer     The problem's answer
	 */
	private function do_sum( $a, $b, $op ) {
		switch( $op ) {
			case 1: // addition
			default:
				return $a + $b;
			case 2: // subraction
				return $a - $b;
			case 3: // multiplication
				return $a * $b;
			case 4: //division
				return $a / $b;
		}
	}

	/**
	 * Retrieve the HTML entity for an operation
	 * @param  integer $op An operator code
	 * @return string      A HTML entity
	 */
	private function format_operation( $op ) {

		switch ( $op ) {
			case 1: // addition
			default:
				return '&#43;';
			case 2: // subraction
				return '&#8722;';
			case 3: // multiplication
				return '&#215;';
			case 4: // division
				return '&#247;';
		}
	}

	/**
	 * Check if the user's input was correct
	 */
	public function check_validation(){
		global $bp;

		$number_a  = intval( $this->get_field_value( 'number_a' ) );
		$number_b  = intval( $this->get_field_value( 'number_b' ) );
		$answer    = intval( $this->get_field_value( 'answer' ) );
		$operation = $this->get_field_value( 'operation' );

		if ( $this->do_sum( $number_a, $number_b, $operation ) !== $answer ) {
			/* The submitted answer was incorrect */
			$bp->signup->errors['security_check'] = __('Sorry, please answer the question again','buddypress');
		}
		elseif ( empty( $answer ) ) {
			/* The answer field wasn't filled in */
			$bp->signup->errors['security_check'] = __('This is a required field','buddypress');
		}
	}

	/**
	 * Render the input fields
	 */
	public function show_input_field() {

		/* Get a random number between 0 and 10 (inclusive) */
		$a = rand( 0, 10 );
		$b = rand( 0, 10 );

		/* Make sure that $a is greater then $b; if not, switch them */
		if ( $b > $a ) {
			$_a = $a;     // backup $a
			$a = $b;      // assign $a (lower number) to $b (higher number)
			$b = $_a;     // assign $b to the original $a
			unset( $_a ); // destroy the backup variable
		} elseif ($a == $b) {
			$a++;  // Increment $a so that we never get 0 and hit validation errors being required
		}

		/* Get a random operation */
		$op = rand( 1, 2 );

		?>
		<div style="float: left; clear: left; width: 48%; margin: 12px 0;" class="security-question-section">
			<h4><?php esc_html_e('Security Question', 'buddypress'); ?></h4>
			<?php do_action( 'bp_security_check_errors' ); ?>
			<label for="<?php echo $this->prefix; ?>answer" style="display: inline;">
				<?php printf( '%1$d %3$s %2$d &#61;', $a, $b, $this->format_operation( $op ) ); ?>
			</label>
			<input type="hidden" name="<?php echo $this->prefix; ?>number_a" value="<?php echo $a; ?>" />
			<input type="hidden" name="<?php echo $this->prefix; ?>number_b" value="<?php echo $b; ?>" />
			<input type="hidden" name="<?php echo $this->prefix; ?>operation" value="<?php echo $op; ?>" />
			<input type="number" name="<?php echo $this->prefix; ?>answer" min="0" max="20" required="required" />
		</div>
		<?php
	}
}

/**
 * Initialize the plugin class
 */
function bp_security_check_init() {
	$prefix = apply_filters( 'bp_security_check_prefix', 'security_question_' );
	$GLOBALS['bp_security_check'] = new BuddyPress_Security_Check( $prefix );
}

add_action( 'bp_init', 'buddypress_security_check_init' );
