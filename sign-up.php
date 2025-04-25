<?php
// sign-up.php
require 'blocks/header.php';
?>
<main class="container">
    <h1>Регистрация</h1>
    <form action="lib/reg.php" method="post">
        <label for="login">Логин:</label>
        <input type="text" name="login" id="login" required>
        
        <label for="username">Имя пользователя:</label>
        <input type="text" name="username" id="username" required>
        
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        
        <label for="password">Пароль:</label>
        <input type="password" name="password" id="password" required>
        
        <button type="submit">Зарегистрироваться</button>
    </form>
</main>
<?php require 'blocks/footer.php'; ?>
