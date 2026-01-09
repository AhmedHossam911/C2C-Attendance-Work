<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskReviewedNotification extends Notification
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
            'type' => 'task_reviewed',
            'task_id' => $this->submission->task_id,
            'title' => $this->submission->task->title,
            'status' => $this->submission->status,
            'grade' => $this->submission->grade, // or rating
            'message' => 'Your submission for ' . $this->submission->task->title . ' has been reviewed.',
            'url' => route('tasks.show', $this->submission->task_id),
        ];
    }
}
