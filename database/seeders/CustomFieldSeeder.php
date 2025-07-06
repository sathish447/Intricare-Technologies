<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomField;

class CustomFieldSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            ['name' => 'Birthday', 'type' => 'date'],
            ['name' => 'Address',  'type' => 'text'],
        ];

        foreach ($fields as $f) {
            CustomField::firstOrCreate(['name'=>$f['name']], $f);
        }
    }
}
