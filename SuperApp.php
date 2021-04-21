<?php

class SuperApp {

	private $db;

	function __construct() {
		$server = 'localhost';
		$dbName = 'super_app';
		$dbUser = '';
		$dbPassword = '';

		$this->db = new mysqli($server, $dbUser, $dbPassword, $dbName);
		if ($this->db->connect_error) {
			die("Connection failed: " . $this->db->connect_error);
		}
	}

	function signInWithCredentials() {
		$url = 'https://devshop-376948.shoparena.pl/webapi/rest/auth';
		$login = 'webapi';
		$pass = 'Webapi4321;';
		$encoded_credentials = base64_encode($login.':'.$pass);

		$request = array(
	        "client_id" => $login,
	        "client_secret" => $pass
	    );
	    $request_json = json_encode($request);

		$headers = array(
			'Authorization: Basic '.$encoded_credentials,
	        'Accept: application/json',
	        'Content-Type: application/json'
	    );

	    $curl = curl_init($url);
	    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
	    curl_setopt($curl, CURLINFO_HEADER_OUT, 1);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    curl_setopt($curl, CURLOPT_POST, 1);
	    curl_setopt($curl, CURLOPT_POSTFIELDS, $request_json);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

	    $response = curl_exec($curl);
	    $status = curl_getinfo($curl);
	    curl_close($curl);

	    $decoded = json_decode($response);
	    
	    return $decoded->access_token ? $decoded->access_token : false;
	}

	function importCategories($token) {
		$url = 'https://devshop-376948.shoparena.pl/webapi/rest/categories?limit=34';

		if (!$token) return 'Bad request - no token provided';

		$headers = array(
			'Authorization: Bearer '.$token,
	        'Accept: application/json',
	        'Content-Type: application/json'
	    );

	    $curl = curl_init($url);
	    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
	    curl_setopt($curl, CURLINFO_HEADER_OUT, 1);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

	    $response = curl_exec($curl);
	    $status = curl_getinfo($curl);
	    curl_close($curl);

	    $decoded = json_decode($response);

	    return $decoded->list ? $decoded->list : false;
	}

	function importProducts($token) {
		$url = 'https://devshop-376948.shoparena.pl/webapi/rest/products?limit='.rand(16, 20);

		if (!$token) return 'Bad request - no token provided';

		$headers = array(
			'Authorization: Bearer '.$token,
	        'Accept: application/json',
	        'Content-Type: application/json'
	    );

	    $curl = curl_init($url);
	    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	    curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
	    curl_setopt($curl, CURLINFO_HEADER_OUT, 1);
	    curl_setopt($curl, CURLOPT_HEADER, false);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

	    $response = curl_exec($curl);
	    $status = curl_getinfo($curl);
	    curl_close($curl);

	    $decoded = json_decode($response);

	    return $decoded->list ? $decoded->list : false;
	}

	function saveCategories($categories) {

		if (is_array($categories)) {
			foreach ($categories as $category) {
				$name = $category->translations->pl_PL->name;
				$desc = $category->translations->pl_PL->description;
				$sql = "INSERT INTO categories (name, description)
					VALUES ('$name', '$desc')";

				if(!$this->db->query($sql))
					echo $this->db->error;
			}
		}
	}

	function saveProducts($products) {

		if (is_array($products)) {
			foreach ($products as $product) {
				$name = $product->translations->pl_PL->name;
				$desc = $product->translations->pl_PL->description;
				$price = $product->stock->price;
				$sql = "INSERT INTO products (name, category_id, description, price, add_date)
					VALUES ('$name', '$product->category_id', '$desc', '$price', '$product->add_date')";

				if(!$this->db->query($sql))
					echo $this->db->error;
			}
		}
	}

	function displayProducts() {

		if (isset($_POST['load_products'])) {
			$products = $this->loadProducts();
		}
		require('./templates/index.php');
	}

	private function loadProducts() {
		$sql = "SELECT products.*, categories.name AS cat_name FROM products INNER JOIN categories ON products.category_id = categories.id";
		$results = $this->db->query($sql);

		if ($results->num_rows > 0) {
			while($row = $results->fetch_assoc()) {
				$products[] = $row;
			}
		}
		else {
			$products = false;
		}

		return $products;
	}
}

$app = new SuperApp();

$token = $app->signInWithCredentials();

$categories = $app->importCategories($token);

$app->saveCategories($categories);

$products = $app->importProducts($token);

$app->saveProducts($products);

$app->displayProducts();