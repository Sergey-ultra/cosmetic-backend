<?php

namespace App\Mail;

use App\Models\Sku;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DropPriceMail extends Mailable
{
    use Queueable, SerializesModels;


    protected  $skus;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($skus)
    {
        $this->skus = $skus;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('admin@smart-beautiful.ru', 'Smart-Beautiful')
            ->subject('Цены снизились ')
            ->view('mail.drop-price', ['skus' => $this->skus]);
    }
}
