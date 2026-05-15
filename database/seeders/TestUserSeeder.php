<?php
 
namespace Database\Seeders;
 
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
 
class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nama_lengkap' => 'Test User',
            'username'     => 'testuser',
            'email'        => 'test@example.com',
            'password'     => Hash::make('password'),
            'role'         => 'admin',
        ]);
    }
}
