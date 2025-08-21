<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FollowupCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'status',
        'serial',
    ];

    public function countFollowup($employeeId = null)
    {
        $authUser = Auth::user();
 
        $query = SalesPipeline::query()
            ->where('status', 'Active')  
            ->where('type', 'lead_data')
            ->where('followup_categorie_id', $this->id);
 
        if ($employeeId !== null) {
            $query->where('assigned_to', $employeeId);

        } else {
            if (can('all-lead')) { 

            } elseif (can('own-team-lead')) {
                $juniorUserIds = json_decode($authUser->junior_user ?? "[]");
                $query->whereIn('assigned_to', $juniorUserIds);

            } elseif (can('own-lead')) {
                $query->where('assigned_to', $authUser->id);

            } else {
                return 0;  
            }
        }

        return $query->count();
    }
}
