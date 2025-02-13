<?php 
namespace App\Services;

use App\Models\SalesPipeline;
use App\Models\User;
use Illuminate\Support\Collection;

class SalesPipelineService
{
    /**
     * Get sales pipelines based on status and category, then filter by user permissions.
     *
     * @param string $status
     * @param string|null $category
     * @param User $authUser
     * @param int $perPage
     * @param int $currentPage
     * @return \Illuminate\Support\Collection
     */
    public function getSalesPipelines($status, $category, $authUser, $perPage = 20, $currentPage = 1)
    {
        // Build the query for sales pipelines
        $query = $this->buildQuery($status, $category);
        
        // Filter based on user permissions
        $filteredQuery = $this->filterByPermissions($query, $authUser);

        // Get the total count for pagination
        $totalItems = $filteredQuery->count();

        // Get the paginated results
        $offset = ($currentPage - 1) * $perPage;
        $salesPipelines = $filteredQuery->skip($offset)->take($perPage)->get();

        // Return paginated data along with metadata
        return $this->formatPaginationResponse($salesPipelines, $totalItems, $perPage, $currentPage);
    }

    /**
     * Build the base query for sales pipelines.
     *
     * @param string $status
     * @param string|null $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function buildQuery($status, $category)
    {
        $query = SalesPipeline::query()
            ->leftJoin('users', 'sales_pipelines.user_id', '=', 'users.id')
            ->leftJoin('sales_pipeline_services', 'sales_pipelines.id', '=', 'sales_pipeline_services.sales_pipeline_id')
            ->leftJoin('services', 'sales_pipeline_services.service_id', '=', 'services.id')
            ->select('sales_pipelines.id as lead_id', 'sales_pipelines.next_followup_date', 'sales_pipelines.last_contacted_at', 
                     'users.id as user_id', 'users.name as user_name', 'users.email as user_email', 'users.phone as user_phone', 
                     'services.id as service_id', 'services.title as service_name')
            ->where('sales_pipelines.status', $status);

        if ($category) {
            $query->where('sales_pipelines.followup_categorie_id', $category);
        }

        return $query;
    }

    /**
     * Filter the query by the user's permissions.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param User $authUser
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function filterByPermissions($query, $authUser)
    {
        if (can('all-lead')) {
            return $query;
        } elseif (can('own-team-lead')) {
            $juniorUserIds = json_decode($authUser->junior_user ?? "[]");
            return $query->whereIn('sales_pipelines.assigned_to', $juniorUserIds);
        } elseif (can('own-lead')) {
            $directJuniors = $authUser->directJuniors->pluck('user_id')->toArray();
            return $query->whereIn('sales_pipelines.assigned_to', $directJuniors);
        } else {
            return $query->whereRaw('1 = 0');  
        }
    }

    /**
     * Format and return paginated data along with metadata.
     *
     * @param Collection $salesPipelines
     * @param int $totalItems
     * @param int $perPage
     * @param int $currentPage
     * @return array
     */
    private function formatPaginationResponse(Collection $salesPipelines, $totalItems, $perPage, $currentPage)
    {
        $pagination = [
            'current_page' => $currentPage,
            'total_items' => $totalItems,
            'per_page' => $perPage,
            'total_pages' => ceil($totalItems / $perPage), // Calculate total pages
        ];

        return [
            'data' => $salesPipelines,
            'meta' => $pagination
        ];
    }
}
