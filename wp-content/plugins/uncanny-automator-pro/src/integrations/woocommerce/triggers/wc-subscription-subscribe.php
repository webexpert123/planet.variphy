<?php

namespace Uncanny_Automator_Pro;


/**
 * Class WC_SUBSCRIPTION_SUBSCRIBE
 * @package Uncanny_Automator_Pro
 */
class WC_SUBSCRIPTION_SUBSCRIBE {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'WC';

	private $trigger_code;
	private $trigger_meta;


	/**
	 * SetAutomatorTriggers constructor.
	 */
	public function __construct() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( is_plugin_active( 'woocommerce-subscriptions' . DIRECTORY_SEPARATOR . 'woocommerce-subscriptions.php' ) ) {
			$this->trigger_code = 'WCSUBSCRIPTIONSUBSCRIBE';
			$this->trigger_meta = 'WOOSUBSCRIPTIONS';
			$this->define_trigger();
		}
	}

	/**
	 *
	 */
	public function define_trigger() {

		global $uncanny_automator;

		$options = $uncanny_automator->helpers->recipe->woocommerce->options->pro->all_wc_subscriptions();

		$trigger = array(
			'author'              => $uncanny_automator->get_author_name( $this->trigger_code ),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code, 'integration/woocommerce/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Logged-in trigger - WooCommerce */
			'sentence'            => sprintf( __( "A user subscribes to {{a product:%1\$s}}", 'uncanny-automator-pro' ), $this->trigger_meta ),
			/* translators: Logged-in trigger - WooCommerce */
			'select_option_name'  => __( "A user subscribes to {{a product}}", 'uncanny-automator-pro' ),
			'action'              => 'woocommerce_subscription_payment_complete',
			'priority'            => 30,
			'accepted_args'       => 1,
			'validation_function' => array( $this, 'subscription_created' ),
			'options'             => [
				$options,
			]
		);

		$uncanny_automator->register->trigger( $trigger );
	}

	/**
	 * @param $subscription
	 *
	 * @since 2.12
	 */
	public function subscription_created( $subscription ) {
		if ( ! $subscription instanceof \WC_Subscription ) {
			return;
		}

		$last_order_id = $subscription->get_last_order();

		if ( ! empty( $last_order_id ) && $last_order_id !== $subscription->get_parent_id() ) {
			return;
		}

		global $uncanny_automator;

		$user_id            = $subscription->get_user_id();
		$recipes            = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );
		$required_product   = $uncanny_automator->get->meta_from_recipes( $recipes, $this->trigger_meta );
		$matched_recipe_ids = [];

		if ( empty( $recipes ) ) {
			return;
		}

		$items       = $subscription->get_items();
		$product_ids = array();
		foreach ( $items as $item ) {
			$product = $item->get_product();
			if ( class_exists( '\WC_Subscriptions_Product' ) && \WC_Subscriptions_Product::is_subscription( $product ) ) {
				$product_ids[] = $product->get_id();
			}
		}

		//Add where option is set to Any product
		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];//return early for all products
				if ( intval( '-1' ) === intval( $required_product[ $recipe_id ][ $trigger_id ] ) || in_array( $required_product[ $recipe_id ][ $trigger_id ], $product_ids, false ) ) {
					$matched_recipe_ids[] = [
						'recipe_id'  => $recipe_id,
						'trigger_id' => $trigger_id,
					];

				}
			}
		}

		if ( ! empty( $matched_recipe_ids ) ) {
			foreach ( $matched_recipe_ids as $matched_recipe_id ) {
				$pass_args = [
					'code'             => $this->trigger_code,
					'meta'             => $this->trigger_meta,
					'user_id'          => $user_id,
					'recipe_to_match'  => $matched_recipe_id['recipe_id'],
					'trigger_to_match' => $matched_recipe_id['trigger_id'],
					'ignore_post_id'   => true,
				];

				$args = $uncanny_automator->maybe_add_trigger_entry( $pass_args, false );

				if ( $args ) {
					foreach ( $args as $result ) {
						if ( true === $result['result'] ) {

							// Add token for options
							$uncanny_automator->insert_trigger_meta(
								[
									'user_id'        => $user_id,
									'trigger_id'     => $result['args']['trigger_id'],
									'meta_key'       => 'subscription_id',
									'meta_value'     => $subscription->get_id(),
									'trigger_log_id' => $result['args']['get_trigger_id'],
									'run_number'     => $result['args']['run_number'],
								]
							);

							$uncanny_automator->maybe_trigger_complete( $result['args'] );
						}
					}
				}
			}
		}
	}
}
