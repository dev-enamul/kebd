<?php

namespace App\Http\Controllers\Dashbaord;

use App\Http\Controllers\Controller;
use App\Models\FollowupCategory;
use App\Models\SalesPipeline;
use Illuminate\Http\Request;
use Carbon\Carbon; 

class DashboardController extends Controller
{
    public function leadChart(){  
    for ($i = 5; $i >= 0; $i--) {
            $startDate = Carbon::now()->subMonths($i)->startOfMonth()->toDateString();
            $endDate = Carbon::now()->subMonths($i)->endOfMonth()->toDateString();

            $leadsData[] = [
                'month' => Carbon::now()->subMonths($i)->format('F Y'),
                'leads' => SalesPipeline::whereBetween('created_at', [$startDate, $endDate])->count()
            ];
        }

        return success_response($leadsData);
    }
}
