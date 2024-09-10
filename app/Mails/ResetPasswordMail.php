<?php

namespace App\Mails;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    private $newPassword;
    private $email;
    public function __construct($newPassword,$email)
    {
        $this->email=$email;
        $this->newPassword = $newPassword;
    }

    public function build()
    {
        return $this->view('emails.auth.reset-password')
            ->with(['newPassword' => $this->newPassword , "email"=> $this->email]);
    }
}
