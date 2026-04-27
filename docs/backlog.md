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
- [x] [DEV-201] Database Migrations (Users, Categories, Channels, NotificationLogs).
- [x] [DEV-202] Seeders for Catalog Data (Mock Users & Categories).
- [x] [DEV-203] Define Domain Interfaces & DTOs (The "Contract").

## Phase 3: Core Notification Logic (The Strategy & Pub-Sub) [DEV-003]
- [x] [DEV-301] Implement `MessageReceived` Event & `SendNotifications` Listener.
- [x] [DEV-302] Implement Notification Strategy Pattern (Abstract + SMS/Email/Push).
- [x] [DEV-303] Implement Repository Pattern for User & Log Persistence.
- [x] [DEV-304] Implement Notification Service (Orchestrator).

## Phase 4: API & Real-time Integration [DEV-004]
- [x] [DEV-401] API Endpoint: POST `/api/notifications` (Message Submission).
- [x] [DEV-402] API Endpoint: GET `/api/logs` (History Retrieval).
- [x] [DEV-403] Configure Laravel Reverb for WebSocket Broadcasting.

## Phase 5: Frontend Experience [DEV-005]
- [x] [DEV-501] UI: Message Submission Form (Validations included).
- [x] [DEV-502] UI: Real-time Log Table (WebSocket integration).
- [x] [DEV-503] UI: Design Polish & Responsiveness.
- [x] [DEV-504] UI: "Terminal" Log Console for raw system output.

## Phase 6: Quality Assurance & Delivery [DEV-006]
- [x] [DEV-601] Unit Tests for Services & Strategies.
- [x] [DEV-602] Feature Tests for API Endpoints.
- [x] [DEV-603] Frontend Component Testing (Vitest + RTL).
- [x] [DEV-604] Documentation (README, Architectural Decisions, Traceability).
- [x] [DEV-605] Observability: Batch grouping, retry tracking, and failure logging.

## Phase 7: Bonus - Security & Polish [DEV-007]
- [x] [DEV-701] Simple Admin Authentication layer.
