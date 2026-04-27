import { render, screen, act, fireEvent } from '@testing-library/react';
import { describe, it, expect, vi, beforeEach } from 'vitest';
import { SystemTerminal } from '../components/SystemTerminal';
import { echo } from '../lib/echo';

describe('SystemTerminal', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('renders and shows waiting message when empty', () => {
        render(<SystemTerminal />);
        
        expect(screen.getByText(/System Trace/i)).toBeInTheDocument();
        expect(screen.getByText(/Waiting for system events/i)).toBeInTheDocument();
    });

    it('caps logs at 100 items to prevent memory leaks', () => {
        let logCallback;
        echo.listen.mockImplementation((event, cb) => {
            logCallback = cb;
            return echo;
        });

        render(<SystemTerminal />);
        
        act(() => {
            for (let i = 0; i < 105; i++) {
                logCallback({ level: 'INFO', message: `Test message ${i}` });
            }
        });

        const logEntries = screen.getAllByText(/Test message/i);
        expect(logEntries).toHaveLength(100);
        expect(screen.getByText('Test message 5')).toBeInTheDocument();
        expect(screen.queryByText('Test message 4')).not.toBeInTheDocument();
    });

    it('clears logs when clear button is clicked', () => {
        let logCallback;
        echo.listen.mockImplementation((event, cb) => {
            logCallback = cb;
            return echo;
        });

        render(<SystemTerminal />);
        
        act(() => {
            logCallback({ level: 'INFO', message: 'First message' });
        });

        expect(screen.getByText('First message')).toBeInTheDocument();

        const clearButton = screen.getByRole('button', { name: /Clear/i });
        fireEvent.click(clearButton);

        expect(screen.queryByText('First message')).not.toBeInTheDocument();
        expect(screen.getByText(/Waiting for system events/i)).toBeInTheDocument();
    });
});
