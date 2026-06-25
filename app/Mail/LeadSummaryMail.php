<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Str;

class LeadSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $vehicles;
    public $groupToken;

    // Inizializza i dati del client e dei veicoli configurati
    public function __construct($client, $vehicles, $groupToken = null)
    {
        $this->client = $client;
        $this->vehicles = $vehicles;
        $this->groupToken = $groupToken;
    }

    // Imposta l'oggetto dell'email
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuovo Lead Configurato: ' . ($this->client['company'] ?? 'Azienda'),
        );
    }

    // Imposta il template HTML da utilizzare
    public function content(): Content
    {
        return new Content(
            view: 'emails.lead-summary',
            with: [
                'isPdf' => false,
            ]
        );
    }

    // Eventuali allegati (genera e allega il PDF a runtime)
    public function attachments(): array
    {
        $pdf = Pdf::loadView('emails.lead-summary', [
            'client' => $this->client,
            'vehicles' => $this->vehicles,
            'isPdf' => true
        ]);

        $company = $this->client['company'] ?? 'azienda';
        $safeCompany = Str::slug($company);
        if (empty($safeCompany)) {
            $safeCompany = 'azienda';
        }
        $date = now()->format('Y-m-d');
        $fileName = "riepilogo-flotta-{$safeCompany}-{$date}.pdf";

        return [
            Attachment::fromData(fn () => $pdf->output(), $fileName)
                ->withMime('application/pdf'),
        ];
    }
}
