<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\NotificationTemplate;
use App\Constants\NotificationMessages;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    /**
     * Create a notification for user or admin.
     */
    public function create(string $type, ?int $userId = null, array $data = [], int $is_admin = 0)
    {
        // ✅ 1️⃣ Load from cache to avoid repeated queries
        $template = Cache::remember("notification.template.{$type}", 60, function () use ($type) {
            return NotificationTemplate::where('key', $type)->first();
        });

        // ✅ 2️⃣ Fallback to constant if template not found
        if (!$template) {
            $templateData = NotificationMessages::MESSAGES[$type] ?? null;

            if (!$templateData) {
                throw new \InvalidArgumentException("Notification type '{$type}' not found.");
            }

            $template = (object) $templateData;
        }

        // ✅ 3️⃣ Replace placeholders
        $title = $this->replacePlaceholders($template->title ?? '', $data);
        $message = $this->replacePlaceholders($template->message ?? '', $data);

        // ✅ 4️⃣ Only store route name (not full URL)
        $routeName = $template->route ?? null;

        // ✅ 5️⃣ Save notification
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'route' => $routeName,
            'is_for_admin' => $is_admin,
        ]);
    }

    /**
     * Replace :placeholders in text using provided data.
     */
    private function replacePlaceholders(string $text, array $data): string
    {
        foreach ($data as $key => $value) {
            $text = str_replace(':' . $key, $value, $text);
        }
        return $text;
    }
}
