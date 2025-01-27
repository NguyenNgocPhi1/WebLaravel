<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // DB::table('users')->insert([
        //     'name' => 'Newbie Laravel',
        //     'email' => 'nguyenngocphi10.2003@gmail.com',
        //     'password' => Hash::make('01685169500')
        // ]);
        $this->call([
            UserSeeder::class
        ]);
    }
}
