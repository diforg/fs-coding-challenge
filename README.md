# 🚀 Desafio Técnico: Desenvolvedor Pleno Full Stack (Laravel/Vue)

## 📋 Índice

- [🎯 Sobre o Desafio](#-sobre-o-desafio)
- [⚙️ Pré-requisitos](#️-pré-requisitos)
- [🚀 Instalação e Configuração](#-instalação-e-configuração)
- [🌐 Acesso à Aplicação](#-acesso-à-aplicação)
- [🔧 Comandos Úteis](#-comandos-úteis)
- [🔄 Testes de Webhook](#-testes-de-webhook)
- [📈 Próximas Features](#-próximas-features)

---

## 🎯 Sobre o Desafio

Desafio técnico para vaga de Desenvolvedor Pleno Full Stack, focado na integração com APIs de mensageria (WhatsApp, Messenger e Telegram) utilizando Laravel e Vue.js.

---

## ⚙️ Pré-requisitos

- 🐳 **Docker** instalado na máquina
- 📦 **Git** para baixar o código

---

## 🚀 Instalação e Configuração

Siga os passos abaixo para configurar o ambiente de desenvolvimento:

### 1. 📥 **Clonar o Repositório**
```
git clone https://github.com/diforg/fs-coding-challenge.git && cd fs-coding-challenge
```

### 2. 🌿 **Acessar a Branch do Desafio**
```
git fetch origin diego-forganes && git checkout diego-forganes
```

### 3. ⚙️ **Configurar Variáveis de Ambiente**
```
# Linux/Mac
cp src/.env.example src/.env

# Windows
copy src/.env.example src/.env
```

### 4. 🐳 **Subir os Containers Docker**
```
cd docker/ && docker compose up
```

### 5. 🔄 **Acessar o Container da Aplicação**
```
docker exec -it fscc-php_app bash
```

---

## 🌐 Acesso à Aplicação

- **Interface do Usuário**: http://localhost:8088/

---

## 🔧 Comandos Úteis

### 🎭 **Simular Novas Mensagens**
```
php artisan messages:generate
```

### 🧪 **Executar Testes**
```
php artisan test
```

### 📊 **Ver Logs da Aplicação**
```
tail -f storage/logs/laravel.log
```

---

## 🔄 Testes de Webhook

### 📱 **WhatsApp Webhook**
```
curl -X POST \
  http://localhost:8088/api/webhook/whatsapp/received \
  -H "Content-Type: application/json" \
  -H "User-Agent: WhatsAppWebhook/1.0" \
  -d '{
  "object": "whatsapp_business_account",
  "entry": [
    {
      "id": "123456789012345",
      "changes": [
        {
          "value": {
            "messaging_product": "whatsapp",
            "metadata": {
              "display_phone_number": "15550001234",
              "phone_number_id": "123456789012345"
            },
            "contacts": [
              {
                "profile": {
                  "name": "Diego"
                },
                "wa_id": "5513981257788"
              }
            ],
            "messages": [
              {
                "from": "5511999999999",
                "id": "wamid.ABGGFDSEF5TJR3I4O5K6L7M8N9O0P1Q2R3S4T5U6V7W8X9Y0Z",
                "timestamp": "1705336222",
                "text": {
                  "body": "Olá, gostaria de entender melhor seus serviços!"
                },
                "type": "text"
              }
            ]
          },
          "field": "messages"
        }
      ]
    }
  ]
}'
```

### 💬 **Messenger Webhook**
```
curl -X POST \
  http://localhost:8088/api/webhook/messenger/received \
  -H "Content-Type: application/json" \
  -H "User-Agent: MessengerWebhook/1.0" \
  -d '{
  "object": "page",
  "entry": [
    {
      "id": "123456789012345",
      "time": 1705336222000,
      "messaging": [
        {
          "sender": {
            "id": "893572827070123"
          },
          "recipient": {
            "id": "987654321098765"
          },
          "timestamp": 1705336222500,
          "message": {
            "mid": "mid.4334433455:v34fe4frvtrf",
            "text": "Olá, estou interessado nos seus produtos!"
          }
        }
      ]
    }
  ]
}'
```

### 📲 **Telegram Webhook**
```
curl -X POST \
  http://localhost:8088/api/webhook/telegram/received \
  -H "Content-Type: application/json" \
  -H "User-Agent: TelegramWebhook/1.0" \
  -d '{
  "update_id": 4435456654,
  "message": {
    "message_id": 123,
    "from": {
      "id": 45544343,
      "is_bot": false,
      "first_name": "João",
      "username": "joaofernandes",
      "language_code": "pt-br"
    },
    "chat": {
      "id": 77544e5665,
      "first_name": "João",
      "username": "joaofernandes",
      "type": "private"
    },
    "date": 1705336222,
    "text": "Olá, quais as opções de produtos vocês possuem?"
  }
}'
```

---

## 📈 Próximas Features

- 👤 **Integração com API do Messenger** para trazer o nome do contato pelo ID
- 🔔 **Envio das mensagens para as APIs** que estão marcadas com status pending
- 🔄 **Atualização do status das mensagens** quando lida pelo contato
- 🔐 **Sistema de autenticação** para controle de acesso

---

## 🆘 Troubleshooting

### 🔄 Reiniciar os Containers
```
docker compose down && docker compose up -d
```

### 🗑️ Limpar Cache da Aplicação
```
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### 📋 Ver Status dos Containers
```
docker compose ps
```

---

<div align="center">

**Desenvolvido com ❤️ por Diego Forganes**

</div>