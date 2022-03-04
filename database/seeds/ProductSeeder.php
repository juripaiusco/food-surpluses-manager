<?php

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Model\Product::class, 15)->create()
            ->each(function ($product) {

                factory(\App\Model\Store::class, random_int(1, 3))->create([
                    'product_id' => $product->id,
                    'cod' => $product->cod,
                    'kg' => $product->type == 'fead no' ? NULL : random_int(10, 100)
                ]);

            });
    }
}
