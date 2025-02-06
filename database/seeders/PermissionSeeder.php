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
            'all-employee',
            'own-employee',
            'manage-all-employee',
            'manage-own-employee',
            'manage-own-team-employee',

            'lead',
            'all-lead',
            'own-lead',
            'manage-all-lead',
            'manage-own-lead',
            'manage-own-team-lead',

            'client',
            'all-client',
            'own-client',
            'manage-all-client',
            'manage-own-client',
            'manage-own-team-client',

            'sales',
            'all-sales',
            'own-sales',
            'manage-all-sales',
            'manage-own-sales',
            'manage-own-team-sales',

            'payment_schedule',
            'all-payment-schedule',
            'own-payment-schedule',
            'manage-all-payment-schedule',
            'manage-own-payment-schedule',
            'manage-own-team-payment-schedule',

            'payment-receive',
            'all-payment-receive',
            'own-payment-receive',
            'own-team-payment-receive',

            'account',
            'all-account',
            'own-account',
            'own-team-account'
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
