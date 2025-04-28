<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $docs = [
            ['name'=>'Documento nacional de identidad','short_name'=>'DNI','min_val'=>8,'max_val'=>8,'alphanumeric'=>false],
            ['name'=>'Registro Único de Contribuyentes','short_name'=>'RUC','min_val'=>11,'max_val'=>11,'alphanumeric'=>false],
            ['name'=>'Carnet de extranjeria','short_name'=>'CARNET EXT.','min_val'=>10,'max_val'=>12,'alphanumeric'=>true],
            ['name'=>'Pasaporte','short_name'=>'PASAPORTE','min_val'=>10,'max_val'=>12,'alphanumeric'=>true],
        ];
        foreach($docs as $d){
          Document::create(array_merge($d,['status'=>1]));
        }
    }
}
