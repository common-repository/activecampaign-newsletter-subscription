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

/**
 * Add User to ActiveCampaign.
 *
 * @param string $email Get Email.
 * @param string $firstname Get FistName.
 * @param string $lastname Get LastName.
 * @return obj $response Return Response.
 */
function acns_add_user( $email, $firstname = '', $lastname = '' ) {

	if ( empty( $email ) ) {
		return;
	}
	// Retrive API URL and API Key From Database.
	$api_url = ! empty( get_option( 'acns_api_url' ) ) ? get_option( 'acns_api_url' ) : '';
	$api_key = ! empty( get_option( 'acns_api_key' ) ) ? base64_decode( get_option( 'acns_api_key' ) ) : '';

	if ( ! empty( $api_url ) && ! empty( $api_key ) ) {
		$url  = trailingslashit( $api_url . '/api/3/contact/sync' );
		$body = array(
			'contact' => array(
				'email'     => $email,
				'firstName' => $firstname,
				'lastName'  => $lastname,
			),
		);
		$body = wp_json_encode( $body );

		$response = wp_remote_post(
			esc_url_raw( $url ),
			array(
				'headers' => array(
					'Api-Token' => $api_key,
				),
				'body'    => $body,
			)
		);
		return $response;
	}
}

/**
 * Add ActiveCampaign Contacts to List.
 *
 * @param string $list_id Get List ID.
 * @param string $contact_id Get Contact ID.
 * @return obj   $response Return Response.
 */
function acns_add_user_list( $list_id, $contact_id ) {

	if ( ! empty( $list_id ) && ! empty( $contact_id ) ) {
		$api_url = ! empty( get_option( 'acns_api_url' ) ) ? get_option( 'acns_api_url' ) : '';
		$api_key = ! empty( get_option( 'acns_api_key' ) ) ? base64_decode( get_option( 'acns_api_key' ) ) : '';
		$url     = $api_url . '/api/3/contactLists';
		$body    = array(
			'contactList' => array(
				'list'    => $list_id,
				'contact' => $contact_id,
				'status'  => '1',
			),
		);
		$body    = wp_json_encode( $body );

		$response = wp_remote_post(
			esc_url_raw( $url ),
			array(
				'headers' => array(
					'Api-Token' => $api_key,
				),
				'body'    => $body,
			)
		);

		return $response;
	}
}
