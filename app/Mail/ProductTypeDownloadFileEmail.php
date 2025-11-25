<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Product;
use App\Models\EmailContent;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\View;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProductTypeDownloadFileEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected Product $product;
    protected Order $order;
    public function __construct($product,$order)
    {
        $this->product = $product;
        $this->order = $order;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->product->name.' siparişiniz',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $email_content = EmailContent::where('use_for','order_type_download')->first();
        $producttt = $this->product->loadMissing('product_type');

        return new Content(
            view: 'emails.dynamic-email-layout',
            with: [
                'product' => $producttt,
                'order' => $this->order,
                'email_content' => $email_content,
                'productDetails' => View::make('emails.inc.urun-detay',[
                    'product' => $producttt,
                ])->render(),
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
