# Architecture & Data Models

## System Overview

EscoffieNews uses an **event-driven, asynchronous** architecture. The HTTP request returns immediately after queuing background jobs — delivery, retries, and failure handling all happen in a separate container.

## Notification Flow

```mermaid
sequenceDiagram
    participant Client
    participant API as NotificationController
    participant Event as MessageReceived Event
    participant Listener as SendNotificationToSubscribers
    participant Service as NotificationService
    participant Queue as Database Queue
    participant Worker as Queue Worker
    participant Provider as SMS/Email/Push Provider
    participant Log as NotificationLog
    participant WS as Laravel Reverb (WebSocket)

    Client->>API: POST /api/notifications
    API->>Event: fire(MessageReceived)
    API-->>Client: 202 Accepted (immediate)

    Event->>Listener: handle(event)
    Listener->>Service: notifyByCategory(category, message, chaosMonkey)
    Service->>Queue: dispatch(SendProviderNotificationJob) × N×M
    Service->>WS: broadcast "N jobs queued"

    loop For each Job (async, in worker container)
        Worker->>Provider: send(NotificationData)
        alt Chaos Monkey fires (30% chance)
            Provider-->>Worker: throws RuntimeException
            Worker->>WS: broadcast ERROR log
            Worker->>Queue: retry after backoff (5s → 10s → 20s)
        else Delivery succeeds
            Provider->>Log: persist NotificationLog
            Provider->>WS: broadcast NotificationLogged event
            Provider->>WS: broadcast "Delivered" INFO log
        end
        alt All 3 retries exhausted
            Worker->>WS: broadcast PERMANENT FAILURE error
        end
    end
```

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
        string name "idx: name"
    }

    CHANNELS {
        bigint id PK
        string name "idx: name"
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
        bigint user_id FK "idx: user_id"
        string user_name "denormalized"
        string user_email "denormalized"
        string category "idx: category"
        string channel "idx: channel"
        text message
        timestamp created_at "idx: created_at"
    }
```

> **Why denormalize `user_name` and `user_email`?** Log integrity. If a user is later updated or deleted, the historical log still reflects the exact data at delivery time.

## Folder Structure

```
app/
├── Contracts/
│   └── Repositories/          # Interfaces (Repository Pattern)
├── DTOs/
│   └── NotificationData.php   # Typed data transfer object
├── Events/
│   ├── MessageReceived.php
│   ├── NotificationLogged.php
│   └── SystemLogBroadcast.php
├── Http/Controllers/Api/      # Thin controllers, no business logic
├── Jobs/
│   └── SendProviderNotificationJob.php  # Queue job with retry logic
├── Listeners/
│   └── SendNotificationToSubscribers.php
├── Models/
├── Notifications/Channels/
│   ├── Contracts/
│   │   └── NotificationProviderInterface.php  # Strategy contract
│   ├── AbstractNotificationProvider.php       # Shared delivery logic
│   ├── SmsProvider.php
│   ├── EmailProvider.php
│   └── PushProvider.php
├── Providers/
│   └── NotificationServiceProvider.php  # Wires providers into container
├── Repositories/Eloquent/     # Concrete repository implementations
└── Services/
    └── NotificationService.php  # Orchestrates queue dispatch
```

## Adding a New Notification Channel

The Strategy Pattern makes this a minimal change:

1. Create `app/Notifications/Channels/SlackProvider.php` implementing `NotificationProviderInterface`.
2. Add one line to `NotificationServiceProvider::register()`:
   ```php
   $this->app->tag([..., SlackProvider::class], 'notification.providers');
   $service->addProvider($app->make(SlackProvider::class));
   ```
3. Done. The job resolution, retry logic, and logging are all inherited automatically.
