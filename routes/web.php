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
