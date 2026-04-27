<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReceived
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public string $category,
        public string $message,
        public bool $chaosMonkey = false,
        public string $batchId = ''
    ) {
        if (empty($this->batchId)) {
            $this->batchId = (string) \Illuminate\Support\Str::uuid();
        }
    }
}
