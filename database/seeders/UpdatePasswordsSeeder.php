<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\NguoiDung;

class UpdatePasswordsSeeder extends Seeder
{
    public function run()
    {
        $users = NguoiDung::all();
        foreach ($users as $user) {
            // Check if the password is not an actual starting with $2y$ (bcrypt)
            if (!str_starts_with($user->MatKhau, '$2y$')) {
                // If it's a raw string like "hashed_pw_1", change it to "password123"
                $user->MatKhau = Hash::make('password123');
                $user->save();
            }
        }
    }
}
