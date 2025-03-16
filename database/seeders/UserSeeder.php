<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::create([
            'name' => 'MD Enamul Haque',  
            'email' => 'admin@example.com',
            'phone' => '1234567890',
            'user_type' => 'employee',
            'password' => Hash::make('password123'),  
            'dob' => '1990-01-01',
            'gender' => 'male',   
            'blood_group' => 'A+',  
            'created_by' => 1,
            'updated_by' => 1,
        ]);
 
        // $johnDoeUser = User::create([
        //     'name' => 'Mehedi Hasan',  
        //     'email' => 'john@example.com',
        //     'phone' => '0987654321',
        //     'user_type' => 'employee',
        //     'password' => Hash::make('password123'),  
        //     'dob' => '1995-03-10',
        //     'gender' => 'female',   
        //     'blood_group' => 'B-',
        //     'created_by' => 1,
        //     'updated_by' => 1,
        // ]);
 
        Employee::create([
            'user_id' => $adminUser->id,
            'employee_id' => Employee::generateNextEmployeeId(),
            'designation_id' => 1,   
            'referred_by' => $adminUser->id, 
            'signature' => 'Admin Signature',
            'salary' => 100000,
            'status' => 1,
            'resigned_at' => null,
            'created_by' => 1,
            'updated_by' => 1,
        ]);
 
        // Employee::create([
        //     'user_id' => $johnDoeUser->id,
        //     'employee_id' => Employee::generateNextEmployeeId(),
        //     'designation_id' => 2,   
        //     'referred_by' => $adminUser->id, 
        //     'signature' => 'John Signature',
        //     'salary' => 60000,
        //     'status' => 1,
        //     'resigned_at' => null,
        //     'created_by' => 1,
        //     'updated_by' => 1,
        // ]);
    }
}
