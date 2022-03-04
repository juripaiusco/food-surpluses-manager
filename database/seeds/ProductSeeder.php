<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

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
                    'user_id' => 1,
                    'product_id' => $product->id,
                    'cod' => $product->cod,
                    'kg' => $product->type == 'fead no' ? NULL : random_int(10, 100)
                ]);

            });

        $stores = DB::table('stores')
            ->select([
                'product_id',
                DB::raw('SUM(kg) AS kg_total'),
                DB::raw('SUM(amount) AS amount_total'),
            ])
            ->groupBy('product_id')
            ->get();

        foreach ($stores as $store) {

            $product = \App\Model\Product::find($store->product_id);

            $product->kg_total = $store->kg_total;
            $product->amount_total = $store->amount_total;

            $product->save();

        }
    }
}
