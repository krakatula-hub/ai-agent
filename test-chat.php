<?php
require_once __DIR__ . '/includes/header.php';
?>

<style>
    .chat-container { max-width: 800px; margin: 0 auto; padding: 20px; }
    .chat-box { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; padding: 20px; min-height: 400px; display: flex; flex-direction: column; }
    .messages { flex: 1; overflow-y: auto; padding: 10px; max-height: 400px; min-height: 300px; display: flex; flex-direction: column; }
    .message { padding: 12px 18px; border-radius: 15px; max-width: 80%; margin-bottom: 10px; word-wrap: break-word; animation: fadeIn 0.3s; }
    .message.user { background: #4facfe; align-self: flex-end; border-bottom-right-radius: 5px; margin-left: auto; }
    .message.bot { background: rgba(255,255,255,0.08); align-self: flex-start; border-bottom-left-radius: 5px; }
    .input-area { display: flex; gap: 10px; margin-top: 15px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 15px; }
    .input-area input { flex: 1; padding: 14px; border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; background: rgba(0,0,0,0.3); color: #fff; outline: none; font-size: 16px; }
    .input-area button { padding: 14px 30px; border: none; border-radius: 12px; background: linear-gradient(135deg, #4facfe, #00f2fe); color: #fff; font-weight: 700; cursor: pointer; transition: all 0.3s; }
    .input-area button:hover { transform: scale(1.05); }
    .typing { display: none; padding: 10px; color: rgba(255,255,255,0.4); font-style: italic; }
    .count { text-align: center; margin-top: 15px; font-size: 13px; color: rgba(255,255,255,0.3); }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div class="container">
    <div class="chat-container">
        <h1 style="text-align:center; font-size:36px; font-weight:700; margin-bottom:10px;">
            💬 <span class="gradient-text">Тестовый чат</span>
        </h1>
        <p style="text-align:center; color:rgba(255,255,255,0.6); font-size:18px; margin-bottom:30px;">
            Попробуйте AI-агента бесплатно. Без регистрации!
        </p>

        <div class="chat-box">
            <div class="messages" id="chatMessages">
                <div style="text-align:center; color:rgba(255,255,255,0.4); font-size:14px; padding:40px 0;">
                    <div style="font-size:48px;">🤖</div>
                    <div>Напишите сообщение, чтобы начать общение</div>
                    <div style="font-size:12px; color:rgba(255,255,255,0.2);">Тестовый режим — работает без регистрации</div>
                </div>
            </div>
            <div class="typing" id="typingIndicator">🤖 AI печатает...</div>
            <div class="input-area">
                <input type="text" id="testMessageInput" placeholder="Напишите сообщение..." />
                <button id="sendButton">➤</button>
            </div>
            <div class="count">⚡ Бесплатно: <span id="testMessagesCount">0</span> / 3 сообщений</div>
        </div>
    </div>
</div>

<script>
    // === ЧАТ ===
    let testMessageCount = 0;
    const MAX_TEST_MESSAGES = 3;
    const chatMessages = document.getElementById('chatMessages');
    const typingIndicator = document.getElementById('typingIndicator');
    const messageInput = document.getElementById('testMessageInput');
    const countDisplay = document.getElementById('testMessagesCount');
    const sendButton = document.getElementById('sendButton');

    function addMessage(text, isUser) {
        var placeholder = chatMessages.querySelector('.empty-placeholder');
        if (placeholder) placeholder.remove();

        var div = document.createElement('div');
        div.className = 'message ' + (isUser ? 'user' : 'bot');
        div.textContent = text;
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function sendTestMessage() {
        var message = messageInput.value.trim();
        if (!message) return;

        if (testMessageCount >= MAX_TEST_MESSAGES) {
            alert('⚠️ Лимит сообщений исчерпан! Зарегистрируйтесь для полного доступа.');
            return;
        }

        addMessage(message, true);
        messageInput.value = '';
        testMessageCount++;
        countDisplay.textContent = testMessageCount;

        typingIndicator.style.display = 'block';
        chatMessages.scrollTop = chatMessages.scrollHeight;

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/api/test-chat.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                typingIndicator.style.display = 'none';
                if (xhr.status === 200) {
                    try {
                        var data = JSON.parse(xhr.responseText);
                        if (data.success) {
                            addMessage(data.response, false);
                        } else {
                            addMessage('❌ ' + (data.error || 'Ошибка'), false);
                        }
                    } catch (e) {
                        addMessage('❌ Ошибка обработки ответа', false);
                    }
                } else {
                    addMessage('❌ Ошибка соединения (' + xhr.status + ')', false);
                }
            }
        };
        xhr.send(JSON.stringify({ message: message }));
    }

    sendButton.addEventListener('click', sendTestMessage);
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendTestMessage();
    });

    console.log('✅ Чат загружен!');
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
