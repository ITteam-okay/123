<?php
// user.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit;
}
require 'blocks/header.php';
require_once 'lib/db.php';

// Получаем идентификатор пользователя из сессии
$user_id = $_SESSION['user_id'];
?>

<main class="container">
    <h1>Қош келдіңіз, <?php echo htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8'); ?>!</h1>
    </br>
    <p><a href="/add_product.php" class="btn">Жаңа тауар қосыңыз</a></p>
    </br>
    
    <h2>Менің тауарларым</h2>
    <?php
    // Запрос для получения товаров, добавленных данным пользователем
    $stmt = $pdo->prepare("SELECT * FROM products WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $myProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <?php if (count($myProducts) > 0): ?>
        <div class="products">
            <?php foreach ($myProducts as $product): ?>
                <!-- Оборачиваем карточку в ссылку на страницу редактирования товара -->
                <a href="/edit_product.php?id=<?php echo urlencode($product['id']); ?>" style="text-decoration: none;">
                    <div class="card">
                        <?php 
                        // Получаем имя файла изображения из базы данных
                        $imageFile = isset($product['photo']) ? $product['photo'] : '';
                        // Если значение начинается со слеша – предполагаем, что это абсолютный путь
                        if (!empty($imageFile) && $imageFile[0] === "/") {
                            $imgPath = __DIR__ . $imageFile;
                            $imgSrc = $imageFile;
                        } else {
                            $imgPath = __DIR__ . '/uploads/' . $imageFile;
                            $imgSrc = '/uploads/' . $imageFile;
                        }
                        ?>
                        
                        <?php if (!empty($imageFile) && file_exists($imgPath)): ?>
                            <img 
                                src="<?php echo htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8'); ?>" 
                                alt="<?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?>" 
                                style="width: 100%; height: 200px; object-fit: cover; border-bottom: 1px solid #eee; margin-bottom: 1rem;"
                            />
                        <?php else: ?>
                            <div style="width: 100%; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #aaa; margin-bottom: 1rem; font-size: 1.4rem;">
                                Сурет жоқ
                            </div>
                        <?php endif; ?>

                        <div class="card_body">
                            <div class="card_title"><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="card_price"><?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?> ₸</div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Сізде әлі тауарлар жоқ.</p>
    <?php endif; ?>
</main>

<?php require 'blocks/footer.php'; ?>
