<?php

use Illuminate\Database\Seeder;

class RetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Model\Retail::class, 3)->create();
    }
}
