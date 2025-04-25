<?php
// login.php
require 'blocks/header.php';
?>
<main class="container">
    <h1>Кіру</h1>
    <form action="lib/auth.php" method="post">
        <label for="login">Логин:</label>
        <input type="text" name="login" id="login" required>
        
        <label for="password">Пароль:</label>
        <input type="password" name="password" id="password" required>
        
        <button type="submit">Кіру</button>
    </form>
</main>
<?php require 'blocks/footer.php'; ?>
