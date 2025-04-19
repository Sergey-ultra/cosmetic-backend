<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserBalanceAccrual;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ReviewPublishedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected int $reviewUserId, protected int $reviewId){}
    public function handle(): void
    {
        try {
            DB::beginTransaction();
            $currentDate = now()->toDate();

             UserBalanceAccrual::query()
                ->firstOrCreate([
                    'review_id' => $this->reviewId,
                    'user_id' => $this->reviewUserId,
                    'type' => UserBalanceAccrual::VIEW_BONUS,
                    'date' => $currentDate,
                ]);


            $user = User::query()->find($this->reviewUserId);
            $user->balance += UserBalanceAccrual::REVIEW_BONUS_COST;
            $user->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            echo $e->getMessage();
        }
    }
}
