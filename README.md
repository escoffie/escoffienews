# EscoffieNews Notification System

A high-performance, scalable notification routing system built with Laravel 11, React, and WebSockets.

## 🚀 Overview

This system allows sending messages to specific categories (Sports, Finance, Movies) and automatically routes them to subscribed users via their preferred channels (SMS, Email, Push).

### Core Features
- **Laravel 11:** Core API with PHP 8.4.
- **React 19:** SPA with Vite and Tailwind CSS v4.
- **Laravel Reverb:** High-performance WebSocket server for real-time monitoring.
- **Design Patterns:** Strategy (Notification Providers), Pub-Sub (Event-driven orchestration), and Repository (Data Abstraction).
- **Docker:** Fully containerized environment using OrbStack/Docker Compose.

## 🏗 Architecture

The project follows a **Contract-First** approach with SOLID principles at its core:

- **Pub-Sub Pattern:** Employs Laravel Events/Listeners to decouple message ingestion from notification delivery.
- **Strategy Pattern:** Implements an extensible provider system for different notification channels.
- **Repository Pattern:** Abstracts data persistence to ensure the domain logic remains decoupled from the DB.
- **DTOs & Interfaces:** Ensures strict typing and clear boundaries between layers.

See the [Architecture & Data Models](docs/architecture.md) for a detailed ER diagram and flow description.

## 🛠 Setup Instructions

### Prerequisites
- Docker & OrbStack (or Docker Desktop)
- Make (optional, but recommended)

### 🚀 Running the App

1. **Start all services:**
   ```bash
   docker-compose up -d
   ```

2. **Access the Frontend:**
   Navigate to [http://localhost:3000](http://localhost:3000)

3. **Access the API:**
   The backend is available at [http://localhost:8000](http://localhost:8000)

4. **WebSockets (Real-time):**
   Reverb is running on [ws://localhost:8080](ws://localhost:8080)

### 🧪 Running Tests
```bash
docker-compose exec app php artisan test
```

## 📄 License
MIT
