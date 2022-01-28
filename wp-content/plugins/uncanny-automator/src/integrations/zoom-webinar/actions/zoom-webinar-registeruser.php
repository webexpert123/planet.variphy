<?php

namespace Uncanny_Automator;

/**
 * Class ZOOM_WEBINAR_REGISTERUSER
 *
 * @package Uncanny_Automator
 */
class ZOOM_WEBINAR_REGISTERUSER {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'ZOOMWEBINAR';

	private $action_code;
	private $action_meta;

	/**
	 * Set up Automator action constructor.
	 */
	public function __construct() {
		$this->action_code = 'ZOOMWEBINARREGISTERUSER';
		$this->action_meta = 'ZOOMWEBINAR';
		$this->define_action();
	}

	/**
	 * Define and register the action by pushing it into the Automator object
	 */
	public function define_action() {

		$action = array(
			'author'             => Automator()->get_author_name( $this->action_code ),
			'support_link'       => Automator()->get_author_support_link( $this->action_code, 'knowledge-base/zoom/' ),
			'is_pro'             => false,
			//'is_deprecated'      => true,
			'integration'        => self::$integration,
			'code'               => $this->action_code,
			'sentence'           => sprintf( __( 'Add the user to {{a webinar:%1$s}}', 'uncanny-automator' ), $this->action_meta ),
			'select_option_name' => __( 'Add the user to {{a webinar}}', 'uncanny-automator' ),
			'priority'           => 10,
			'accepted_args'      => 1,
			'execution_function' => array( $this, 'zoom_webinar_register_user' ),
			'options_callback'   => array( $this, 'load_options' ),
		);

		Automator()->register->action( $action );
	}

	/**
	 * load_options
	 *
	 * @return void
	 */
	public function load_options() {
		return array(
			'options' => array(
				Automator()->helpers->recipe->zoom_webinar->get_webinars( null, $this->action_meta ),
			),
		);
	}

	/**
	 * Validation function when the action is hit
	 *
	 * @param $user_id
	 * @param $action_data
	 * @param $recipe_id
	 */
	public function zoom_webinar_register_user( $user_id, $action_data, $recipe_id, $args ) {

		$webinar_key = Automator()->parse->text( $action_data['meta'][ $this->action_meta ], $recipe_id, $user_id, $args );

		if ( empty( $user_id ) ) {
			$error_msg                           = __( 'User not found.', 'uncanny-automator' );
			$action_data['do-nothing']           = true;
			$action_data['complete_with_errors'] = true;
			Automator()->complete_action( $user_id, $action_data, $recipe_id, $error_msg );

			return;
		}

		if ( empty( $webinar_key ) ) {
			$error_msg                           = __( 'Webinar not found.', 'uncanny-automator' );
			$action_data['do-nothing']           = true;
			$action_data['complete_with_errors'] = true;
			Automator()->complete_action( $user_id, $action_data, $recipe_id, $error_msg );

			return;
		}

		if ( ! empty( $webinar_key ) ) {
			$webinar_key = str_replace( '-objectkey', '', $webinar_key );
		}

		$result = Automator()->helpers->recipe->zoom_webinar->register_user( $user_id, $webinar_key );

		if ( ! $result['result'] ) {
			$error_msg                           = $result['message'];
			$action_data['do-nothing']           = true;
			$action_data['complete_with_errors'] = true;
			Automator()->complete_action( $user_id, $action_data, $recipe_id, $error_msg );

			return;
		}

		Automator()->complete_action( $user_id, $action_data, $recipe_id );

	}

}
