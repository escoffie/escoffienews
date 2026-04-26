# LoanPro Notification System

A high-performance, scalable notification routing system built with Laravel 11, React, and WebSockets.

## 🚀 Overview

This system allows sending messages to specific categories (Sports, Finance, Movies) and automatically routes them to subscribed users via their preferred channels (SMS, Email, Push).

### Core Features
- **Real-time Updates:** Uses Laravel Reverb (WebSockets) to update the log history instantly.
- **Clean Architecture:** Strict separation of concerns using Service, Repository, and Strategy patterns.
- **Robust Testing:** High coverage with unit and feature tests.
- **Dockerized:** Fully containerized for easy setup and review.

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

### Quick Start
1. **Clone the repository:**
   ```bash
   git clone <repo-url>
   cd LoanPro
   ```

2. **Initialize the environment:**
   ```bash
   make setup
   ```
   *This will build the containers, install dependencies, run migrations, and seed the database.*

3. **Start the application:**
   ```bash
   make up
   ```

4. **Access the application:**
   - **Frontend:** [http://localhost:3000](http://localhost:3000)
   - **API:** [http://localhost:8000](http://localhost:8000)

## 🧪 Running Tests
```bash
make test
```

## 📄 License
MIT
