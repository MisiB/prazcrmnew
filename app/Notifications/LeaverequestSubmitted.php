<?php

namespace App\Notifications;

use App\Interfaces\ileaverequestapprovalInterface;
use App\Interfaces\ileavetypeInterface;
use App\Models\Leaverequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaverequestSubmitted extends Notification
{
    use Queueable;

    private $leaverequest, $leaverequestuuid, $approvalrecordid, $approverid;
    protected $leavetyperepo, $leaverequestapprovalrepo;
    /**
     * Create a new notification instance.
     */
    public function __construct(Leaverequest $leaverequest, ileavetypeInterface $leavetyperepo, ileaverequestapprovalInterface $leaverequestapprovalrepo)
    {
        $this->leaverequest = $leaverequest;
        $this->leavetyperepo=$leavetyperepo;
        $this->leaverequestapprovalrepo=$leaverequestapprovalrepo;
        $this->leaverequestuuid=$this->leaverequest->leaverequestuuid;
        $approvalrecord=$leaverequestapprovalrepo->getleaverequestapproval($this->leaverequestuuid);
        $this->approvalrecordid=$approvalrecord->leaverequest_uuid;
        $this->approverid=$approvalrecord->user_id;
    }
 
    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        //approval url
        $finalizationurl=url('/admin/'.$this->approvalrecordid.'/'.$this->leaverequestuuid.'/'.$this->approverid);
        $leavetype=$this->leavetyperepo->getleavetype($this->leaverequest->leavetype_id);
        return (new MailMessage)
            ->success()
            ->greeting('Good day from PRAZ')
            ->subject('RE: LEAVE REQUEST SUBMISSION')
            ->line('')
            ->line('A new '.$leavetype->name.' leave request has been submitted by '.$this->leaverequest->user->firstname.' '.$this->leaverequest->user->surname)
            ->line('')
            ->action('Make decision', $finalizationurl)
            ->line('Thank you for using our application, we are here to serve!')
            ->line('')
            ->line('Regards')
            ->line('PRAZ (ICT)');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [];
    }
}
