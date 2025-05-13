<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Absence;
use Carbon\Carbon;

class VerifierAbsencesNonJustifiees extends Command
{
    protected $signature = 'absences:verifier-impact';
    protected $description = 'Marquer les absences non justifiées depuis plus de 48h comme impactant le salaire';

    public function handle()
    {
        $now = Carbon::now();
        $limite = $now->subHours(48);

        $absences = Absence::where('justifiee', false)
            ->where('impact_salaire', false)
            ->where('date_debut', '<=', $limite)
            ->get();

        foreach ($absences as $absence) {
            $absence->impact_salaire = true;
            $absence->save();
        }

        $this->info("Absences mises à jour : " . count($absences));
    }
}
