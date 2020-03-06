<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\WelcomeEmail;
use Mail;
class WelcomeEmailJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
      public $data;
    public $email;
    public $tries = 3;
//    public $timeout = 600;
    public function __construct($data,$email)
    {
        //
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
        //
         try { 
        Mail::to($this->email)->send(new WelcomeEmail($this->data));
        } catch (\Exception $ex) {
            dd($ex);
        }
    }
//      public function retryUntil()
//    {
//        return now()->addSeconds(100);
//    }
    
    public function failed(Exception $exception)
    {
        Log::error($exception);
    }
}
