<?php
// index.php
require 'blocks/header.php';
require 'lib/db.php';

$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<main class="container">
    <h1>Тауарлар тізімі</h1>
    <?php if (count($products) > 0): ?>
        <div class="products">
            <?php foreach ($products as $product): ?>
                <!-- Оборачиваем карточку в ссылку -->
                <a href="/product_details.php?id=<?php echo urlencode($product['id']); ?>" style="text-decoration: none;">
                    <div class="card">
                        <?php 
                            // Берем имя файла из базы (поле photo)
                            $imageFile = isset($product['photo']) ? $product['photo'] : '';
                            // Если сохранен абсолютный путь (начинается с "/"), используем его, иначе добавляем префикс "/uploads/"
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
                            <div class="card_rating">★★★★★</div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="margin-top: 2rem;">Ештеңе табылған жоқ.</p>
    <?php endif; ?>
</main>
<?php require 'blocks/footer.php'; ?>
