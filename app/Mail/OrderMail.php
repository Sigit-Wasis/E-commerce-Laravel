<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Order;

class OrderMail extends Mailable
{
    use Queueable, SerializesModels;
    protected $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    //MEMINTA DATA ORDER
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //DAN KIRIM EMAIL DENGAN SUBJECT BERIKUT
        //TEMPLATE YANG DIGUNAKAN ADALAH ORDER.BLADE.PHP YANG ADA DI FOLDER EMAILS
        //DAN PASSING DATA ORDER KE FILE ORDER.BLADE.PHP
        return $this->subject('Pesanan Anda Dikirim ' . $this->order->invoice)
            ->view('emails.order')
            ->with([
                'order' => $this->order
            ]);
    }
}
