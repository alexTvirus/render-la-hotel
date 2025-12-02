<?php

namespace App\Providers;

use Dacastro4\LaravelGmail\Services\Message\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use App\Providers\GmailServiceExtend;

class GmailServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('gmailProvider', function ($app) {
			
			$mail = new Mail;
			$mail->using("ya29.a0ATi6K2vI6mCObYV3PDIUzWJdAB9aX-EqBxEWp0UEAOf17Nok_B-gE0izvPUL7u87UChFox4eaxdKY6HfPnQDAr1k94dW3wgEj4sdsiId1R227IJTmdCsfPB7b1vedCmIE4JMVtvxBNQ1v7X8ky_vCZdbD8VtRvVLkU5-A82jsmDUR2p4fRC0ag-tFK2nuLZ7Av-z6SUaCgYKAW8SARYSFQHGX2MiG2Ed7lh8ue40JzdvgP0Ajg0206");
			$mail->refreshToken("1//0gtWyM7JJEZ5kCgYIARAAGBASNwF-L9IrbWazxcrY6l6sj_-tPtQykO8rapZY4MqKv1dgqmH2MmwKuiYozsWLBKsJBcMjrA8_Gxk");
			$mail->from('lisatthu35@gmail.com');
			$mail->subject( "noreply-mail" );
			
            return $mail;
        });
		


    }
}
