<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $designations = [
            ['title' => 'admin', 'slug' => 'admin', 'status' => 1],
            ['title' => 'Chief Executive Officer', 'slug' => 'chief-executive-officer', 'status' => 1],
            ['title' => 'Chief Operating Officer', 'slug' => 'chief-operating-officer', 'status' => 1],
            ['title' => 'Chief Technology Officer', 'slug' => 'chief-technology-officer', 'status' => 1],
            ['title' => 'Software Engineer', 'slug' => 'software-engineer', 'status' => 1],
            ['title' => 'Junior Software Engineer', 'slug' => 'junior-software-engineer', 'status' => 1],
            ['title' => 'Senior Software Engineer', 'slug' => 'senior-software-engineer', 'status' => 1],
            ['title' => 'Project Manager', 'slug' => 'project-manager', 'status' => 1],
            ['title' => 'Human Resources Manager', 'slug' => 'human-resources-manager', 'status' => 1],
            ['title' => 'Marketing Manager', 'slug' => 'marketing-manager', 'status' => 1],
            ['title' => 'Sales Executive', 'slug' => 'sales-executive', 'status' => 1],
            ['title' => 'Accountant', 'slug' => 'accountant', 'status' => 1],
            ['title' => 'Graphic Designer', 'slug' => 'graphic-designer', 'status' => 1],
        ];

        foreach ($designations as $designation) {
            DB::table('designations')->insert([
                'title' => $designation['title'],
                'slug' => Str::slug($designation['title']),
                'created_at' => now(), 
            ]);
        }
    }
}
