<?php

namespace App\Console\Commands;

use App\Utils\Sms;
use App\Utils\Rzd;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class CheckNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $keys = Redis::keys('*notification*');
        foreach ($keys as $key) {
            $redisKey = str_replace(config('database.redis.options.prefix'), '', $key);
            $redisData = json_decode(Redis::get($redisKey));
            $rzdResponse = Rzd::get($redisData->date, $redisData->to);
            foreach ($rzdResponse as $item) {
                if ($item->number === $redisData->number) {
                    $this->info('Есть места!');
                    Sms::send(env('PHONE'), 'Test');
                    Redis::del($redisKey);
                }
            }
        }
    }
}
