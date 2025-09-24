<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public $name;
    public $surname;
    public $taskId;
    public function __construct($taskId,$name,$surname=null)
    {
        $this->name=$name;
        $this->surname=$surname;
        $this->taskId=$taskId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database','mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->greeting('Good day'.$this->name.' '.$this->surname.'!')
                    ->line('An issue requiring your attention has been added to your tasks please login to you admin portal .')
                    ->action('View task', route("adminportal.issues.viewassignedissue",$this->taskId))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'taskId' => $this->taskId,
            'name' => $this->name,
            'surname' => $this->surname,
        ];
    }
    public function databaseType(object $notifiable): string
    {
        return 'task-assigned';
    }
}
