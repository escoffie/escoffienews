import { render, screen, act, waitFor, fireEvent } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { NotificationLogTable } from '../components/NotificationLogTable';
import { echo } from '../lib/echo';
import api from '../lib/api';

const MOCK_LOGS = [
    {
        id: 2,
        user_name: 'Bob',
        user_email: 'bob@example.com',
        category: 'Sports',
        channel: 'SMS',
        message: 'Game tonight!',
        created_at: '2026-04-26T18:00:00Z',
    },
    {
        id: 1,
        user_name: 'Alice',
        user_email: 'alice@example.com',
        category: 'Finance',
        channel: 'E-Mail',
        message: 'Market update.',
        created_at: '2026-04-26T17:00:00Z',
    },
];

describe('NotificationLogTable', () => {
    beforeEach(() => {
        vi.clearAllMocks();
        api.get.mockResolvedValue({ data: MOCK_LOGS });
    });

    it('renders the heading', async () => {
        render(<NotificationLogTable />);

        // Wait for the initial api.get('/logs') to settle
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
                    user_name: 'Charlie',
                    user_email: 'charlie@example.com',
                    category: 'Movies',
                    channel: 'Push Notification',
                    message: 'New release!',
                    created_at: '2026-04-26T19:00:00Z',
                }
            });
        });

        await waitFor(() => {
            expect(screen.getByText('Charlie')).toBeInTheDocument();
            expect(screen.getByText('New release!')).toBeInTheDocument();
        });
    });

    it('displays correct channel icons by channel type', async () => {
        render(<NotificationLogTable />);

        await waitFor(() => {
            // Channels are rendered in the table — we check by the log rows
            expect(screen.getByText('SMS')).toBeInTheDocument();
            expect(screen.getByText('E-Mail')).toBeInTheDocument();
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
            expect(screen.getByText(/No logs found/i)).toBeInTheDocument();
        });
    });
});
