<?php
// cart.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require 'blocks/header.php';
require 'lib/db.php';
require 'lib/cart.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $productId = (int) $_POST['product_id'];
    $quantity  = (int) $_POST['quantity'];
    addToCart($productId, $quantity);
    header("Location: /cart.php");
    exit;
}

$cartItems = getCartItems();
?>
<main class="container">
    <h1>Себет</h1>
    <?php if (empty($cartItems)): ?>
        <p>Себет бос.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Тауар</th>
                <th>Количество</th>
                <th>Бағасы</th>
                <th>Сумма</th>
            </tr>
            <?php
            $total = 0;
            foreach ($cartItems as $productId => $quantity):
                $stmt = $pdo->prepare("SELECT name, price FROM products WHERE id = ?");
                $stmt->execute([$productId]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($product):
                    $sum = $product['price'] * $quantity;
                    $total += $sum;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo $quantity; ?></td>
                <td><?php echo number_format($product['price'], 2); ?></td>
                <td><?php echo number_format($sum, 2); ?></td>
            </tr>
            <?php
                endif;
            endforeach;
            ?>
            <tr>
                <td colspan="3">Барлығы:</td>
                <td><?php echo number_format($total, 2); ?></td>
            </tr>
        </table>
        <a href="/checkout.php">Тапсырыс беру</a>
    <?php endif; ?>
</main>
<?php require 'blocks/footer.php'; ?>
