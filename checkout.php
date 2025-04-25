<?php
// checkout.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit;
}
require 'blocks/header.php';
require 'lib/db.php';
require 'lib/cart.php';

$cartItems = getCartItems();
if (empty($cartItems)) {
    die("Сіздің себетіңіз бос.");
}

$total = 0;
foreach ($cartItems as $productId => $quantity) {
    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($product) {
        $total += $product['price'] * $quantity;
    }
}
?>
<main class="container">
    <h1>Тапсырысты рәсімдеу</h1>
    <p>Тапсырыстың жалпы сомасы: <?php echo number_format($total, 2); ?> руб.</p>
    <form action="/confirm_order.php" method="post">
        <button type="submit">Тапсырысты растау</button>
    </form>
</main>
<?php require 'blocks/footer.php'; ?>
