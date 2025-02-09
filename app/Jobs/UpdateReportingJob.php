<?php

namespace App\Jobs;

use App\Helpers\ReportingService;
use App\Models\User;
use App\Models\UserReporting; 
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateReportingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reporting_user_id;
    protected $user_id;

    /**
     * Create a new job instance.
     *
     * @param  int  $reporting_user_id
     * @param  int  $user_id
     * @return void
     */
    public function __construct($reporting_user_id, $user_id)
    {
        $this->reporting_user_id = $reporting_user_id;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $user = User::find($this->user_id);
            if (!$user) {
                throw new Exception("User not found", 404);
            }

            $junior_users = json_decode($user->junior_user ?? "[]");

            if ($junior_users && in_array($this->reporting_user_id, $junior_users)) {
                throw new Exception("You cannot make this employee your senior because they are already your junior.");
            }

            // Update the reporting relationship
            UserReporting::where('user_id', $user->id)
                ->update([
                    'end_date' => now()->subDay()
                ]);

            UserReporting::create([
                'user_id' => $user->id,
                'reporting_user_id' => $this->reporting_user_id,
                'start_date' => now()
            ]);

            $old_senior = json_decode($user->senior_user ?? "[]");
            $old_junior = json_decode($user->junior_user ?? "[]");
            $new_senior = ReportingService::getAllSenior($user->id);
            $new_junior = ReportingService::getAllJunior($user->id);
            $combined = array_merge([$user->id], $old_senior, $old_junior, $new_senior, $new_junior);
            $unique_user = array_unique($combined, SORT_REGULAR);
            $unique_user_ids = array_values($unique_user);

            foreach ($unique_user_ids as $user_id) {
                $user = User::find($user_id);
                $user->senior_user = json_encode(ReportingService::getAllSenior($user->id));
                $user->junior_user = json_encode(ReportingService::getAllJunior($user->id));
                $user->save();
            }

        } catch (Exception $e) { 
        }
    }
}
