<?php

namespace App\Jobs;

use App\Models\ReviewView;
use App\Models\User;
use App\Models\UserBalanceAccrual;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ReviewViewJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $reviewUserId,
        protected int $reviewId,
        protected string $ipAddress
    ){}

    public function handle(): void
    {
        try {
            DB::beginTransaction();
            ReviewView::query()->updateOrCreate([
                'review_id' => $this->reviewId,
                'ip_address' => $this->ipAddress,
                'handled' => 1,
            ], []);

            $currentDate = now()->toDate();

            $existingBalanceAccrual = UserBalanceAccrual::query()
                ->where([
                    'review_id' => $this->reviewId,
                    'user_id' => $this->reviewUserId,
                    'type' => UserBalanceAccrual::VIEW_TYPE,
                    'date' => $currentDate,
                ])
                ->first();

            if (!$existingBalanceAccrual) {
                UserBalanceAccrual::query()->create([
                    'review_id' => $this->reviewId,
                    'user_id' => $this->reviewUserId,
                    'type' => UserBalanceAccrual::VIEW_TYPE,
                    'accrual' => ReviewView::REVIEW_COST,
                    'date' => $currentDate,
                ]);
            } else {
                $existingBalanceAccrual->accrual += ReviewView::REVIEW_COST;
                $existingBalanceAccrual->save();
            }


            $user = User::query()->find($this->reviewUserId);
            $user->balance += ReviewView::REVIEW_COST;
            $user->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            echo $e->getMessage();
        }
    }
}
