# EscoffieNews Notification System

A scalable, fault-tolerant notification routing system built with **Laravel 11**, **React 19**, **WebSockets**, and **background queue workers**.

## 🚀 Overview

EscoffieNews receives messages for a given category (Sports, Finance, Movies) and routes them asynchronously to every subscribed user via their preferred channels (SMS, E-Mail, Push Notification). Each delivery is handled by an independent background job with automatic retry logic — ensuring true fault tolerance even when providers fail.

---

## ✨ Core Features

| Feature | Detail |
|---|---|
| **Laravel 11 API** | PHP 8.4, strict typing, clean architecture |
| **React 19 SPA** | Vite + Tailwind CSS v4, real-time updates |
| **Laravel Reverb** | WebSocket server for live terminal & log streaming |
| **Queue Workers** | Async delivery via `SendProviderNotificationJob` |
| **Retry Logic** | 3 attempts with exponential backoff (5s → 10s → 20s) |
| **Chaos Monkey** | Controlled failure simulation to test fault tolerance live |
| **Docker** | Fully containerized — runs with a single command |

---

## 🏗 Architecture

The project follows a **Contract-First** design with SOLID principles throughout:

- **Pub-Sub (Events/Listeners):** Decouples message ingestion (`NotificationController`) from notification delivery (`SendNotificationToSubscribers` listener).
- **Strategy Pattern:** Each channel (`SMS`, `E-Mail`, `Push Notification`) has its own provider class implementing `NotificationProviderInterface`. Adding a new channel = one new class + one line in `NotificationServiceProvider`.
- **Repository Pattern:** `UserRepositoryInterface` and `NotificationLogRepositoryInterface` abstract all data access from domain logic.
- **Queue Jobs:** `SendProviderNotificationJob` handles each user/channel delivery atomically in the background, with independent retry state.
- **DTOs & Interfaces:** `NotificationData` enforces strict contracts between layers.

See [docs/architecture.md](docs/architecture.md) for a detailed flow diagram and ER model.

---

## 🛠 Setup Instructions

### Prerequisites
- Docker & OrbStack (or Docker Desktop)
- `make` (optional, but recommended)

### First-Time Setup

```bash
# 1. Clone and enter the project
git clone <repo-url> escoffienews && cd escoffienews

# 2. Copy environment file
cp .env.example .env

# 3. Full setup: build images, install deps, generate key, migrate & seed
make setup
```

> The `.env.example` is pre-configured with all defaults needed to run locally. The Reverb keys are intentionally included (they are local-only random strings with no external service attached).

### Starting the App (After Setup)

```bash
# Start all containers (app, web, reverb, worker, db, redis, frontend)
make up

# Or with rebuild:
docker-compose up -d --build
```

| Service | URL |
|---|---|
| **Frontend (React)** | http://localhost:3000 |
| **Backend API** | http://localhost:8000 |
| **WebSocket (Reverb)** | ws://localhost:8080 |

---

## 🛠 Makefile Commands

For convenience, a `Makefile` is included to simplify common tasks:

| Command | Description |
|---|---|
| `make setup` | Full initial setup (env, build, install, migrate, seed) |
| `make up` | Start all services in the background |
| `make down` | Stop and remove all containers |
| `make restart` | Restart all services |
| `make build` | Rebuild all docker images |
| `make test` | Run all backend (PHPUnit) and frontend (Vitest) tests |
| `make migrate` | Run database migrations |
| `make seed` | Run database seeders |
| `make shell` | Open a shell inside the `app` container |

---

## 🧪 Running the Test Suite

```bash
# Run all tests (backend + frontend)
make test

# Backend only
docker-compose exec app php artisan test

# Frontend only
docker-compose exec frontend npm test
```

**Current Coverage:** 41 backend tests (111 assertions) · 17 frontend tests — all passing.

---

## 🐒 Chaos Monkey — Fault Tolerance Demo

The **Chaos Monkey** is the recommended way for reviewers to observe the system's fault-tolerance and retry infrastructure in action.

### What It Does

When Chaos Monkey mode is enabled, each background delivery job has a **30% chance of throwing a simulated exception**. The queue worker catches this, marks the job as failed, and automatically retries it after a backoff delay — exactly as it would in production when a real provider (Twilio, SendGrid, etc.) is temporarily unavailable.

### How to Use It

1. **Open** [http://localhost:3000](http://localhost:3000).
2. In the **Send Notification** panel, select a **Category** and type a **Message**.
3. Toggle the **🐒 Chaos Monkey Mode** switch to ON (it turns red).
4. Click **Send Notification**.

### What to Observe

Watch the **System Trace terminal** (bottom left) in real time:

```
[INFO]  Found 5 subscribers for Finance. Queuing jobs...
[INFO]  Job queued: [SMS] for Alice.
[INFO]  Job queued: [E-Mail] for Alice.
[INFO]  Job queued: [SMS] for Bob.
...

(5–10 seconds later, as the queue worker processes each job)

[INFO]  Delivered [E-Mail] to Alice.
[ERROR] Chaos Monkey intercepted [SMS] for Alice. Will retry...

(5 seconds later — first retry)

[INFO]  Retrying [SMS] for Alice (attempt #2)...
[INFO]  Delivered [SMS] for Alice.

(If all 3 attempts fail)

[ERROR] PERMANENT FAILURE after 3 attempts: [Push Notification] for Bob.
```

> **Note:** Because delivery is asynchronous, you will see the "jobs queued" messages appear immediately, and the actual delivery/failure logs will stream in over the following seconds as the queue worker processes them. This is expected and correct behaviour.

### Key Observations

- **Non-blocking:** The API returns `200 OK` as soon as jobs are queued. The HTTP request does not wait for delivery.
- **Isolation:** A failure for one user/channel does not affect other jobs in the queue.
- **Retries:** Each failed job is retried up to **3 times** with backoff delays of **5s → 10s → 20s**.
- **Permanent Failure:** If all retries are exhausted, a final `PERMANENT FAILURE` error is broadcast to the terminal.
- **Toast Notifications:** In addition to the terminal, a red toast popup appears in the top-center for each failure, giving instant visibility without needing to watch the terminal.

---

## 📡 API Reference

### `POST /api/notifications`
Dispatch a notification to all users subscribed to a category.

```json
{
  "category": "Finance",
  "message": "Markets closed higher today.",
  "chaos_monkey": false
}
```

### `GET /api/logs`
Returns all notification delivery logs, sorted newest-to-oldest.

### `GET /api/categories`
Returns all available categories (seeded from the database).

### `POST /api/users`
Create a new user with category subscriptions and channel preferences.

```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "phone": "+1234567890",
  "categories": ["Sports", "Finance"],
  "channels": ["SMS", "E-Mail"]
}
```

### `GET /api/users`
Returns all registered users.

---

## 📄 License
MIT
