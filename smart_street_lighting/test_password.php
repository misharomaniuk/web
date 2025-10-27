<?php
// test_password.php - ВИДАЛИТИ ПІСЛЯ ВИКОНАННЯ!
$new_password = 'admin123';
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);

echo "<h1>Пароль для тесту: $new_password</h1>";
echo "<h2>Сгенерований Хеш:</h2>";
echo "<textarea rows='5' cols='80'>{$new_hash}</textarea><br><br>";

if (password_verify($new_password, $new_hash)) {
    echo "<h3 style='color: green;'>✅ Перевірка успішна! Хеш працює.</h3>";
} else {
    echo "<h3 style='color: red;'>❌ Перевірка не пройшла.</h3>";
}

// ПІСЛЯ ЦЬОГО КРОКУ, ВИКОРИСТАЙТЕ ЗГЕНЕРОВАНИЙ ХЕШ (текст з textarea)
// ТА ВСТАВТЕ ЙОГО БЕЗПОСЕРЕДНЬО У БАЗУ ДАНИХ.
?>