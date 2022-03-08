<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('users')->insert([
            'name' => 'Juri',
            'email' => 'juri@mr-j.it',
            'password' => Hash::make('12345'),
            'json_modules' => '{"home":"on","shop":"on","orders":"on","products":"on","store":"on","customers":"on","users":"on"}'
        ]);

        \Illuminate\Support\Facades\DB::table('users')->insert([
            'name' => 'Christian',
            'email' => 'bastasprechi.vi@gmail.com',
            'password' => Hash::make('12345'),
            'json_modules' => '{"home":"on","shop":"on","orders":"on","products":"on","store":"on","customers":"on","users":"on"}'
        ]);
    }
}
