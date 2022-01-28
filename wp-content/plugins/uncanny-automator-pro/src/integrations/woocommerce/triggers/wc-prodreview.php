<?php

namespace Uncanny_Automator_Pro;


/**
 * Class WC_PRODREVIEW
 * @package Uncanny_Automator_Pro
 */
class WC_PRODREVIEW {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'WC';

	/**
	 * @var string
	 */
	private $trigger_code;
	/**
	 * @var string
	 */
	private $trigger_meta;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'WCPRODREVIEW';
		$this->trigger_meta = 'WOOPRODUCT';
		$this->define_trigger();
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		global $uncanny_automator;

		$options = $uncanny_automator->helpers->recipe->woocommerce->options->all_wc_products( __( 'Product', 'uncanny-automator' ) );

		$options['options'] = array( '-1' => __( 'Any product', 'uncanny-automator' ) ) + $options['options'];

		$trigger = array(
			'author'              => $uncanny_automator->get_author_name( $this->trigger_code ),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code, 'integration/woocommerce/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Logged-in trigger - WooCommerce */
			'sentence'            => sprintf( __( 'A user reviews {{a product:%1$s}}', 'uncanny-automator-pro' ), $this->trigger_meta ),
			/* translators: Logged-in trigger - WooCommerce */
			'select_option_name'  => __( 'A user reviews {{a product}}', 'uncanny-automator-pro' ),
			'action'              => 'wp_insert_comment',
			'priority'            => 90,
			'accepted_args'       => 2,
			'validation_function' => array( $this, 'comment_approved' ),
			'options'             => array( $options ),
		);

		$uncanny_automator->register->trigger( $trigger );
	}


	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $id
	 * @param \WP_Comment $comment Comment object.
	 */
	public function comment_approved( $id, \WP_Comment $comment ) {
		if ( 'review' !== (string) $comment->comment_type ) {
			return;
		}

		if ( ! $comment instanceof \WP_Comment ) {
			return;
		}

		if ( isset( $comment->user_id ) && 0 === absint( $comment->user_id ) ) {
			return;
		}

		global $uncanny_automator;
		$recipes            = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );
		$required_post      = $uncanny_automator->get->meta_from_recipes( $recipes, $this->trigger_meta );
		$matched_recipe_ids = array();

		//Add where option is set to Any post / specific post
		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];
				if ( intval( '-1' ) === intval( $required_post[ $recipe_id ][ $trigger_id ] ) || absint( $required_post[ $recipe_id ][ $trigger_id ] ) === absint( $comment->comment_post_ID ) ) {
					$matched_recipe_ids[] = [
						'recipe_id'  => $recipe_id,
						'trigger_id' => $trigger_id,
					];
				}
			}
		}

		if ( empty( $matched_recipe_ids ) ) {
			return;
		}

		//	If recipe matches
		foreach ( $matched_recipe_ids as $matched_recipe_id ) {
			$pass_args = array(
				'code'             => $this->trigger_code,
				'meta'             => $this->trigger_meta,
				'user_id'          => $comment->user_id,
				'recipe_to_match'  => $matched_recipe_id['recipe_id'],
				'trigger_to_match' => $matched_recipe_id['trigger_id'],
				'post_id'          => $comment->comment_post_ID,
			);

			$args = $uncanny_automator->maybe_add_trigger_entry( $pass_args, false );

			do_action( 'uap_wp_comment_approve', $comment, $matched_recipe_id['recipe_id'], $matched_recipe_id['trigger_id'], $args );
			do_action( 'uap_wc_trigger_save_product_meta', $comment->comment_ID, $matched_recipe_id['recipe_id'], $args, 'product' );

			if ( $args ) {
				foreach ( $args as $result ) {
					if ( true === $result['result'] ) {
						$uncanny_automator->maybe_trigger_complete( $result['args'] );
					}
				}
			}
		}
	}

}