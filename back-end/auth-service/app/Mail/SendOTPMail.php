<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendOTPMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp; // Variabel untuk menampung kode OTP

    /**
     * Membuat instance baru dan menangkap kiriman kode OTP
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Mengatur Judul Email (Amplop)
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode Verifikasi Akun Purnama Hotel',
        );
    }

    /**
     * Mengatur Tampilan Isi Email (Surat)
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp', // Kita akan buat file tampilannya setelah ini
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}