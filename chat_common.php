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

require_once __DIR__ . '/includes/header.php';
?>

<style>
    .chat-container { max-width: 800px; margin: 0 auto; padding: 20px; }
    .chat-box { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 20px; min-height: 500px; display: flex; flex-direction: column; }
    .chat-messages { flex: 1; overflow-y: auto; padding: 10px; max-height: 450px; min-height: 350px; display: flex; flex-direction: column; }
    .chat-message { padding: 10px 16px; border-radius: 12px; max-width: 80%; margin-bottom: 8px; word-wrap: break-word; animation: fadeIn 0.3s; }
    .chat-message.self { background: #4facfe; align-self: flex-end; border-bottom-right-radius: 4px; margin-left: auto; }
    .chat-message.other { background: rgba(255,255,255,0.06); align-self: flex-start; border-bottom-left-radius: 4px; }
    .chat-message .meta { font-size: 11px; color: rgba(255,255,255,0.4); margin-top: 4px; display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
    .chat-message .meta .plan-badge { padding: 1px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; }
    .plan-free { background: rgba(255,255,255,0.1); color: #aaa; }
    .plan-pro { background: rgba(79,172,254,0.2); color: #4facfe; }
    .plan-business { background: rgba(46,204,113,0.2); color: #2ecc71; }
    .chat-input-area { display: flex; gap: 10px; margin-top: 15px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 15px; }
    .chat-input-area input { flex: 1; padding: 14px; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; background: rgba(0,0,0,0.3); color: #fff; outline: none; font-size: 16px; }
    .chat-input-area button { padding: 14px 30px; border: none; border-radius: 12px; background: linear-gradient(135deg, #4facfe, #00f2fe); color: #fff; font-weight: 700; cursor: pointer; transition: all 0.3s; }
    .chat-input-area button:hover { transform: scale(1.05); }
    .chat-info { text-align: center; margin-top: 15px; font-size: 13px; color: rgba(255,255,255,0.3); }
    .chat-info strong { color: rgba(255,255,255,0.6); }
    .empty-chat { text-align: center; color: rgba(255,255,255,0.3); padding: 40px 0; }
    .empty-chat .icon { font-size: 48px; margin-bottom: 10px; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    .back-btn { display: inline-block; padding: 10px 24px; background: rgba(255,255,255,0.05); color: #fff; text-decoration: none; border-radius: 10px; margin-bottom: 20px; border: 1px solid rgba(255,255,255,0.1); }
    .back-btn:hover { background: rgba(255,255,255,0.1); }
     .delete-btn {
    background: none;
    border: none;
    color: #ff4757;
    cursor: pointer;
    font-size: 12px;
    padding: 2px 6px;
    border-radius: 4px;
    transition: all 0.3s;
    opacity: 0.5;
}
.delete-btn:hover {
    opacity: 1;
    background: rgba(255,71,87,0.1);
}
</style>

<div class="container">
    <div class="chat-container">
        <a href="/agent_select.php" class="back-btn">← Назад к агентам</a>
        
        <h1 style="text-align:center; font-size:28px; margin-bottom:10px;">
            💬 <span class="gradient-text">Общий чат</span>
        </h1>
        <p style="text-align:center; color:rgba(255,255,255,0.5); font-size:14px; margin-bottom:20px;">
            Обсуждайте сервис, задавайте вопросы и общайтесь с сообществом
        </p>

        <div class="chat-box">
            <div class="chat-messages" id="chatMessages">
                <div class="empty-chat">
                    <div class="icon">💬</div>
                    <div>Загрузка сообщений...</div>
                </div>
            </div>
            <div class="chat-input-area">
                <input type="text" id="chatInput" placeholder="Напишите сообщение..." />
                <button id="chatSend">➤</button>
            </div>
            <div class="chat-info">
    ⚡ <strong id="chatDailyLimit">10</strong> сообщений в день в общем чате<br>
    <span id="chatTodayCount">0</span> использовано сегодня(осталось: <span id="chatRemainingCount">10</span>)
</div>
        </div>
    </div>
</div>

<script>
    const chatMessages = document.getElementById('chatMessages');
    const chatInput = document.getElementById('chatInput');
    const chatSend = document.getElementById('chatSend');

    // === ЗАГРУЗКА СООБЩЕНИЙ ===
    function loadMessages() {
        fetch('/api/chat_common.php')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    renderMessages(data.messages);
                }
            })
            .catch(function(e) { console.error('Ошибка загрузки:', e); });
    }

    // === ОТОБРАЖЕНИЕ СООБЩЕНИЙ ===
   function renderMessages(messages) {
    chatMessages.innerHTML = '';
    if (messages.length === 0) {
        chatMessages.innerHTML = `
            <div class="empty-chat">
                <div class="icon">💬</div>
                <div>Пока нет сообщений. Начните общение первым!</div>
            </div>
        `;
        return;
    }
    
    // === ПРОВЕРКА, ЯВЛЯЕТСЯ ЛИ ПОЛЬЗОВАТЕЛЬ АДМИНИСТРАТОРОМ ===
    var isAdmin = <?= isAdmin($_SESSION['user_id']) ? 'true' : 'false' ?>;
    
    messages.forEach(function(msg) {
        const div = document.createElement('div');
        const isSelf = msg.user_id == <?= $user['id'] ?>;
        div.className = 'chat-message ' + (isSelf ? 'self' : 'other');
        div.dataset.messageId = msg.id;
        
        const planNames = { free: 'Бесплатный', pro: 'PRO', business: 'Бизнес' };
        const planClass = { free: 'plan-free', pro: 'plan-pro', business: 'plan-business' };
        
        // === КНОПКА УДАЛЕНИЯ (ТОЛЬКО ДЛЯ АДМИНА) ===
        var deleteButton = '';
        if (isAdmin) {
            deleteButton = '<button class="delete-btn" onclick="deleteMessage(' + msg.id + ')">🗑️</button>';
        }
        
        div.innerHTML = `
            <div>${escapeHtml(msg.message)}</div>
            <div class="meta">
                <span>${escapeHtml(msg.email)}</span>
                <span class="plan-badge ${planClass[msg.plan] || 'plan-free'}">${planNames[msg.plan] || 'Бесплатный'}</span>
                <span>${new Date(msg.created_at).toLocaleTimeString()}</span>
                ${deleteButton}
            </div>
        `;
        chatMessages.appendChild(div);
    });
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

    // === ОТПРАВКА СООБЩЕНИЯ ===
    function sendMessage() {
        const message = chatInput.value.trim();
        if (!message) return;
        
        chatInput.value = '';
        chatInput.disabled = true;
        chatSend.disabled = true;
        
        fetch('/api/chat_common.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: message })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            chatInput.disabled = false;
            chatSend.disabled = false;
            if (data.success) {
                loadMessages();
                getTodayCount();
            } else {
                alert('❌ ' + (data.error || 'Ошибка'));
            }
        })
        .catch(function() {
            chatInput.disabled = false;
            chatSend.disabled = false;
            alert('❌ Ошибка соединения');
        });
    }

    // === ПОЛУЧЕНИЕ КОЛИЧЕСТВА СООБЩЕНИЙ СЕГОДНЯ ===
    function getTodayCount() {
        fetch('/api/chat_common.php?action=count')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.success) {
                    var used = parseInt(data.count) || 0;
                    var limit = 10;
                    var remaining = Math.max(0, limit - used);
                    document.getElementById('chatTodayCount').textContent = used;
                    document.getElementById('chatRemainingCount').textContent = remaining;
                    
                    if (remaining <= 0) {
                        chatInput.disabled = true;
                        chatSend.disabled = true;
                        chatInput.placeholder = '⚠️ Лимит исчерпан';
                    } else {
                        chatInput.disabled = false;
                        chatSend.disabled = false;
                        chatInput.placeholder = 'Напишите сообщение...';
                    }
                }
            })
            .catch(function(e) { console.error('Ошибка получения счётчика:', e); });
    }

    // === ЭСКЕЙП ===
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // === СОБЫТИЯ ===
    chatSend.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendMessage();
    });

    // === АВТО-ОБНОВЛЕНИЕ ===
    loadMessages();
    getTodayCount();
    setInterval(loadMessages, 3000);
    setInterval(getTodayCount, 5000);
    
    // === УДАЛЕНИЕ СООБЩЕНИЯ (ТОЛЬКО ДЛЯ АДМИНА) ===
function deleteMessage(messageId) {
    if (!confirm('Удалить это сообщение?')) return;
    
    fetch('/api/chat_common.php?action=delete', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message_id: messageId })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            loadMessages();
            getTodayCount();
        } else {
            alert('❌ ' + (data.error || 'Ошибка удаления'));
        }
    })
    .catch(function() {
        alert('❌ Ошибка соединения');
    });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
