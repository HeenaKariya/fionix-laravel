<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $roles = ['admin', 'manager', 'owner', 'supervisor', 'account manager'];
        $mobileNo = 9999999991;
        foreach ($roles as $role) {
            $user = User::create([
                'name' => ucfirst($role),
                'email' => $role . '@example.com',
                'password' => Hash::make('123456'), 
                'mobile_no' => $mobileNo,
            ]);

            $user->assignRole($role);
            $mobileNo++;
        }
    }
}
