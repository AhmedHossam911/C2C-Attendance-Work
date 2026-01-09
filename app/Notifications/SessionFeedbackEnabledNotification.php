<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SessionFeedbackEnabledNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public \App\Models\AttendanceSession $session) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'session_feedback_enabled',
            'session_id' => $this->session->id,
            'title' => $this->session->title,
            'message' => 'Feedback is now enabled for session: ' . $this->session->title,
            'url' => route('sessions.show', $this->session->id), // or wherever feedback is submitted
        ];
    }
}
