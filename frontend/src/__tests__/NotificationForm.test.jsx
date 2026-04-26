import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import { describe, it, expect, vi } from 'vitest';
import { NotificationForm } from '../components/NotificationForm';
import api from '../lib/api';

describe('NotificationForm', () => {
    it('renders category selection and message textarea', () => {
        render(<NotificationForm />);
        
        expect(screen.getByText(/Select Category/i)).toBeInTheDocument();
        expect(screen.getByPlaceholderText(/Enter the notification message here/i)).toBeInTheDocument();
        expect(screen.getByRole('button', { name: /Send Notification/i })).toBeInTheDocument();
    });

    it('submits form when all fields are filled', async () => {
        api.post.mockResolvedValueOnce({ data: { message: 'Success' } });
        
        render(<NotificationForm />);
        
        // Select category
        fireEvent.click(screen.getByText('Sports'));
        
        // Type message
        fireEvent.change(screen.getByPlaceholderText(/Enter the notification message here/i), {
            target: { value: 'Test Message' }
        });
        
        // Submit
        fireEvent.click(screen.getByRole('button', { name: /Send Notification/i }));
        
        await waitFor(() => {
            expect(api.post).toHaveBeenCalledWith('/notifications', {
                category: 'Sports',
                message: 'Test Message'
            });
            expect(screen.getByText(/Notification dispatched successfully/i)).toBeInTheDocument();
        });
    });

    it('shows error message on API failure', async () => {
        api.post.mockRejectedValueOnce({ 
            response: { data: { message: 'Validation Error' } } 
        });
        
        render(<NotificationForm />);
        
        fireEvent.click(screen.getByText('Finance'));
        fireEvent.change(screen.getByPlaceholderText(/Enter the notification message here/i), {
            target: { value: 'Error Test' }
        });
        
        fireEvent.click(screen.getByRole('button', { name: /Send Notification/i }));
        
        await waitFor(() => {
            expect(screen.getByText(/Validation Error/i)).toBeInTheDocument();
        });
    });
});
