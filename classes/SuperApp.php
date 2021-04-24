<?php

require_once('./classes/Import.php');
require_once('./classes/Products.php');


/**
 * Main application logic
 */
class SuperApp {

	private $products;

	function __construct() {
		$this->products = new Products();
	}

	function index() {

		if ($this->products->checkIfProductsExist() == false) {
			$importer = new Import();

			$token = $importer->signInWithCredentials();

			if (!$token) return 'Bad request - no token provided';

			$categories = $importer->importCategories($token);

			if (!$categories) return 'Error - no categories imported';

			$this->products->saveCategories($categories);

			$products_data = $importer->importProducts($token);
			$products = $products_data['products'];
			$import_time = $products_data['time'];

			if (!$products) return 'Error - no products imported';

			$this->products->saveImportInfo($products, $import_time);
			$this->products->saveProducts($products);
		}
		
		$this->products->displayProducts();
	}
}
