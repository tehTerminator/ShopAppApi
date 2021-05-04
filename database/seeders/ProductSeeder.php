<?php

namespace Database\Seeders;

use App\Models\Product;
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
        Product::create(['title' => 'Photocopy', 'rate' => 2]);
        Product::create(['title' => 'Lamination Small', 'rate' => 10]);
        Product::create(['title' => 'Lamination Large', 'rate' => 20]);
        Product::create(['title' => 'Job Work', 'rate' => 1]);
        Product::create(['title' => 'Scanning', 'rate' => 5]);
        Product::create(['title' => 'Print BW Text', 'rate' => 5]);
        Product::create(['title' => 'Print BW Image', 'rate' => 10]);
        Product::create(['title' => 'Print Color Text', 'rate' => 10]);
        Product::create(['title' => 'Print Color Image', 'rate' => 30]);
        Product::create(['title' => 'Print Color Gloss', 'rate' => 50]);
        Product::create(['title' => 'Photoprint Card', 'rate' => 30]);
    }
}
