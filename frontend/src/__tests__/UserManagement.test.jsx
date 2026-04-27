import { render, screen, fireEvent, waitFor, act } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { UserManagement } from '../components/UserManagement';
import api from '../lib/api';

const MOCK_USERS = [
    { id: 1, name: 'Alice' },
    { id: 2, name: 'Bob' },
];

describe('UserManagement', () => {
    beforeEach(() => {
        vi.clearAllMocks();
        api.get.mockResolvedValue({ data: MOCK_USERS });
    });

    it('renders the title and form elements', async () => {
        render(<UserManagement />);

        // Wait for the initial api.get('/users') to settle
        await waitFor(() => {
            expect(screen.getByText(/Simple New User Request/i)).toBeInTheDocument();
        });

        expect(screen.getByText(/POST \/api\/users/i)).toBeInTheDocument();
        expect(screen.getByRole('button', { name: /Send Request/i })).toBeInTheDocument();
    });

    it('fetches and displays current users on mount', async () => {
        render(<UserManagement />);

        await waitFor(() => {
            expect(screen.getByText('Alice')).toBeInTheDocument();
            expect(screen.getByText('Bob')).toBeInTheDocument();
        });

        expect(api.get).toHaveBeenCalledWith('/users');
    });

    it('shows user count', async () => {
        render(<UserManagement />);

        await waitFor(() => {
            expect(screen.getByText(/Current Users \(2\)/i)).toBeInTheDocument();
        });
    });

    it('shows success message after successful form submission', async () => {
        api.post.mockResolvedValueOnce({ data: { id: 3, name: 'Jane' } });
        api.get.mockResolvedValue({ data: [...MOCK_USERS, { id: 3, name: 'Jane' }] });

        render(<UserManagement />);

        fireEvent.click(screen.getByRole('button', { name: /Send Request/i }));

        await waitFor(() => {
            expect(screen.getByText(/User created successfully/i)).toBeInTheDocument();
        });

        expect(api.post).toHaveBeenCalledWith('/users', expect.any(Object));
    });

    it('shows error on invalid JSON payload', async () => {
        render(<UserManagement />);

        // Clear textarea and type invalid JSON
        const textarea = screen.getByPlaceholderText(/Enter JSON here/i);
        fireEvent.change(textarea, { target: { value: '{ invalid json' } });
        fireEvent.click(screen.getByRole('button', { name: /Send Request/i }));

        await waitFor(() => {
            expect(screen.getByText(/Invalid JSON format/i)).toBeInTheDocument();
        });

        expect(api.post).not.toHaveBeenCalled();
    });

    it('shows API error message on failed submission', async () => {
        api.post.mockRejectedValueOnce({
            response: { data: { message: 'Email already taken.' } }
        });

        render(<UserManagement />);

        fireEvent.click(screen.getByRole('button', { name: /Send Request/i }));

        await waitFor(() => {
            expect(screen.getByText(/Email already taken/i)).toBeInTheDocument();
        });
    });
});
