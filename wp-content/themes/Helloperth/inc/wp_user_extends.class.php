<?php 
    final class wp_user_extended {

	static $instance;

	/**
	 * Initialize all the things
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		
		self::$instance =& $this;
		
		// Actions
		add_action( 'init',                     array( $this, 'load_textdomain'         )        );
		add_action( 'show_user_profile',        array( $this, 'use_profile_field'       )        );
		add_action( 'edit_user_profile',        array( $this, 'use_profile_field'       )        );
		add_action( 'personal_options_update',  array( $this, 'user_profile_field_save' )        );
		add_action( 'edit_user_profile_update', array( $this, 'user_profile_field_save' )        );
		add_action( 'wp_login',                 array( $this, 'user_login'              ), 10, 2 );
		add_filter( 'login_message',            array( $this, 'user_login_message'      )        );
	}

	/**
	 * Load the textdomain so we can support other languages
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'wp_user_extended', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );                
	}

	/**
	 * Add the field to user profiles
	 *
	 * @since 1.0.0
	 * @param object $user
	 */
	public function use_profile_field( $user ) {
                echo '<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">';
		// Only show this option to users who can delete other users
		if ( !current_user_can( 'edit_users' ) )
			return;
		
                $attachment_id = get_the_author_meta( 'profile_pic', $user->ID );
                $isset_profile_pic = 0;
                if(is_numeric($attachment_id)){
                    $profile_pic = wp_get_attachment_image_src($attachment_id, 'full');
                    $isset_profile_pic = 1;
                }
                ?>
		<table class="form-table">
                    <tbody>
                        <tr>
                            <th>
                                <label for="contact_number"><?php _e(' Contact Number', 'wp_user_extended' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="contact_number" id="contact_number" value="<?php echo get_the_author_meta( 'contact_number', $user->ID ); ?>" class="regular-text code" />
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="profile_pic"><?php _e(' Profile Picture', 'wp_user_extended' ); ?></label>
                            </th>
                            <td>
                                <div class="upload-image-block" id="figure_parent">
                                    <figure class="upload-image-img" id="dir_feature_image" style="margin: 0;">
                                        <?php if($isset_profile_pic == 1): ?>
                                        <img src="<?php echo $profile_pic[0]; ?>" width="200" height="150" alt="Profile picture"/>
                                        <a href="javascript:void(0);" title="Remove image" id="remove_dir_feature_image" class="btn btn-remove-image"><i class="fa fa-times-circle"></i></a>
                                        <?php endif; ?>
                                    </figure>
                                    <div class="upload-btn-group" id="control-div">
                                        <?php if($isset_profile_pic == 0) :?>
                                        <button id="upload_image_button" class="button btn upload-btn" type="button" value="Upload Image" ><i class="fa fa-plus-circle"></i><span>Upload Image</span></button>
                                        <?php endif; ?>                                     
                                    </div>
                                </div>
                                <input type="hidden" name="post_thumbnail" id="post_thumbnail" value="<?php echo $attachment_id; ?>" />
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="ja_disable_user"><?php _e(' Disable User Account', 'wp_user_extended' ); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" name="ja_disable_user" id="ja_disable_user" value="1" <?php checked( 1, get_the_author_meta( 'ja_disable_user', $user->ID ) ); ?> />
                                <span class="description"><?php _e( 'If checked, the user cannot login with this account.' , 'wp_user_extended' ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="free_user"><?php _e(' Free access for lifetime', 'wp_user_extended' ); ?></label>
                            </th>
                            <td>
                                <input type="checkbox" name="free_access" id="free_access" value="1" <?php checked( 1, get_the_author_meta( 'free_access', $user->ID ) ); ?> />
                                <span class="description"><?php _e( 'If checked, the user will have lifetime access for free.' , 'wp_user_extended' ); ?></span>
                            </td>
                        </tr>
                    <tbody>
		</table>
		<?php
	}

	/**
	 * Saves the custom field to user meta
	 *
	 * @since 1.0.0
	 * @param int $user_id
	 */
	public function user_profile_field_save( $user_id ) {

		// Only worry about saving this field if the user has access
		if ( !current_user_can( 'edit_users' ) )
			return;

		if ( !isset( $_POST['ja_disable_user'] ) ) {
                    $disabled = 0;
		} else {
                    $disabled = $_POST['ja_disable_user'];
		}
                
                if ( !isset( $_POST['free_access'] ) ) {
                    $free_access = 0;
		} else {
                    $free_access = $_POST['free_access'];
		}
                
		update_user_meta( $user_id, 'contact_number', $_POST['contact_number'] );
		update_user_meta( $user_id, 'profile_pic', $_POST['post_thumbnail'] );
		update_user_meta( $user_id, 'ja_disable_user', $disabled );
		update_user_meta( $user_id, 'free_access', $free_access );
                
                $cus_id = get_user_meta($user_id, 'stripe_cus_id', true);
                $userdata = get_userdata($user_id);
                $metadata = array(
                    'system_id' => $user_id,
                    'system_name' => $userdata->display_name,
                    'system_username' => $userdata->user_login,
                );
                update_stripe_customer($cus_id, $userdata->user_email, $metadata);
	}

	/**
	 * After login check to see if user account is disabled
	 *
	 * @since 1.0.0
	 * @param string $user_login
	 * @param object $user
	 */
	public function user_login( $user_login, $user ) {

		// Get user meta
		$disabled = get_user_meta( $user->ID, 'ja_disable_user', true );
		
		// Is the use logging in disabled?
		if ( $disabled == '1' ) {
			// Clear cookies, a.k.a log user out
			wp_clear_auth_cookie();

			// Build login URL and then redirect
			$login_url = site_url( 'wp-login.php', 'login' );
			$login_url = add_query_arg( 'disabled', '1', $login_url );
			wp_redirect( $login_url );
			exit;
		}
	}

	/**
	 * Show a notice to users who try to login and are disabled
	 *
	 * @since 1.0.0
	 * @param string $message
	 * @return string
	 */
	public function user_login_message( $message ) {

		// Show the error message if it seems to be a disabled user
		if ( isset( $_GET['disabled'] ) && $_GET['disabled'] == 1 ) 
			$message =  '<div id="login_error">' . apply_filters( 'wp_user_extended_notice', __( 'Account disabled', 'wp_user_extended' ) ) . '</div>';

		return $message;
	}

}
new wp_user_extended();
?>