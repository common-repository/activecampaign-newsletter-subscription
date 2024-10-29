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
if ( ! class_exists( 'Activecampaign_Newsletter_Subscription' ) ) {

	/**
	 * Calls for admin methods.
	 */
	class Activecampaign_Newsletter_Subscription {

		/**
		 * Constructor for class.
		 */
		public function __construct() {

			// Add Input Field On SignUp page.
			add_action( 'register_form', array( $this, 'acns_add_register_field' ) );

			// Store Checkbox Input data in database.
			add_action( 'user_register', array( $this, 'acns_add_user_input' ) );

		}

		/**
		 * Add Custom Input Field On Registration Form.
		 */
		public function acns_add_register_field() {

			$api_url = ! empty( get_option( 'acns_api_url' ) ) ? get_option( 'acns_api_url' ) : '';
			$api_key = ! empty( get_option( 'acns_api_key' ) ) ? get_option( 'acns_api_key' ) : '';

			if ( ! empty( $api_url ) && ! empty( $api_key ) ) {
				?>
			<p>
				<label for="firstname">
					<input type="checkbox" name="newsletter" id="newsletter" value="true" />
					<?php
						$fetch_show_msg = ! empty( get_option( 'acns_show_msg' ) ) ? get_option( 'acns_show_msg' ) : esc_html__( 'Subscribe For Newsletter', 'ac-newsletter' );
						echo esc_html( $fetch_show_msg );
					?>
				</label>
			</p>
				<?php
			}
		}

		/**
		 * Store Checkbox Value To Database.
		 *
		 * @param int $user_id Get User ID.
		 */
		public function acns_add_user_input( $user_id ) {

			// Get Checkbox Value which we created with acns_add_register_field().
			$check = filter_input( INPUT_POST, 'newsletter' );

			if ( ! empty( $check ) ) {

				// Get value Of Email field from Sign up Page.
				$email = filter_input( INPUT_POST, 'user_email', FILTER_SANITIZE_EMAIL );

				if ( ! empty( $email ) ) {

					// Retrive API URL and API Key From Database.
					$api_url = ! empty( get_option( 'acns_api_url' ) ) ? get_option( 'acns_api_url' ) : '';
					$api_key = ! empty( get_option( 'acns_api_key' ) ) ? base64_decode( get_option( 'acns_api_key' ) ) : '';

					if ( ! empty( $api_url ) && ! empty( $api_key ) ) {

						$response = acns_add_user( $email );

						if ( is_wp_error( $response ) ) {
							// Store Error Log to User Meta.
							$error_message = ! empty( $response->get_error_message() ) ? $response->get_error_message() : 'Unknown Error';
							update_user_meta( $user_id, 'acns_error_log', $error_message );
							return;

						}

						$contact_body = wp_remote_retrieve_body( $response );
						$contact_body = json_decode( $contact_body );
						$contact_id   = isset( $contact_body->contact->id ) ? $contact_body->contact->id : '';
						$list_id      = ! empty( get_option( 'acns_list_id' ) ) ? get_option( 'acns_list_id' ) : '';

						if ( ! empty( $contact_id ) && ! empty( $list_id ) ) {

							$response = acns_add_user_list( $list_id, $contact_id );

							if ( is_wp_error( $response ) ) {

								// Store Error Log to User Meta.
								$error_message = ! empty( $response->get_error_message() ) ? $response->get_error_message() : 'Unknown Error';
								update_user_meta( $user_id, 'acns_error_log', $error_message );
								return;

							}
						}

						update_user_meta( $user_id, 'acns_check_newsletter', $check );

					}
				}
			}
		}
	}
	new Activecampaign_Newsletter_Subscription();
}
