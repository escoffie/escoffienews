import '@testing-library/jest-dom';
import { vi } from 'vitest';

// Mock Laravel Echo
vi.mock('../lib/echo', () => ({
    echo: {
        channel: vi.fn().mockReturnThis(),
        listen: vi.fn().mockReturnThis(),
        stopListening: vi.fn().mockReturnThis(),
    }
}));

// Mock Axios
vi.mock('../lib/api', () => ({
    default: {
        get: vi.fn(),
        post: vi.fn(),
        delete: vi.fn(),
    }
}));
