# ReactPHP - RabbitMQ

```
    1. Clone repo
    2. docker-compose build
    3. docker-compose up -d && docker logs -f sv-consume
    4. Open new tab of terminal, execute this command
    
    curl -X POST \
      http://localhost:15672/api/exchanges/%2f/events/publish \
      -H 'Authorization: Basic dXNlcjp1c2Vy' \
      -H 'Content-Type: application/json' \
      -H 'Postman-Token: 1ef84721-37bb-425e-8c03-7882a8a82b87' \
      -H 'cache-control: no-cache' \
      -d '{
        "properties": {
            "content_type": "json"
        },
        "routing_key": "user.create",
        "payload": "{\"username\": \"this is my username\"}",
        "payload_encoding": "string"
    }'
```