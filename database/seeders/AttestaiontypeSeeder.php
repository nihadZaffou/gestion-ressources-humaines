<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class attestationtypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('attestationtypes')->insert([
            [
                'type' => 'Attestation de travail',
                'description' => 'Cette attestation certifie que l\'employé travaille dans notre entreprise.',
            ],
            [
                'type' => 'Attestation de salaire',
                'description' => 'Cette attestation certifie le salaire de l\'employé.',
            ],
            [
                'type' => 'Attestation de congé',
                'description' => 'Cette attestation certifie que l\'employé est en congé.',
            ],
            [
                'type' => 'Attestation de fin de contrat',
                'description' => 'Cette attestation certifie la fin du contrat de l\'employé.',
            ],
            [
                'type' => 'Attestation de formation',
                'description' => 'Cette attestation certifie que l\'employé a suivi une formation.',
            ],
        ]);
    }
}
