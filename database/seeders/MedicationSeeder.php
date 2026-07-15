<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medication;

class MedicationSeeder extends Seeder
{
    public function run(): void
    {
        $medications = [
            // Analgésicos
            [
                'nombre'      => 'Doroxina Forte 250 mg',
                'categoria'   => 'Analgésicos',
                'indicaciones'=> 'Tomar una tableta cada 12 horas durante 3 días.',
            ],
            [
                'nombre'      => 'Dorixina TMR (Clonisinato de lisina / Tramadol) 125 mg / 25 mg',
                'categoria'   => 'Analgésicos',
                'indicaciones'=> 'Tomar una tableta cada 6 horas por 5 días.',
            ],
            [
                'nombre'      => 'Loxonin (Loxoprofeno sódico) 60 mg tabletas',
                'categoria'   => 'Analgésicos',
                'indicaciones'=> 'Tomar una tableta cada 8 horas por 5 días.',
            ],
            [
                'nombre'      => 'Dorixina (Clonixinato de lisina) 250 mg tabletas',
                'categoria'   => 'Analgésicos',
                'indicaciones'=> 'Tomar una tableta cada 8 horas por 5 días.',
            ],
            [
                'nombre'      => 'Dolocam Plus (Meloxicam 7.5 mg / Metocarbamol 215 mg) cápsulas',
                'categoria'   => 'Analgésicos',
                'indicaciones'=> 'Tomar una cápsula cada 12 horas por 3 días.',
            ],
            [
                'nombre'      => 'Dolocam (Meloxicam 15 mg) tableta sublingual',
                'categoria'   => 'Analgésicos',
                'indicaciones'=> 'Uso indicado por el médico.',
            ],
            [
                'nombre'      => 'Febrax (Naproxeno sódico 275 mg / Paracetamol 300 mg)',
                'categoria'   => 'Analgésicos',
                'indicaciones'=> 'Tomar una tableta cada 8 horas por 5 días.',
            ],
            [
                'nombre'      => 'Ibuprofeno (Actron) 600 mg cápsulas',
                'categoria'   => 'Analgésicos',
                'indicaciones'=> 'Tomar una cápsula cada 8 horas por 4 días.',
            ],
            [
                'nombre'      => 'Stadium 25 mg tabletas (Dexketoprofeno)',
                'categoria'   => 'Analgésicos',
                'indicaciones'=> 'Tomar una tableta cada 12 horas por 5 días.',
            ],
            [
                'nombre'      => 'Emisen tabletas (Ketorolaco complejo B)',
                'categoria'   => 'Analgésicos',
                'indicaciones'=> 'Tomar una tableta cada 8 horas en caso de dolor por 5 días.',
            ],
            [
                'nombre'      => 'Motrin 800 mg tabletas (Ibuprofeno)',
                'categoria'   => 'Analgésicos',
                'indicaciones'=> 'Tomar una tableta cada 24 horas por tres días.',
            ],
            [
                'nombre'      => 'Alin (Dexametazona) 10 mg inyectable',
                'categoria'   => 'Analgésicos',
                'indicaciones'=> 'Aplicar dosis única intramuscular.',
            ],
            [
                'nombre'      => 'Dolac (Ketorolaco) 30 mg sublingual',
                'categoria'   => 'Analgésicos',
                'indicaciones'=> 'Colocar una tableta debajo de la lengua en caso de dolor. No exceder 3 días continuos.',
            ],
            // Antibióticos
            [
                'nombre'      => 'Ampliron Duo (Amoxicilina / Ácido clavulánico) 875/125 mg tabletas',
                'categoria'   => 'Antibióticos',
                'indicaciones'=> 'Tomar una tableta cada 12 horas por 7 días.',
            ],
            [
                'nombre'      => 'Ampliron (Amoxicilina) 750 mg tabletas',
                'categoria'   => 'Antibióticos',
                'indicaciones'=> 'Tomar dos cápsulas cada 12 horas por 7 días.',
            ],
            [
                'nombre'      => 'Ciprofloxacino 500 mg',
                'categoria'   => 'Antibióticos',
                'indicaciones'=> 'Tomar una tableta cada 12 horas de 5 a 7 días.',
            ],
            [
                'nombre'      => 'Azitromicina 500 mg tabletas',
                'categoria'   => 'Antibióticos',
                'indicaciones'=> 'Tomar una tableta cada 24 horas por 3 días.',
            ],
            [
                'nombre'      => 'Ceftriaxona inyectable 1 gr',
                'categoria'   => 'Antibióticos',
                'indicaciones'=> 'Aplicar como dosis única intramuscular.',
            ],
            [
                'nombre'      => 'Flagenase (Metronidazol) 500 mg',
                'categoria'   => 'Antibióticos',
                'indicaciones'=> 'Tomar una tableta cada 12 horas por 7 días.',
            ],
            [
                'nombre'      => 'Clindamicina (Clendix) 300 mg',
                'categoria'   => 'Antibióticos',
                'indicaciones'=> 'Tomar una cápsula cada 8 horas por 7 días.',
            ],
            [
                'nombre'      => 'Augmentin (Amoxicilina y ácido clavulánico)',
                'categoria'   => 'Antibióticos',
                'indicaciones'=> 'Tomar una tableta cada 12 horas por 5 días.',
            ],
        ];

        foreach ($medications as $m) {
            Medication::create($m);
        }
    }
}
