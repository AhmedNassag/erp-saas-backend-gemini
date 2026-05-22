<?php

namespace App\Traits;

use App\Services\FirebaseNotificationService;

trait HasNotifications
{
    public function sendNotification(
        $users,
        string $title,
        string $body,
        array $extraData = [],
        ?int $senderId = null
    ) {
        return app(FirebaseNotificationService::class)->send(
            users: $users,
            title: $title,
            body: $body,

            model: $this,

            data: $extraData,

            senderId: $senderId
        );
    }

    public function notifications()
    {
        return $this->morphMany(
            \App\Models\Notification::class,
            'model'
        );
    }
}
