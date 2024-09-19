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
    private $title;

    public function __construct($booking,$payment,$title)
    {
        $this->title=$title;
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
        // tự code header vào footer và tự css
        return $this->markdown('emails.bookings.detail_all')
            ->subject($this->title)
            ->with([
                'booking' => $this->booking,
                'payment' => $this->payment,
            ]);
    }
}
