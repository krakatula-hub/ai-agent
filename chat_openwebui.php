<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /login_page.php');
    exit;
}

require_once 'includes/config.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

$user = getUserById($_SESSION['user_id']);
$openWebUIUrl = 'http://217.197.115.92:8080';
$plan = $user['plan'];
$isFree = ($plan === 'free');

require_once __DIR__ . '/includes/header.php';
?>

<style>
    .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
    .back-btn { display: inline-block; padding: 10px 24px; background: rgba(255,255,255,0.05); color: #fff; text-decoration: none; border-radius: 10px; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.1); }
    .back-btn:hover { background: rgba(255,255,255,0.1); }
    .info-bar { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 15px 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }
    .info-bar .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-free { background: rgba(255,255,255,0.1); color: #aaa; }
    .badge-pro { background: rgba(79,172,254,0.2); color: #4facfe; }
    .badge-business { background: rgba(46,204,113,0.2); color: #2ecc71; }
    .btn { display: inline-block; padding: 14px 36px; border: none; border-radius: 50px; font-weight: 700; text-decoration: none; transition: all 0.3s; }
    .btn-primary { background: linear-gradient(135deg, #4facfe, #00f2fe); color: #fff; }
    .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(79,172,254,0.4); }
    .btn-secondary { background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); }
    .btn-secondary:hover { background: rgba(255,255,255,0.2); }
    .chat-launch { text-align: center; padding: 40px 0; }
    .chat-launch h2 { font-size: 28px; margin-bottom: 15px; }
    .chat-launch p { color: rgba(255,255,255,0.6); font-size: 18px; margin-bottom: 30px; }

    /* ===== ВСТРОЕННЫЙ ЧАТ ДЛЯ БЕСПЛАТНЫХ ===== */
    .chat-box { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 20px; min-height: 450px; display: flex; flex-direction: column; }
    .chat-messages { flex: 1; overflow-y: auto; padding: 10px; max-height: 350px; min-height: 250px; display: flex; flex-direction: column; }
    .chat-message { padding: 12px 18px; border-radius: 15px; max-width: 80%; margin-bottom: 10px; word-wrap: break-word; animation: fadeIn 0.3s; }
    .chat-message.user { background: #4facfe; align-self: flex-end; border-bottom-right-radius: 5px; margin-left: auto; }
    .chat-message.bot { background: rgba(255,255,255,0.08); align-self: flex-start; border-bottom-left-radius: 5px; }
    .chat-input-area { display: flex; gap: 10px; margin-top: 15px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 15px; }
    .chat-input-area input { flex: 1; padding: 14px; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; background: rgba(0,0,0,0.3); color: #fff; outline: none; font-size: 16px; }
    .chat-input-area button { padding: 14px 30px; border: none; border-radius: 12px; background: linear-gradient(135deg, #4facfe, #00f2fe); color: #fff; font-weight: 700; cursor: pointer; transition: all 0.3s; }
    .chat-input-area button:hover { transform: scale(1.05); }
    .chat-typing { display: none; padding: 10px; color: rgba(255,255,255,0.4); font-style: italic; }
    .chat-count { text-align: center; margin-top: 15px; font-size: 13px; color: rgba(255,255,255,0.3); }
    .chat-upgrade { text-align: center; margin-top: 15px; padding: 15px; background: rgba(79,172,254,0.05); border-radius: 12px; border: 1px solid rgba(79,172,254,0.1); }
    .chat-upgrade a { color: #4facfe; text-decoration: none; font-weight: 600; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="container">
    <a href="/cabinet.php" class="back-btn">← Назад в кабинет</a>
    
    <div class="info-bar">
        <span>👤 <strong><?= htmlspecialchars($user['email']) ?></strong></span>
        <span>📊 Тариф: <span class="badge badge-<?= $plan ?>"><?= getPlanName($plan) ?></span></span>
        <span>💬 Сообщений сегодня: <?= $user['messages_today'] ?> / <?= PLAN_LIMITS[$plan] ?? 0 ?></span>
        <?php if ($user['subscription_end']): ?>
            <span>📅 Подписка до: <?= date('d.m.Y', strtotime($user['subscription_end'])) ?></span>
        <?php endif; ?>
    </div>

    <?php if ($isFree): ?>
        <!-- ===== ВСТРОЕННЫЙ ЧАТ ДЛЯ БЕСПЛАТНЫХ ===== -->
        <div class="chat-box">
            <div class="chat-messages" id="freeChatMessages">
                <div style="text-align:center; color:rgba(255,255,255,0.4); font-size:14px; padding:40px 0;">
                    <div style="font-size:48px;">🤖</div>
                    <div>Напишите сообщение, чтобы начать общение</div>
                    <div style="font-size:12px; color:rgba(255,255,255,0.2);">Бесплатный тариф — 5 сообщений в день</div>
                </div>
            </div>
            <div class="chat-typing" id="freeChatTyping">🤖 AI печатает...</div>
            <div class="chat-input-area">
                <input type="text" id="freeChatInput" placeholder="Напишите сообщение..." />
                <button id="freeChatSend">➤</button>
            </div>
            <div class="chat-count">⚡ Бесплатно: <span id="freeChatCount"><?= $user['messages_today'] ?></span> / 5 сообщений</div>
            <?php if ($user['messages_today'] >= 5): ?>
                <div class="chat-upgrade">
                    🔒 Лимит исчерпан. <a href="/cabinet.php">Купите PRO</a> для продолжения.
                </div>
            <?php endif; ?>
        </div>

        <script>
        // === БЕСПЛАТНЫЙ ЧАТ ===
        let freeMessageCount = <?= $user['messages_today'] ?>;
        const MAX_FREE_MESSAGES = 5;
        const chatMessages = document.getElementById('freeChatMessages');
        const typingIndicator = document.getElementById('freeChatTyping');
        const messageInput = document.getElementById('freeChatInput');
        const sendButton = document.getElementById('freeChatSend');
        const countDisplay = document.getElementById('freeChatCount');

        function addMessage(text, isUser) {
            const placeholder = chatMessages.querySelector('.empty-placeholder');
            if (placeholder) placeholder.remove();
            const div = document.createElement('div');
            div.className = 'chat-message ' + (isUser ? 'user' : 'bot');
            div.textContent = text;
            chatMessages.appendChild(div);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function sendFreeMessage() {
            const message = messageInput.value.trim();
            if (!message) return;
            if (freeMessageCount >= MAX_FREE_MESSAGES) {
                alert('⚠️ Лимит исчерпан! Купите PRO для продолжения.');
                return;
            }
            addMessage(message, true);
            messageInput.value = '';
            freeMessageCount++;
            countDisplay.textContent = freeMessageCount;
            typingIndicator.style.display = 'block';

            fetch('/api/test-chat.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: message })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                typingIndicator.style.display = 'none';
                if (data.success) {
                    addMessage(data.response, false);
                } else {
                    addMessage('❌ ' + (data.error || 'Ошибка'), false);
                }
                if (freeMessageCount >= MAX_FREE_MESSAGES) {
                    document.querySelector('.chat-upgrade').style.display = 'block';
                }
            })
            .catch(function() {
                typingIndicator.style.display = 'none';
                addMessage('❌ Ошибка соединения', false);
            });
        }

        sendButton.addEventListener('click', sendFreeMessage);
        messageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') sendFreeMessage();
        });
        </script>

    <?php else: ?>
        <!-- ===== OPEN WEBUI ДЛЯ ПЛАТНЫХ ===== -->
        <div class="chat-launch">
            <h2>💬 <span class="gradient-text">Вход в AI-Агент</span></h2>
            <p>Нажмите кнопку, чтобы открыть чат с вашим AI-Агентом. И приступить к работе.</p>
            <a href="<?= $openWebUIUrl ?>" target="_blank" class="btn btn-primary">🚀 Открыть чат</a>
            <div style="margin-top: 20px; padding: 15px; background: rgba(255,255,255,0.02); border-radius: 12px; border: 1px solid rgba(255,255,255,0.05);">
                <p style="color: rgba(255,255,255,0.4); font-size: 14px;">💡 Чат откроется в новой вкладке.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
