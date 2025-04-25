<?php
// product_details.php
require 'blocks/header.php';
require 'lib/db.php';
require 'lib/reviews.php';

// Получаем ID товара из URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Некорректный идентификатор товара.");
}
$product_id = (int) $_GET['id'];

// Получаем данные товара
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    die("Товар не найден.");
}

// Обработка отправки отзыва
$reviewMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_review') {
    if (!isset($_SESSION['user'])) {
        die("Для оставления отзыва необходимо войти в систему.");
    }
    $user_id = $_SESSION['user_id'];
    $rating = (int) $_POST['rating'];
    $comment = trim($_POST['comment']);
    if ($rating < 1 || $rating > 5) {
        $reviewMessage = "Оценка должна быть от 1 до 5.";
    } elseif (strlen($comment) < 3) {
        $reviewMessage = "Комментарий слишком короткий.";
    } else {
        if (addReview($product_id, $user_id, $rating, $comment)) {
            $reviewMessage = "Пікір қосылды.";
        } else {
            $reviewMessage = "Пікір жазылу кезінде қате.";
        }
    }
}

// Получаем список отзывов для данного товара
$reviews = getReviewsByProduct($product_id);
?>

<main class="container">
    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
    <?php if ($product['photo']): ?>
        <img src="<?php echo $product['photo']; ?>" alt="Тауардың суреті" style="max-width:300px;">
    <?php endif; ?>
    <p>Бағасы: <?php echo number_format($product['price'], 2); ?> руб.</p>
    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
    
    <!-- Форма для добавления товара в корзину и кнопка -->
    <form action="/cart.php" method="post">
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
        <label for="quantity">Саны:</label>
        <input type="number" name="quantity" id="quantity" value="1" min="1">
        <button type="submit" name="action" value="add">Себетке қосу</button>
    </form>
    
    <!-- Раздел отзывов -->
    <section>
        <h2>Пікірлер</h2>
        <?php if ($reviewMessage): ?>
            <p><?php echo htmlspecialchars($reviewMessage); ?></p>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['user'])): ?>
            <form action="product_details.php?id=<?php echo $product_id; ?>" method="post">
                <input type="hidden" name="action" value="add_review">
                <label for="rating">Рейтинг (1-5):</label>
                <input type="number" name="rating" id="rating" min="1" max="5" required>
                <br>
                <label for="comment">пікір:</label>
                <textarea name="comment" id="comment" required></textarea>
                <br>
                <button type="submit">Пікір жіберу</button>
            </form>
        <?php else: ?>
            <p>Пікір жазу үшін<a href="/login.php">кіріңіз</a></p>
        <?php endif; ?>
        
        <?php if (count($reviews) > 0): ?>
            <ul>
                <?php foreach ($reviews as $rev): ?>
                    <li>
                        <strong><?php echo htmlspecialchars($rev['username']); ?></strong> (<?php echo $rev['rating']; ?>/5):
                        <p><?php echo nl2br(htmlspecialchars($rev['comment'])); ?></p>
                        <small><?php echo $rev['created_at']; ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>пікір жоқ</p>
        <?php endif; ?>
    </section>
</main>

<?php require 'blocks/footer.php'; ?>
