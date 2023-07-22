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

//            $currentDate = now()->toDate();
//            $user = User::query()->find($this->reviewUserId);
//
//            $existingBalanceAccrual = UserBalanceAccrual::query()
//                ->where([
//                    'review_id' => $this->reviewId,
//                    'user_id' => $this->reviewUserId,
//                    'type' => UserBalanceAccrual::VIEW_TYPE,
//                    'date' => $currentDate,
//                ])
//                ->first();
//
//            if (!$existingBalanceAccrual) {
//                UserBalanceAccrual::query()->create([
//                    'review_id' => $this->reviewId,
//                    'user_id' => $this->reviewUserId,
//                    'type' => UserBalanceAccrual::VIEW_TYPE,
//                    'accrual' => UserBalanceAccrual::REVIEW_COST,
//                    'date' => $currentDate,
//                ]);
//            } else {
//                $existingBalanceAccrual->accrual += UserBalanceAccrual::REVIEW_COST;
//                $existingBalanceAccrual->save();
//            }
//
//
//
//            $user->balance += UserBalanceAccrual::REVIEW_COST;
//            $user->save();
//
//            $referralOwner = User::query()->find($user->referral_owner);
//            if ($referralOwner) {
//                $referralOwnerBalanceAccrual = UserBalanceAccrual::query()
//                    ->where([
//                        'review_id' => $this->reviewId,
//                        'user_id' => $referralOwner->id,
//                        'type' => UserBalanceAccrual::VIEW_REFERRAL,
//                        'date' => $currentDate,
//                    ])
//                    ->first();
//
//                if (!$referralOwnerBalanceAccrual) {
//                    UserBalanceAccrual::query()->create([
//                        'review_id' => $this->reviewId,
//                        'user_id' => $referralOwner->id,
//                        'type' => UserBalanceAccrual::VIEW_REFERRAL,
//                        'accrual' => UserBalanceAccrual::REFERRAL_COST,
//                        'date' => $currentDate,
//                    ]);
//                } else {
//                    $referralOwnerBalanceAccrual->accrual += UserBalanceAccrual::REFERRAL_COST;
//                    $referralOwnerBalanceAccrual->save();
//                }
//
//                $referralOwner->balance += UserBalanceAccrual::REFERRAL_COST;
//                $referralOwner->referral_balance += UserBalanceAccrual::REFERRAL_COST;
//                $referralOwner->save();
//            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            echo $e->getMessage();
        }
    }
}
