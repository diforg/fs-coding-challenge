# 📋 Desafio Técnico: Desenvolvedor Pleno Full Stack (Laravel/Vue)

## 📝 Pré requisitos
- Docker e Git instalado na máquina

## 📝 Instalação

Acesse o terminal e rode os comandos abaixo:

1. ✅ **Clonar o projeto:** git clone git@github.com:diforg/fs-coding-challenge.git && cd coding-challenge;
2. ✅ **Acessar a branch do desafio:** git fetch origin diego-forganes && git checkout diego-forganes
3. ✅ **Configurar .env:** Linux / Mac: cp src/.env.example src/.env; Windows: copy src/.env.example src/.env
4. ✅ **Subir o Projeto:** cd docker/ && docker compose up
5. ✅ **Entrar no projeto:** docker exec -it fscc-php_app bash

## 📝 Testar/Simular a integração com o webhook do whatsapp

- Rodar no terminal o comando abaixo:

```bash
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
                "type": "text",
                "context": {
                  "from": "5511888888888",
                  "id": "wamid.ABGGFDSEF5TJR3I4O5K6L7M8N9O0P1Q2R3S4T5U6V7W8X9Y0Z"
                }
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

## 📝 Testar/Simular a integração com o webhook do Messenger

- Rodar no terminal o comando abaixo:

```bash
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
            "text": "Olá, estou interessado nos seus produtos!",
            "quick_reply": {
              "payload": "PAYLOAD_123"
            },
            "nlp": {
              "entities": {
                "greeting": [
                  {
                    "confidence": 0.99,
                    "value": "true"
                  }
                ],
                "sentiment": [
                  {
                    "confidence": 0.85,
                    "value": "positive"
                  }
                ]
              }
            }
          }
        }
      ]
    }
  ]
  }'
  
  ```

## 📝 Testar/Simular a integração com o webhook do telegram

- Rodar no terminal o comando abaixo:

```bash
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
      "last_name": "Fernandes",
      "username": "joaofernandes",
      "language_code": "pt-br"
    },
    "chat": {
      "id": 77544e5665,
      "first_name": "João",
      "last_name": "Fernandes",
      "username": "joaofernandes",
      "type": "private"
    },
    "date": 1705336222,
    "text": "Olá, quais as opções de produtos vocês possuem?",
    "entities": [
      {
        "offset": 0,
        "length": 3,
        "type": "bold"
      }
    ]
  }
  }'

  ```


## 📋 Próximas Features

- Comando para gerar novas mensagens automaticamente