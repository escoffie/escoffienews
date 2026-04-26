# Architecture & Data Models

## Entity Relationship Diagram

```mermaid
erDiagram
    USERS ||--o{ CATEGORY_USER : "subscribes to"
    CATEGORIES ||--o{ CATEGORY_USER : "has subscribers"
    
    USERS ||--o{ CHANNEL_USER : "prefers"
    CHANNELS ||--o{ CHANNEL_USER : "notifies via"
    
    USERS ||--o{ NOTIFICATION_LOGS : "receives"

    USERS {
        bigint id PK
        string name
        string email
        string phone
        timestamp created_at
    }

    CATEGORIES {
        bigint id PK
        string name
    }

    CHANNELS {
        bigint id PK
        string name
    }

    CATEGORY_USER {
        bigint category_id FK
        bigint user_id FK
    }

    CHANNEL_USER {
        bigint channel_id FK
        bigint user_id FK
    }

    NOTIFICATION_LOGS {
        bigint id PK
        bigint user_id FK
        string user_name
        string user_email
        string category
        string channel
        text message
        timestamp created_at
    }
```

## Notification Flow (Pub-Sub)

1. **API Ingestion:** Message received via POST `/api/notifications`.
2. **Event Dispatch:** `MessageReceived` event is fired.
3. **Subscriber Discovery:** Listener fetches users matching the category.
4. **Strategy Execution:** For each user, the system identifies preferred channels and executes the corresponding Strategy (`SmsProvider`, `EmailProvider`, `PushProvider`).
5. **Persistence:** Every attempt is logged in `notification_logs`.
6. **Real-time Update:** Success events are broadcasted via Laravel Reverb to the frontend log history.
