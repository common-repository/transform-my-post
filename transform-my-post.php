<?php

/**
 * Plugin Name: Transform My Post
 * Plugin URI: https://wordpress.org/plugins/transform-my-post/
 * Description: Transform your posts into different post types.
 * Version: 2.0
 * Author: Daniel James
 * Author URI: https://danieltj.uk/
 * Text Domain: transform-my-post
 */

/**
 * (c) Copyright 2019, Daniel James
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

if ( ! defined( 'ABSPATH' ) ) {

	die();

}

new Transform_My_Post;

class Transform_My_Post {

	/**
	 * Hook into WordPress.
	 * 
	 * @return void
	 */
	public function __construct() {

		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ), 10, 0 );
		add_action( 'save_post', array( __CLASS__, 'save_post_data' ), 10, 2 );

	}

	/**
	 * Get all the public post types.
	 * 
	 * @return array $post_types An array of post types.
	 */
	public static function get_post_types() {

		// Fetch all custom post types.
		$get_custom_types = get_post_types(
			array(
				'public' => true,
				'show_ui' => true,
				'_builtin' => false,
			),
			'names',
			'and'
		);

		$post_types = array();

		$post_types[] = 'post';
		$post_types[] = 'page';

		foreach ( $get_custom_types as $key => $value ) {

			$post_types[] = $value;

		}

		/**
		 * Filter the array of post types.
		 * 
		 * @since 1.0
		 * 
		 * @param array $post_types The array of post types.
		 * 
		 * @return array $post_types The filtered post types.
		 */
		$post_types = apply_filters( 'transformable_post_types', $post_types );

		return $post_types;

	}

	/**
	 * Adds the meta to the post screens.
	 * 
	 * @return void
	 */
	public static function add_meta_box() {

		$post_types = self::get_post_types();

		add_meta_box(
			'transform_my_post',
			esc_html__( 'Post Type', 'transform-my-post' ),
			array( __CLASS__, 'meta_box_content' ),
			$post_types,
			'side',
			'default',
			array( '__block_editor_compatible_meta_box' => true )
		);

	}

	/**
	 * Prints the setting on the post screens.
	 * 
	 * @param object $post WP_Post object of the current post.
	 * 
	 * @return mixed
	 */
	public static function meta_box_content( $post ) {

		$post_types = self::get_post_types();

		// Create the nonce.
		$transform_my_post_nonce = wp_create_nonce('transform_my_post_nonce');

		$post_type = get_post_type_object( $post->post_type );

		?>
			<p class="transform_my_post-meta-box">
				<label for="transform_my_post_option" class="screen-reader-text post-attributes-label"><?php esc_html_e( 'Post Type', 'transform-my-post' ); ?></label>
				<select name="transform_my_post_option" id="transform_my_post_option">
					<?php foreach ( $post_types as $type ) : ?>
						<?php $type_meta = get_post_type_object( $type ); ?>
						<option value="<?php echo esc_attr( $type ); ?>"<?php if ( $type == $post->post_type ) : ?> selected="selected"<?php endif; ?>><?php echo $type_meta->labels->singular_name; ?></option>
					<?php endforeach; ?>
				</select>
				<input type="hidden" name="transform_my_post_nonce" id="transform_my_post_nonce" value="<?php echo esc_attr( $transform_my_post_nonce ); ?>" />
			</p>
			<p>
				<?php printf( esc_html__( 'Leave the setting unchanged if you want to keep this %s as it&#39;s current post type.', 'transform-my-post' ), $post_type->labels->singular_name ); ?>
			</p>
		<?php 

	}

	/**
	 * Save the choice for the listing option.
	 * 
	 * This function was called `save_post_meta` in version 1.0
	 * and was sinec renamed. In 1.1 this function changed how the
	 * current post data was fetched (formerly `get_posts`) and saved.
	 * 
	 * @param string $post_id   The current post ID.
	 * @param object $post_data The current post object.
	 * 
	 * @return void
	 */
	public static function save_post_data( $post_id, $post_data ) {

		global $wpdb;

		// Filter the nonce value.
		$transform_my_post_nonce = isset ( $_POST['transform_my_post_nonce'] ) ? sanitize_text_field( $_POST['transform_my_post_nonce'] ) : '';

		// Verify the nonce value.
		if ( wp_verify_nonce( $transform_my_post_nonce, 'transform_my_post_nonce' ) ) {

			// Get the new post type.
			$new_type = isset ( $_POST['transform_my_post_option'] ) ? sanitize_text_field( $_POST['transform_my_post_option'] ) : false;

			$post_types = self::get_post_types();

			// Has the post type changed to a valid one?
			if ( $new_type !== $post_data->post_type && in_array( $new_type, $post_types ) ) {

				/**
				 * Filters the new post type.
				 * 
				 * @since 1.4
				 * 
				 * @param string $new_type The new post type.
				 * @param int    $post_id  The current post id.
				 */
				$new_type = apply_filters( 'transform_new_post_type', $new_type, $post_id );

				// Update the post type.
				$wpdb->update(
					$wpdb->prefix . 'posts',
					array(
						'post_type' => $new_type
					),
					array(
						'ID' => $post_id
					)
				);

				/**
				 * Fires after the post type is updated.
				 * 
				 * @since 1.4
				 * 
				 * @param string $new_type The new post type.
				 * @param int    $post_id  The current post id.
				 */
				do_action( 'after_transform_post_type', $new_type, $post_id );

			}

		}

	}

}

