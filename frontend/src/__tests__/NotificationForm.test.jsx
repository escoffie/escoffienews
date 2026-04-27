import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import { describe, it, expect, vi } from 'vitest';
import { NotificationForm } from '../components/NotificationForm';
import api from '../lib/api';

describe('NotificationForm', () => {
    beforeEach(() => {
        api.get.mockResolvedValue({ 
            data: [
                { id: 1, name: 'Sports' },
                { id: 2, name: 'Finance' },
                { id: 3, name: 'Movies' }
            ] 
        });
    });

    it('renders category selection and message textarea', async () => {
        render(<NotificationForm />);
        
        await waitFor(() => {
            expect(screen.getByText(/Select Category/i)).toBeInTheDocument();
            expect(screen.getByText('Sports')).toBeInTheDocument();
        });
        
        expect(screen.getByPlaceholderText(/Enter the notification message here/i)).toBeInTheDocument();
        expect(screen.getByRole('button', { name: /Send Notification/i })).toBeInTheDocument();
    });

    it('submits form when all fields are filled', async () => {
        api.post.mockResolvedValueOnce({ data: { message: 'Success' } });
        
        render(<NotificationForm />);
        
        // Wait for categories
        await waitFor(() => {
            expect(screen.getByText('Sports')).toBeInTheDocument();
        });

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
                message: 'Test Message',
                chaos_monkey: false
            });
            expect(screen.getByText(/Notification dispatched successfully/i)).toBeInTheDocument();
        });
    });

    it('shows error message on API failure', async () => {
        api.post.mockRejectedValueOnce({ 
            response: { data: { message: 'Validation Error' } } 
        });
        
        render(<NotificationForm />);
        
        // Wait for categories
        await waitFor(() => {
            expect(screen.getByText('Finance')).toBeInTheDocument();
        });

        fireEvent.click(screen.getByText('Finance'));
        fireEvent.change(screen.getByPlaceholderText(/Enter the notification message here/i), {
            target: { value: 'Error Test' }
        });
        
        fireEvent.click(screen.getByRole('button', { name: /Send Notification/i }));
        
        await waitFor(() => {
            expect(screen.getByText(/Validation Error/i)).toBeInTheDocument();
        });
    });

    it('submits with chaos_monkey: true when toggle is enabled', async () => {
        api.post.mockResolvedValueOnce({ data: { message: 'Success' } });

        render(<NotificationForm />);

        await waitFor(() => {
            expect(screen.getByText('Sports')).toBeInTheDocument();
        });

        // Select category and type message
        fireEvent.click(screen.getByText('Sports'));
        fireEvent.change(screen.getByPlaceholderText(/Enter the notification message here/i), {
            target: { value: 'Chaos test' }
        });

        // Toggle Chaos Monkey ON (the button inside the toggle row)
        const chaosToggle = screen.getByRole('button', { name: '' });
        fireEvent.click(chaosToggle);

        // Submit
        fireEvent.click(screen.getByRole('button', { name: /Send Notification/i }));

        await waitFor(() => {
            expect(api.post).toHaveBeenCalledWith('/notifications', {
                category: 'Sports',
                message: 'Chaos test',
                chaos_monkey: true,
            });
        });
    });
});
