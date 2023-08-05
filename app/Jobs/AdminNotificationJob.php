<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\TelegramApiService\TelegramAdminNotificationApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class AdminNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected string $message){}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(TelegramAdminNotificationApiService $telegramBotService): void
    {
        try {
            $telegramBotService->sendMessage(config('telegrambot.admin_user_id'), $this->message);
        } catch (Throwable $e) {
            logger()->error($e);
        }
    }
}
