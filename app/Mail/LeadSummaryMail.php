<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeadSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $vehicles;

    // Inizializza i dati del client e dei veicoli configurati
    public function __construct($client, $vehicles)
    {
        $this->client = $client;
        $this->vehicles = $vehicles;
    }

    // Imposta l'oggetto dell'email
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuovo Lead Configurato: ' . ($this->client['company'] ?? 'Azienda'),
        );
    }

    // Imposta il template Markdown da utilizzare
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.lead-summary',
        );
    }

    // Eventuali allegati
    public function attachments(): array
    {
        return [];
    }
}

