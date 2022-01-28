<?php

namespace Uncanny_Automator_Pro;

/**
 * Class BB_POSTAREPLY
 * @package Uncanny_Automator_Pro
 */
class BB_POSTAREPLY {

	/**
	 * Integration code
	 * @var string
	 */
	public static $integration = 'BB';

	private $trigger_code;
	private $trigger_meta;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'BBPOSTAREPLY';
		$this->trigger_meta = 'BBTOPIC';
		$this->define_trigger();
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		global $uncanny_automator;

		$bbp_forum_post_type = apply_filters( 'bbp_forum_post_type',  'forum'     );
		$args = [
			'post_type'      => $bbp_forum_post_type,
			'posts_per_page' => 999,
			'orderby'        => 'title',
			'order'          => 'ASC',
			'post_status'    => [ 'publish', 'private' ],
		];

		global $uncanny_automator;
		$options               = $uncanny_automator->helpers->recipe->options->wp_query( $args, true, __( 'Any forum', 'uncanny-automator' ) );
		$forum_relevant_tokens = [
			'BBFORUMS'     => __( 'Forum title', 'uncanny-automator' ),
			'BBFORUMS_ID'  => __( 'Forum ID', 'uncanny-automator' ),
			'BBFORUMS_URL' => __( 'Forum URL', 'uncanny-automator' ),
		];

		$relevant_tokens = [
			$this->trigger_meta          => __( 'Topic title', 'uncanny-automator' ),
			$this->trigger_meta . '_ID'  => __( 'Topic ID', 'uncanny-automator' ),
			$this->trigger_meta . '_URL' => __( 'Topic URL', 'uncanny-automator' ),
		];

		$trigger = array(
			'author'              => $uncanny_automator->get_author_name( $this->trigger_code ),
			'support_link'        => $uncanny_automator->get_author_support_link( $this->trigger_code, 'integration/bbpress/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Logged-in trigger - bbPress */
			'sentence'            => sprintf( __( 'A user replies to {{a topic:%1$s}}', 'uncanny-automator-pro' ), $this->trigger_meta ),
			/* translators: Logged-in trigger - bbPress */
			'select_option_name'  => __( 'A user replies to {{a topic}}', 'uncanny-automator-pro' ),
			'action'              => 'bbp_new_reply',
			'priority'            => 10,
			'accepted_args'       => 3,
			'validation_function' => array( $this, 'bbp_insert_reply' ),
			'options_group'       => [
				$this->trigger_meta => [
					$uncanny_automator->helpers->recipe->field->select_field_ajax(
						'BBFORUMS',
						__( 'Forum', 'uncanny-automator' ),
						$options,
						'',
						'',
						false,
						true,
						[
							'target_field' => $this->trigger_meta,
							'endpoint'     => 'select_topic_from_forum_TOPICREPLY',
						],
						$forum_relevant_tokens
					),
					$uncanny_automator->helpers->recipe->field->select_field( $this->trigger_meta, __( 'Topic', 'uncanny-automator' ), [], false, false, false, $relevant_tokens ),
				],
			],
		);

		$uncanny_automator->register->trigger( $trigger );

		return;
	}

	/**
	 *  Validation function when the trigger action is hit
	 *
	 * @param $reply_id
	 * @param $topic_id
	 * @param $forum_id
	 */
	public function bbp_insert_reply( $reply_id, $topic_id, $forum_id ) {

		global $uncanny_automator;

		$recipes = $uncanny_automator->get->recipes_from_trigger_code( $this->trigger_code );

		$conditions = $this->match_condition( $topic_id, $forum_id, $recipes, $this->trigger_meta, $this->trigger_code, 'SUBVALUE' );

		if ( ! $conditions ) {
			return;
		}

		$user_id = get_current_user_id();
		if ( ! empty( $conditions ) ) {
			foreach ( $conditions['recipe_ids'] as $recipe_id => $trigger_id ) {
				if ( ! $uncanny_automator->is_recipe_completed( $recipe_id, $user_id ) ) {
					$args = [
						'code'             => $this->trigger_code,
						'meta'             => $this->trigger_meta,
						'recipe_to_match'  => $recipe_id,
						'trigger_to_match' => $trigger_id,
						'post_id'          => $topic_id,
						'user_id'          => $user_id,
					];

					$uncanny_automator->maybe_add_trigger_entry( $args );
				}
			}
		}
	}

	/**
	 * Match condition for form field and value.
	 *
	 * @param int $topic_id .
	 * @param int $forum_id .
	 * @param null|array $recipes .
	 * @param null|string $trigger_meta .
	 * @param null|string $trigger_code .
	 * @param null|string $trigger_second_code .
	 *
	 * @return array|bool
	 */
	public function match_condition( $topic_id, $forum_id, $recipes = null, $trigger_meta = null, $trigger_code = null, $trigger_second_code = null ) {
		if ( null === $recipes ) {
			return false;
		}

		$recipe_ids = [];
		foreach ( $recipes as $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				if ( key_exists( 'BBFORUMS', $trigger['meta'] ) && ( $trigger['meta']['BBFORUMS'] == - 1 || $trigger['meta']['BBFORUMS'] == $forum_id ) ) {
					if ( key_exists( $trigger_meta, $trigger['meta'] ) && ( $trigger['meta'][ $trigger_meta ] == - 1 || $trigger['meta'][ $trigger_meta ] == $topic_id ) ) {
						$recipe_ids[ $recipe['ID'] ] = $trigger['ID'];
					}
				}
			}
		}

		if ( ! empty( $recipe_ids ) ) {
			return [ 'recipe_ids' => $recipe_ids, 'result' => true ];
		}

		return false;
	}
}
