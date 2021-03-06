<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
namespace Uncanny_Automator_Pro;

/**
 * Acf_Tokens
 *
 * @package Uncanny_Automator
 */
class Acf_Tokens {

	public $load_options;

	/**
	 * Our class constructor. Hooks `parse_tokens` method to `automator_maybe_parse_token` filter.
	 *
	 * @return void
	 */
	public function __construct() {

		$this->load_options = Automator()->helpers->recipe->maybe_load_trigger_options( __CLASS__ );

		add_filter( 'automator_maybe_parse_token', array( $this, 'parse_tokens' ), 36, 6 );

	}

	/**
	 * Process the tokens.
	 *
	 * @param mixed $value The value accepted from `automator_maybe_parse_token`.
	 * @param mixed $pieces The pieces accepted from `automator_maybe_parse_token`.
	 * @param mixed $recipe_id The recipe id accepted from `automator_maybe_parse_token`.
	 * @param mixed $trigger_data The trigger data accepted from `automator_maybe_parse_token`.
	 * @param mixed $user_id The user id accepted from `automator_maybe_parse_token`.
	 * @param mixed $replace_args The arguments accepted from `automator_maybe_parse_token`.
	 *
	 * @return mixed The token value to display.
	 */
	public function parse_tokens( $value, $pieces, $recipe_id, $trigger_data, $user_id, $replace_args ) {

		$to_match = array(
			'ACF_TRIGGER_FIELD',
			'ACF_TRIGGER_POST_TYPE',
			'ACF_TRIGGER_POST_ID',
			'ACF_TRIGGER_POST_URL',
			'ACF_TRIGGER_POST_TITLE',
			'ACF_TRIGGER_FIELD_NAME',
		);

		if ( $pieces ) {

			if ( array_intersect( $to_match, $pieces ) ) {

				$value = $this->replace_values( $value, $pieces, $recipe_id, $trigger_data, $user_id, $replace_args );

			}
		}

		return $value;

	}

	/**
	 * Replaces the token values.
	 *
	 * @return mixed The value.
	 */
	public function replace_values( $value, $pieces, $recipe_id, $trigger_data, $user_id, $replace_args ) {

		$trigger_meta = $pieces[1];
		$parse        = $pieces[2];

		$recipe_log_id = isset( $replace_args['recipe_log_id'] ) ? (int) $replace_args['recipe_log_id'] : Automator()->maybe_create_recipe_log_entry( $recipe_id, $user_id )['recipe_log_id'];

		if ( ! $trigger_data || ! $recipe_log_id ) {
			return $value;
		}

		$acf_field_value = $this->get_meta_value_from_trigger_log_meta(
			$user_id,
			'ACF_FIELD_META_VALUE',
			$replace_args['trigger_id'],
			$replace_args['trigger_log_id']
		);

		$acf_field_key = $this->get_meta_value_from_trigger_log_meta(
			$user_id,
			'ACF_FIELD_META_KEY',
			$replace_args['trigger_id'],
			$replace_args['trigger_log_id']
		);

		$post_type_value = $this->get_meta_value_from_trigger_log_meta(
			$user_id,
			'ACF_POST_TYPE_NAME',
			$replace_args['trigger_id'],
			$replace_args['trigger_log_id']
		);

		$post_id = $this->get_meta_value_from_trigger_log_meta(
			$user_id,
			'ACF_POST_ID',
			$replace_args['trigger_id'],
			$replace_args['trigger_log_id']
		);

		foreach ( $trigger_data as $trigger ) {

			if ( ! isset( $trigger['meta'] ) ) {
				continue;
			}

			if ( ! key_exists( $trigger_meta, $trigger['meta'] ) && ( ! isset( $trigger['meta']['code'] ) && $trigger_meta !== $trigger['meta']['code'] ) ) {
				continue;
			}

			$value = '';

			switch ( $parse ) {
				case 'ACF_TRIGGER_FIELD':
					$value = $acf_field_value;
					break;
				case 'ACF_TRIGGER_POST_TYPE':
					$value = $post_type_value;
					break;
				case 'ACF_TRIGGER_POST_ID':
					$value = absint( $post_id );
					break;
				case 'ACF_TRIGGER_POST_URL':
					$value = esc_url( get_permalink( $post_id ) );
					break;
				case 'ACF_TRIGGER_POST_TITLE':
					$value = esc_html( get_the_title( $post_id ) );
					break;
				case 'ACF_TRIGGER_FIELD_NAME':
					$value = $acf_field_key;
					break;
			}
		}

		return $value;

	}

	/**
	 * Get the meta value from the trigger log table.
	 *
	 * @param mixed $user_id The user id.
	 * @param mixed $meta_key The meta key.
	 * @param mixed $trigger_id The trigger id.
	 * @param mixed $trigger_log_id The trigger log id.
	 *
	 * @return mixed The meta value.
	 */
	public function get_meta_value_from_trigger_log_meta( $user_id, $meta_key, $trigger_id, $trigger_log_id ) {

		global $wpdb;

		if ( empty( $meta_key ) || empty( $trigger_id ) || empty( $trigger_log_id ) ) {
			return '';
		}

		$meta_value = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT meta_value
				FROM {$wpdb->prefix}uap_trigger_log_meta
				WHERE user_id = %d
				AND meta_key = %s
				AND automator_trigger_id = %d
				AND automator_trigger_log_id = %d
				ORDER BY ID DESC LIMIT 0,1",
				$user_id,
				$meta_key,
				$trigger_id,
				$trigger_log_id
			)
		);

		if ( ! empty( $meta_value ) ) {
			return maybe_unserialize( $meta_value );
		}

		return '';
	}

}
