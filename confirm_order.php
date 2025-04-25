<?php
// confirm_order.php
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
require 'lib/order.php';

$userLogin = $_SESSION['user'];
$orderId = createOrder($userLogin);
?>
<main class="container">
    <h1>Тапсырыс ресімделді</h1>
    <p>Сіздің тапсырысыңыз № <?php echo $orderId; ?> сәтті рәсімделді!</p>
    <a href="/index.php">Негізгі бетке оралу</a>
</main>
<?php require 'blocks/footer.php'; ?>
