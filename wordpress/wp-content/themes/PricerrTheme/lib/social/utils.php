<?php


function social_connect_get_user_by_meta_pr( $meta_key, $meta_value ) {
	global $wpdb;

	$sql = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '%s' AND meta_value = '%s'";
	return $wpdb->get_var( $wpdb->prepare( $sql, $meta_key, $meta_value ) );
}

function social_connect_generate_signature_pr( $data ) {
	return hash( 'SHA256', AUTH_KEY . $data );
}

function social_connect_verify_signature_pr( $data, $signature, $redirect_to ) {
	$generated_signature = social_connect_generate_signature_pr( $data );

	if( $generated_signature != $signature ) {
		wp_safe_redirect( $redirect_to );
		exit();
	}
}

function sc_curl_get_contents_pr( $url ) {
	$curl = curl_init();
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $curl, CURLOPT_URL, $url );
	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );

	$html = curl_exec( $curl );

	curl_close( $curl );

	return $html;
}
