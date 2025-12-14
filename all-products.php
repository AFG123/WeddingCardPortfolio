<?php
require_once 'admin/config.php';
require_once 'admin/functions.php';

// Fetch categories for nav/filter
$categories = getAllCategories();

$categorySlug = isset($_GET['category']) ? trim($_GET['category']) : '';
$isCustom = isset($_GET['custom']) ? (bool)$_GET['custom'] : false;
$isGifting = isset($_GET['gifting']) ? (bool)$_GET['gifting'] : false;

// Decide query based on filters
if ($isCustom) {
    // Show products marked as custom invitations
    $stmt = $pdo->query(
        "SELECT d.*, c.name AS category_name, c.slug
        FROM designs d
        LEFT JOIN categories c ON d.category_id = c.id
        WHERE d.is_custom = 1
        ORDER BY d.id DESC"
    );
    $products = $stmt->fetchAll();
} elseif ($isGifting) {
    // Show products marked as gifting accessories
    $stmt = $pdo->query(
        "SELECT d.*, c.name AS category_name, c.slug
        FROM designs d
        LEFT JOIN categories c ON d.category_id = c.id
        WHERE d.is_gifting = 1
        ORDER BY d.id DESC"
    );
    $products = $stmt->fetchAll();
} elseif ($categorySlug) {
    // Filter by category slug
    $stmt = $pdo->prepare(
        "SELECT d.*, c.name AS category_name, c.slug
         FROM designs d
         LEFT JOIN categories c ON d.category_id = c.id
         WHERE c.slug = ?
         ORDER BY d.id DESC"
    );
    $stmt->execute([$categorySlug]);
    $products = $stmt->fetchAll();
} else {
    // Fetch all products
    $stmt = $pdo->query(
        "SELECT d.*, c.name AS category_name, c.slug
         FROM designs d
         LEFT JOIN categories c ON d.category_id = c.id
         ORDER BY d.id DESC"
    );
    $products = $stmt->fetchAll();
}


// Change this to your full site URL (without trailing slash)
$siteURL = "https://yourwebsite.com";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Wedding Cards | <?php echo $categorySlug ? htmlspecialchars(ucfirst(str_replace('-', ' ', $categorySlug))) : 'All Products'; ?></title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
<style>
    body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        background-color: #fff7fc;
        color: #4e4351;
    }
    a {
        color: #9a789b;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    a:hover, a.active {
        color: #f9c5d1;
        text-decoration: underline;
    }
    .back-home-btn {
        display: inline-block;
        background-color: #f9c5d1;
        color: #4e4351;
        padding: 8px 16px;
        font-weight: 500;
        font-size: 0.95rem;
        border-radius: 6px;
        text-decoration: none;
        margin-bottom: 1rem;
        transition: background-color 0.2s ease, transform 0.2s ease;
    }
    .back-home-btn:hover {
        background-color: #010006;
        color: white;
        transform: translateY(-2px);
    }
    h1 {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .container {
        max-width: 1200px;
        margin: auto;
        padding: 1.5rem 1rem 3rem;
    }
    .category-links {
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 0.75rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        margin-top: 1.5rem;
        text-align: center;
    }
    .category-links a {
        display: inline-block;
        margin: 0 12px 8px;
        font-weight: 500;
        font-size: 1rem;
        padding: 6px 14px;
        border-radius: 20px;
        border: 2px solid transparent;
    }
    .category-links a.active {
        background-color: #f9c5d1;
        border-color: #f9c5d1;
        color: #4e4351;
        font-weight: 700;
    }
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
        gap: 2rem;
        margin-top: 2em;
    }
    .product-card {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 6px 16px rgb(199 191 199 / 20%);
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    .product-card:hover {
        transform: translateY(-6px);
    }
    .product-image {
        width: 100%;
        height: 220px;
        overflow: hidden;
    }
    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.3s ease;
    }
    .product-card:hover img {
        transform: scale(1.05);
    }
    .product-info {
        padding: 1rem 1.5rem 1.5rem;
        text-align: center;
    }
    .product-info h3 {
        font-size: 1.25rem;
        margin-bottom: 0.4rem;
    }
    .product-info p.category {
        color: #9a789b;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .product-info span.price {
        font-weight: 700;
        font-size: 1.15rem;
        color: #4e4351;
        display: block;
        margin-bottom: 0.8rem;
    }
    /* WhatsApp Share Button */
    .share-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background-color: #25D366;
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }
    .share-btn:hover {
        background-color: #1ebe5d;
    }
    .share-icon-img {
        width: 16px;
        height: 16px;
    }
    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        }
        .product-image {
            height: 180px;
        }
    }
</style>
</head>
<body>
<div class="container">
    <a href="index.php" class="back-home-btn">← Back to Home</a>
    <h1>Our Wedding Card Designs</h1>

    <!-- Categories Navigation -->
    <nav class="category-links">
        <a href="all-products.php" class="<?php echo $categorySlug === '' ? 'active' : ''; ?>">All Products</a>
        <?php foreach ($categories as $cat): ?>
            <a href="all-products.php?category=<?php echo urlencode($cat['slug']); ?>"
               class="<?php echo ($categorySlug === $cat['slug']) ? 'active' : ''; ?>">
                <?php echo htmlspecialchars($cat['name']); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <!-- Products Grid -->
    <section class="products-grid">
        <?php if (empty($products)): ?>
            <p style="grid-column: 1/-1; text-align: center; color: #9a789b; font-size: 1.1rem;">
                No products found in this category.
            </p>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <article class="product-card">
                    <a href="product.php?id=<?php echo $product['id']; ?>">
                        <div class="product-image">
                            <?php if (!empty($product['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" loading="lazy" />
                            <?php else: ?>
                                <div style="height: 220px; background: <?php echo $product['gradient'] ?? '#f9c5d1'; ?>; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 600;">
                                    No Image Available
                                </div>
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                        <p class="category"><?php echo htmlspecialchars($product['category_name']); ?></p>
                        <span class="price">₹<?php echo number_format($product['price']); ?></span>

                        <?php
                        $shareMessage = "Check out this wedding card: " . $product['title'] . " - " . $siteURL . "/product.php?id=" . $product['id'];
                        $encodedShare = urlencode($shareMessage);
                        ?>
                        <a href="https://wa.me/?text=<?php echo $encodedShare; ?>" target="_blank" class="share-btn">
                            <img src="uploads/whatsappIcon.png" alt="Share" class="share-icon-img"> Share
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</div>
</body>
</html>
