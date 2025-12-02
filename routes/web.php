<?php

use App\Mails\ResetPasswordMail;
use App\Services\ComicServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransportFactory;
use Symfony\Component\Mime\Email;
use Dacastro4\LaravelGmail\Services\Message\Mail as Mail1;
use App\Mails\VerifyMail;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/oauth/gmail', function (){
    return LaravelGmail::redirect();
});

Route::get('/oauth2', function (){
    LaravelGmail::makeToken();
    return redirect()->to('/');
});

Route::get('/oauth/gmail/logout', function (){
    LaravelGmail::logout(); //It returns exception if fails
    return redirect()->to('/');
});

Route::get('test22', function (){
    $mail = new Mail1;
	$mail->using("ya29.a0ATi6K2vI6mCObYV3PDIUzWJdAB9aX-EqBxEWp0UEAOf17Nok_B-gE0izvPUL7u87UChFox4eaxdKY6HfPnQDAr1k94dW3wgEj4sdsiId1R227IJTmdCsfPB7b1vedCmIE4JMVtvxBNQ1v7X8ky_vCZdbD8VtRvVLkU5-A82jsmDUR2p4fRC0ag-tFK2nuLZ7Av-z6SUaCgYKAW8SARYSFQHGX2MiG2Ed7lh8ue40JzdvgP0Ajg0206");
	$mail->refreshToken("1//0gtWyM7JJEZ5kCgYIARAAGBASNwF-L9IrbWazxcrY6l6sj_-tPtQykO8rapZY4MqKv1dgqmH2MmwKuiYozsWLBKsJBcMjrA8_Gxk");
	$mailableInstance = new VerifyMail("tét");
	$mail->to('proxywindert9@gmail.com');
	$mail->from('lisatthu35@gmail.com');
	$mail->subject( "sub" );
	$mail->message($mailableInstance->render());
	$mail->send();
    return "ok";
});

Route::get('test33', function (){
     $mail = app()->make('gmailProvider');
	 
	$mailableInstance = new VerifyMail("tét");
	$mail->to('proxywindert9@gmail.com');
	$mail->message($mailableInstance->render());
	$mail->send();
    return "ok";
});

Route::post('/github-webhook', function () {
    return "ok";
});


Route::get('/test11', function () {
    $mailConfig = config('mail');
    $mailConfig['mailers']['smtp']['transport'] = 'smtp';
    $mailConfig['mailers']['smtp']['host'] = 'smtp.gmail.com';
    $mailConfig['mailers']['smtp']['port'] = 587;
    $mailConfig['mailers']['smtp']['encryption'] = 'tls';
    $mailConfig['mailers']['smtp']['username'] = 'proxywindert9@gmail.com';
    $mailConfig['mailers']['smtp']['password'] ='nhbmwenmehvcxnfo';
    config(['mail' => $mailConfig]);



    $mailer = new \Symfony\Component\Mailer\Mailer((new EsmtpTransportFactory)
        ->create(new Dsn('smtp', 'smtp.gmail.com', 'proxywindert9@gmail.com', 'nhbmwenmehvcxnfo', 587)));


    $email = (new Email())
        ->from('proxywindert9@gmail.com')
        ->to('proxywindert10@gmail.com')
        ->subject('Email from Laravel')
        ->text('Sending emails through Symfony components in Laravel.');
    try {
        $mailer->send($email);
        echo 'Email sent successfully';
    } catch (TransportExceptionInterface $e) {
        echo 'Failed to send email: '.$e->getMessage();
    }

    dd($mailConfig);

    $giaTriX = request()->query('x');
    $duongDanFile = 'file.txt'; // Thay thế bằng đường dẫn thực tế đến file của bạn
    $noiDung = 'Đây là nội dung chuỗi mà tôi muốn ghi vào file.';

    try {
        File::put($duongDanFile, $giaTriX);
        echo 'Đã ghi thành công vào file.';
    } catch (\Exception $e) {
        echo 'Có lỗi xảy ra khi ghi file: ' . $e->getMessage();
    }
})->name('test11');
