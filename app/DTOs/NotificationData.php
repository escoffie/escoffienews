<?php

namespace App\DTOs;

readonly class NotificationData
{
    public function __construct(
        public string $batchId,
        public int $userId,
        public string $userName,
        public string $userEmail,
        public string $category,
        public string $channel,
        public string $message,
        public ?string $userPhone = null,
    ) {}

    /**
     * Create a DTO from an array or model if needed.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            batchId: $data['batch_id'],
            userId: $data['user_id'],
            userName: $data['user_name'],
            userEmail: $data['user_email'],
            category: $data['category'],
            channel: $data['channel'],
            message: $data['message'],
            userPhone: $data['user_phone'] ?? null,
        );
    }
}
