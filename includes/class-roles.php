<?php
/**
 * Friends Roles
 *
 * This contains the Roles functions.
 *
 * @package Friends_Roles
 */

namespace Friends;

/**
 * This is the class for the administering roles for the Friends Plugin.
 *
 * @since 0.3
 *
 * @package Friends_Roles
 * @author Alex Kirk
 */
class Roles {
	/**
	 * Contains a reference to the Friends class.
	 *
	 * @var Friends
	 */
	private $friends;

	/**
	 * Constructor
	 *
	 * @param Friends $friends A reference to the Friends object.
	 */
	public function __construct( Friends $friends ) {
		$this->friends = $friends;
		$this->register_hooks();
	}

	function register_hooks() {
		add_filter( 'friends_template_paths', array( $this, 'friends_template_paths' ) );
		add_filter( 'friends_admin_tabs', array( $this, 'friends_admin_tabs' ) );
		add_filter( 'admin_menu', array( $this, 'admin_menu' ), 50 );
	}

	public function friends_template_paths( $paths ) {
		$paths[52] = FRIENDS_ROLES_PLUGIN_DIR . 'templates/';
		return $paths;
	}

	public function friends_admin_tabs( $tabs ) {
		$tabs[ __( 'Roles', 'friends') ] = 'friends-roles';
		return $tabs;
	}

	public function admin_menu() {
		if ( '' === menu_page_url( 'friends', false ) ) {
		// Don't add menu when no Friends menu is shown.
			return;
		}
		add_submenu_page( 'friends', 'Roles', 'Roles', 'administrator', 'friends-roles', array( $this, 'render_admin_roles' ) );

		$menu_title = __( 'Friends', 'friends' ) . Friends::get_instance()->admin->get_unread_badge();
		$page_type = sanitize_title( $menu_title );
		add_action( 'load-' . $page_type . '_page_friends-roles', array( $this, 'process_admin_roles' ) );
	}

	private function get_roles() {
		$friend_roles = array();

		$roles = new \WP_Roles;
		foreach ( $roles->roles as $role => $data ) {
			if ( isset( $data['capabilities']['friends_plugin'] ) ) {
				$friend_roles[ $role ] = $data;
			}
		}
		return $friend_roles;
	}

	/**
	 * Check access for the Friends Admin settings page
	 */
	public function check_admin_roles() {
		if ( ! current_user_can( Friends::REQUIRED_ROLE ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to change the settings.', 'friends' ) );
		}
	}

	public function process_admin_roles() {
		$this->check_admin_roles();

		if ( empty( $_POST ) ) {
			return;
		}
		if ( empty( $_POST['friend_role'] ) && empty( $_POST['new_role_name'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'friends-roles' ) ) {
			return;
		}

		$roles = $this->get_roles();
		$role_names = wp_roles()->get_names();
		$updated = 0;

		foreach( $roles as $role => $data ) {
			if ( ! isset( $_POST['friend_role'][ $role ] ) ) {
				continue;
			}

			$name = sanitize_text_field( $_POST['friend_role'][ $role ]['name'] );
			if ( empty( $name ) || $name === $data['name'] ) {
				continue;
			}
			$existing_role = array_search( $name, $role_names, true );
			if ( $existing_role && $existing_role !== $role ) {
				// Another role with this name already exists.
				continue;
			}

			$new_slug = sanitize_title_with_dashes( $name );
			if ( $role === $new_slug || ! get_role( $new_slug ) ) {
				remove_role( $role );
				unset( $roles[ $role ] );

				$updated = 1;

				add_role( $new_slug, $name, $data['capabilities'] );
				$roles[ $new_slug ] = array(
					'name' => $name,
					'capabilities' => $data['capabilities'],
				);
			}
		}

		if ( ! empty( $_POST['new_role_name'] ) && ! empty( $_POST['new_role_type'] )) {
			$name = sanitize_text_field( $_POST['new_role_name'] );
			$capabilities = Friends::get_role_capabilities( $_POST['new_role_type'] );

			// Does another role with this name already exist?
			$existing_role = array_search( $name, $role_names, true );

			if ( ! empty( $name ) && ! $existing_role && $capabilities ) {
				$new_slug = sanitize_title_with_dashes( $name );
				$c = 0;
				$existing_role = get_role( $new_slug );
				while ( $existing_role ) {
					$new_slug = sanitize_title_with_dashes( $name ) . '-' . ++$c;
					$existing_role = get_role( $new_slug );
				}
				if ( $new_slug ) {
					$updated = 1;

					echo '<pre>add_role';var_dump( $new_slug, $name, Friends::get_role_capabilities( $_POST['new_role_type'] ) );
					$r = add_role( $new_slug, $name, $capabilities );
					$roles[ $new_slug ] = array(
						'name' => $name,
						'capabilities' => $capabilities,
					);
					var_dump( $r );
				}
			}
		}
exit;
		if ( isset( $_GET['_wp_http_referer'] ) ) {
			wp_safe_redirect( wp_get_referer() );
		} else {
			$url = remove_query_arg( array( '_wp_http_referer', '_wpnonce' ), wp_unslash( $_SERVER['REQUEST_URI'] ) );
			if ( $updated ) {
				$url = add_query_arg( 'updated', $updated, $url );
			}
			wp_safe_redirect( $url );
		}
		exit;
	}

	public function render_admin_roles() {
		$this->check_admin_roles();

		Friends::template_loader()->get_template_part(
			'admin/settings-header',
			null,
			array(
				'active' => 'friends-roles',
				'title'  => __( 'Friends', 'friends' ),
			)
		);

		if ( isset( $_GET['updated'] ) ) {
			?>
			<div class="notice notice-success is-dismissible"><p>
				<?php
				esc_html_e( 'The roles were updated.', 'friends' );
				?>
			</p></div>
			<?php
		}

		$friend_roles = $this->get_roles();

		Friends::template_loader()->get_template_part(
			'admin/roles',
			null,
			array(
				'roles' => $friend_roles,
			)
		);
		Friends::template_loader()->get_template_part( 'admin/settings-footer' );
	}
}
