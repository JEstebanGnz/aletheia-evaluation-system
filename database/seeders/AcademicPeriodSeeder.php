<?php

namespace Database\Seeders;

use App\Http\Controllers\AcademicPeriodController;
use App\Models\AcademicPeriod;
use Illuminate\Database\Seeder;

class AcademicPeriodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @param $name
     * @return void
     */
    public function run($name): void
    {
        AcademicPeriod::create([
            'name' => $name,
            'description' => 'Periodo generado automáticamente por migración',
            'class_start_date' => '2022-02-01',
            'class_end_date' => '2022-02-01',
            'assessment_period_id' => 2
        ]);

        (new AcademicPeriodController())->sync();
    }
}
