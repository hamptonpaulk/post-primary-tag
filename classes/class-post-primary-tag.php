<?php
/**
 * Hook into WordPress and add post meta fields and save data
 */

if ( ! defined( 'ABSPATH' ) ) {
	echo 'Direct Access Not Allowed';
	exit;
}

/**
 * Main Post_Primary_Tag class for plugin
 *
 * Will allow the manipulation of the post meta for a post primary tag
 */
class Post_Primary_Tag {
	/**
	 * Factory object generation
	 * @return Post_Primary_Tag instance
	 */
	public static function factory() {
		static $instance = false;

		if ( ! $instance ) {
			$instance = new self();
			$instance->setup();
		}

		return $instance;
	}

	 /**
	  * Empty to protect against non factory production
	  */
	public function __construct() {}

	 /**
	  * Setup the actions for post_meta
	  *
	  * @method setup
	  * @internal called via factory method just after instantiation
	  */
	public function setup() {
		add_action( 'add_meta_boxes', array( $this, 'action_add_metabox' ) );
		add_action( 'save_post', array( $this, 'pt_save_meta' ) );
	}

	/**
	 * Get the primary tag object.
	 * @param int $post_id Post ID you want the primary tag of.
	 * @return object post primary tag object
	 */
	public function get_primary_tag( $post_id ) {
		$ppt_tag = null;
		$ppt_tag_id = get_post_meta( $post_id, 'pt_primary_tag_id' );

		if ( ! empty( $ppt_tag_id ) ) {
			$ppt_tag = get_term( $ppt_tag_id, 'post_tag' );
		}
		return $ppt_tag;
	}

	/**
	 * Get the primary tag name.
	 * @param int $post_id Post ID you want the primary tag of.
	 * @return string post primary tag name
	 */
	public function get_primary_tag_name( $post_id ) {
		$ppt_tag = $this->get_primary_tag( $post_id );

		if ( empty( $ppt_tag ) ) {
			return ''; }

		return $ppt_tag->name;
	}

	/**
	 * Add meta box to post with proper permissions
	 *
	 * @method action_add_metabox
	 * @internal called via add_meta_boxes action in setup method
	 * @param type $post_type post type from add_meta_box action.
	 */
	public function action_add_metabox( $post_type ) {
		$this->capability = 'edit_posts';
		if ( current_user_can( $this->capability ) ) {
			add_meta_box( 'primary-tag', 'Post Primary Tag', array( $this, '_post_primary_tag_fields' ), get_post_type(), 'side' );
		}
	}

	/**
	 * Display fieldset markup for the post_meta
	 *
	 * @method _post_primary_tag_fields
	 * @internal called via WP add_meta_box method in action_add_metabox
	 * @param type $post the post.
	 */
	public function _post_primary_tag_fields( $post ) {

		// Get primary tag for post.
		$pt_primary_tag = get_post_meta( $post->ID, 'pt_primary_tag_id', true );

		echo '<p>Choose a Primary Tag for This Post</p>';
		$post_tags = wp_get_post_tags( $post->ID );?>
		<p>
			<select name="pt_primary_tag_id">
				<option default> -- select an option -- </option>
				<?php foreach ( $post_tags as $tag ) : ?>
					<option value='<?php echo $tag->term_id ?>' <?php selected( esc_attr( $pt_primary_tag ), $tag->term_id ) ?> > <?php echo esc_attr( $tag->name ) ?> </option>
				<?php endforeach; ?>
			</select>
		</p>
		<?php
		wp_nonce_field( "update_{$post->ID}", "{$post->ID}_nonce" );
	}

	/**
	 * Manage The custom data store for primary tag id in post_meta
	 *
	 * Save post_meta for the posts primary tag for later use
	 * and will delete the post_meta if no primary tag is selected
	 *
	 * @method pt_save_meta
	 * @internal called via save_post action in setup method.
	 * @param int $post_id the post id.
	 * @return int $post_id only if autosave condition is met
	 */
	public function pt_save_meta( $post_id ) {

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
        }

		if ( isset( $_POST['pt_primary_tag_id'] ) && wp_verify_nonce( $_REQUEST[ "{$post_id}_nonce" ], "update_{$post_id}" ) ) {
			update_post_meta( $post_id, 'pt_primary_tag_id', absint( $_POST['pt_primary_tag_id'] ) );
		} else {
			delete_post_meta( $post_id, 'pt_primary_tag_id' );
		}
	}
}


/**
 *  Accessor for primary tag object
 * @param int $post_id the post id.
 * @return object primary tag term object
 */
function ppt_get_primary_tag( $post_id ) {
	return Post_Primary_Tag::factory()->get_primary_tag( $post_id );
}

/**
 *  Accessor for primary tag name
 * @param int $post_id the post id.
 * @return string primary tag name as string.
 */
function ppt_get_primary_tag_name( $post_id ) {
	return Post_Primary_Tag::factory()->get_primary_tag_name( $post_id );
}
