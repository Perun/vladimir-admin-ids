<?php
/**
 * Plugin Name: Vladimir Admin IDs
 * Description: Zeigt Objekt-IDs in den Verwaltungslisten und kopierbare ID-Links für Beiträge und Seiten.
 * Version: 0.1.3
 * Author: Vladimir Simović
 * Author URI: https://www.perun.net/
 * Requires at least: 6.8
 * Requires PHP: 8.1
 * License: GPL-2.0
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: vladimir-admin-ids
 * Update URI: false
 *
 * Based on Simply Show IDs 1.3.3, Copyright (c) 2009-2010 Matt Martz (http://sivel.net).
 * Reworked in 2026 for Vladimir Admin IDs. Distributed under the GNU General Public License v2.
 *
 * @package VladimirAdminIDs
 */

namespace Vladimir\AdminIDs;

defined( 'ABSPATH' ) || exit;

const VERSION = '0.1.3';

/**
 * Append the numeric ID column, preserving columns registered by others.
 *
 * @param array $columns Existing list table columns.
 * @return array
 */
function id_column( $columns ) {
	$columns['vladimir_admin_id'] = esc_html__( 'ID', 'vladimir-admin-ids' );
	return $columns;
}

/**
 * Append the optional ID link column for pages and posts.
 *
 * @param array $columns Existing list table columns.
 * @return array
 */
function link_column( $columns ) {
	$columns['vladimir_admin_id_link'] = esc_html__( 'ID-Link', 'vladimir-admin-ids' );
	return $columns;
}

/**
 * Print a numeric cell for tables that dispatch an action.
 *
 * @param string $column Column key.
 * @param int    $id     Object ID supplied by the list table.
 */
function print_id( $column, $id ) {
	if ( 'vladimir_admin_id' === $column ) {
		echo absint( $id );
	}
}

/**
 * Supply a numeric cell for tables that filter column content.
 *
 * @param string $value  Previous filter result.
 * @param string $column Column key.
 * @param int    $id     Object ID supplied by the list table.
 * @return string
 */
function filter_id( $value, $column, $id ) {
	return 'vladimir_admin_id' === $column ? (string) absint( $id ) : $value;
}

/**
 * Render the URL and controls in a post or page row.
 *
 * @param string $column  Column key.
 * @param int    $post_id Post or page ID supplied by WordPress.
 */
function print_link( $column, $post_id ) {
	if ( 'vladimir_admin_id_link' !== $column ) {
		return;
	}

	$post = get_post( $post_id );
	if ( ! $post || ! in_array( $post->post_type, array( 'post', 'page' ), true ) || ! current_user_can( 'read_post', $post->ID ) ) {
		return;
	}

	$key  = 'page' === $post->post_type ? 'page_id' : 'p';
	$url  = add_query_arg( $key, $post->ID, home_url( '/' ) );
	$note = '';
	if ( ! is_post_status_viewable( $post->post_status ) ) {
		$note = __( 'Kein öffentlicher Zugriff. Für eine Vorschau nutze die WordPress-Vorschau.', 'vladimir-admin-ids' );
	} elseif ( '' !== $post->post_password ) {
		$note = __( 'Zum Lesen ist das Passwort erforderlich.', 'vladimir-admin-ids' );
	}
	?>
	<div class="vladimir-admin-ids-link">
		<label>
			<span class="screen-reader-text">
			<?php
				/* translators: %d: post or page ID. */
				printf( esc_html__( 'ID-Link für Inhalt %d', 'vladimir-admin-ids' ), absint( $post->ID ) );
			?>
			</span>
			<input type="text" class="vladimir-admin-ids-url" readonly dir="ltr" value="<?php echo esc_url( $url ); ?>">
		</label>
		<button type="button" class="button button-small vladimir-admin-ids-copy"
			data-success="<?php esc_attr_e( 'Kopiert', 'vladimir-admin-ids' ); ?>"
			data-manual="<?php esc_attr_e( 'Bitte die markierte URL manuell kopieren.', 'vladimir-admin-ids' ); ?>">
			<?php esc_html_e( 'Kopieren', 'vladimir-admin-ids' ); ?>
			<span class="screen-reader-text">
			<?php
				/* translators: %d: post or page ID. */
				printf( esc_html__( '(ID-Link für Inhalt %d)', 'vladimir-admin-ids' ), absint( $post->ID ) );
			?>
			</span>
		</button>
		<span class="vladimir-admin-ids-status" role="status" aria-live="polite" aria-atomic="true"></span>
		<?php if ( '' !== $note ) : ?>
			<small class="vladimir-admin-ids-note"><?php echo esc_html( $note ); ?></small>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Register hooks during admin requests, including inline-edit row refreshes.
 */
function register_columns() {
	foreach ( array( 'manage_posts_columns', 'manage_pages_columns', 'manage_media_columns', 'manage_edit-comments_columns', 'manage_users_columns', 'manage_users-network_columns', 'manage_site-users-network_columns' ) as $hook ) {
		add_filter( $hook, __NAMESPACE__ . '\id_column' );
	}
	foreach ( array( 'manage_posts_custom_column', 'manage_pages_custom_column', 'manage_media_custom_column', 'manage_comments_custom_column' ) as $hook ) {
		add_action( $hook, __NAMESPACE__ . '\print_id', 10, 2 );
	}
	foreach ( array( 'post', 'page' ) as $type ) {
		add_filter( "manage_{$type}_posts_columns", __NAMESPACE__ . '\link_column' );
		add_action( "manage_{$type}_posts_custom_column", __NAMESPACE__ . '\print_link', 10, 2 );
	}

	add_filter( 'manage_users_custom_column', __NAMESPACE__ . '\filter_id', 10, 3 );

	foreach ( get_taxonomies() as $taxonomy ) {
		add_filter( "manage_edit-{$taxonomy}_columns", __NAMESPACE__ . '\id_column' );
		add_filter( "manage_{$taxonomy}_custom_column", __NAMESPACE__ . '\filter_id', 10, 3 );
	}

	if ( get_option( 'link_manager_enabled' ) ) {
		add_filter( 'manage_link-manager_columns', __NAMESPACE__ . '\id_column' );
		add_action( 'manage_link_custom_column', __NAMESPACE__ . '\print_id', 10, 2 );
	}
}

/**
 * Load styles on supported tables and copying code on the two link screens.
 */
function enqueue_assets() {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return;
	}

	$is_list = in_array( $screen->base, array( 'edit', 'edit-tags', 'upload', 'edit-comments', 'users', 'users-network', 'site-users-network' ), true );
	if ( ! $is_list && ! ( 'link-manager' === $screen->id && get_option( 'link_manager_enabled' ) ) ) {
		return;
	}

	wp_enqueue_style( 'vladimir-admin-ids', plugins_url( 'assets/admin.css', __FILE__ ), array(), VERSION );
	if ( 'edit' === $screen->base && in_array( $screen->post_type, array( 'post', 'page' ), true ) ) {
		wp_enqueue_script( 'vladimir-admin-ids', plugins_url( 'assets/copy.js', __FILE__ ), array(), VERSION, true );
	}
}

add_action( 'admin_init', __NAMESPACE__ . '\register_columns' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_assets' );
