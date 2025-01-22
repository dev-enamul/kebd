<?php

namespace Database\Seeders;

use App\Models\FollowupCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FollowupCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FollowupCategory::create([
            'title' => "Prospect",
            'slug' => 'prospect',
            'serial' => 1,
        ]);
    
        FollowupCategory::create([
            'title' => "Cold Call",
            'slug' => 'cold-call',
            'serial' => 5,
        ]);
    
        FollowupCategory::create([
            'title' => "Lead",
            'slug' => 'lead',
            'serial' => 10,
        ]);
    
        FollowupCategory::create([
            'title' => "Followup",
            'slug' => 'followup',
            'serial' => 15,
        ]);
    
        FollowupCategory::create([
            'title' => "Negotiation",
            'slug' => 'negotiation',
            'serial' => 20,
        ]);
    }
}
