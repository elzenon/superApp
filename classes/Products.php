<?php

require_once('./conf/config.php');

/**
 * Class for storing products and categories in database
 */
class Products {

	private $db;

	function __construct() {

		try {
			$this->db = new mysqli(DB_SERVER, DB_USER, DB_PASSWORD, DB_NAME);
		} catch (Exception $ex) { 
			die($ex->getMessage()); 
		}
	}


	function saveCategories($categories) {

		if (is_array($categories)) {
			foreach ($categories as $category) {
				$name = $category->translations->pl_PL->name;
				$desc = $category->translations->pl_PL->description;
				$sql = "INSERT INTO categories (id, name, description)
					VALUES ('$category->category_id', '$name', '$desc')";

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

	function saveImportInfo($products, $import_time) {

		$product_names = array();
		date_default_timezone_set("Europe/Warsaw");
		$import_date = date('Y-m-d H:i:s');

		if (is_array($products)) {
			$products_count = count($products);
			foreach ($products as $product) {
				$product_names[] = $product->translations->pl_PL->name;
			}
			$product_names = implode(', ', $product_names);

			$sql = "INSERT INTO info (imported_count, import_date, import_time, product_names)
				VALUES ('$products_count', '$import_date', '$import_time', '$product_names')";

			if(!$this->db->query($sql))
				echo $this->db->error;
		}
	}

	function displayProducts() {

		if (isset($_POST['load_products'])) {
			$products = $this->loadProducts();
		}
		require('./templates/index.php');
	}

	function checkIfProductsExist() {
		$sql = "SELECT * FROM products";

		$results = $this->db->query($sql);
		if(!$results)
			echo $this->db->error;


		if ($results->num_rows > 0) {
			return true;
		}
		return false;
	}

	private function loadProducts() {
		$sql = "SELECT products.*, categories.name AS cat_name FROM products INNER JOIN categories ON products.category_id = categories.id";

		$results = $this->db->query($sql);
		if(!$results)
			echo $this->db->error;

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