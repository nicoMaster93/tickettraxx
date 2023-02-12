<?php

namespace Database\Seeders;

use App\Models\ConfigModel;
use Illuminate\Database\Seeder;

class DefaultConfig extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ConfigModel::insert([["fee" => "4.00", "insurance" => "15.00"]]);
    }
}
