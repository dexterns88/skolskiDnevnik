<?php

use Illuminate\Database\Seeder;

Use App\User;

class UsersTableSeeder extends Seeder {
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run() {
    User::create([
      'name' => 'admin',
      'email' => 'sasans87@gmail.com',
      'password' => bcrypt('admin'),
      'role' => 'admin',
      'firstName' => 'Sasa',
      'lastName' => 'admin',
    ]);

    User::create([
      'name' => 'student',
      'email' => 'student@gmail.com',
      'password' => bcrypt('admin'),
      'role' => 'student',
      'firstName' => 'Sasa',
      'lastName' => 'student',
    ]);

    User::create([
      'name' => 'teacher',
      'email' => 'teacher@gmail.com',
      'password' => bcrypt('admin'),
      'role' => 'teacher',
      'firstName' => 'Sasa',
      'lastName' => 'teacher',
    ]);

    User::create([
      'name' => 'parent',
      'email' => 'parent@gmail.com',
      'password' => bcrypt('admin'),
      'role' => 'parent',
      'firstName' => 'Sasa',
      'lastName' => 'parent',
    ]);
  }
}
