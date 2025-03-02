<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\DesignationPermission;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {   
        $this->call(FollowupCategorySeeder::class); 
        $this->call(DesignationSeeder::class);  
        $this->call(UserSeeder::class); 
        $this->call(DesignationPermissionSeeder::class);
        $this->call(PermissionSeeder::class);    
        $this->call(CountrySeeder::class);        
        $this->call(DivisionSeeder::class);  
        $this->call(DistrictSeeder::class);  
        $this->call(UpazilaSeeder::class);  
        $this->call(UnionSeeder::class);  
        
    }
}
