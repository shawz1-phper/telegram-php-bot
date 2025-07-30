# Telegram PHP Bot using Webhook (Render Deployment)

🤖 A simple Telegram bot built with PHP using the Telegram Bot API and deployed on [Render](https://render.com).

---

## 🚀 Features

- Webhook-based Telegram bot
- Lightweight PHP implementation (no frameworks)
- Auto-deployment via Render + GitHub
- Simple `/start` command support

---

## 🧰 Requirements

- PHP 8.1 or higher
- Telegram Bot Token (from @BotFather)
- GitHub account
- Render account

---

## 📦 Deployment (on Render)

1. Fork or clone this repo
2. Connect your GitHub to [Render](https://render.com)
3. Create a **new Web Service** on Render
4. Use Docker environment
5. Add your `TELEGRAM_BOT_TOKEN` under environment variables
6. Wait for Render to build and deploy

---

## 🔗 Set the Webhook

After the app is deployed, set the Telegram webhook using the following:
