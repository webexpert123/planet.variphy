<?php

namespace Uncanny_Automator_Pro;

/**
 * Abstract class Action_Condition
 *
 * See integrations/wp/wp-token-meets-condition.php for an example how to extend it
 *
 * @package Uncanny_Automator_Pro
 */
abstract class Action_Condition {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public $integration = '';
	public $code = '';
	public $name = '';
	public $dynamic_name;
	public $is_pro = true;
	public $requires_user = false;

	/**
	 * define_condition
	 *
	 * @return void
	 */
	abstract public function define_condition();

	/**
	 * fields
	 *
	 * @return void
	 */
	abstract public function fields();

	/**
	 * evaluate_condition
	 *
	 * @param mixed $result
	 * @param mixed $action
	 * @param mixed $condition
	 *
	 * @return void
	 */
	abstract public function evaluate_condition();

	/**
	 * Set up Automator trigger constructor.
	 *
	 * Do not override this function
	 */
	public function __construct() {
		$this->define_condition();
		add_filter( 'automator_pro_actions_conditions_list', array( $this, 'register' ) );
		add_filter( 'automator_pro_evaluate_actions_conditions', array( $this, 'maybe_evaluate_condition' ), 10, 2 );
	}

	/**
	 * register
	 *
	 * Do not override this function
	 *
	 * @param mixed $actions_conditions
	 *
	 * @return void
	 */
	public function register( $actions_conditions ) {

		if ( empty( $this->name ) ) {
			throw new \Exception( "Condition name is required" );
		}

		if ( empty( $this->dynamic_name ) ) {
			throw new \Exception( "Condition dynamic is required" );
		}

		if ( empty( $this->fields() ) ) {
			throw new \Exception( "Condition fields are required" );
		}

		$actions_conditions[ $this->integration ][ $this->code ] = array(
			'name'          => $this->name,
			'dynamic_name'  => $this->dynamic_name,
			'is_pro'        => $this->is_pro,
			'requires_user' => $this->requires_user,
			'fields'        => $this->fields(),
		);

		return $actions_conditions;
	}

	/**
	 * maybe_evaluate_condition
	 *
	 * Do not override this function
	 *
	 * @param mixed $result
	 * @param mixed $action
	 * @param mixed $condition
	 *
	 * @return void
	 */
	public function maybe_evaluate_condition( $action, $condition ) {

		try {
			$this->hydrate( $action, $condition );
			$this->evaluate_condition();
		} catch ( \Exception $e ) {
			automator_log( $e->getMessage() );
		}

		return $this->action;
	}

	public function hydrate( $action, $condition ) {

		$this->condition = $condition;
		$this->action    = $action;

		if ( ! isset( $this->condition['condition'] ) ) {
			throw new \Exception( "Missing condition" );
		}

		if ( $this->condition['condition'] !== $this->code ) {
			throw new \Exception( "Condition code doesn't match" );
		}

		if ( ! isset( $this->condition['fields'] ) ) {
			throw new \Exception( "Condition fields are missing" );
		}

		$this->fields      = $condition['fields'];
		$this->action_data = $action['action_data'];
		$this->recipe_id   = $action['recipe_id'];
		$this->user_id     = $action['user_id'];
		$this->args        = $action['args'];
	}

	/**
	 * condition_failed
	 *
	 * @param mixed $action
	 * @param mixed $log_message
	 */
	public function condition_failed( $log_message ) {
		$this->action['process_further']                          = false;
		$this->action['action_data']['failed_actions_conditions'] = true;
		$this->action['action_data']['actions_conditions_log'][]  = sanitize_text_field( $log_message );
	}

	/**
	 * condition_met
	 *
	 * @param mixed $action
	 */
	public function condition_met() {
		$this->action['process_further'] = true;
	}

	/**
	 * get_parsed_field
	 *
	 * @param mixed $name
	 *
	 * @return string|mixed
	 */
	public function get_parsed_field( $name ) {

		if ( ! isset( $this->fields[ $name ] ) ) {
			throw new \Exception( $name . " field is missing" );
		}

		return Automator()->parse->text( $this->fields[ $name ], $this->recipe_id, $this->user_id, $this->args );
	}

}
