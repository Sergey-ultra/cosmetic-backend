<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\TelegramBotService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
    public function handle(TelegramBotService $telegramBotService)
    {
        $telegramBotService->sendMessage(config('telegrambot.telegram_chat_id'), $this->message);
    }
}
