<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;

class FicheDePaieNotification extends Notification
{
    use Queueable;

    protected $employe;
    protected $fiche;

    public function __construct($employe, $fiche)
    {
        $this->employe = $employe;
        $this->fiche = $fiche;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Génération du PDF depuis la vue
        $pdf = Pdf::loadView('pdf.fiche_paie', [
            'employe' => $this->employe,
            'fiche' => $this->fiche
        ]);

        // Enregistrer temporairement le PDF (dans storage/app/temp/)
        $fileName = 'fiche_paie_' . $this->fiche->mois . '_' . $this->fiche->annee . '.pdf';
        $filePath = storage_path('app/temp/' . $fileName);
        file_put_contents($filePath, $pdf->output());

        // Envoyer l’email avec la pièce jointe
        return (new MailMessage)
            ->subject('Votre fiche de paie - ' . $this->fiche->mois . '/' . $this->fiche->annee)
            ->greeting('Bonjour ' . $this->employe->prenom . ' ' . $this->employe->nom)
            ->line('Veuillez trouver ci-joint votre fiche de paie pour le mois de ' . $this->fiche->mois . '.')
            ->attach($filePath)
            ->line('Cordialement,')
            ->salutation('L\'équipe RH');
    }
}
