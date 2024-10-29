<?php
/**
 * Class for admin methods for field repeater.
 *
 * @package Activecampaign_Newsletter_Subscription
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// If class is exist, then don't execute this.
if ( ! class_exists( 'Activecampaign_Newsletter_Subscription_Admin' ) ) {

	/**
	 * Calls for admin methods.
	 */
	class Activecampaign_Newsletter_Subscription_Admin {


		/**
		 * Constructor for class.
		 */
		public function __construct() {
			// Create Admin Menu.
			add_action( 'admin_menu', array( $this, 'acns_admin_menu' ) );

			// Submit Data Of Admin Menu Form.
			add_action( 'admin_init', array( $this, 'acns_submit_data' ) );

		}

		/**
		 * Custom Admin Menu For AC Newsletter Subscription.
		 */
		public function acns_admin_menu() {
			add_menu_page(
				esc_html__( 'Newsletter Subscription', 'ac-newsletter' ),
				esc_html__( 'Newsletter Subscription', 'ac-newsletter' ),
				'manage_options',
				'ac-newsletter',
				array( $this, 'acns_menu_callback' ),
				'dashicons-clipboard',
				100
			);
		}

		/**
		 * Callback Function Of Custom Admin Menu.
		 */
		public function acns_menu_callback() {
			echo sprintf(
				'<div class="warp"><h1>%s</h1></div>',
				esc_html__( 'Activecampaign Newsletter Subscription.', 'ac-newsletter' )
			);
			?>
			<form method="post" action="" id="acns_form">
				<table class="form-table">
					<tbody>
						<tr>
							<th>
								<label for="acns_api_url"> <?php esc_html_e( 'Enter Your ActiveCampaign API URL', 'ac-newsletter' ); ?> </label>
							</th>
							<td>
								<?php $fetch_api_url = ! empty( get_option( 'acns_api_url' ) ) ? get_option( 'acns_api_url' ) : ''; ?>
								<input name="acns_api_url" id="acns_api_url" type="text" class="regular-text" value="<?php echo esc_attr( $fetch_api_url ); ?>" placeholder="<?php esc_attr_e( 'https://youraccountname.api-us1.com', 'ac-newsletter' ); ?>" required>
								<p class="description" id="tagline-description">
									<?php esc_html_e( 'Enter ActiveCapmpaign API URL Here. Don\'t know What\'s this?', 'ac-newsletter' ); ?>
									<a href="https://i.imgur.com/wA2BEPl.png" target="_blank"> <?php esc_html_e( 'Click Here', 'ac-newsletter' ); ?> </a>
								</p>
							</td>
						</tr>
						<tr>
							<th>
								<label for="acns_api_key"> <?php esc_html_e( 'Enter Your ActiveCampaign API Key', 'ac-newsletter' ); ?> </label>
							</th>
							<td>
								<?php $fetch_api_key = ! empty( get_option( 'acns_api_key' ) ) ? base64_decode( get_option( 'acns_api_key' ) ) : ''; ?>
								<input name="acns_api_key" id="acns_api_key" type="password" class="regular-text" value="<?php echo esc_attr( $fetch_api_key ); ?>" required>
								<p class="description" id="tagline-description">
									<?php esc_html_e( 'Enter ActiveCapmpaign API Key Here. Don\'t know What\'s this?', 'ac-newsletter' ); ?>
									<a href="https://i.imgur.com/wA2BEPl.png" target="_blank"> <?php esc_html_e( 'Click Here', 'ac-newsletter' ); ?> </a>
								</p>
							</td>
						</tr>
						<tr>
							<th>
								<label for="acns_list_id"> <?php esc_html_e( 'Enter List ID', 'ac-newsletter' ); ?> </label>
							</th>
							<td>
								<?php $fetch_list_id = ! empty( get_option( 'acns_list_id' ) ) ? get_option( 'acns_list_id' ) : ''; ?>
								<input name="acns_list_id" id="acns_list_id" type="number" step="1" min="1" class="small-text" value="<?php echo esc_attr( $fetch_list_id ); ?>">
								<p class="description" id="tagline-description">
									<?php esc_html_e( 'Enter ActiveCapmpaign List ID Here, By Default Users will be added to "Contacts". Don\'t know What\'s this?', 'ac-newsletter' ); ?>
									<a href="https://i.imgur.com/XoD88yc.png" target="_blank"> <?php esc_html_e( 'Click Here', 'ac-newsletter' ); ?> </a>
								</p>
							</td>
						</tr>
						<tr>
							<th>
								<label for="acns_show_msg"> <?php esc_html_e( 'Enter Custom Text', 'ac-newsletter' ); ?> </label>
							</th>
							<td>
								<?php $fetch_show_msg = ! empty( get_option( 'acns_show_msg' ) ) ? get_option( 'acns_show_msg' ) : ''; ?>
								<input name="acns_show_msg" id="acns_show_msg" type="text" class="regular-text" value="<?php echo esc_attr( $fetch_show_msg ); ?>">
								<p class="description" id="tagline-description">
									<?php esc_html_e( 'Enter Text you Want to display on SignUp Page. Don\'t know What\'s this?', 'ac-newsletter' ); ?>
									<a href="https://prnt.sc/Cx7TLKoyIQnt" target="_blank"> <?php esc_html_e( 'Click Here', 'ac-newsletter' ); ?> </a>
								</p>
							</td>
						</tr>
						<input type="hidden" name="acns_save" value="1">
			<?php wp_nonce_field( 'acns_nonce_action', 'acns_nonce' ); ?>
					</tbody>
				</table>
				<?php do_action( 'acns_before_submit' ); ?> 
				<p class="submit">
					<input id="submitbtn" class="button button-primary" type="submit" />
				</p>
			</form>
			<div id="my-loader" class="ldld full em-1 d-inline-block dark ml-2"></div>
			<?php
		}

		/**
		 * Submit Form Data to Databse.
		 */
		public function acns_submit_data() {
			$save = filter_input( INPUT_POST, 'acns_save', FILTER_SANITIZE_NUMBER_INT );

			if ( empty( $save ) ) {
				return;
			}

			$api_url  = filter_input( INPUT_POST, 'acns_api_url', FILTER_SANITIZE_URL );
			$api_key  = base64_encode( filter_input( INPUT_POST, 'acns_api_key' ) );
			$list_id  = filter_input( INPUT_POST, 'acns_list_id', FILTER_SANITIZE_NUMBER_INT );
			$show_msg = filter_input( INPUT_POST, 'acns_show_msg' );

			// Nonce Verification.
			if ( ! isset( $_POST['acns_nonce'] )
				|| ! wp_verify_nonce( $_POST['acns_nonce'], 'acns_nonce_action' )
			) {
				echo esc_html__( 'Invalid Submission', 'ac-newsletter' );
				die;
			}

			// Add or Update data to database.
			update_option( 'acns_api_url', $api_url );
			update_option( 'acns_api_key', $api_key );
			update_option( 'acns_list_id', $list_id );
			update_option( 'acns_show_msg', $show_msg );

			do_action( 'acns_submit_data' );

			// Display Admin Notice.
			add_action( 'admin_notices', array( $this, 'acns_success_notice' ) );

		}

		/**
		 * Display Success Notice.
		 */
		public function acns_success_notice() {
			echo sprintf(
				'<div class="%1$s"><p>%2$s</p></div>',
				'notice notice-success is-dismissible',
				esc_html__( 'Settings Saved', 'ac-newsletter' )
			);

		}
	}
	new Activecampaign_Newsletter_Subscription_Admin();
}
