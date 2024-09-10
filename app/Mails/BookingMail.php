<?php

namespace App\Mails;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class BookingMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    private $booking;
    private $payment;
    public function __construct($booking,$payment)
    {
        $this->booking=$booking;
        $this->payment=$payment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [
            "name" => "test",
            "code" => "123",
            "note" => "note",
            "items" => [
                [
                    "key"=>"item1",
                    "value"=>"value1"
                ],
                [
                    "key"=>"item2",
                    "value"=>"value2"
                ]
            ]
        ];

        $generalRegulation['note_email'] = "note_email";

        // tự code header vào footer và tự css
        return $this->markdown('emails.bookings.detail_all')
            ->subject('Booking confirm ')
            ->with([
                'booking' => $this->booking,
                'payment' => $this->payment,
            ]);
    }
}
