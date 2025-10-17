<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $user = DB::table('users')->latest('id')->first();

        if ($user) {
            DB::table('notes')->insert([
                'user_id' => $user->id,
                'title' => Str::random(10),
                'text' => Str::random(100),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}