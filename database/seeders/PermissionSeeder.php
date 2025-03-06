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
            'create-employee',
            'manage-employee',

            'lead',
            'own-lead',
            'own-team-lead',
            'all-lead', 
            'create-lead',
            'manage-lead', 

            'rejection',
            'own-rejection',
            'own-team-rejection',
            'all-rejection', 
            'create-rejection',
            'manage-rejection', 

            // 'client',
            // 'own-client',
            // 'own-team-client',
            // 'all-client', 
            // 'create-client', 
            // 'manage-client', 

            'sales',
            'own-sales',
            'own-team-sales',
            'all-sales', 
            'create-sales', 
            'manage-sales', 

            // 'payment-schedule',
            // 'own-payment-schedule',
            // 'own-team-payment-schedule',
            // 'all-payment-schedule', 
            // 'create-payment-schedule',
            // 'manage-payment-schedule',

            // 'payment-receive',
            // 'own-payment-receive',
            // 'own-team-payment-receive',
            // 'all-payment-receive',

            // 'account',
            // 'own-account',
            // 'own-team-account',
            // 'all-account',
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
