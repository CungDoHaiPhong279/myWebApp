<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Status::insert([
            ['name' => 'Pending'],
            ['name' => 'In Progress'],
            ['name' => 'Completed'],
            ['name' => 'Cancelled'],
        ]);
    }
}