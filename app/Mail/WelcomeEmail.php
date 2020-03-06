<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $email;
    public $otp;
    public $test_message;
    public $data;
    public function __construct($data)
    {
        //
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {        
        $subject = 'Welcome to Intoactive';
        $address = config('constants.ADMIN_EMAIL');
        $name = config('constants.ADMIN_NAME');
        
        return $this->view('email.welcomeEmail')
                    ->from($address, $name)
//                    ->cc($address, $name)
//                    ->bcc($address, $name)
//                    ->replyTo($address, $name)
                    ->subject($subject);
    
    }
}
