<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\EmailContent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\View;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class AdminOrderReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    protected Order $order;
    
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Yeni müşteri siparişi #'.$this->order->code,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $email_content = EmailContent::where('use_for','admin_order_new')->first();

        return new Content(
            view: 'emails.dynamic-email-layout',
            with: [
                'order' => $this->order,
                'email_content' => $email_content,
                'cartHtml' => View::make('emails.inc.sepet-detay',[
                    'order' => $this->order,
                ])->render(),
                'shippingHtml' => View::make('emails.inc.shipping-address',[
                    'shipping' => $this->order->cart_details['extra_info']['shipping'],
                ])->render(),
                'billingHtml' => View::make('emails.inc.billing-address',[
                    'billing' => $this->order->cart_details['extra_info']['billing'],
                    'billing_extra' => $this->order->cart_details['extra_info']['billing_extra'],
                ])->render()
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
