<?php

namespace Database\Seeders;

use App\Models\TypeOcurrence;
use Illuminate\Database\Seeder;

class TypeOcurrenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            'Problema de Iluminação',
            'Problema de Pavimentação',
            'Alagamento',
            'Acúmulo de Lixo',
            'Falta de Pavimentação',
        ];

        foreach ($types as $typeName) {
            TypeOcurrence::firstOrCreate(['name' => $typeName]);

        }
    }
}
