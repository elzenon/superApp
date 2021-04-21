<html lang="pl">
<head>
	<meta charset="utf-8">

	<title>Produkty</title>
	<meta name="description" content="zadanie rekrutacyjne">
	<meta name="author" content="Paweł Strózik">

	<link rel="stylesheet" href="./css/style.css">
</head>

<body>
 	<div>
		<h1>Produkty</h1>
		<form action="" method="post">
			<input type="submit" name="load_products" value="Wyświetl produkty">	
		</form>
	</div>

	<?php if ($products) : ?>

	<div class="products">
		<?php foreach ($products as $product) : ?>
			<div class="product-card">
				<div>
					<h2><?php echo $product['name']; ?></h2>
					<p><?php echo $product['description']; ?></p>
					<p>Cena: <?php echo $product['price']; ?></p>
					<p>Kategoria: <?php echo $product['cat_name']; ?></p>
					<p>ID: <?php echo $product['id']; ?></p>
					<p>Data dodania: <?php echo $product['add_date']; ?></p>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<?php endif; ?>
</body>
</html>






