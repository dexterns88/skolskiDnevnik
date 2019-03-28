<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PredmetTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('predmet')->insert([
        ['name' => 'matematika'],
        ['name' => 'srpski'],
      ]);
    }
}
