<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FichePaieMailable extends Mailable
{
    use Queueable, SerializesModels;

    public $employe;
    public $pdf;

    public function __construct($employe, $pdf)
    {
        $this->employe = $employe;
        $this->pdf = $pdf;
    }

    public function build()
    {
        return $this->subject('Votre fiche de paie du mois')
            ->view('emails.fiche_paie')
            ->attachData($this->pdf->output(), 'fiche_paie.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
