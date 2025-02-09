<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'admin',
            'employee',
            'own-employee',
            'own-team-employee',
            'all-employee', 
            'manage-own-employee',
            'manage-own-team-employee',
            'manage-all-employee', 

            'lead',
            'own-lead',
            'own-team-lead',
            'all-lead', 
            'manage-all-lead',
            'manage-own-lead',
            'manage-own-team-lead',

            'client',
            'own-client',
            'own-team-client',
            'all-client', 
            'manage-own-client',
            'manage-own-team-client',
            'manage-all-client', 

            'sales',
            'own-sales',
            'own-team-sales',
            'all-sales', 
            'manage-own-sales',
            'manage-own-team-sales',
            'manage-all-sales', 

            'payment_schedule',
            'own-team-payment-schedule',
            'all-payment-schedule', 
            'manage-all-payment-schedule',
            'manage-own-payment-schedule',
            'manage-own-team-payment-schedule',

            'payment-receive',
            'own-payment-receive',
            'own-team-payment-receive',
            'all-payment-receive',

            'account',
            'own-account',
            'own-team-account',
            'all-account',
            'setting'
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
                'name' => $permission, 
                'slug' => $permission, 
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        $this->command->info('Permissions seeded successfully!');
    }
}
