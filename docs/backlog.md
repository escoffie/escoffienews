# Project Backlog & Task Tracking

This backlog follows a "Divide and Conquer" approach, organized by milestones. Each task is designed to represent a discrete piece of work.

## Legend
- [ ] Todo
- [/] In Progress
- [x] Done

---

## Phase 1: Foundation & Environment [DEV-001]
- [x] [DEV-101] Initialize Docker Environment (PHP 8.3, Nginx, MySQL/PostgreSQL, Reverb).
- [x] [DEV-102] Laravel 11 Skeleton Setup (API Mode).
- [x] [DEV-103] React (Vite) Skeleton Setup.
- [x] [DEV-104] Git Repository Structure & Initial Commit.

## Phase 2: Domain & Data Modeling [DEV-002]
- [ ] [DEV-201] Database Migrations (Users, Categories, Channels, NotificationLogs).
- [ ] [DEV-202] Seeders for Catalog Data (Mock Users & Categories).
- [ ] [DEV-203] Define Domain Interfaces & DTOs (The "Contract").

## Phase 3: Core Notification Logic (The Strategy & Pub-Sub) [DEV-003]
- [ ] [DEV-301] Implement `MessageReceived` Event & `SendNotifications` Listener.
- [ ] [DEV-302] Implement Notification Strategy Pattern (Abstract + SMS/Email/Push).
- [ ] [DEV-303] Implement Repository Pattern for User & Log Persistence.
- [ ] [DEV-304] Implement Notification Service (Orchestrator).

## Phase 4: API & Real-time Integration [DEV-004]
- [ ] [DEV-401] API Endpoint: POST `/api/notifications` (Message Submission).
- [ ] [DEV-402] API Endpoint: GET `/api/logs` (History Retrieval).
- [ ] [DEV-403] Configure Laravel Reverb for WebSocket Broadcasting.

## Phase 5: Frontend Experience [DEV-005]
- [ ] [DEV-501] UI: Message Submission Form (Validations included).
- [ ] [DEV-502] UI: Real-time Log Table (WebSocket integration).
- [ ] [DEV-503] UI: Design Polish & Responsiveness.

## Phase 6: Quality Assurance & Delivery [DEV-006]
- [ ] [DEV-601] Unit Tests for Services & Strategies.
- [ ] [DEV-602] Feature Tests for API Endpoints.
- [/] [DEV-603] Documentation (README, Architectural Decisions).
