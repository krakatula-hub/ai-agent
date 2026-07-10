<?php
// includes/notifications.php - функции для отправки уведомлений

/**
 * Отправка email-уведомления о новой регистрации
 */
function notifyNewUser($userEmail, $plan, $userId) {
    $adminEmail = 'your-email@example.com'; // ЗАМЕНИТЕ НА ВАШ EMAIL!
    $siteUrl = 'https://ai.nkvopros.ru';
    
    $subject = "🆕 Новая регистрация на AI Agent!";
    
    $message = "
    <html>
    <head><title>Новая регистрация</title></head>
    <body style='font-family: Arial; background: #f4f4f4; padding: 20px;'>
        <div style='max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px;'>
            <h2 style='color: #4facfe;'>🆕 Новая регистрация</h2>
            <table style='width: 100%; border-collapse: collapse;'>
                <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Email:</strong></td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$userEmail}</td></tr>
                <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Тариф:</strong></td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee;'><span style='background: #4facfe; color: #fff; padding: 3px 10px; border-radius: 5px;'>{$plan}</span></td></tr>
                <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>ID пользователя:</strong></td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$userId}</td></tr>
                <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Дата:</strong></td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee;'>" . date('d.m.Y H:i:s') . "</td></tr>
            </table>
            <p style='margin-top: 20px;'>
                <a href='{$siteUrl}/cabinet.php' style='background: #4facfe; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Перейти в админку</a>
            </p>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
    $headers .= "From: AI Agent <noreply@ai.nkvopros.ru>" . "\r\n";
    
    return mail($adminEmail, $subject, $message, $headers);
}

/**
 * Уведомление об изменении тарифа
 */
function notifyPlanChange($userEmail, $oldPlan, $newPlan, $userId) {
    $adminEmail = 'ejikovvladimir@yandex.ru'; // ЗАМЕНИТЕ НА ВАШ EMAIL!
    
    $subject = "🔄 Изменение тарифа у пользователя";
    
    $message = "
    <html>
    <head><title>Смена тарифа</title></head>
    <body style='font-family: Arial; background: #f4f4f4; padding: 20px;'>
        <div style='max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px;'>
            <h2 style='color: #f39c12;'>🔄 Смена тарифа</h2>
            <table style='width: 100%; border-collapse: collapse;'>
                <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Email:</strong></td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$userEmail}</td></tr>
                <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Старый тариф:</strong></td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee;'>{$oldPlan}</td></tr>
                <tr><td style='padding: 10px; border-bottom: 1px solid #eee;'><strong>Новый тариф:</strong></td>
                    <td style='padding: 10px; border-bottom: 1px solid #eee;'><span style='background: #2ecc71; color: #fff; padding: 3px 10px; border-radius: 5px;'>{$newPlan}</span></td></tr>
            </table>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type: text/html; charset=utf-8" . "\r\n";
    $headers .= "From: AI Agent <noreply@ai.nkvopros.ru>" . "\r\n";
    
    return mail($adminEmail, $subject, $message, $headers);
}
?>
