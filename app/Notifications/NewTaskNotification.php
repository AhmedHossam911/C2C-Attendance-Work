<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTaskNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public \App\Models\Task $task) {}

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
            'type' => 'new_task',
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'committee' => $this->task->committee->name,
            'deadline' => $this->task->deadline,
            'message' => 'New Task posted: ' . $this->task->title . ' (' . $this->task->committee->name . ')',
            'url' => route('tasks.show', $this->task->id),
        ];
    }
}
