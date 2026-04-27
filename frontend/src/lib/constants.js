/**
 * Centralized constants for the EscoffieNews Frontend.
 * This helps avoid magic numbers and strings across components.
 */

export const NOTIFICATION_STATUS = {
    SENT: 'sent',
    FAILED: 'failed',
};

export const UI_LIMITS = {
    LOG_HISTORY_CAP: 100,
    SYSTEM_TRACE_CAP: 100,
    USER_CHIPS_PREVIEW: 10,
};

export const TIMEOUTS = {
    FORM_RESET_MS: 3000,
    AUTH_SIMULATION_MS: 800,
};

export const CHANNELS = {
    SMS: 'SMS',
    EMAIL: 'E-Mail',
    PUSH: 'Push Notification',
};
