<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdminMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user_name;
    public $query;
    public $meassge;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
   
    public function __construct($data)
    {        
        $this->user_name = $data['uid'];  
        $this->query = $data['queryType']; 
        $this->meassge = $data['message'];
        $this->subject = $data['subject'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $address = config('constants.ADMIN_EMAIL');
        $name = config('constants.ADMIN_NAME');
        return $this->view('email.contactadmin')
                    ->from($address, $name)
                    ->cc($address, $name)
                    ->bcc($address, $name)
                    ->replyTo($address, $name)
                    ->subject($this->subject)
                    ->with([ 'meassge' => $this->meassge, 'query' => $this->query, 'subject' => $this->subject, 'user_name' => $this->user_name]);
    }
}
