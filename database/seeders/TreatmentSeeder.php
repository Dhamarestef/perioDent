<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Treatment;

class TreatmentSeeder extends Seeder
{
    public function run(): void
    {
        $treatments = [
            // General
            ['abreviacion' => 'DET',              'nombre' => 'Detartraje y aplicación tópica de flúor',                    'costo' => 750.00,   'categoria' => 'General'],
            ['abreviacion' => 'RF',               'nombre' => 'Resina fotopolimerizable fluida',                            'costo' => 950.00,   'categoria' => 'General'],
            ['abreviacion' => 'RFC',              'nombre' => 'Resina fotopolimerizable en téc. directa',                   'costo' => 1250.00,  'categoria' => 'General'],
            ['abreviacion' => 'RFv',              'nombre' => 'Reconstrucción de zona cervical',                            'costo' => 650.00,   'categoria' => 'General'],
            ['abreviacion' => 'INC',              'nombre' => 'Incrustación estética libre de metal',                       'costo' => 2200.00,  'categoria' => 'General'],
            ['abreviacion' => 'CZ',               'nombre' => 'Corona de zirconia',                                         'costo' => 4500.00,  'categoria' => 'General'],
            ['abreviacion' => 'EX',               'nombre' => 'Extracción atraumática',                                     'costo' => 1200.00,  'categoria' => 'General'],
            ['abreviacion' => 'ALARG',            'nombre' => 'Alargamiento de corona',                                     'costo' => 400.00,   'categoria' => 'General'],
            ['abreviacion' => 'PROTESIS UNI',     'nombre' => 'Prótesis unilateral Ivoclar',                                'costo' => 4500.00,  'categoria' => 'General'],
            ['abreviacion' => 'PROTESIS BIL',     'nombre' => 'Prótesis bilateral Ivoclar',                                 'costo' => 8500.00,  'categoria' => 'General'],
            ['abreviacion' => 'PROTESIS T.SUP',   'nombre' => 'Prótesis total superior Ivoclar',                            'costo' => 8500.00,  'categoria' => 'General'],
            ['abreviacion' => 'PROTESIS T.INF',   'nombre' => 'Prótesis total inferior Ivoclar',                            'costo' => 8500.00,  'categoria' => 'General'],
            ['abreviacion' => 'SFF',              'nombre' => 'Sellador de fosetas y fisuras',                              'costo' => 550.00,   'categoria' => 'General'],
            ['abreviacion' => 'TX ATM',           'nombre' => 'Tratamiento para disfunción de articulación temporomandibular', 'costo' => null, 'categoria' => 'General'],
            ['abreviacion' => 'GUARDA',           'nombre' => 'Guarda oclusal nocturno',                                    'costo' => 750.00,   'categoria' => 'General'],
            // Endodoncia
            ['abreviacion' => 'ENDO ANT',         'nombre' => 'Endodoncia anterior',                                        'costo' => 2500.00,  'categoria' => 'Endodoncia'],
            ['abreviacion' => 'ENDO POST',        'nombre' => 'Endodoncia posterior',                                       'costo' => 2700.00,  'categoria' => 'Endodoncia'],
            ['abreviacion' => 'RET ENDO',         'nombre' => 'Retratamiento endodóntico',                                  'costo' => 3100.00,  'categoria' => 'Endodoncia'],
            ['abreviacion' => 'ENDOP',            'nombre' => 'Endoposte fibra de cuarzo y reconstrucción de muñón',        'costo' => 2100.00,  'categoria' => 'Endodoncia'],
            // Cirugía y Periodoncia
            ['abreviacion' => 'CX TERCER M',      'nombre' => 'Cirugía tercer molar',                                       'costo' => 2200.00,  'categoria' => 'Cirugia y Periodoncia'],
            ['abreviacion' => 'CURT. C',          'nombre' => 'Curetaje periodontal a campo cerrado',                       'costo' => 2500.00,  'categoria' => 'Cirugia y Periodoncia'],
            ['abreviacion' => 'CURT. A',          'nombre' => 'Curetaje periodontal a campo abierto',                       'costo' => 3200.00,  'categoria' => 'Cirugia y Periodoncia'],
            ['abreviacion' => 'ALARG QX',         'nombre' => 'Alargamiento de corona quirúrgico',                          'costo' => 1500.00,  'categoria' => 'Cirugia y Periodoncia'],
        ];

        foreach ($treatments as $t) {
            Treatment::create($t);
        }
    }
}
