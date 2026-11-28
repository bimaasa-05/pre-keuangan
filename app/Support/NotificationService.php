<?php

namespace App\Support;

use App\Models\User;
use App\Notifications\ActivityNotification;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Send an in-app notification to the actor and any extra recipients.
     *
     * @param  iterable<User>  $extraRecipients
     */
    public static function send(
        User $actor,
        string $title,
        string $message,
        string $icon = 'bell',
        string $tone = 'neutral',
        ?string $url = null,
        iterable $extraRecipients = [],
    ): void {
        $recipients = collect([$actor])
            ->merge($extraRecipients)
            ->filter(fn ($recipient) => $recipient instanceof User)
            ->unique('id');

        foreach ($recipients as $recipient) {
            $recipient->notify(new ActivityNotification([
                'title' => $title,
                'message' => $message,
                'icon' => $icon,
                'tone' => $tone,
                'url' => $url,
                'actor' => $actor->name,
                'created_at' => now()->toIso8601String(),
            ]));
        }
    }

    /**
     * Super Admin recipients for account, landing, and help-center changes.
     *
     * @return Collection<int, User>
     */
    public static function superAdmins(): Collection
    {
        return User::query()
            ->where('role', 'super_admin')
            ->get();
    }
}
