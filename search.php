<?php
// Показываем ошибки (для отладки)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Сессия
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Подключения
require 'blocks/header.php';
require_once 'lib/db.php';
require_once 'lib/categories.php';

// Обработка GET-параметров
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';

// Построение SQL-запроса
$query = "SELECT * FROM products WHERE 1";
$params = [];

if ($categoryFilter) {
    $query .= " AND category_id = ?";
    $params[] = $categoryFilter;
}

if ($searchQuery !== '') {
    $query .= " AND name LIKE ?";
    $params[] = "%" . $searchQuery . "%";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получение всех категорий
$categories = getAllCategories();
?>

<main class="container">
    <h1 style="margin-top: 2rem;">Поиск товаров</h1>

    <!-- Форма поиска -->
    <form method="get" action="search.php" class="search_form">
        <input type="text" name="q" placeholder="Поиск..." value="<?php echo htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8'); ?>">
        <select name="category">
            <option value="0">Все категории</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php if ($cat['id'] == $categoryFilter) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Найти</button>
    </form>

    <!-- Результаты поиска -->
    <?php if (count($products) > 0): ?>
        <div class="products">
            <?php foreach ($products as $product): ?>
                <!-- Оборачиваем карточку в ссылку на детали товара -->
                <a href="/product_details.php?id=<?php echo urlencode($product['id']); ?>" style="text-decoration: none;">
                    <div class="card">
                        <?php 
                            // Получаем значение из поля photo
                            $imageFile = isset($product['photo']) ? $product['photo'] : '';
                            // Если значение начинается с "/", используем его как абсолютный путь
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
                                Нет изображения
                            </div>
                        <?php endif; ?>

                        <div class="card_body">
                            <div class="card_title"><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="card_price"><?php echo htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8'); ?> ₸</div>
                            <div class="card_rating">★★★★★</div>
                            
                            <?php 
                            // Если текущий пользователь владеет товаром, показываем кнопку редактирования
                            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $product['user_id']): 
                            ?>
                                <a href="/edit_product.php?id=<?php echo urlencode($product['id']); ?>" 
                                   class="btn"
                                   style="display: inline-block; margin-top: 1rem; font-size: 1.4rem; text-align: center;">
                                   Редактировать товар
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="margin-top: 2rem;">Ничего не найдено.</p>
    <?php endif; ?>
</main>

<?php require 'blocks/footer.php'; ?>
