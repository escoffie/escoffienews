<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SystemLogBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $level,
        public string $message,
        public array $context = []
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('system-logs'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'system.log';
    }
}
