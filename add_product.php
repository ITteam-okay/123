<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
// add_product.php
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    
    if (strlen($name) < 2) {
        $message = "тауар аты ең аз дегенде бір сөз.";
    } elseif ($price <= 0) {
        $message = "Тауар бағасы нөлден үлкен болу қажет.";
    } else {
        $photoPath = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileExtension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $newFileName = time() . '_' . uniqid() . '.' . $fileExtension;
            $targetPath = $uploadDir . $newFileName;
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetPath)) {
                $photoPath = '/uploads/' . $newFileName;
            } else {
                $message = "Суретті жүктеу мүмкін болмады.";
            }
        }
        
        if ($message === '') {
            $stmt = $pdo->prepare("INSERT INTO products (user_id, category_id, name, description, price, photo) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$user_id, $category_id, $name, $description, $price, $photoPath])) {
                $message = "Тауар сәтті қосылды.";
            } else {
                $message = "Тауарды жүктеу мүмкін болмады.";
            }
        }
    }
}

$categories = getAllCategories();
?>

<main class="container">
    <h1>Жаңа тауарды жүктеу</h1>
    <?php if ($message): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form action="add_product.php" method="post" enctype="multipart/form-data">
        <label for="name">Тауар аты:</label>
        <input type="text" name="name" id="name" required>
        
        <label for="description">Сипаттама:</label>
        <textarea name="description" id="description"></textarea>
        
        <label for="price">Бағасы:</label>
        <input type="number" name="price" id="price" step="0.01" required>
        
        <label for="category_id">Категория:</label>
        <select name="category_id" id="category_id">
            <option value="">-- Категория таңдау --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
            <?php endforeach; ?>
        </select>
        
        <label for="photo">Тауар фотосы:</label>
        <input type="file" name="photo" id="photo" accept="image/*">
        
        <button type="submit">Тауарды жүктеу</button>
    </form>
</main>
<?php require 'blocks/footer.php'; ?>
