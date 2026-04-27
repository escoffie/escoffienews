import { render, screen, fireEvent, waitFor, act } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { LoginPage } from '../components/LoginPage';

describe('LoginPage', () => {
    const mockOnLogin = vi.fn();

    beforeEach(() => {
        vi.clearAllMocks();
        localStorage.clear();
    });

    it('renders the login form', () => {
        render(<LoginPage onLogin={mockOnLogin} />);
        expect(screen.getByText(/Administrator Access Required/i)).toBeInTheDocument();
        expect(screen.getByPlaceholderText(/Enter your security token/i)).toBeInTheDocument();
    });

    it('shows error message for invalid token', async () => {
        render(<LoginPage onLogin={mockOnLogin} />);
        
        const input = screen.getByPlaceholderText(/Enter your security token/i);
        const button = screen.getByRole('button', { name: /Access Dashboard/i });

        fireEvent.change(input, { target: { value: 'wrong-token' } });
        fireEvent.click(button);

        await waitFor(() => {
            expect(screen.getByText(/Invalid administrator token/i)).toBeInTheDocument();
        });
        expect(mockOnLogin).not.toHaveBeenCalled();
    });

    it('successfully logs in with correct token', async () => {
        render(<LoginPage onLogin={mockOnLogin} />);
        
        const input = screen.getByPlaceholderText(/Enter your security token/i);
        const button = screen.getByRole('button', { name: /Access Dashboard/i });

        fireEvent.change(input, { target: { value: 'escoffie_secret_2026' } });
        fireEvent.click(button);

        await waitFor(() => {
            expect(localStorage.getItem('admin_token')).toBe('escoffie_secret_2026');
            expect(mockOnLogin).toHaveBeenCalled();
        }, { timeout: 2000 });
    });
});
