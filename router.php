<?php
// router.php - обработка маршрутов

$request = $_SERVER['REQUEST_URI'];
$request = parse_url($request, PHP_URL_PATH);

// Карта маршрутов
$routes = [
    '/test' => 'test_router.php',
    '/cabinet' => 'cabinet.php',
    '/success' => 'success.php',
    '/cancel' => 'cancel.php',
    '/agents' => 'agents.php',
    '/logout' => 'logout.php',
    '/api/auth' => 'api/auth.php',
    '/api/chat' => 'api/chat.php',
    '/api/payment' => 'api/payment.php',
    '/api/me' => 'api/me.php',
    '/api/check-payment' => 'api/check-payment.php',
];

// Проверяем точное совпадение
if (isset($routes[$request])) {
    require __DIR__ . '/' . $routes[$request];
    exit;
}

// Проверяем частичное совпадение (для API с параметрами)
foreach ($routes as $route => $file) {
    if (strpos($request, $route) === 0) {
        require __DIR__ . '/' . $file;
        exit;
    }
}

// Если маршрут не найден - просто показываем index.php
// (это уже обработано в nginx)
?>
