<?php
// edit_product.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

require 'blocks/header.php';
require 'lib/db.php';
require_once 'lib/categories.php';

$message = '';

// Получаем id товара из GET-параметров
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Дұрыс емес тауар идентификаторы.");
}
$product_id = (int) $_GET['id'];

// Получаем товар из базы
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    die("Тауар табылмады.");
}
// Проверяем, что текущий пользователь владеет товаром
if ($product['user_id'] != $user_id) {
    die("Сізде бұл тауарды өңдеуге құқығыңыз жоқ.");
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    
    if (strlen($name) < 2) {
        $message = "тауар аты ең аз дегенде бір сөз.";
    } elseif ($price <= 0) {
        $message = "Тауардың бағасы нөлден жоғары болуы керек.";
    } else {
        // Обработка загрузки нового фото (если выбрано)
        $photoPath = $product['photo'];
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileTmpPath = $_FILES['photo']['tmp_name'];
            $fileName = basename($_FILES['photo']['name']);
            $targetPath = $uploadDir . time() . '_' . $fileName;
            if (move_uploaded_file($fileTmpPath, $targetPath)) {
                $photoPath = '/uploads/' . time() . '_' . $fileName;
            } else {
                $message = "Суретті жүктеу қатесі.";
            }
        }
        if ($message === '') {
            $stmt = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, description = ?, price = ?, photo = ? WHERE id = ?");
            if ($stmt->execute([$category_id, $name, $description, $price, $photoPath, $product_id])) {
                $message = "Тауар сәтті жаңартылды.";
                // Обновляем данные товара для формы:
                $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                $stmt->execute([$product_id]);
                $product = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $message = "Элементті жаңарту кезіндегі қате.";
            }
        }
    }
}

$categories = getAllCategories();
?>

<main class="container">
    <h1>Редактировать товар</h1>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form action="edit_product.php?id=<?php echo $product_id; ?>" method="post" enctype="multipart/form-data">
        <label for="name">Название товара:</label>
        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        
        <label for="description">Описание:</label>
        <textarea name="description" id="description"><?php echo htmlspecialchars($product['description']); ?></textarea>
        
        <label for="price">Цена:</label>
        <input type="number" name="price" id="price" step="0.01" value="<?php echo $product['price']; ?>" required>
        
        <label for="category_id">Категория:</label>
        <select name="category_id" id="category_id">
            <option value="">-- Выберите категорию --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>" <?php if($cat['id'] == $product['category_id']) echo "selected"; ?>>
                    <?php echo htmlspecialchars($cat['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <label for="photo">Фото товара (если хотите заменить):</label>
        <input type="file" name="photo" id="photo" accept="image/*">
        <?php if ($product['photo']): ?>
            <img src="<?php echo $product['photo']; ?>" alt="Фото товара" style="max-width:150px;">
        <?php endif; ?>
        <button type="submit">Обновить товар</button>
    </form>
</main>

<?php require 'blocks/footer.php'; ?>
