import { render, screen, act, waitFor, fireEvent } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { NotificationLogTable } from '../components/NotificationLogTable';
import { echo } from '../lib/echo';
import api from '../lib/api';

const MOCK_LOGS = [
    {
        id: 2,
        batch_id: 'batch-1',
        user_name: 'Bob',
        user_email: 'bob@example.com',
        category: 'Sports',
        channel: 'SMS',
        message: 'Game tonight!',
        created_at: '2026-04-26T18:00:00Z',
        attempts: 1,
        status: 'sent'
    },
    {
        id: 1,
        batch_id: 'batch-2',
        user_name: 'Alice',
        user_email: 'alice@example.com',
        category: 'Finance',
        channel: 'E-Mail',
        message: 'Market update.',
        created_at: '2026-04-26T17:00:00Z',
        attempts: 1,
        status: 'sent'
    },
];

describe('NotificationLogTable', () => {
    beforeEach(() => {
        vi.clearAllMocks();
        api.get.mockResolvedValue({ data: MOCK_LOGS });
    });

    it('renders the heading', async () => {
        render(<NotificationLogTable />);
        await waitFor(() => {
            expect(screen.getByText(/Notification History/i)).toBeInTheDocument();
        });
    });

    it('fetches and displays logs on mount', async () => {
        render(<NotificationLogTable />);
        await waitFor(() => {
            expect(screen.getByText('Bob')).toBeInTheDocument();
            expect(screen.getByText('Alice')).toBeInTheDocument();
            expect(screen.getByText('Game tonight!')).toBeInTheDocument();
        });
        expect(api.get).toHaveBeenCalledWith('/logs');
    });

    it('shows empty state when no logs exist', async () => {
        api.get.mockResolvedValue({ data: [] });
        render(<NotificationLogTable />);
        await waitFor(() => {
            expect(screen.getByText(/No logs found/i)).toBeInTheDocument();
        });
    });

    it('prepends new log when WebSocket event fires', async () => {
        let wsCallback;
        echo.listen.mockImplementation((event, cb) => {
            wsCallback = cb;
            return echo;
        });

        render(<NotificationLogTable />);
        await waitFor(() => {
            expect(screen.getByText('Bob')).toBeInTheDocument();
        });

        act(() => {
            wsCallback({
                log: {
                    id: 3,
                    batch_id: 'batch-3',
                    user_name: 'Charlie',
                    user_email: 'charlie@example.com',
                    category: 'Movies',
                    channel: 'Push Notification',
                    message: 'New release!',
                    created_at: '2026-04-26T19:00:00Z',
                    attempts: 1,
                    status: 'sent'
                }
            });
        });

        await waitFor(() => {
            expect(screen.getByText('Charlie')).toBeInTheDocument();
            expect(screen.getByText('New release!')).toBeInTheDocument();
        });
    });

    it('displays retry badge when attempts > 1', async () => {
        api.get.mockResolvedValue({ data: [
            { ...MOCK_LOGS[0], attempts: 2 }
        ]});
        render(<NotificationLogTable />);
        await waitFor(() => {
            expect(screen.getByText(/Delivered after 2 attempts/i)).toBeInTheDocument();
        });
    });

    it('displays failure badge and danger styling when status is failed', async () => {
        api.get.mockResolvedValue({ data: [
            { ...MOCK_LOGS[0], status: 'failed', attempts: 3 }
        ]});
        render(<NotificationLogTable />);
        await waitFor(() => {
            expect(screen.getByText(/Failed after 3 attempts/i)).toBeInTheDocument();
            const row = screen.getByRole('row', { name: /Bob/i });
            expect(row).toHaveClass('bg-red-500/10');
        });
    });

    it('alternates background classes based on batch_id grouping', async () => {
        api.get.mockResolvedValue({ data: [
            { ...MOCK_LOGS[0], id: 3, batch_id: 'batch-A' },
            { ...MOCK_LOGS[1], id: 2, batch_id: 'batch-A' },
            { ...MOCK_LOGS[0], id: 1, batch_id: 'batch-B' },
        ]});
        render(<NotificationLogTable />);
        await waitFor(() => {
            const rows = screen.getAllByRole('row').slice(1); // skip header
            expect(rows[0]).toHaveClass('bg-slate-900/40'); // Group 1 (even index in map cycle)
            expect(rows[1]).toHaveClass('bg-slate-900/40'); // Group 1
            expect(rows[2]).toHaveClass('bg-slate-800/60'); // Group 2 (toggled)
        });
    });

    it('clears logs when button is clicked', async () => {
        vi.spyOn(window, 'confirm').mockReturnValue(true);
        api.delete.mockResolvedValueOnce({ data: { message: 'Success' } });
        render(<NotificationLogTable />);
        await waitFor(() => {
            expect(screen.getByText('Bob')).toBeInTheDocument();
        });
        const clearButton = screen.getByRole('button', { name: /Clear History/i });
        fireEvent.click(clearButton);
        await waitFor(() => {
            expect(api.delete).toHaveBeenCalledWith('/logs');
            expect(screen.queryByText('Bob')).not.toBeInTheDocument();
        });
    });
});
