<?php

/**
 * Class for importing products and categories via API
 */
class Import {
	
	function signInWithCredentials() {
		$url = 'https://devshop-376948.shoparena.pl/webapi/rest/auth';
		$login = 'webapi';
		$pass = 'Webapi4321;';
		$encoded_credentials = base64_encode($login.':'.$pass);

		$post_arr = array(
	        "client_id" => $login,
	        "client_secret" => $pass
	    );
	    $post_fields = json_encode($post_arr);

		$headers = array(
			'Authorization: Basic '.$encoded_credentials,
	        'Accept: application/json',
	        'Content-Type: application/json'
	    );

	    $decoded = $this->curlRequest($url, $headers, $post_fields);
	    
	    return $decoded->access_token ? $decoded->access_token : false;
	}

	function importCategories($token) {
		$url = 'https://devshop-376948.shoparena.pl/webapi/rest/categories?limit=20';

		$headers = array(
			'Authorization: Bearer '.$token,
	        'Accept: application/json',
	        'Content-Type: application/json'
	    );

	    $decoded = $this->curlRequest($url, $headers);

	    return $decoded->list ? $decoded->list : false;
	}

	function importProducts($token) {
		$url = 'https://devshop-376948.shoparena.pl/webapi/rest/products?limit='.rand(16, 20);

		$headers = array(
			'Authorization: Bearer '.$token,
	        'Accept: application/json',
	        'Content-Type: application/json'
	    );
		$time1 = hrtime(true);
	    $decoded = $this->curlRequest($url, $headers);
	    $time2 = hrtime(true);

	    $time = round(($time2 - $time1)/1e+6);

	    return $decoded->list ? array('products' => $decoded->list, 'time' => $time) : false;
	}

	private function curlRequest($url, $headers, $post_fields = false) {

		$curl = curl_init($url);
	    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
	    curl_setopt($curl, CURLINFO_HEADER_OUT, 1);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    if ($post_fields) {
	    	curl_setopt($curl, CURLOPT_POST, 1);
	    	curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);
	    }
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

	    $response = curl_exec($curl);
	    $status = curl_getinfo($curl);
	    curl_close($curl);

	    $decoded = json_decode($response);

	    return $decoded;
	}
}