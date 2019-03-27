<?php

use Illuminate\Database\Seeder;

Use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'sasa',
            'email' => 'sasans87@gmail.com',
            'password' => bcrypt('123456'),
        ]);
    }
}
