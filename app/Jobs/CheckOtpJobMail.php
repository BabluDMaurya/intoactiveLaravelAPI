<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\UserOtp;
use Mail;

class CheckOtpJobMail implements ShouldQueue
{
   use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $data;
    public $email;
    public $tries = 3;
    public $timeout = 600;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data,$email)
    {
        $this->data = $data;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try { 
                $this->data['content'] = 'Please use this otp to activate your account';
                $this->data['subject'] = 'New Otp for verification of mail';
                Mail::to($this->email)->send(new UserOtp($this->data));
        } catch (\Exception $ex) {
            dd($ex);
        }
    }
    public function retryUntil()
    {
        return now()->addSeconds(100);
    }
    
    public function failed(Exception $exception)
    {
        Log::error($exception);
    }
}
