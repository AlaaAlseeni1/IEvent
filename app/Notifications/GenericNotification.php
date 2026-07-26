<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class GenericNotification extends Notification
{
    public function __construct(
        public string $message,
        public ?string $url = null,
        public string $title = 'إشعار',
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'generic',
            'title'   => $this->title,
            'message' => $this->message,
            'url'     => $this->url,
        ];
    }
}
