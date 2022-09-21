<?php
/**
 * This template contains the Friends Role Mode Switcher
 *
 * @package Friends
 */

if ( empty( $args['roles'] ) ) {
	return;
}

?>

<p>
	<?php esc_html_e( 'The friend plugin defines a couple of relevant roles that make it work.', 'friends' ); ?>
	<?php esc_html_e( 'You can customize those roles to your liking to better represent your network.', 'friends' ); ?>
</p>

<form method="post">

	<h3><?php esc_html_e( 'Non-Friend Roles', 'friends' ); ?></h3>

	<?php wp_nonce_field( 'friends-roles' ); ?>
	<table class="widefat">
		<thead>
			<tr>
				<th><?php echo esc_html( _x( 'Role Name', 'role-type', 'friends' ) ); ?></th>
				<th><?php echo esc_html( _x( 'Type', 'role-type', 'friends' ) ); ?></th>
			</tr>
		</thead>
<?php
foreach ( $args['roles'] as $role => $data ) {
	if ( isset( $data['capabilities']['acquaintance'] ) || isset( $data['capabilities']['friend'] ) ) {
		continue;
	}
	?>
	<tr id="role=<?php echo esc_attr( $role ); ?>">
		<td><input type="text" name="friend_role[<?php echo esc_attr( $role ); ?>][name]" value="<?php echo esc_attr( $data['name'] ); ?>" placeholder="Name"></td>
		<td>
		<?php
		if ( isset( $data['capabilities']['friend_request'] ) ) {
			?>
			<?php esc_html_e( 'A received request to form a connection', 'friends' ); ?>
			<?php
		} elseif ( isset( $data['capabilities']['pending_friend_request'] ) ) {
			?>
			<?php esc_html_e( 'A request sent by you to form a connection', 'friends' ); ?>
			<?php
		} elseif ( isset( $data['capabilities']['subscription'] ) ) {
			?>
			<?php esc_html_e( 'You are just subscribed to their posts', 'friends' ); ?>
			<?php
		}
		?>
		</td>
	</tr>
	<?php
}
?>
	</table>
	<p><button><?php esc_html_e( 'Save' ); ?></button></p>

	<h3><?php esc_html_e( 'Friend Roles' ); ?></h3>

	<table class="widefat">
		<thead>
			<tr>
				<th><?php echo esc_html( _x( 'Role Name', 'role-type', 'friends' ) ); ?></th>
				<th><?php echo esc_html__( 'Capabilities', 'friends' ); ?></th>
				<th><?php echo esc_html_e( 'Action', 'friends' ); ?></th>
			</tr>
		</thead>
<?php
foreach ( $args['roles'] as $role => $data ) {
	if ( ! isset( $data['capabilities']['acquaintance'] ) && ! isset( $data['capabilities']['friend'] ) ) {
		continue;
	}
	?>
	<tr id="role=<?php echo esc_attr( $role ); ?>">
		<td><input type="text" name="friend_role[<?php echo esc_attr( $role ); ?>][name]" value="<?php echo esc_attr( $data['name'] ); ?>" placeholder="Name"></td>
		<td>
		<?php
		if ( isset( $data['capabilities']['acquaintance'] ) ) {
			?>
			<?php esc_html_e( 'Public posts only', 'friends' ); ?>
			<?php
		} elseif ( isset( $data['capabilities']['friend'] ) ) {
			?>
			<?php esc_html_e( 'Private and public posts', 'friends' ); ?>
			<?php
		}
		?>
		</td>
		<td></td>
	</tr>
	<?php
}
?>
	</table>
	<p><button><?php esc_html_e( 'Save' ); ?></button></p>
</form>

<form method="post">

	<h3><?php esc_html_e( 'Add More Roles', 'friends' ); ?></h3>

	<?php wp_nonce_field( 'friends-roles' ); ?>

	<table class="form-table">
		<tr>
			<th scope="row"><label for="additional-role-name"><?php echo esc_html( _x( 'Label', 'role-type', 'friends' ) ); ?></label></th>
			<td><input type="text" name="new_role_name" id="additional-role-name" value="" title="<?php esc_attr_e( 'This will be displayed for the role.', 'friends' ); ?>" required="required" />
				<p class="description"><?php esc_html_e( 'This will be displayed for the role.', 'friends' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="additional-role-type"><?php echo esc_html( _x( 'Type', 'role-type', 'friends' ) ); ?></label></th>
			<td>
				<select id="additional-role-type" required="required" name="new_role_type">
					<option value="acquaintance"><?php esc_html_e( 'Friend: Public posts only', 'friends' ); ?></option>
					<option value="friend"><?php esc_html_e( 'Friend: Private and public posts', 'friends' ); ?></option>
					<option value="subscription"><?php esc_html_e( 'Subscription', 'friends' ); ?></option>
				</select>
			</td>
		</tr>
	</table>
	<p><button><?php esc_html_e( 'Create role', 'friends' ); ?></button></p>
</form>
