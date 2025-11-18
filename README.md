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

## 📝 Testar a integração com o webhook do whatsapp (simulando json da api do whatsapp)

- Rodar no terminal o comando abaixo:

```markdown
```bash
curl -X POST \
  http://localhost/api/webhook/whatsapp/received \
  -H "Content-Type: application/json" \
  -H "User-Agent: WhatsAppWebhook/1.0" \
  -d '{
    "event": "message_received",
    "timestamp": "2024-01-15T14:30:25Z",
    "message": {
      "id": "msg_123456789",
      "from": {
        "phone": "+5511999999999",
        "name": "João Silva",
        "contact_id": "cont_987654321"
      },
      "to": {
        "phone": "+5511888888888",
        "name": "Empresa XYZ"
      },
      "content": {
        "type": "text",
        "text": "Olá, gostaria de saber mais sobre seus produtos!"
      },
      "timestamp": "2024-01-15T14:30:22Z",
      "direction": "inbound"
    },
    "channel": {
      "type": "whatsapp",
      "phone_number": "+5511888888888",
      "business_id": "biz_12345"
    },
    "context": {
      "conversation_id": "conv_abcdef123",
      "message_count": 15,
      "last_interaction": "2024-01-15T14:25:10Z"
    },
    "metadata": {
      "api_version": "v2.1",
      "webhook_id": "wh_67890",
      "delivery_status": "delivered"
    }
  }'
```

## 📋 Próximas Features

- Ambiente frontend com vue, inertia e tailwind