<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Customer; //USE STATEMENT MODEL CUSTOMER

class CustomerRegisterMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $customer;
    protected $randomPassword;

    //MEMINTA DATA BERUPA INFORMASI CUSTOMER DAN RANDOM PASSWORD YANG BELUM DI-ENCRYPT
    public function __construct(Customer $customer, $randomPassword)
    {
        $this->customer = $customer;
        $this->randomPassword = $randomPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        //MENGESET SUBJECT EMAIL, VIEW MANA YANG AKAN DI-LOAD DAN DATA APA YANG AKAN DIPASSING KE VIEW
        return $this->subject('Verifikasi Pendaftaran Anda')
            ->view('emails.register')
            ->with([
                'customer' => $this->customer,
                'password' => $this->randomPassword
            ]);
    }
}
