<?php
require 'admin/config.php'; // this connects to MySQL and has file paths

// 1. Load products from products.json
$products = json_decode(file_get_contents(PRODUCTS_FILE), true);

foreach ($products as $product) {
    $title = $conn->real_escape_string($product['title']);
    $price = floatval($product['price']);
    $image = $conn->real_escape_string($product['image'] ?? '');
    $type = $conn->real_escape_string($product['type'] ?? '');
    $description = $conn->real_escape_string($product['description'] ?? '');

    // Get category ID from slug
    $category_slug = $product['category'] ?? '';
    $category_id = null;

    if ($category_slug) {
        $result = $conn->query("SELECT category_id FROM categories WHERE slug = '$category_slug'");
        if ($row = $result->fetch_assoc()) {
            $category_id = $row['id'];
        }
    }

    // Insert into MySQL
  $sql = "INSERT INTO products (title, price, image, category_id, type, description)
            VALUES ('$title', $price, '$image', " . ($category_id ?? 'NULL') . ", '$type', '$description')";

    if ($conn->query($sql)) {
        echo "Inserted: $title<br>";
    } else {
        echo "Error: " . $conn->error . "<br>";
    }
}
?>
