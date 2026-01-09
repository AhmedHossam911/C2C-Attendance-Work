<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LateSubmissionNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public \App\Models\TaskSubmission $submission) {}

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
            'type' => 'late_submission',
            'task_id' => $this->submission->task_id,
            'title' => $this->submission->task->title,
            'user' => $this->submission->user->name,
            'message' => 'Late submission by ' . $this->submission->user->name . ' for task: ' . $this->submission->task->title,
            'url' => route('tasks.show', $this->submission->task_id),
        ];
    }
}
