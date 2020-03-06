<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserOtp extends Mailable
{
    use Queueable, SerializesModels;
   /**
     * Create a new message instance.
     *
     * @return void
     */
     public $data;
    public function __construct($data)
    { 
        $this->data = $data;
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
        return $this->view('email.userOtp')
                    ->from($address, $name)
//                    ->cc($address, $name)
//                    ->bcc($address, $name)
//                    ->replyTo($address, $name)
                    ->subject($this->data['subject']);
    }
}
