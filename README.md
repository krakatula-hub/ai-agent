# 🤖 AI Agent — Ваш личный AI-агент для бизнеса

[![PHP](https://img.shields.io/badge/PHP-7.4+-777BB4?logo=php&logoColor=white)](https://php.net)
[![DeepSeek](https://img.shields.io/badge/DeepSeek-API-4facfe)](https://deepseek.com)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

## 🚀 О проекте

**AI Agent** — это готовое SaaS-решение для бизнеса на базе DeepSeek API. 
Позволяет автоматизировать рутинные задачи, анализировать данные и принимать решения в 10 раз быстрее.

## 🌟 Особенности

- 🤖 **6 AI-агентов**: Юрист, Программист, Маркетолог, Дизайнер, Аналитик, Виртуальный ассистент
- 💬 **Общий чат** для пользователей
- 📝 **Блог** с админ-панелью
- 🏆 **Кейсы** с админ-панелью
- 📢 **Система заказа рекламы**
- 💳 **Ручная оплата** через ЮKassa
- 🔒 **Полная безопасность** (HSTS, CSRF, подготовленные запросы)
- 📱 **Адаптивный дизайн** под все устройства

## 🛠️ Технологии

| Компонент | Технология |
|-----------|------------|
| Backend | PHP 8.2 |
| Frontend | HTML5, CSS3, JavaScript |
| База данных | MySQL / MariaDB |
| AI API | DeepSeek API |
| Платежи | ЮKassa |
| Сервер | nginx |
| Аналитика | Яндекс.Метрика |

## 🚀 Быстрый старт

### Требования

- PHP 8.2+
- MySQL 5.7+
- Composer
- nginx / Apache

### Установка

```bash
# 1. Клонируйте репозиторий
git clone https://github.com/ваш-username/ai-agent.git
cd ai-agent

# 2. Установите зависимости
composer install

# 3. Настройте .env
cp .env.example .env
nano .env

# 4. Создайте базу данных
mysql -u root -p -e "CREATE DATABASE ai_agent"

# 5. Импортируйте таблицы
mysql -u root -p ai_agent < database.sql

# 6. Настройте веб-сервер
