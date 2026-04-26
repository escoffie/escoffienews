import { render, screen } from '@testing-library/react';
import { describe, it, expect } from 'vitest';
import { SystemTerminal } from '../components/SystemTerminal';

describe('SystemTerminal', () => {
    it('renders and shows waiting message when empty', () => {
        render(<SystemTerminal />);
        
        expect(screen.getByText(/System Trace/i)).toBeInTheDocument();
        expect(screen.getByText(/Waiting for system events/i)).toBeInTheDocument();
    });
});
